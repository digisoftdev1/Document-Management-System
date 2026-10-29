<?php

namespace App\Http\Controllers;

use App\Models\{BudgetType, Document, DocumentCategory, FiscalYear, GovernmentLevel, RepositoryFile};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDocuments = Document::count();
        $totalFiles = RepositoryFile::count();

        $totalStorage = RepositoryFile::sum('size') + Document::sum('file_size');
        $storageUsed = \App\Support\Bytes::format($totalStorage);

        $governmentStats = GovernmentLevel::leftJoin('documents', 'government_levels.id', '=', 'documents.government_level_id')
            ->select('government_levels.name as government', DB::raw('COUNT(documents.id) as total'))
            ->groupBy('government_levels.id', 'government_levels.name')
            ->orderByDesc('total')->get();

        $budgetStats = BudgetType::leftJoin('documents', 'budget_types.id', '=', 'documents.budget_type_id')
            ->select('budget_types.name as budget', DB::raw('COUNT(documents.id) as total'))
            ->groupBy('budget_types.id', 'budget_types.name')
            ->orderByDesc('total')->get();
        $categoryStats = DocumentCategory::query()
            ->leftJoin('documents', 'document_categories.id', '=', 'documents.document_category_id')
            ->select(
                'document_categories.id',
                'document_categories.name_en',
                'document_categories.name_np',
                DB::raw('COUNT(documents.id) as total')
            )
            ->groupBy(
                'document_categories.id',
                'document_categories.name_en',
                'document_categories.name_np'
            )
            ->orderByDesc('total')
            ->get();

        $uncategorizedDocuments = Document::whereNull('document_category_id')->count();

        $categoryTotal = $categoryStats->sum('total') + $uncategorizedDocuments;
        $fiscalYearStats = FiscalYear::query()
            ->leftJoinSub(
                Document::query()
                    ->select('fiscal_year_id', DB::raw('COUNT(*) as document_total'))
                    ->groupBy('fiscal_year_id'),
                'document_counts',
                fn($join) => $join->on('fiscal_years.id', '=', 'document_counts.fiscal_year_id')
            )
            ->leftJoinSub(
                RepositoryFile::query()
                    ->select('fiscal_year_id', DB::raw('COUNT(*) as archived_total'))
                    ->where('extension', 'pdf')
                    ->whereNotNull('fiscal_year_id')
                    ->groupBy('fiscal_year_id'),
                'archive_counts',
                fn($join) => $join->on('fiscal_years.id', '=', 'archive_counts.fiscal_year_id')
            )
            ->select(
                'fiscal_years.name',
                DB::raw('
            COALESCE(document_counts.document_total, 0)
            + COALESCE(archive_counts.archived_total, 0) as total
        ')
            )
            ->orderBy('fiscal_years.id')
            ->get();

        $now = Carbon::now();

        $documentsThisMonth = Document::whereBetween('created_at', [
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth(),
        ])->count();

        $lastMonth = $now->copy()->subMonth();

        $lastMonthDocuments = Document::whereBetween('created_at', [
            $lastMonth->copy()->startOfMonth(),
            $lastMonth->copy()->endOfMonth(),
        ])->count();

        $documentGrowth = $lastMonthDocuments
            ? round((($documentsThisMonth - $lastMonthDocuments) / $lastMonthDocuments) * 100, 1)
            : 100;

        $documentsToday = Document::whereBetween('created_at', [
            today()->startOfDay(),
            today()->endOfDay(),
        ])->count();

        return view('dashboard', compact(
            'totalDocuments',
            'totalFiles',
            'storageUsed',
            'governmentStats',
            'budgetStats',
            'fiscalYearStats',
            'documentsThisMonth',
            'documentGrowth',
            'documentsToday',
            'categoryStats',
            'uncategorizedDocuments',
            'categoryTotal',
        ));
    }
}
