<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\StoreRequest;
use App\Http\Requests\Orders\UpdateRequest;
use App\Models\HistoryOrders;
use App\Models\OrderStatuses;
use App\Models\Users;
use Illuminate\Http\Request;

class HistoryOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete', 'index_by_user', 'getStatuses']]);
    }

    public function index()
    {
        $orders = HistoryOrders::with([
            'color:id,name', // Завантажуємо лише `id` та `name` з `color`
            'size:id,size',  // Завантажуємо лише `id` та `name` з `size`
            'user'
        ])->get();

        return response()->json($orders, 200);
    }

    public function getStatuses(){
        $statuses = OrderStatuses::get();
        return response()->json($statuses, 200);
    }

    public function index_by_user(Users $user)
    {
        $orders = $user->orders;
        return response()->json($orders, 200);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $order = HistoryOrders::create($data);
        return response()->json($order, 200);
    }

    public function update(UpdateRequest $request, HistoryOrders $order)
    {
        $data = $request->validated();
        $order->update($data);
        return response()->json($order, 200);
    }
    public function delete(HistoryOrders $order)
    {
        $order->delete();
        return response()->json(['message'=>'done'],200);
    }
}
