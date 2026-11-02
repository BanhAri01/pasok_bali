<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesMonth;
use App\Models\Receipt;
use App\Models\SupplyRequest;
use App\Services\ComplianceCalculator;
use App\Services\RecommendationEngine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ResolvesMonth;

    public function __invoke(Request $request, ComplianceCalculator $compliance, RecommendationEngine $recommendations): View
    {
        $business = $request->attributes->get('business');
        $month = $this->month($request);
        $report = $compliance->forMonth($business->id, $month);

        return view('app.dashboard', [
            'business' => $business,
            'month' => $month,
            'report' => $report,
            'recommendations' => $recommendations->forMonth($business, $month, $report, 1),
            'recentReceipts' => Receipt::query()->with('supplier:id,name')->latest()->limit(6)->get(),
            'requests' => SupplyRequest::query()->with(['supplier:id,name', 'product:id,name'])->latest()->limit(5)->get(),
        ]);
    }
}
