<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::orderBy('name_en')->get();

        return view('document-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('document-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'name_en' => ['nullable', 'string', 'max:255', 'unique:document_categories,name_en'],
            'name_np' => ['nullable', 'string', 'max:255', 'unique:document_categories,name_np'],
        ]);

        DocumentCategory::create($validated);

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Document category created successfully.');
    }

    public function edit(DocumentCategory $document_category): View
    {
        return view('document-categories.edit', [
            'category' => $document_category,
        ]);
    }

    public function update(Request $request, DocumentCategory $document_category): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => [

                'nullable',
                'string',
                'max:255',
                'unique:document_categories,name_en,' . $document_category->id,
            ],
            'name_np' => [

                'nullable',
                'string',
                'max:255',
                'unique:document_categories,name_np,' . $document_category->id,
            ],
        ]);

        $document_category->update($validated);

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Document category updated successfully.');
    }

    public function destroy(DocumentCategory $document_category): RedirectResponse
    {
        $document_category->delete();

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Document category deleted successfully.');
    }
}