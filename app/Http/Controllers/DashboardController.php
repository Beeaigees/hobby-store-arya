<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Restock;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Produk dianggap "menipis" jika stoknya sama dengan atau di bawah angka ini.
    private const LOW_STOCK_THRESHOLD = 5;

    public function index(): View
    {
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

        $summary = [
            'month' => [
                'income'  => (int) Sale::whereBetween('sold_at', [$monthStart, $monthEnd])->sum('total'),
                'expense' => (int) Restock::whereBetween('restocked_at', [$monthStart, $monthEnd])->sum('total'),
                'sales_count' => Sale::whereBetween('sold_at', [$monthStart, $monthEnd])->count(),
            ],
            'all' => [
                'income'  => (int) Sale::sum('total'),
                'expense' => (int) Restock::sum('total'),
                'sales_count' => Sale::count(),
            ],
        ];

        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $topProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('sale', fn ($query) => $query->whereBetween('sold_at', [$monthStart, $monthEnd]))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(5)
            ->get();

        $latestSales = Sale::with('user')
            ->latest('sold_at')
            ->latest('id')
            ->limit(5)
            ->get();

        $latestRestocks = Restock::with('supplier')
            ->latest('restocked_at')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'summary'          => $summary,
            'monthLabel'       => now()->format('F Y'),
            'lowStockLimit'    => self::LOW_STOCK_THRESHOLD,
            'lowStockProducts' => $lowStockProducts,
            'topProducts'      => $topProducts,
            'latestSales'      => $latestSales,
            'latestRestocks'   => $latestRestocks,
        ]);
    }
}