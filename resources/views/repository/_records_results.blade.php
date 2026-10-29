<div
    class="grid grid-cols-2 sm:grid-cols-3
           md:grid-cols-4 lg:grid-cols-5
           xl:grid-cols-5 2xl:grid-cols-6
           gap-4 pt-6">

    {{-- ================= Fiscal Years ================= --}}
    @if (!request()->filled('fy') && !request()->filled('search'))

        @foreach ($folders as $folder)
            <a href="{{ route('repository.records', ['fy' => $folder->id]) }}" class="block">

                <div class="repo-item folder-card group rounded-2xl
                   border border-gray-200 bg-white
                   p-4 h-40
                   hover:shadow-lg hover:border-[#03396c]/30
                   transition-all duration-200
                   cursor-pointer select-none"
                    data-type="record-folder" data-name="{{ $folder->name }}"
                    data-open="{{ route('repository.records', ['fy' => $folder->id]) }}"
                    data-folders="{{ $folder->totalFolders() }}" data-files="{{ $folder->totalFiles() }}"
                    data-storage="{{ $folder->formatted_storage }}"
                    data-created="{{ $folder->created_at?->format('d M Y h:i A') }}"
                    data-updated="{{ $folder->updated_at?->format('d M Y h:i A') }}">

                    <div class="flex flex-col items-center justify-center h-full">

                        <i
                            class="fa-solid fa-folder text-yellow-400 text-6xl
                       group-hover:scale-105 transition duration-200">
                        </i>

                        <p class="mt-3 text-sm font-semibold text-gray-700
                       text-center line-clamp-2 break-words px-1"
                            title="{{ $folder->name }}">
                            {{ $folder->name }}
                        </p>

                    </div>

                </div>

            </a>
        @endforeach

    @endif


    {{-- ================= Governments ================= --}}
    @if (request()->filled('fy') && !request()->filled('gov'))

        @foreach ($folders as $folder)
            <a href="{{ route('repository.records', [
                'fy' => $fy->id,
                'gov' => $folder->id,
            ]) }}"
                class="block">

                <div class="repo-item folder-card group rounded-2xl border border-gray-200
                   bg-white p-4 h-40 hover:shadow-lg
                   hover:border-[#03396c]/30
                   transition-all duration-200
                   cursor-pointer select-none"
                    data-type="record-folder" data-name="{{ $folder->name }}"
                    data-open="{{ route('repository.records', [
                        'fy' => $fy->id,
                        'gov' => $folder->id,
                    ]) }}"
                    data-folders="{{ $folder->totalFolders() }}" data-files="{{ $folder->totalFiles($fy->id) }}"
                    data-storage="{{ $folder->formattedStorage($fy->id) }}"
                    data-created="{{ $folder->created_at?->format('d M Y h:i A') }}"
                    data-updated="{{ $folder->updated_at?->format('d M Y h:i A') }}">

                    <div class="flex flex-col items-center justify-center h-full">

                        <i
                            class="fa-solid fa-folder text-yellow-400 text-6xl
                       group-hover:scale-105 transition duration-200">
                        </i>

                        <p class="mt-3 text-sm font-semibold text-gray-700
                       text-center line-clamp-2 break-words px-1"
                            title="{{ $folder->name }}">
                            {{ $folder->name }}
                        </p>

                    </div>

                </div>

            </a>
        @endforeach

    @endif


    {{-- ================= Program Types ================= --}}
    @if (request()->filled('gov') && !request()->filled('budget'))

        @foreach ($folders as $folder)
            <a href="{{ route('repository.records', [
                'fy' => $fy->id,
                'gov' => $government->id,
                'budget' => $folder->id,
            ]) }}"
                class="block">

                <div class="repo-item folder-card group rounded-2xl border border-gray-200
                   bg-white p-4 h-40 hover:shadow-lg
                   hover:border-[#03396c]/30
                   transition-all duration-200
                   cursor-pointer select-none"
                    data-type="record-folder" data-name="{{ $folder->name }}"
                    data-open="{{ route('repository.records', [
                        'fy' => $fy->id,
                        'gov' => $government->id,
                        'budget' => $folder->id,
                    ]) }}"
                    data-created="{{ $folder->created_at?->format('d M Y h:i A') }}"
                    data-updated="{{ $folder->updated_at?->format('d M Y h:i A') }}"
                    data-folders="{{ $folder->totalFolders() }}"
                    data-files="{{ $folder->totalFiles($fy->id, $government->id) }}"
                    data-storage="{{ $folder->formattedStorage($fy->id, $government->id) }}">

                    <div class="flex flex-col items-center justify-center h-full">

                        <i
                            class="fa-solid fa-folder text-yellow-400 text-6xl
                       group-hover:scale-105 transition duration-200">
                        </i>

                        <p class="mt-3 text-sm font-semibold text-gray-700
                       text-center line-clamp-2 break-words px-1"
                            title="{{ $folder->name }}">
                            {{ $folder->name }}
                        </p>

                    </div>

                </div>

            </a>
        @endforeach

    @endif

    {{-- ================= Documents ================= --}}
    @if (request()->filled('budget') || (request()->filled('search') && !request()->filled('fy')))

        @forelse($files as $file)
            <div class="repositoryGrid repo-item file-card group rounded-2xl border border-gray-200
           bg-white p-4 h-40 hover:border-[#03396c]/30 hover:shadow-lg
           transition-all duration-200 cursor-pointer select-none"
                data-type="document" data-name="{{ $file->document_name }}" data-size="{{ $file->formatted_size }}"
                data-created="{{ $file->created_at?->format('d M Y h:i A') }}"
                data-updated="{{ $file->updated_at?->format('d M Y h:i A') }}"
                data-owner="{{ $file->creator?->name }}" data-open="{{ route('repository.recordview', $file) }}"
                data-download="{{ Storage::url($file->pdf_path) }}" data-file-number="{{ $file->file_number }}"
                data-fiscal="{{ $file->fiscalYear?->name }}" data-government="{{ $file->governmentLevel?->name }}"
                data-budget="{{ $file->budgetType?->name }}" data-month="{{ $file->month?->name }}"
                data-day="{{ \App\Helpers\NepaliHelper::number($file->day) }}">

                <div class="flex flex-col items-center justify-center h-full">

                    <x-pdf-icon class="w-16 h-16 group-hover:scale-105 transition" />
                    <h3 class="mt-3 text-sm font-semibold text-gray-700 text-center
                   line-clamp-2 break-all leading-5 px-1"
                        title="{{ $file->document_name }}">
                        {{ $file->document_name }}
                    </h3>

                    <p class="text-[11px] text-gray-400 mt-1 truncate">
                        {{ $file->formatted_size }}
                    </p>

                </div>

            </div>

        @empty

            <div class="col-span-full">

                <div class="rounded-xl border bg-white py-20 text-center">

                    <i class="fa-solid fa-folder-open text-6xl text-gray-300 mb-4"></i>

                    <h3 class="text-xl font-semibold text-gray-700">

                        No Documents Found

                    </h3>

                </div>

            </div>
        @endforelse

    @endif

</div>

@if (request()->filled('budget') &&
        isset($files) &&
        $files instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator &&
        $files->hasPages())
    <div id="paginationContainer" class="mt-10">
        {{ $files->links() }}
    </div>
@endif
