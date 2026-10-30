<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'petugas']);
    }

    public function index()
    {
        // Statistik ringkasan
        $stats = [
            'total'       => Complaint::count(),
            'pending'     => Complaint::where('status', 'pending')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved'    => Complaint::where('status', 'resolved')->count(),
            'rejected'    => Complaint::where('status', 'rejected')->count(),
        ];

        // Laporan terbaru
        $latestComplaints = Complaint::with('category')
            ->latest()
            ->limit(8)
            ->get();

        // Data grafik: laporan per bulan (12 bulan terakhir)
        $chartData = $this->getMonthlyChartData();

        // Laporan per kategori
        $categoryStats = DB::table('complaints')
            ->join('categories', 'complaints.category_id', '=', 'categories.id')
            ->select('categories.name', 'categories.color', 'categories.icon',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when complaints.status = 'pending' then 1 else 0 end) as pending"),
                DB::raw("sum(case when complaints.status = 'resolved' then 1 else 0 end) as resolved")
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderBy('total', 'desc')
            ->get();

        // Koleksi bulanan terstruktur untuk view blade
        $monthlyStats = collect($chartData['labels'] ?? [])->map(function ($label, $idx) use ($chartData) {
            return (object) [
                'month_name'  => $label,
                'total'       => $chartData['totals'][$idx] ?? 0,
                'resolved'    => $chartData['resolved'][$idx] ?? 0,
                'in_progress' => $chartData['in_progress'][$idx] ?? 0,
            ];
        });

        return view('admin.dashboard', compact('stats', 'latestComplaints', 'chartData', 'categoryStats', 'monthlyStats'));
    }

    private function getMonthlyChartData(): array
    {
        // Titik awal periode grafik laporan: Oktober 2026
        $startDate = \Carbon\Carbon::create(2026, 10, 1)->startOfMonth();
        $totalMonths = max(12, $startDate->diffInMonths(now()) + 1);

        $months = collect(range(0, $totalMonths - 1))->map(function ($i) use ($startDate) {
            return $startDate->copy()->addMonths($i)->format('Y-m');
        });

        $bulanIndo = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
        ];

        // Format tanggal sesuai driver database (MySQL: DATE_FORMAT, SQLite: strftime, PostgreSQL: TO_CHAR)
        $driver = DB::getDriverName();
        $dateExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql'  => "TO_CHAR(created_at, 'YYYY-MM')",
            default  => "DATE_FORMAT(created_at, '%Y-%m')", // MySQL / MariaDB
        };

        // Menggabungkan query statistik bulanan menjadi satu query efisien
        $monthlyRows = DB::table('complaints')
            ->select(
                DB::raw("{$dateExpr} as month"),
                DB::raw("count(*) as total"),
                DB::raw("sum(case when status = 'resolved' then 1 else 0 end) as resolved"),
                DB::raw("sum(case when status = 'in_progress' then 1 else 0 end) as in_progress")
            )
            ->where('created_at', '>=', $startDate->toDateTimeString())
            ->groupBy(DB::raw($dateExpr))
            ->get()
            ->keyBy('month');

        return [
            'period'      => 'Okt 2026 – ' . ($bulanIndo[substr($months->last(), 5, 2)] ?? '') . ' ' . substr($months->last(), 0, 4),
            'labels'      => $months->map(function($m) use ($bulanIndo) {
                $y = substr($m, 0, 4);
                $mn = substr($m, 5, 2);
                return ($bulanIndo[$mn] ?? $mn) . ' ' . $y;
            })->values()->toArray(),
            'totals'      => $months->map(fn($m) => (int) ($monthlyRows->get($m)->total ?? 0))->values()->toArray(),
            'resolved'    => $months->map(fn($m) => (int) ($monthlyRows->get($m)->resolved ?? 0))->values()->toArray(),
            'in_progress' => $months->map(fn($m) => (int) ($monthlyRows->get($m)->in_progress ?? 0))->values()->toArray(),
        ];
    }
}
