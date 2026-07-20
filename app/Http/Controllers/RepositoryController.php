<?php

namespace App\Http\Controllers;

use App\Http\Requests\{StoreRepositoryFileRequest, UpdateRepositoryFileRequest};
use App\Models\{BudgetType, Document, FiscalYear, Folder, GovernmentLevel, Month, RepositoryFile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RepositoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->type === 'digital')
            return view('repository.records');

        $folder = Folder::with('creator')->find($request->folder);
        $search = $request->search;

        $folders = Folder::where('parent_id', $folder?->id)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')->get();

        $files = RepositoryFile::where('folder_id', $folder?->id)
            ->when($search, fn($q) => $q->where('original_name', 'like', "%{$search}%"))
            ->latest()->paginate(48)->withQueryString();

        return view('repository.archived', compact('folder', 'folders', 'files'));
    }

    public function records(Request $request)
    {
        $fy = $request->fy;
        $gov = $request->gov;
        $budget = $request->budget;

        if (!$fy)
            return view('repository.records', ['level' => 'fy', 'folders' => FiscalYear::latest()->get()]);

        if (!$gov)
            return view('repository.records', [
                'level' => 'government',
                'fy' => FiscalYear::findOrFail($fy),
                'folders' => GovernmentLevel::orderBy('id')->get()
            ]);

        if (!$budget)
            return view('repository.records', [
                'level' => 'budget',
                'fy' => FiscalYear::findOrFail($fy),
                'government' => GovernmentLevel::findOrFail($gov),
                'folders' => BudgetType::orderBy('id')->get()
            ]);

        $query = Document::with(['month', 'fiscalYear', 'governmentLevel', 'budgetType', 'creator'])
            ->where('fiscal_year_id', $fy)->where('government_level_id', $gov)->where('budget_type_id', $budget);

        if ($request->filled('month'))
            $query->where('month_id', $request->month);
        if ($request->filled('day'))
            $query->where('day', $request->day);
        if ($request->filled('search'))
            $query->where(fn($q) => $q->where('document_name', 'like', "%{$request->search}%")->orWhere('file_number', 'like', "%{$request->search}%"));

        return view('repository.records', [
            'level' => 'files',
            'fy' => FiscalYear::findOrFail($fy),
            'government' => GovernmentLevel::findOrFail($gov),
            'budgetType' => BudgetType::findOrFail($budget),
            'files' => $query->paginate(48)->withQueryString(),
            'months' => Month::orderBy('order_no')->get(),
            'days' => range(1, 32)
        ]);
    }

    public function create()
    {
        return view('repository.create', ['folders' => Folder::orderBy('name')->get()]);
    }

    public function store(StoreRepositoryFileRequest $request)
    {
        $folder = $request->filled('folder_id') ? Folder::findOrFail($request->folder_id) : null;
        $directory = $folder ? $folder->full_path : 'repository';

        foreach ($request->file('files') as $file) {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = $file->extension();
            $filename = $file->getClientOriginalName();
            $counter = 1;

            while (Storage::disk('public')->exists("$directory/$filename")) {
                $filename = "{$name} ({$counter}).{$ext}";
                $counter++;
            }

            $path = $file->storeAs($directory, $filename, 'public');
            RepositoryFile::create([
                'folder_id' => $request->folder_id,
                'original_name' => $filename,
                'stored_name' => $filename,
                'extension' => $ext,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'disk' => 'public',
                'path' => $path,
                'uploaded_by' => auth()->id()
            ]);
        }
        return back()->with('success', 'File(s) uploaded successfully.');
    }

    public function show(RepositoryFile $repository)
    {
        return response()->download(Storage::disk($repository->disk)->path($repository->path), $repository->original_name);
    }

    public function archivedview(RepositoryFile $repository)
    {
        return view('repository.viewer', [
            'repository' => $repository,
            'pdf' => Storage::disk($repository->disk)->url($repository->path),
            'title' => $repository->original_name
        ]);
    }

    public function recordsview(Document $repository)
    {
        return view('repository.viewer', [
            'repository' => $repository,
            'pdf' => Storage::url($repository->pdf_path),
            'title' => $repository->document_name
        ]);
    }

    public function edit(RepositoryFile $repository)
    {
        return view('repository.edit', ['repository' => $repository, 'folders' => Folder::orderBy('name')->get()]);
    }

    public function update(UpdateRepositoryFileRequest $request, RepositoryFile $repository)
    {
        $repository->update(['folder_id' => $request->folder_id]);
        return redirect()->route('repository.index')->with('success', 'File updated successfully.');
    }

    public function destroy(RepositoryFile $repository)
    {
        Storage::disk($repository->disk)->delete($repository->path);
        $repository->delete();
        return back()->with('success', 'File deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['items' => ['required', 'array'], 'items.*.id' => ['required', 'integer'], 'items.*.type' => ['required', 'in:file,folder']]);

        $deleted = $skipped = 0;
        foreach ($request->items as $item) {
            if ($item['type'] === 'file') {
                if ($file = RepositoryFile::find($item['id'])) {
                    Storage::disk($file->disk)->delete($file->path);
                    $file->delete();
                    $deleted++;
                } else
                    $skipped++;
            } else {
                $folder = Folder::find($item['id']);
                if ($folder && !$folder->children()->exists() && !$folder->files()->exists()) {
                    Storage::disk('public')->deleteDirectory($folder->full_path);
                    $folder->delete();
                    $deleted++;
                } else
                    $skipped++;
            }
        }

        if ($deleted === 0)
            return back()->with('error', 'Selected folders contain contents and cannot be deleted.');
        return back()->with($skipped > 0 ? 'warning' : 'success', $skipped > 0 ? "$deleted item(s) deleted. $skipped item(s) skipped." : 'Selected items deleted successfully.');
    }
}
