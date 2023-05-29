<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuyRequest;
use App\Http\Requests\ListPurchasesRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Product;
use App\Models\Purchase;
use App\Services\PurchasesListingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListPurchasesRequest $request, PurchasesListingService $service)
    {
        $query = $service->getPurchaseListQuery(
            $request->input('from'), $request->input('to'));

        $perPage = $request->input('per_page', 10);
        $collection = PurchaseResource::collection($query->paginate($perPage));

        return new LengthAwarePaginator($collection->forPage(null, $perPage), Product::count(), $perPage);
    }

    /**
     * Count total revenue over a time period
     */
    public function revenue(ListPurchasesRequest $request, PurchasesListingService $service)
    {
        $query = $service->getPurchaseListQuery(
            $request->input('from'), $request->input('to'));

        return response([
            'data' => [
                'total_revenue' => $query->sum('total_paid'),
            ]
        ]);
    }

    public function buy(BuyRequest $request)
    {
        DB::beginTransaction();
        $product = Product::where('sku', $request->input('sku'))->lockForUpdate()->firstOrFail();
        if ($product->amount > 0) {

            $purchase = new Purchase([
                'sku' => $product->sku,
                'total_paid' => $product->price,
                'user_id' => Auth::id(),
                'amount' => 1
            ]);
            $purchase->save();
            $product->amount -= 1;
            $product->save();

            DB::commit();

            return response(['purchase' => $purchase], 201);
        } else {
            DB::rollBack();
            return response([
                'error' => true,
                'error_msg' => "Product {$product->name} is out of stock"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        //
    }
}
