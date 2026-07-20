<div class="repo-item file-card group rounded-2xl border border-gray-200 bg-white
           p-4 h-40 hover:shadow-lg hover:border-[#03396c]/30
           transition-all duration-200 cursor-pointer select-none"
    data-id="{{ $file->id }}" data-type="file" data-name="{{ $file->original_name }}"
    data-size="{{ $file->formatted_size }}" data-mime="{{ $file->mime_type }}"
    data-open-url="{{ route('repository.archivedview', $file) }}"
    data-download-url="{{ route('repository.show', $file) }}" data-preview-url="{{ $file->url }}"
    data-open="{{ route('repository.archivedview', $file) }}" data-delete="{{ route('repository.destroy', $file) }}"
    data-update="{{ route('repository.update', $file) }}"
    data-created="{{ $file->created_at?->format('d M Y h:i A') }}"
    data-updated="{{ $file->updated_at?->format('d M Y h:i A') }}" data-owner="{{ $file->uploader?->name }}"
    data-path="{{ $file->repository_path }}">

    <div class="flex flex-col items-center justify-center h-full">

        <x-pdf-icon class="w-16 h-16 group-hover:scale-105 transition" />

        <h3 class="mt-3 text-sm font-semibold text-gray-700 text-center
                   line-clamp-2 break-all leading-5 px-1"
            title="{{ $file->original_name }}">
            {{ $file->original_name }}
        </h3>

        <p class="text-xs text-gray-400 mt-1">
            {{ $file->formatted_size }}
        </p>

    </div>
</div>
