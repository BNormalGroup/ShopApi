<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\StoreRequest;
use App\Http\Requests\Orders\UpdateOrderStatusRequest;
use App\Http\Requests\Orders\UpdateRequest;
use App\Models\HistoryOrders;
use App\Models\OrderStatuses;
use App\Models\Users;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class HistoryOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','store','update', 'delete', 'index_by_user', 'getStatuses', 'updateStatus','getUserOrders']]);
    }

    public function index()
    {
        $orders = HistoryOrders::withOnly([
            'color:id,name', // Завантажуємо лише `id` та `name` з `color`
            'size:id,size',  // Завантажуємо лише `id` та `name` з `size`
            'user',
            'item'
        ])->get();

        return response()->json($orders, 200);
    }

    public function getStatuses(){
        $statuses = OrderStatuses::get();
        return response()->json($statuses, 200);
    }

    public function getUserOrders(Users $user)
    {
        $orders = $user->orders;
        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {

    }

    public function updateStatus(UpdateOrderStatusRequest $request, $orderId)
    {
        // Знаходимо замовлення, або повертаємо 404, якщо не знайдено
        try {
            $order = HistoryOrders::findOrFail($orderId);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Зберігаємо новий статус замовлення
        try {
            $order->updateOrFail(['status_id' => $request->status_id]);

            return response()->json(['message' => 'Order status updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating order status',
                'error' => $e->getMessage(),
            ], 500);
        }
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
