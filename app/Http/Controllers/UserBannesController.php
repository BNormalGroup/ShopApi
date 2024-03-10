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
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete','index_by_user']]);
    }

    public function index()
    {
        $bans = Bannes::get();
        return response()->json($bans, 200);
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
