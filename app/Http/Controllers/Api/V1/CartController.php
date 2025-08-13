<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CartStoreRequest;
use App\Http\Requests\Api\V1\CartUpdateRequest;
use App\Http\Requests\FinancingAmountRequest;
use App\Http\Resources\Api\V1\CartResource;
use App\Models\Cart;
use App\Models\Category;
use App\Services\CartService;
use App\Services\PriceCalculationService;
use Illuminate\Support\Facades\Request;

class CartController extends Controller
{
    protected $cartService;
    protected $priceCalculationService;
    public function __construct(CartService $cartService, PriceCalculationService $priceCalculationService)
    {
        $this->cartService = $cartService;
        $this->priceCalculationService = $priceCalculationService;
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
            'user_id'       => auth()->id(),
            'category_id'   => $request['category_id'],
            'configuration' => $request['configuration'],
            'configuration_meta' => $category->configuration,
            'pricing_meta'  => $category->pricing,
            'adders'        => $request['adders'] ?? [],
            'price'         => $price,
            'appointment_id' => request('appointment_id', null),
        ])->load(['category']);
        return successResponse([
            'cart'  => new CartResource($cartItem),
        ]);
    }

    // List Cart Items for Auth User
    public function index(Request $request)
    {
        $cartItems = Cart::with('category')
            ->where('user_id', auth()->id())
            ->where('appointment_id', request('appointment_id', null))
            ->get();
        return successResponse([
            'cart'  => CartResource::collection($cartItems),
        ]);
    }

    // Update Cart Item
    public function update(CartUpdateRequest $request, $id)
    {
        $cartItem = Cart::where('user_id', auth()->id())
        ->where('appointment_id', request('appointment_id', null))
        ->findOrFail($id);


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
            'price'         => $price,
            'configuration_meta' => $category->configuration,
            'pricing_meta'  => $category->pricing,
        ]);
        return successResponse([
            'cart'  => new CartResource($cartItem),
        ]);
    }

    // Delete Single Cart Item
    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', auth()->id())
        ->where('appointment_id', request('appointment_id', null))
        ->findOrFail($id);
        $cartItem->delete();
        return successResponse([
            'message'  => 'cart item removed successfully.',
        ]);
    }

    // Clear Entire User Cart
    public function clear(Request $request)
    {
        Cart::where('user_id', auth()->id())
            ->where('appointment_id', request('appointment_id', null))
            ->delete();
        return successResponse([
            'message' => 'Cart Clear Successfully.'
        ]);
    }
    public function calculateFinancingAmount(FinancingAmountRequest $request)
    {
        $totalPrice = Cart::where('user_id', auth()->id())
        ->where('appointment_id', request('appointment_id', null))
        ->sum('price');
        if ($totalPrice == 0) {
            return errorResponse('Cart is empty or contains no priced items.', 400);
        }
        if (!is_null($request->mosaic_apr)) {
            // Calculate financing amounts
            $mosaicAmount = $this->priceCalculationService->calculateFinance(
                'mosaic',
                $totalPrice,
                $request->mosaic_apr
            );
        }
        if (!is_null($request->renew_solar_apr)) {
            $renewSolarAmount = $this->priceCalculationService->calculateFinance(
                'renew_solar',
                $totalPrice,
                $request->renew_solar_apr
            );
        }
        // Structure response data
        $data = [
            'mosaic_amount'      => $mosaicAmount ?? 0.00,
            'renew_solar_amount' => $renewSolarAmount ?? 0.00
        ];

        return successResponse($data);
    }
    public function getFinanceProviderAPROptions()
    {
        $financeOptions = config("finance.finance_options");
        return successResponse($financeOptions);
    }
}
