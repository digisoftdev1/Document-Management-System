<x-app-layout>

    <div class="py-24">

        <div class="px-6">

            <div class="flex items-center justify-between mb-4">

                <div>

                    <h2 class="text-3xl font-bold text-[#03396c]">

                        {{ __('Digital Records') }}

                    </h2>

                    <div class="mt-2 flex items-center flex-wrap gap-2 text-sm text-gray-500">

                        <a href="{{ route('repository.records') }}" class="hover:text-[#03396c] font-medium">
                            {{ __('Digital Records') }}
                        </a>

                        @if (request()->filled('fy'))
                            <i class="fa-solid fa-chevron-right text-xs"></i>

                            <a href="{{ route('repository.records', ['fy' => request('fy')]) }}"
                                class="hover:text-[#03396c]">
                                {{ $fy->name }}
                            </a>
                        @endif

                        @if (request()->filled('gov'))
                            <i class="fa-solid fa-chevron-right text-xs"></i>

                            <a href="{{ route('repository.records', [
                                'fy' => request('fy'),
                                'gov' => request('gov'),
                            ]) }}"
                                class="hover:text-[#03396c]">
                                {{ $government->name }}
                            </a>
                        @endif

                        @if (request()->filled('budget'))
                            <i class="fa-solid fa-chevron-right text-xs"></i>

                            <span class="font-medium text-[#03396c]">
                                {{ $budgetType->name }}
                            </span>
                        @endif

                    </div>

                </div>

                <div class="flex gap-3">

                    @if (request()->filled('fy'))
                        @php
                            $params = request()->only(['fy', 'gov', 'budget']);

                            if (request()->filled('budget')) {
                                unset($params['budget']);
                            } elseif (request()->filled('gov')) {
                                unset($params['gov']);
                            } elseif (request()->filled('fy')) {
                                unset($params['fy']);
                            }

                            $backUrl = route('repository.records', $params);
                        @endphp

                        <div class="flex gap-3">

                            @if (request()->filled('fy') || request()->filled('gov') || request()->filled('budget'))
                                <a href="{{ $backUrl }}"
                                    class="inline-flex items-center px-5 py-2 border rounded-lg hover:bg-gray-100">

                                    <i class="fa-solid fa-arrow-left mr-2"></i>

                                    {{ __('Back') }}

                                </a>
                            @endif

                        </div>
                    @endif

                </div>

            </div>
            {{-- Filters --}}
            @if (request()->filled('budget'))

                <div class="bg-white rounded-xl border shadow-sm p-5">
                    <form method="GET">
                        <input type="hidden" name="fy" value="{{ request('fy') }}">
                        <input type="hidden" name="gov" value="{{ request('gov') }}">
                        <input type="hidden" name="budget" value="{{ request('budget') }}">

                        <div class="flex flex-wrap items-center gap-3">

                            {{-- Month --}}
                            <div class="w-28 lg:w-56">
                                <select name="month"
                                    class="w-full rounded-lg border-gray-300 focus:ring-[#03396c] focus:border-[#03396c] text-sm lg:text-base">
                                    <option value="">All Months</option>

                                    @foreach ($months as $month)
                                        <option value="{{ $month->id }}" @selected(request('month') == $month->id)>
                                            {{ $month->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Day --}}
                            <div class="w-24 lg:w-40">
                                <select name="day"
                                    class="w-full rounded-lg border-gray-300 focus:ring-[#03396c] focus:border-[#03396c] text-sm lg:text-base">
                                    <option value="">{{ __('All Days') }}</option>

                                    @foreach ($days as $day)
                                        <option value="{{ $day['value'] }}" @selected(request('day') == $day['value'])>
                                            {{ $day['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex items-center gap-2">

                                <button
                                    class="px-3 lg:px-5 py-2 bg-[#03396c] text-white rounded-lg hover:bg-[#022d54] whitespace-nowrap text-sm">
                                    <i class="fa-solid fa-filter mr-1 lg:mr-2"></i>
                                    {{ __('Apply') }}
                                </button>

                                <a href="{{ route('repository.records', [
                                    'fy' => request('fy'),
                                    'gov' => request('gov'),
                                    'budget' => request('budget'),
                                ]) }}"
                                    class="px-3 lg:px-5 py-2 border rounded-lg hover:bg-gray-100 whitespace-nowrap text-sm">

                                    <i class="fa-solid fa-rotate-left mr-1 lg:mr-2"></i>
                                    {{ __('Reset') }}
                                </a>

                            </div>

                            {{-- Search --}}
                            <div class="ml-auto w-40 lg:w-64">
                                <div class="relative">

                                    <input id="searchInput" name="search" type="text" autocomplete="off"
                                        value="{{ request('search') }}" placeholder="{{ __('Search...') }}"
                                        class="w-full rounded-lg border pl-9 pr-3 py-2 text-sm">

                                    <i
                                        class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

                                </div>
                            </div>

                        </div>
                    </form>
                </div>

            @endif
            <div id="repositoryWrapper">
                <div
                    class="grid grid-cols-2 sm:grid-cols-3
           md:grid-cols-4 lg:grid-cols-5
           xl:grid-cols-5 2xl:grid-cols-6
           gap-4 pt-6">

                    {{-- ================= Fiscal Years ================= --}}
                    @if (!request()->filled('fy'))

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
                                    data-folders="{{ $folder->totalFolders() }}"
                                    data-files="{{ $folder->totalFiles() }}"
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
                                    data-folders="{{ $folder->totalFolders() }}"
                                    data-files="{{ $folder->totalFiles($fy->id) }}"
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

                    @if (request()->filled('budget'))


                        @forelse($files as $file)
                            <div class="repositoryGrid repo-item file-card group rounded-2xl border border-gray-200
           bg-white p-4 h-40 hover:border-[#03396c]/30 hover:shadow-lg
           transition-all duration-200 cursor-pointer select-none"
                                data-type="document" data-name="{{ $file->document_name }}"
                                data-size="{{ $file->formatted_size }}"
                                data-created="{{ $file->created_at?->format('d M Y h:i A') }}"
                                data-updated="{{ $file->updated_at?->format('d M Y h:i A') }}"
                                data-owner="{{ $file->creator?->name }}"
                                data-open="{{ route('repository.recordview', $file) }}"
                                data-download="{{ Storage::url($file->pdf_path) }}"
                                data-file-number="{{ $file->file_number }}"
                                data-fiscal="{{ $file->fiscalYear?->name }}"
                                data-government="{{ $file->governmentLevel?->name }}"
                                data-budget="{{ $file->budgetType?->name }}" data-month="{{ $file->month?->name }}"
                                data-day="{{ $file->day }}">

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
            </div>
            @if (request()->filled('budget') && isset($files) && $files->hasPages())
                <div id="paginationContainer" class="mt-10">
                    {{ $files->links() }}
                </div>
            @endif
        </div>

    </div>
    @include('repository.partials._context_menu')
    @include('repository.partials._properties_sidebar', [
        'recordsMode' => true,
    ])

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                let timer;
                const searchInput = document.getElementById('searchInput');

                searchInput?.addEventListener('input', function() {

                    clearTimeout(timer);

                    timer = setTimeout(async () => {

                        const url = new URL(window.location.href);

                        if (this.value.trim()) {
                            url.searchParams.set('search', this.value.trim());
                        } else {
                            url.searchParams.delete('search');
                        }

                        url.searchParams.delete('page');

                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const html = await response.text();

                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        const newWrapper = doc.getElementById('repositoryWrapper');

                        if (newWrapper) {
                            document.getElementById('repositoryWrapper').innerHTML =
                                newWrapper.innerHTML;

                            clearSelection?.();
                            initRepositoryEvents?.();
                        }

                        window.history.replaceState({}, '', url);

                    }, 300); // debounce
                });

                let selectedItem = null;

                const contextMenu =
                    document.getElementById('contextMenu');

                const sidebar =
                    document.getElementById('propertiesSidebar');

                const propName =
                    document.getElementById('propName');

                const propCreated =
                    document.getElementById('propCreated');

                const propUpdated =
                    document.getElementById('propUpdated');

                const propOwner =
                    document.getElementById('propOwner');

                const propSubtitle =
                    document.getElementById('propertiesSubtitle');

                const propIcon =
                    document.getElementById('propertiesIcon');

                const propType =
                    document.getElementById('propType');

                const propSize =
                    document.getElementById('propSize');

                // const propOpen =
                //     document.getElementById('recordPropOpen');

                const sizeRow =
                    document.getElementById('sizeRow');
                const propDownload = document.getElementById('recordPropDownload');
                const recordActions = document.getElementById('recordActions');


                function clearSelection() {

                    document.querySelectorAll('.repo-item')
                        .forEach(item => {

                            item.classList.remove(
                                'ring-2',
                                'ring-[#03396c]',
                                'bg-blue-50'
                            );

                        });

                    selectedItem = null;
                }

                function selectItem(item) {

                    clearSelection();

                    item.classList.add(
                        'ring-2',
                        'ring-[#03396c]',
                        'bg-blue-50'
                    );

                    selectedItem = item;
                }

                function initRepositoryEvents() {
                    document.querySelectorAll('.repo-item')
                        .forEach(item => {

                            item.addEventListener('click', function() {

                                selectItem(this);

                            });


                            item.addEventListener(
                                'click',
                                function() {

                                    window.location =
                                        this.dataset.open;

                                }
                            );


                            item.addEventListener(
                                'contextmenu',
                                function(e) {

                                    e.preventDefault();

                                    selectItem(this);

                                    openContextMenu(e);

                                    document
                                        .getElementById('menuRename')
                                        ?.classList.add('hidden');

                                    document
                                        .getElementById('menuDelete')
                                        ?.classList.add('hidden');

                                }
                            );

                        });
                }

                function openProperties(item) {

                    propName.textContent =
                        item.dataset.name;

                    propCreated.textContent =
                        item.dataset.created ?? '—';

                    propUpdated.textContent =
                        item.dataset.updated ?? '—';

                    propOwner.textContent =
                        item.dataset.owner ?? 'System';



                    // propOpen.href = item.dataset.open;

                    propDownload.href = item.dataset.download;






                    if (item.dataset.type === 'record-folder') {

                        propSubtitle.textContent = 'Folder';

                        propType.textContent = 'Folder';

                        propIcon.innerHTML = `
        <i class="
            fa-solid
            fa-folder
            text-yellow-400
            text-6xl
        "></i>
    `;

                        sizeRow.classList.add('hidden');

                        // Show statistics card
                        document.getElementById('folderStats')
                            ?.classList.remove('hidden');

                        document.getElementById('documentInfoCard')
                            ?.classList.add('hidden');

                        sizeRow.classList.add('hidden');

                        document.getElementById('descriptionCard')
                            ?.classList.add('hidden');

                        document.getElementById('fileActions')
                            ?.classList.add('hidden');

                        const propFolderCount =
                            document.getElementById('propFolderCount');

                        if (propFolderCount) {
                            propFolderCount.textContent =
                                item.dataset.folders ?? '0';
                        }

                        const propFileCount =
                            document.getElementById('propFileCount');

                        if (propFileCount) {
                            propFileCount.textContent =
                                item.dataset.files ?? '0';
                        }

                        const propStorage =
                            document.getElementById('propStorage');

                        if (propStorage) {
                            propStorage.textContent =
                                item.dataset.storage ?? '0';
                        }

                        recordActions.classList.add('hidden');



                    } else {


                        document.getElementById('fileActions')
                            ?.classList.add('hidden');

                        document.getElementById('recordActions')
                            ?.classList.remove('hidden');
                        propSubtitle.textContent =
                            'PDF Document';

                        propType.textContent =
                            'PDF Document';

                        propSize.textContent =
                            item.dataset.size ?? '—';

                        document.getElementById('folderStats')
                            ?.classList.add('hidden');

                        document.getElementById('documentInfoCard')
                            ?.classList.remove('hidden');

                        sizeRow.classList.remove('hidden');

                        propIcon.innerHTML =
                            `
                <x-pdf-icon class="w-16 h-16 group-hover:scale-105 transition" />
                `;


                        document.getElementById(
                                'propFileNumber'
                            ).textContent =
                            item.dataset.fileNumber ?? '—';

                        document.getElementById(
                                'propFiscalYear'
                            ).textContent =
                            item.dataset.fiscal ?? '—';

                        document.getElementById(
                                'propGovernment'
                            ).textContent =
                            item.dataset.government ?? '—';

                        document.getElementById(
                                'propBudget'
                            ).textContent =
                            item.dataset.budget ?? '—';

                        document.getElementById(
                                'propMonth'
                            ).textContent =
                            item.dataset.month ?? '—';

                        document.getElementById(
                                'propDay'
                            ).textContent =
                            item.dataset.day ?? '—';
                    }
                    console.log('OPENING');
                    sidebar.classList.remove(
                        'translate-x-full'
                    );
                }


                function closeProperties() {

                    sidebar.classList.add(
                        'translate-x-full'
                    );

                }


                document
                    .getElementById(
                        'closePropertiesBtn'
                    )
                    .addEventListener(
                        'click',
                        closeProperties
                    );


                document
                    .getElementById('menuOpen')
                    .addEventListener('click', function() {

                        if (!selectedItem) return;

                        window.open(
                            selectedItem.dataset.open,
                            '_blank'
                        );

                    });


                document
                    .getElementById(
                        'menuProperties'
                    )
                    .addEventListener(
                        'click',
                        function() {

                            if (!selectedItem)
                                return;

                            contextMenu.classList
                                .add('hidden');

                            openProperties(
                                selectedItem
                            );

                        }
                    );


                document.addEventListener('click', function(e) {

                    if (!e.target.closest('#contextMenu')) {
                        contextMenu.classList.add('hidden');
                    }

                    if (
                        !e.target.closest('.repo-item') &&
                        !e.target.closest('#propertiesSidebar') &&
                        !e.target.closest('#contextMenu')
                    ) {
                        clearSelection();
                        closeProperties();
                    }
                });


                document.addEventListener(
                    'keydown',
                    function(e) {

                        if (
                            e.key === 'Escape'
                        ) {

                            closeProperties();

                        }

                    }
                );

                initRepositoryEvents();

            });
        </script>
    @endpush
</x-app-layout>
