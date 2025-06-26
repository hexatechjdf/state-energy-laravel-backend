<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderStoreRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Jobs\SendOrderToWebhook;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    // Add to Order
    public function store(OrderStoreRequest $request)
    {

        $user = auth()->user();
        $cartItems = Cart::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return errorResponse('Cart is empty');
        }

        // Calculate total cart amount
        $totalAmount = $cartItems->sum('price');

        // Calculate order amount (total - loan_financed_amount)
        $loanFinanced = $request->loan_financed_amount ?? 0;
        $orderAmount = $totalAmount - $loanFinanced;
        // Create order
        $order = Order::create([
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
            'loan_financed_amount'    => $loanFinanced,
            'finance_provider'        => $request->finance_provider,
            'total_amount'            => $totalAmount,
            'order_amount'            => $orderAmount ?? 0.00,
        ]);

        // Move cart items to order items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id'      => $order->id,
                'category_id'   => $item->category_id,
                'configuration' => json_encode($item->configuration),
                'adders'        => json_encode($item->adders),
                'unit_price'    => $item->price,
                'total_price'   => $item->price,
            ]);
        }

        // Clear user's cart
        Cart::where('user_id', $user->id)->delete();
        dispatch(new SendOrderToWebhook($order));
        return successResponse([
            'message' => 'Order created successfully',
            'order'  => new OrderResource($order),
        ]);
    }
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('orderItems')->get();
        return successResponse([
            'order'  => OrderResource::collection($orders),
        ]);
    }

    public function show($id)
    {
        $order = Order::with('orderItems')->where('user_id', Auth::id())->findOrFail($id);
        return successResponse([
            'message' => 'Order created successfully',
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
}
