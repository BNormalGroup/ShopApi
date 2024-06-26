<?php

namespace App\Http\Controllers;

use App\Models\Bannes;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
    /**
     * Register a User.
     *
     * @return JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'lastName' => 'string',
            'firstName' => 'required|string',
            'birthday' => 'string',
            'isAdmin' => 'required|bool',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = Users::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $isBanned = Bannes::where('user_id', auth()->user()->id)->exists();
        if($isBanned)
            return response()->json(['banned' => 'true'], 401);
        else
            return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated Users.
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    public function update(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'birthday' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->firstName = $request->input('firstName');
        $user->lastName = $request->input('lastName');
        $user->email = $request->input('email');
        $user->birthday = $request->input('birthday');
        $user->save();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User updated successfully',
            'access_token' => $token
        ]);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|max:255',
            'new_password' => 'required|string|max:255|min:6',
        ]);

        $user = Auth::user();

        // Перевіряємо, чи співпадає поточний пароль
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Неправильний поточний пароль'], 400);
        }

        // Оновлюємо пароль
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Пароль успішно змінено'], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        $isBanned = Bannes::where('user_id', auth()->user()->id)->exists();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
            "isBanned" => $isBanned
        ]);
    }
}
