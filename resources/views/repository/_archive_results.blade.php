<div id="repositoryGrid"
    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">

    @foreach ($folders as $childFolder)
        @include('repository.archived_folder_card', [
            'folder' => $childFolder,
        ])
    @endforeach

    @foreach ($files as $file)
        @include('repository.archived_file_card')
    @endforeach

    @if ($folders->isEmpty() && $files->isEmpty())
        <div class="col-span-full py-16 text-center text-gray-400">
            <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
            <p>{{ __('No matching folders or documents found.') }}</p>
        </div>
    @endif

</div>
