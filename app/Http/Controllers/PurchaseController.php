<?php

namespace App\Http\Controllers;

use App\Exceptions\OutOfStock;
use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * @throws OutOfStock
     */
    public function buy(PurchaseRequest $request)
    {
        DB::beginTransaction();
        $product = Product::where('sku', $request->input('sku'))->lockForUpdate()->first();
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
            throw OutOfStock::create($product);
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
