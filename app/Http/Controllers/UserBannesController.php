<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bans\StoreRequest;
use App\Http\Requests\Bans\UpdateRequest;
use App\Models\Bannes;
use App\Models\Users;
use Illuminate\Http\Request;

class UserBannesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $users = Users::with('bans')->get()->map(function ($user) {
            $user->isBanned = $user->bans->isNotEmpty(); // Додаємо поле isBanned згідно з наявністю бану
            return $user;
        });

        return response()->json($users, 200);
    }



    public function index_by_user(Users $user)
    {
        $bans = $user->bans;
        return response()->json($bans, 200);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $ban = Bannes::create($data);
        return response()->json($ban, 200);
    }

    public function update(UpdateRequest $request, Bannes $ban)
    {
        $data = $request->validated();
        $ban->update($data);
        return response()->json($ban, 200);
    }
    public function delete(Bannes $ban)
    {
        $ban->delete();
        return response()->json(['message'=>'done'],200);
    }
}
