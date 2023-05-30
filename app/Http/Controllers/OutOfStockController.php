<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;

#[Group('Products')]
class OutOfStockController extends Controller
{
    /**
     * List out-of-stock products
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    #[Authenticated]
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 100);
        $page = $request->input('page', 1);

        $query = Product::where('amount', '<=', 0);

        $collection = ProductResource::collection($query->paginate($perPage));

        return new LengthAwarePaginator($collection->forPage(null, $perPage), Product::count(), $perPage, $page);
    }
}
