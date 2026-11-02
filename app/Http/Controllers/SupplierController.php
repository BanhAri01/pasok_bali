<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __invoke(Request $request): View
    {
        $business = $request->attributes->get('business');
        $category = $request->query('kategori');
        $regency = $request->query('kabupaten');

        $suppliers = Supplier::query()
            ->visibleTo($business->id)
            ->where('active', true)
            ->when(in_array($regency, config('pasok.regencies'), true), fn ($query) => $query->where('regency', $regency))
            ->whereHas('products', fn ($query) => $query
                ->where('origin', 'bali')
                ->when(array_key_exists((string) $category, config('pasok.categories')), fn ($inner) => $inner->whereHas('product', fn ($product) => $product->where('category', $category))))
            ->with(['products' => fn ($query) => $query->with('product:id,name,category')->orderBy('origin')])
            ->orderByRaw("case when verification = 'terverifikasi' then 0 else 1 end")
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('app.suppliers', [
            'suppliers' => $suppliers,
            'category' => $category,
            'regency' => $regency,
        ]);
    }
}
