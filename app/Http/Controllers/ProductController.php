<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchProductRequest $request)
    {
        $perPage = 10;

        $query = Product::whereRaw('1=1');
        $exactMatchAttributes = ['sku', 'name', 'category'];
        foreach ($exactMatchAttributes as $attribute) {
            if ($request->has($attribute)) {
                $query = $query->where($attribute, $request->input($attribute));
            }
        }

        $collection = ProductResource::collection($query->paginate($perPage));

        return new LengthAwarePaginator($collection->forPage(null, $perPage), Product::count(), $perPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = new Product($request->all());
        $product->save();

        return response([
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());

        return response(['data' => new ProductResource($product)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (!Auth::user()->can(Role::DELETE_PRODUCT)) {
            abort(403);
        }
        $product->delete();
    }
}
