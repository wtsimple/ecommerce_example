<?php


namespace App\Services;


use App\Http\Requests\SearchProductRequest;
use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ProductSearchService
{
    /**
     * @param SearchProductRequest $request
     * @return Builder
     */
    public function buildSearchQuery(SearchProductRequest $request)
    {
        // start with dummy query
        $query = Product::whereRaw('1=1');

        // exact matching attributes
        $exactMatchAttributes = ['sku', 'name', 'category'];
        foreach ($exactMatchAttributes as $attribute) {
            if ($request->has($attribute)) {
                $query = $query->where($attribute, '=', $request->input($attribute));
            }
        }

        // lower bound attributes
        $lowerBoundAttributes = [
            'rating_higher_than' => 'avg_rating',
            'min_price' => 'price'
        ];
        foreach ($lowerBoundAttributes as $requestKey => $attribute) {
            if ($request->has($requestKey)) {
                $query = $query->where($attribute, '>=', $request->input($requestKey));
            }
        }

        // upper bound attributes
        $lowerBoundAttributes = [
            'max_price' => 'price',
        ];
        foreach ($lowerBoundAttributes as $requestKey => $attribute) {
            if ($request->has($requestKey)) {
                $query = $query->where($attribute, '<=', $request->input($requestKey));
            }
        }

        // tags
        if ($request->has('tags') && count($request->input('tags')) > 0) {
            $query = $query->withAnyTags($request->input('tags'));
        }

        // full text search
        if ($request->has('text_query')) {
            $textToMatch = $request->input('text_query');
            $escapedText = DB::connection()->getPdo()->quote('%' . $textToMatch . '%');
            $query = $query->whereRaw(
                "(description like $escapedText OR additional_info like $escapedText)"
            );
        }
        return $query;
    }
}
