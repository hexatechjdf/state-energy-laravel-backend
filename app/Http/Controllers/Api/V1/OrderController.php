<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderStoreRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Jobs\SendOrderToWebhook;
use App\Jobs\UpdateContactInCRM;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(OrderStoreRequest $request)
    {
        $user = loginUser();
        $cartItems = Cart::where('user_id', $user->id)->where('appointment_id', request('appointment_id', null))->get();
        if ($cartItems->isEmpty()) {
            return errorResponse('Cart is empty');
        }
        $totalAmount = $cartItems->sum('price');
        $loanFinanced = $request->loan_financed_amount ?? 0;
        $orderAmount = $totalAmount - $loanFinanced;
        $order = Order::updateOrCreate([
            'user_id'        => $user->id,
            'appointment_id' => $request->appointment_id,
        ], [
            'user_id'                 => $user->id,
            'first_name'              => $request->first_name,
            'last_name'               => $request->last_name,
            'email'                   => $request->email,
            'phone_number'            => $request->phone_number,
            'address'                 => $request->address,
            'zip_code'                => $request->zip_code,
            'city'                    => $request->city,
            'monthly_utility_bill'    => $request->monthly_utility_bill,
            'monthly_insurance_bill'  => $request->monthly_insurance_bill,
            'monthly_water_swerage_bill'  => $request->monthly_water_swerage_bill,
            'monthly_gas_bill'  => $request->monthly_gas_bill,
            'loan_financed_amount'    => $loanFinanced,
            'finance_provider'        => $request->finance_provider,
            'total_amount'            => $totalAmount,
            'order_amount'            => $orderAmount ?? 0.00,
            'appointment_id'          => $request->appointment_id ?? null,
            'contact_id'              => $request->contact_id ?? null,
        ]);
        $newCategoryIds = $cartItems->pluck('category_id')->toArray();
        OrderItem::where('order_id', $order->id)
            ->whereNotIn('category_id', $newCategoryIds)
            ->delete();
        foreach ($cartItems as $item) {
            OrderItem::updateOrCreate([
                'order_id'    => $order->id,
                'category_id' => $item->category_id,
            ], [
                'order_id'      => $order->id,
                'category_id'   => $item->category_id,
                'configuration' => json_encode($item->configuration),
                'adders'        => json_encode($item->adders),
                'unit_price'    => $item->price,
                'total_price'   => $item->price,
            ]);
        }

        //Cart::where('user_id', $user->id)->where('appointment_id', request('appointment_id', null))->delete();
        dispatch(new SendOrderToWebhook($order));
        dispatch(new UpdateContactInCRM($order));
        return successResponse([
            'message' => 'Order created successfully',
            'order'  => new OrderResource($order),
        ]);
    }
    public function index()
    {
        $orders = Order::where('user_id', loginUser()->id)->with('orderItems')->get();
        return successResponse([
            'order'  => OrderResource::collection($orders),
        ]);
    }

    public function show($id)
    {
        $order = Order::with('orderItems')->where('user_id', loginUser()->id)->findOrFail($id);
        return successResponse([
            'message' => 'Order retrieved successfully',
            'order'  => new OrderResource($order),
        ]);
    }

    public function destroy($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status !== 'pending') {
            return errorResponse('Only pending orders can be deleted.');
        }
        $order->delete();
        return successResponse([
            'message' => 'Order created successfully',
        ]);
    }
    public function getOrderByAppointment($appointmentId)
    {
        $order = Order::where('user_id', loginUser()->id)
            ->where('appointment_id', $appointmentId)
            ->where('status', '!=', 'canceled')
            ->with('orderItems')
            ->latest()
            ->first();
        if (!$order) {
            return successResponse([
                'order' => null,
            ]);
        }
        return successResponse([
            'order'  => new OrderResource($order),
        ]);
    }
}
