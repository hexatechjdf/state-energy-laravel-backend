<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CartStoreRequest;
use App\Http\Requests\Api\V1\CartUpdateRequest;
use App\Models\Cart;
use App\Models\Category;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // Add to Cart
    public function store(CartStoreRequest $request)
    {

        $category = Category::findOrFail($request['category_id']);

        $price = $this->cartService->calculatePrice(
            $category,
            $request->configuration,
            $request->adders ?? []
        );

        $cartItem = Cart::create([
            'user_id'       => auth()->id()??1,
            'category_id'   => $request['category_id'],
            'configuration' => $request['configuration'],
            'adders'        => $request['adders'] ?? [],
            'price'         => $price
        ]);

        return response()->json($cartItem, 201);
    }

    // List Cart Items for Auth User
    public function index()
    {
        $cartItems = Cart::with('category')
            ->where('user_id', auth()->id()??1)
            ->get();

        return response()->json($cartItems);
    }

    // Update Cart Item
    public function update(CartUpdateRequest $request, $id)
    {
        dd("dd");
        $cartItem = Cart::where('user_id', auth()->id())->findOrFail($id);


        $category = $cartItem->category;

        $newConfig = $request['configuration'] ?? $cartItem->configuration;
        $newAdders = $request['adders'] ?? $cartItem->adders;

        $price = $this->cartService->calculatePrice(
            $category,
            $newConfig,
            $newAdders
        );

        $cartItem->update([
            'configuration' => $newConfig,
            'adders'        => $newAdders,
            'price'         => $price
        ]);

        return response()->json($cartItem);
    }

    // Delete Single Cart Item
    public function destroy($id)
    {
        $cartItem = Cart::where('user_id', auth()->id())->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    // Clear Entire User Cart
    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
}

?>