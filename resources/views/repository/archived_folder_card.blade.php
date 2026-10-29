<div class="repo-item folder-card group rounded-2xl border border-gray-200 bg-white
           p-4 h-40 hover:shadow-lg hover:border-[#03396c]/30
           transition-all duration-200 cursor-pointer select-none"
    data-id="{{ $folder->id }}" data-type="folder" data-name="{{ $folder->name }}"
    data-description="{{ $folder->description }}" data-open="{{ route('repository.index', ['folder' => $folder->id]) }}"
    data-delete="{{ route('folders.destroy', $folder) }}" data-update="{{ route('folders.update', $folder) }}"
    data-created="{{ $folder->created_at?->format('d M Y h:i A') }}"
    data-updated="{{ $folder->updated_at?->format('d M Y h:i A') }}" data-owner="{{ $folder->creator?->name }}"
    data-folders="{{ $folder->total_folders }}" data-files="{{ $folder->total_files }}"
    data-storage="{{ number_format($folder->total_storage / 1024 / 1024, 2) }} MB"
    data-path="{{ $folder->repository_path }}">

    <div class="flex flex-col items-center justify-center h-full">

        <i class="fa-solid fa-folder text-yellow-400 text-6xl
                  group-hover:scale-105 transition">
        </i>

        <p class="mt-3 text-sm font-semibold text-gray-700 text-center
                  line-clamp-2 break-words px-1">
            {{ $folder->name }}
        </p>

    </div>
</div>
