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

                <div class="flex gap-3 items-center">

                    {{-- Global search (root level only) --}}
                    @if (!request()->filled('fy'))
                        <form id="searchForm" method="GET" action="{{ route('repository.records') }}">
                            <div class="relative max-w-md lg:w-96">

                                <input id="searchInput" name="search" type="text" autocomplete="off"
                                    value="{{ request('search') }}" placeholder="{{ __('Search documents...') }}"
                                    class="w-full rounded-lg border pl-10 pr-4 py-2">

                                <i
                                    class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                            </div>
                        </form>
                    @endif

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
                @include('repository._records_results')
            </div>
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
                let searchController;
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

                        // server returns only the results grid for live search
                        searchController?.abort();
                        searchController = new AbortController();

                        try {
                            const response = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                signal: searchController.signal
                            });

                            const html = await response.text();

                            document.getElementById('repositoryWrapper').innerHTML = html;
                        } catch (error) {
                            if (error.name !== 'AbortError') {
                                throw error;
                            }
                            return;
                        }

                        clearSelection?.();
                        initRepositoryEvents?.();

                        window.history.replaceState({}, '', url);

                    }, 350); // debounce
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
