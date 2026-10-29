<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\Folder;
use App\Models\RepositoryFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    public function store(StoreFolderRequest $request)
    {
        $folder = Folder::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'fiscal_year_id' => Folder::resolveFiscalYearId(
                $request->parent_id,
                $request->name
            ),
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        Storage::disk('public')->makeDirectory($folder->full_path);

        Folder::shiftStats($folder->parent_id, folders: 1);

        return back()->with('success', 'Folder created successfully.');
    }

    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        DB::transaction(function () use ($request, $folder) {

            $oldPath = $folder->full_path;
            $oldParentId = $folder->parent_id;

            // Subtree aggregates are already on the row itself
            $movedFolders = 1 + (int) $folder->total_folders;
            $movedFiles = (int) $folder->total_files;
            $movedBytes = (int) $folder->total_storage;

            $folder->update([
                'parent_id' => $request->parent_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ]);

            if ($oldParentId !== $folder->parent_id) {

                Folder::shiftStats($oldParentId, folders: -$movedFolders, files: -$movedFiles, bytes: -$movedBytes);
                Folder::shiftStats($folder->parent_id, folders: $movedFolders, files: $movedFiles, bytes: $movedBytes);

                Folder::clearParentCache();
            }

            $folder->refresh();

            $newPath = $folder->full_path;

            if ($oldPath !== $newPath) {

                Storage::disk('public')->move($oldPath, $newPath);

                RepositoryFile::where('path', 'like', $oldPath . '/%')
                    ->get()
                    ->each(function ($file) use ($oldPath, $newPath) {
                        $file->update([
                            'path' => str_replace($oldPath, $newPath, $file->path),
                        ]);
                    });
            }
        });

        return back()->with('success', 'Folder updated successfully.');
    }

    public function destroy(Folder $folder)
    {
        if ($folder->children()->exists()) {
            return back()->with('error', 'Folder contains subfolders.');
        }

        if ($folder->files()->exists()) {
            return back()->with('error', 'Folder contains files.');
        }

        Storage::disk('public')->deleteDirectory($folder->full_path);

        $parentId = $folder->parent_id;

        $folder->delete();

        Folder::shiftStats($parentId, folders: -1);

        return back()->with('success', 'Folder deleted successfully.');
    }
}
