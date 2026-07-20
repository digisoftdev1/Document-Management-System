<?php

namespace App\Http\Controllers;

use App\Models\{BudgetType, Document, FiscalYear, GovernmentLevel, RepositoryFile};
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Storage};

class DashboardController extends Controller
{
    public function index()
    {
        $totalDocuments = Document::count();
        $totalFiles = RepositoryFile::count();

        $documentStorage = Document::cursor()->sum(
            fn($d) =>
            ($d->pdf_path && Storage::disk('public')->exists($d->pdf_path))
            ? Storage::disk('public')->size($d->pdf_path) : 0
        );

        $totalStorage = RepositoryFile::sum('size') + $documentStorage;
        $storageUsed = $this->formatBytes($totalStorage);

        $governmentStats = GovernmentLevel::leftJoin('documents', 'government_levels.id', '=', 'documents.government_level_id')
            ->select('government_levels.name as government', DB::raw('COUNT(documents.id) as total'))
            ->groupBy('government_levels.id', 'government_levels.name')
            ->orderByDesc('total')->get();

        $budgetStats = BudgetType::leftJoin('documents', 'budget_types.id', '=', 'documents.budget_type_id')
            ->select('budget_types.name as budget', DB::raw('COUNT(documents.id) as total'))
            ->groupBy('budget_types.id', 'budget_types.name')
            ->orderByDesc('total')->get();

        $fiscalYearStats = FiscalYear::leftJoin('documents', 'fiscal_years.id', '=', 'documents.fiscal_year_id')
            ->select('fiscal_years.name', DB::raw('COUNT(documents.id) as total'))
            ->groupBy('fiscal_years.id', 'fiscal_years.name')
            ->orderBy('fiscal_years.id')->get();

        $now = Carbon::now();
        $documentsThisMonth = Document::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $lastMonth = $now->copy()->subMonth();
        $lastMonthDocuments = Document::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();

        $documentGrowth = $lastMonthDocuments
            ? round((($documentsThisMonth - $lastMonthDocuments) / $lastMonthDocuments) * 100, 1)
            : 100;
        $documentsToday = Document::whereDate('created_at', today())->count();
        return view('dashboard', compact(
            'totalDocuments',
            'totalFiles',
            'storageUsed',
            'governmentStats',
            'budgetStats',
            'fiscalYearStats',
            'documentsThisMonth',
            'documentGrowth',
            'documentsToday'
        ));
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }
}
