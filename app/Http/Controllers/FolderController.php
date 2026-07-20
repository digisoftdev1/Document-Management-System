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
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        Storage::disk('public')->makeDirectory($folder->full_path);

        return back()->with('success', 'Folder created successfully.');
    }

    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        DB::transaction(function () use ($request, $folder) {

            $oldPath = $folder->full_path;

            $folder->update([
                'parent_id' => $request->parent_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ]);

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

        $folder->delete();

        return back()->with('success', 'Folder deleted successfully.');
    }
}
