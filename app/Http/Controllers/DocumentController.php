<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\BudgetType;
use App\Models\Document;
use App\Models\FiscalYear;
use App\Models\GovernmentLevel;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $documents = Document::with([
            'fiscalYear',
            'governmentLevel',
            'budgetType',
            'month',
        ])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('document_name', 'like', "%{$search}%")
                        ->orWhere('file_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('documents.index', [
            'documents' => $documents,
            'fiscalYears' => FiscalYear::orderByDesc('id')->get(),
            'governmentLevels' => GovernmentLevel::orderBy('name')->get(),
            'budgetTypes' => BudgetType::orderBy('name')->get(),
            'months' => Month::orderBy('order_no')->get(),
            'days' => range(1, 32),
        ]);
    }

    public function fetchDocuments(Request $request)
    {
        $query = Document::with(['fiscalYear', 'governmentLevel', 'budgetType', 'month']);

        if ($request->fiscal_year_id)
            $query->where('fiscal_year_id', $request->fiscal_year_id);
        if ($request->government_id)
            $query->where('government_level_id', $request->government_id);
        if ($request->budget_type_id)
            $query->where('budget_type_id', $request->budget_type_id);
        if ($request->month_id)
            $query->where('month_id', $request->month_id);
        if ($request->day)
            $query->where('day', $request->day);

        return datatables()->of($query->latest()->get())
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('documents.create', [
            'fiscalYears' => FiscalYear::orderByDesc('id')->get(),
            'governmentLevels' => GovernmentLevel::orderBy('id')->get(),
            'budgetTypes' => BudgetType::orderBy('id')->get(),
            'months' => Month::orderBy('order_no')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = Str::slug($data['document_name']) . '.pdf';
            $path = $file->storeAs('documents', $filename, 'public');
            $data['pdf_path'] = $path;
        }

        $data['created_by'] = auth()->id();

        Document::create($data);

        flash()->success('Document created successfully.');

        return redirect()->route('documents.index');
    }

    public function show(Document $document)
    {

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        return view('documents.edit', [
            'document' => $document,
            'fiscalYears' => FiscalYear::orderByDesc('id')->get(),
            'governmentLevels' => GovernmentLevel::orderBy('id')->get(),
            'budgetTypes' => BudgetType::orderBy('id')->get(),
            'months' => Month::orderBy('order_no')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $data = $request->validated();
        $slug = Str::slug($data['document_name']);
        $newFilename = $slug . '.pdf';

        if ($request->hasFile('file')) {
            if ($document->pdf_path && Storage::disk('public')->exists($document->pdf_path)) {
                Storage::disk('public')->delete($document->pdf_path);
            }

            $path = $request->file('file')->storeAs('documents', $newFilename, 'public');
            $data['pdf_path'] = $path;
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();
        flash()->success('Document deleted successfully.');
        return redirect()->route('documents.index');
    }
}
