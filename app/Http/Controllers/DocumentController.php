<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\BudgetType;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\FiscalYear;
use App\Models\GovernmentLevel;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $documents = Document::with([
            'fiscalYear',
            'governmentLevel',
            'budgetType',
            'month',
            'documentCategory',
        ])
            ->when($search, fn($q) => \App\Support\Fulltext::searchClause(
                $q,
                $search,
                ['document_name', 'file_number']
            ))
            ->latest()
            ->get();

        return view('documents.index', [
            'documents' => $documents,
            'categories' => DocumentCategory::orderBy('name_en')->get(),
            'fiscalYears' => FiscalYear::orderByDesc('id')->get(),
            'governmentLevels' => GovernmentLevel::orderBy('name')->get(),
            'budgetTypes' => BudgetType::orderBy('name')->get(),
            'documentCategories' => DocumentCategory::orderBy('name_en')->get(),
            'months' => Month::orderBy('order_no')->get(),
            'days' => array_map(
                fn($day) => [
                    'value' => $day,
                    'label' => \App\Helpers\NepaliHelper::number($day),
                ],
                range(1, 32)
            ),
        ]);
    }

    public function fetchDocuments(Request $request)
    {
        $query = Document::with([
            'fiscalYear',
            'governmentLevel',
            'budgetType',
            'month',
            'documentCategory',
        ]);

        if ($request->fiscal_year_id) {
            $query->where('fiscal_year_id', $request->fiscal_year_id);
        }

        if ($request->government_id) {
            $query->where('government_level_id', $request->government_id);
        }

        if ($request->budget_type_id) {
            $query->where('budget_type_id', $request->budget_type_id);
        }

        if ($request->month_id) {
            $query->where('month_id', $request->month_id);
        }

        if ($request->day) {
            $query->where('day', $request->day);
        }

        if ($request->document_category_id) {
            $query->where('document_category_id', $request->document_category_id);
        }

        return datatables()->of($query->latest()->get())
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        return view('documents.create', [
            'categories' => DocumentCategory::orderBy('name_en')->get(),
            'fiscalYears' => FiscalYear::orderByDesc('id')->get(),
            'governmentLevels' => GovernmentLevel::orderBy('id')->get(),
            'budgetTypes' => BudgetType::orderBy('id')->get(),
            'months' => Month::orderBy('order_no')->get(),
            'days' => array_map(
                fn($day) => [
                    'value' => $day,
                    'label' => \App\Helpers\NepaliHelper::number($day),
                ],
                range(1, 32)
            ),
        ]);
    }

    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = Str::slug($data['document_name']) . '.pdf';
            $path = $file->storeAs('documents', $filename, 'public');

            $data['pdf_path'] = $path;
            $data['file_size'] = $file->getSize();
        }

        $data['created_by'] = auth()->id();

        Document::create($data);

        flash()->success('Document created successfully.');

        return redirect()->route('documents.index');
    }

    public function show(Document $document)
    {
        $document->load('documentCategory');

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        return view('documents.edit', [
            'document' => $document,
            'categories' => DocumentCategory::orderBy('name_en')->get(),
            'fiscalYears' => FiscalYear::orderByDesc('id')->get(),
            'governmentLevels' => GovernmentLevel::orderBy('id')->get(),
            'budgetTypes' => BudgetType::orderBy('id')->get(),
            'months' => Month::orderBy('order_no')->get(),
            'days' => array_map(
                fn($day) => [
                    'value' => $day,
                    'label' => \App\Helpers\NepaliHelper::number($day),
                ],
                range(1, 32)
            ),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $data = $request->validated();
        // dd($data);
        $slug = Str::slug($data['document_name']);
        $newFilename = $slug . '.pdf';

        if ($request->hasFile('file')) {
            if (
                $document->pdf_path &&
                Storage::disk('public')->exists($document->pdf_path)
            ) {
                Storage::disk('public')->delete($document->pdf_path);
            }

            $file = $request->file('file');
            $path = $file->storeAs('documents', $newFilename, 'public');

            $data['pdf_path'] = $path;
            $data['file_size'] = $file->getSize();
        } elseif ($document->document_name !== $data['document_name']) {
            $oldPath = $document->pdf_path;
            $newPath = 'documents/' . $newFilename;

            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->move($oldPath, $newPath);
                $data['pdf_path'] = $newPath;
            }
        }

        $document->update($data);

        flash()->success('Document updated successfully.');

        return redirect()->route('documents.index');
    }

    public function destroy(Document $document)
    {
        if ($document->pdf_path) {
            Storage::disk('public')->delete($document->pdf_path);
        }

        $document->delete();

        flash()->success('Document deleted successfully.');

        return redirect()->route('documents.index');
    }
}