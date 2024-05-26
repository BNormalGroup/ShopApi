<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\StoreRequest;
use App\Http\Requests\Orders\UpdateOrderStatusRequest;
use App\Http\Requests\Orders\UpdateRequest;
use App\Models\HistoryOrders;
use App\Models\OrderDeliveryAddress;
use App\Models\OrderProduct;
use App\Models\OrderStatuses;
use App\Models\Users;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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
        $validatedData = $request->validate([
            'products' => 'required|array',
            'paymentMethod' => 'required|string',
            'address.email' => 'required|email',
            'address.firstName' => 'required|string',
            'address.lastName' => 'required|string',
            'address.phoneNumber' => 'required|string',
            'address.country' => 'required|string',
            'address.postcode' => 'required|string',
            'address.city' => 'required|string',
            'address.streetAddress' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $address = OrderDeliveryAddress::create($validatedData['address']);

            $historyOrder = HistoryOrders::create([
                'delivery_address_id' => $address->id,
                'user_id' => $request['user_id'],
                'status_id' => 1,
            ]);

            foreach ($validatedData['products'] as $product) {
                OrderProduct::create([
                    'order_id' => $historyOrder->id,
                    'item_id' => $product['product']['id'],
                    'color' => $product['color'],
                    'size' => $product['selectedSize'],
                    'quantity' => $product['quantity'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Order created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Order creation failed', 'error' => $e->getMessage()], 500);
        }
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
