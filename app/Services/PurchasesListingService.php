<?php


namespace App\Services;


use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PurchasesListingService
{
    /**
     * @param string|null $from
     * @param string|null $to
     * @return Builder
     */
    public function getPurchaseListQuery(?string $from, ?string $to): Builder
    {
        $query = Purchase::orderByDesc('created_at');
        if (!empty($from)) {
            $from = Carbon::parse($from);
            $query = $query->where('created_at', '>=', $from);
        }
        if (!empty($to)) {
            $to = Carbon::parse($to);
            $query = $query->where('created_at', '<=', $to);
        }
        return $query;
    }
}
