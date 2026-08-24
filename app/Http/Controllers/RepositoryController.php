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
        if ($request->type === 'digital') {
            return view('repository.records');
        }

        $folder = $request->filled('folder')
            ? Folder::with('creator')->findOrFail($request->folder)
            : null;

        $search = trim($request->input('search', ''));

        if ($search !== '') {

            $folders = Folder::query()
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name')
                ->paginate(24, ['*'], 'folders_page')
                ->withQueryString();

            $files = RepositoryFile::query()
                ->with('folder')
                ->where('original_name', 'like', "%{$search}%")
                ->latest()
                ->paginate(48, ['*'], 'files_page')
                ->withQueryString();

            return view('repository.archived', compact(
                'folder',
                'folders',
                'files'
            ))->with('globalSearch', true);
        }

        $folders = Folder::query()
            ->where('parent_id', $folder?->id)
            ->get()
            ->sortBy(function ($folder) {

                $name = trim($folder->name);

                if (is_numeric($name)) {
                    return (int) $name;
                }

                if (preg_match('/^(\d+)/', $name, $matches)) {
                    return (int) $matches[1];
                }

                return PHP_INT_MAX;
            })
            ->values();

        $files = RepositoryFile::query()
            ->where('folder_id', $folder?->id)
            ->latest()
            ->paginate(48)
            ->withQueryString();

        return view('repository.archived', compact(
            'folder',
            'folders',
            'files'
        ));
    }
    public function uploadFolder(Request $request)
    {
        $request->validate([
            'folder_id' => ['nullable', 'exists:folders,id'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'mimes:pdf', 'max:200240'],
            'relative_paths' => ['nullable', 'array'],
            'relative_paths.*' => ['required', 'string', 'max:1000'],
            'folder_paths' => ['nullable', 'string'],
        ]);

        $files = $request->file('files', []);
        $relativePaths = $request->input('relative_paths', []);
        $folderPaths = json_decode($request->input('folder_paths', '[]'), true);

        if (!is_array($folderPaths)) {
            $folderPaths = [];
        }

        if (count($files) !== count($relativePaths)) {
            return response()->json(['success' => false, 'message' => 'File/path mismatch.'], 422);
        }

        $rootFolder = $request->filled('folder_id') ? Folder::findOrFail($request->folder_id) : null;
        $cache = [];


        foreach ($folderPaths as $folderPath) {

            $folderPath = str_replace('\\', '/', $folderPath);

            if (str_contains($folderPath, '..')) {
                continue;
            }

            $parts = array_values(array_filter(explode('/', $folderPath), fn($part) => $part !== ''));

            if (!$parts) {
                continue;
            }

            $parentId = $rootFolder?->id;

            foreach ($parts as $folderName) {
                $folder = $this->findOrCreateFolder($parentId, $folderName, $cache);
                $parentId = $folder->id;
            }
        }

        foreach ($files as $index => $file) {

            $relativePath = str_replace('\\', '/', $relativePaths[$index]);

            if (str_contains($relativePath, '..')) {
                continue;
            }

            $parts = array_values(array_filter(explode('/', $relativePath), fn($part) => $part !== ''));

            if (!$parts) {
                continue;
            }

            $filename = array_pop($parts);
            $parentId = $rootFolder?->id;

            foreach ($parts as $folderName) {
                $folder = $this->findOrCreateFolder($parentId, $folderName, $cache);
                $parentId = $folder->id;
            }

            $targetFolder = $parentId ? Folder::find($parentId) : $rootFolder;
            $directory = $targetFolder ? $targetFolder->full_path : 'repository';

            $name = pathinfo($filename, PATHINFO_FILENAME);
            $ext = strtolower($file->extension());
            $storedFilename = $filename;
            $counter = 1;

            while (Storage::disk('public')->exists("$directory/$storedFilename")) {
                $storedFilename = "{$name} ({$counter}).{$ext}";
                $counter++;
            }

            $path = $file->storeAs($directory, $storedFilename, 'public');

            RepositoryFile::create([
                'folder_id' => $targetFolder?->id,
                'original_name' => $storedFilename,
                'stored_name' => $storedFilename,
                'extension' => $ext,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'disk' => 'public',
                'path' => $path,
                'uploaded_by' => auth()->id(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Folder structure and files imported successfully.']);
    }
    private function findOrCreateFolder(
        ?int $parentId,
        string $name,
        array &$cache
    ): Folder {

        $key = ($parentId ?? 'root') . '|' . $name;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $folder = Folder::firstOrCreate(
            [
                'parent_id' => $parentId,
                'name' => $name,
            ],
            [
                'slug' => \Illuminate\Support\Str::slug($name),
                'description' => null,
                'created_by' => auth()->id(),
            ]
        );

        return $cache[$key] = $folder;
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
            'days' => array_map(
                fn($day) => [
                    'value' => $day,
                    'label' => \App\Helpers\NepaliHelper::number($day),
                ],
                range(1, 32)
            ),
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

    // public function bulkDestroy(Request $request)
    // {
    //     $request->validate(['items' => ['required', 'array'], 'items.*.id' => ['required', 'integer'], 'items.*.type' => ['required', 'in:file,folder']]);

    //     $deleted = $skipped = 0;
    //     foreach ($request->items as $item) {
    //         if ($item['type'] === 'file') {
    //             if ($file = RepositoryFile::find($item['id'])) {
    //                 Storage::disk($file->disk)->delete($file->path);
    //                 $file->delete();
    //                 $deleted++;
    //             } else
    //                 $skipped++;
    //         } else {
    //             $folder = Folder::find($item['id']);
    //             if ($folder && !$folder->children()->exists() && !$folder->files()->exists()) {
    //                 Storage::disk('public')->deleteDirectory($folder->full_path);
    //                 $folder->delete();
    //                 $deleted++;
    //             } else
    //                 $skipped++;
    //         }
    //     }

    //     if ($deleted === 0)
    //         return back()->with('error', 'Selected folders contain contents and cannot be deleted.');
    //     return back()->with($skipped > 0 ? 'warning' : 'success', $skipped > 0 ? "$deleted item(s) deleted. $skipped item(s) skipped." : 'Selected items deleted successfully.');
    // }
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.type' => ['required', 'in:file,folder'],
        ]);

        $deleted = $skipped = 0;

        foreach ($request->items as $item) {

            // Delete individual file
            if ($item['type'] === 'file') {

                if ($file = RepositoryFile::find($item['id'])) {

                    Storage::disk($file->disk)->delete($file->path);

                    $file->delete();

                    $deleted++;

                } else {
                    $skipped++;
                }

                continue;
            }

            // Delete folder recursively
            $folder = Folder::find($item['id']);

            if (!$folder) {
                $skipped++;
                continue;
            }

            // Delete all files belonging to this folder and descendants
            $deleteFolderContents = function (Folder $currentFolder) use (&$deleteFolderContents) {

                // Delete files in this folder
                foreach ($currentFolder->files()->get() as $file) {

                    Storage::disk($file->disk)->delete($file->path);

                    $file->delete();
                }

                // Recursively delete child folders
                foreach ($currentFolder->children()->get() as $childFolder) {

                    $deleteFolderContents($childFolder);

                    $childFolder->delete();
                }
            };

            $deleteFolderContents($folder);

            // Delete physical directory
            Storage::disk('public')->deleteDirectory($folder->full_path);

            // Delete the selected root folder
            $folder->delete();

            $deleted++;
        }

        if ($deleted === 0) {

            return back()->with(
                'error',
                'No items were deleted.'
            );
        }

        return back()->with(
            $skipped > 0 ? 'warning' : 'success',
            $skipped > 0
            ? "$deleted item(s) deleted. $skipped item(s) skipped."
            : 'Selected items and all their contents were deleted successfully.'
        );
    }
}
