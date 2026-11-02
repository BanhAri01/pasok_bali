<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesMonth;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Services\AiWriter;
use App\Services\ComplianceCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    use ResolvesMonth;

    public function __invoke(Request $request, ComplianceCalculator $compliance, AiWriter $writer): View
    {
        $business = $request->attributes->get('business');
        $month = $this->month($request);
        $report = $compliance->forMonth($business->id, $month);

        $suppliers = ReceiptItem::query()
            ->join('receipts', 'receipts.id', '=', 'receipt_items.receipt_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'receipts.supplier_id')
            ->where('receipts.business_id', $business->id)
            ->where('receipts.status', Receipt::APPROVED)
            ->whereBetween('receipts.receipt_date', ComplianceCalculator::range($month))
            ->whereNotNull('receipt_items.category')
            ->selectRaw("coalesce(suppliers.name, receipts.supplier_name_raw, 'Tanpa nama pemasok') as supplier_name, suppliers.regency, suppliers.verification, receipt_items.origin, sum(receipt_items.quantity_kg) as kg, sum(receipt_items.total_price) as rupiah")
            ->groupBy('supplier_name', 'suppliers.regency', 'suppliers.verification', 'receipt_items.origin')
            ->orderByDesc('kg')
            ->toBase()
            ->get();

        return view('app.report', [
            'business' => $business,
            'month' => $month,
            'report' => $report,
            'suppliers' => $suppliers,
            'summary' => $report['approved_receipts'] > 0 ? $writer->monthlySummary($business, $report) : null,
        ]);
    }
}
