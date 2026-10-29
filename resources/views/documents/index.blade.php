<x-app-layout>

    <div class="py-24">
        <div class="px-4 sm:px-6 lg:px-8 max-w-full">

            <div class="bg-white overflow-hidden shadow-sm rounded-sm">

                <div class="p-6">

                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-6">

                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ __('Records') }}
                        </h2>

                        <a href="{{ route('documents.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">

                            <i class="fa-solid fa-plus mr-2"></i>

                            {{ __('Add Record') }}

                        </a>

                    </div>

                    {{-- Filters --}}
                    <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50 p-4">

                        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-600">
                            <i class="fa-solid fa-filter"></i>
                            {{ __('Filters') }}
                            <span id="activeFilterCount"
                                class="hidden rounded-full bg-blue-600 px-2 py-0.5 text-xs font-bold text-white"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-5 items-end">

                            {{-- Fiscal Year --}}
                            <div id="filter-fiscal-container">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Fiscal Year') }}</label>
                                <select id="fiscalYear" autocomplete="off"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">
                                    <option value="">All</option>
                                    @foreach ($fiscalYears as $year)
                                        <option value="{{ $year->name }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Government (unlocked by Fiscal Year) --}}
                            <div id="filter-gov-container" class="hidden">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Department') }}</label>
                                <select id="government" autocomplete="off"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">
                                    <option value="">All</option>
                                    @foreach ($governmentLevels as $level)
                                        <option value="{{ $level->name }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Program Type (unlocked by Department) --}}
                            <div id="filter-budget-container" class="hidden">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Program Type') }}</label>
                                <select id="budgetType" autocomplete="off"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">
                                    <option value="">All</option>
                                    @foreach ($budgetTypes as $type)
                                        <option value="{{ $type->name }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Month & Day (unlocked by Program Type) --}}
                            <div id="filter-date-container" class="hidden grid grid-cols-2 gap-5 col-span-2">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ __('Months') }}
                                    </label>

                                    <select id="month" autocomplete="off"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">

                                        <option value="">All</option>

                                        @foreach ($months as $month)
                                            @php
                                                $monthName =
                                                    app()->getLocale() === 'ne' ? $month->name_np : $month->name_en;
                                            @endphp

                                            <option value="{{ $monthName }}">
                                                {{ $monthName }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Day') }}</label>
                                    <select id="day" autocomplete="off"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">

                                        <option value="">All</option>

                                        @foreach ($days as $day)
                                            @php
                                                $dayNumber = \App\Helpers\NepaliHelper::number($day['value']);
                                            @endphp

                                            <option value="{{ $dayNumber }}">
                                                {{ $dayNumber }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            {{-- Document Category --}}
                            <div class="min-w-0 xl:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('Document Category') }}
                                </label>

                                <select id="documentCategory" autocomplete="off"
                                    class="block w-full min-w-0 max-w-full appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pl-3 pr-10 text-sm">
                                    <option value="">{{ __('All Categories') }}</option>

                                    @foreach ($documentCategories as $category)
                                        @php
                                            $categoryName =
                                                app()->getLocale() === 'ne'
                                                    ? ($category->name_np ?:
                                                    $category->name_en)
                                                    : ($category->name_en ?:
                                                    $category->name_np);
                                        @endphp

                                        <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Reset --}}
                            <div class="items-end">
                                <button id="resetFilters" type="button"
                                    class="w-32 rounded-lg border border-gray-300 bg-white px-5 py-2.5 font-medium text-gray-700 shadow-sm transition hover:border-blue-500 hover:bg-blue-50 hover:text-blue-700">

                                    <i class="fa-solid fa-rotate-left mr-2"></i>

                                    {{ __('Reset') }}

                                </button>
                            </div>

                        </div>

                    </div>

                    {{-- Table --}}
                    <div id="documentsTableWrapper" class="overflow-x-auto invisible">

                        <table id="documentsTable" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th>{{ __('SN') }}</th>

                                    <th>{{ __('Fiscal Year') }}</th>

                                    <th>{{ __('Government') }}</th>

                                    <th>{{ __('Program Type') }}</th>
                                    <th>{{ __('Document Category') }}</th>

                                    <th>{{ __('File No.') }}</th>

                                    <th>{{ __('Record Name') }}</th>

                                    <th class="hidden">{{ __('Month') }}</th>

                                    <th class="hidden">{{ __('Day') }}</th>

                                    <th class="text-center no-export">{{ __('Actions') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($documents as $document)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ $document->fiscalYear->name }}</td>

                                        <td>{{ $document->governmentLevel->name }}</td>

                                        <td>{{ $document->budgetType->name }}</td>
                                        <td>
                                            {{ $document->documentCategory
                                                ? (app()->getLocale() === 'ne'
                                                    ? $document->documentCategory->name_np ?? $document->documentCategory->name_en
                                                    : $document->documentCategory->name_en ?? $document->documentCategory->name_np)
                                                : '-' }}
                                        </td>

                                        <td>{{ $document->file_number }}</td>
                                        <td>{{ $document->document_name }}</td>
                                        <td>
                                            {{ app()->getLocale() === 'ne' ? $document->month->name_np : $document->month->name_en }}
                                        </td>
                                        <td>{{ \App\Helpers\NepaliHelper::number($document->day) }}</td>

                                        <td>

                                            <div class="flex justify-center gap-4">
                                                <a href="{{ route('documents.show', $document) }}"
                                                    class="text-blue-600 hover:text-blue-800">

                                                    <i class="fa-solid fa-eye"></i>

                                                </a>
                                                <a href="{{ route('documents.edit', $document) }}"
                                                    class="text-yellow-600 hover:text-yellow-800">

                                                    <i class="fa-solid fa-pen-to-square"></i>

                                                </a>

                                                <form action="{{ route('documents.destroy', $document) }}"
                                                    method="POST" class="delete">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="text-red-600 hover:text-red-800">

                                                        <i class="fa-solid fa-trash"></i>

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>
    </div>

    @push('scripts')
        <script type="module">
            /* ---------------------------------------------------------
             * DataTable
             * ------------------------------------------------------- */
            const table = new DataTable('#documentsTable', {
                pageLength: 10,
                layout: {
                    topEnd: {
                        buttons: [{
                                extend: "excel",
                                text: "Excel",
                                title: null,
                                exportOptions: {
                                    columns: ":not(.no-export)"
                                }
                            },
                            {
                                extend: "print",
                                text: "Print",
                                title: "",
                                exportOptions: {
                                    columns: ":not(.no-export)"
                                },
                                customize: function(win) {
                                    $(win.document.head).append(`
                                    <style>
                                        table {
                                            border-collapse: collapse !important;
                                            width: 100% !important;
                                        }

                                        table th,
                                        table td {
                                            border: 1px solid #ddd !important;
                                            padding: 5px 7px !important;
                                        }
                                    </style>
                                    `);
                                }
                            }
                        ],
                        search: {
                            placeholder: "{{ __('Search...') }}",
                        }
                    }
                },
                responsive: true,
                ordering: true,
                info: true,
                language: {
                    search: "",
                    searchPlaceholder: "{{ __('Search...') }}"
                },
                columnDefs: [{
                    targets: [7, 8],
                    visible: false,
                    searchable: true
                }],
                initComplete() {
                    document
                        .getElementById('documentsTableWrapper')
                        ?.classList.remove('invisible');
                }
            });

            /* ---------------------------------------------------------
             * Filters
             * ------------------------------------------------------- */
            const STORAGE_KEY = 'documents.index.filters';

            // select id => DataTable column index
            const filters = {
                fiscalYear: 1,
                government: 2,
                budgetType: 3,
                documentCategory: 4,
                month: 7,
                day: 8,
            };

            // Each step is only visible while the previous select has a value.
            // When a step is locked, its own selects are cleared so a hidden
            // filter can never keep silently filtering the table.
            const cascade = [{
                    container: '#filter-gov-container',
                    requires: '#fiscalYear',
                    fields: '#government'
                },
                {
                    container: '#filter-budget-container',
                    requires: '#government',
                    fields: '#budgetType'
                },
                {
                    container: '#filter-date-container',
                    requires: '#budgetType',
                    fields: '#month, #day'
                },
            ];

            function syncCascade() {
                let unlocked = true;

                cascade.forEach(({
                    container,
                    requires,
                    fields
                }) => {
                    unlocked = unlocked && !!$(requires).val();
                    $(container).toggleClass('hidden', !unlocked);

                    if (!unlocked) {
                        $(fields).val('');
                    }
                });
            }

            function applyFilters() {
                syncCascade();

                const state = {};

                Object.entries(filters).forEach(([id, column]) => {
                    const value = $('#' + id).val() || '';
                    state[id] = value;

                    table.column(column).search(
                        value ? '^' + DataTable.util.escapeRegex(value) + '$' : '',
                        true,
                        false
                    );
                });

                table.draw();

                saveFilters(state);
                updateBadge(state);
            }

            function updateBadge(state) {
                const count = Object.values(state).filter(Boolean).length;

                $('#activeFilterCount')
                    .text(count)
                    .toggleClass('hidden', count === 0);
            }

            function saveFilters(state) {
                try {
                    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                } catch (e) {}
            }

            function restoreFilters() {
                let state = {};

                try {
                    state = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '{}');
                } catch (e) {}

                // Set top-down so the cascade stays consistent
                Object.keys(filters).forEach(id => {
                    const el = document.getElementById(id);
                    const value = state[id] || '';
                    const exists = [...el.options].some(o => o.value === value);

                    el.value = exists ? value : '';
                });

                applyFilters();
            }

            Object.keys(filters).forEach(id => {
                $('#' + id).on('change', applyFilters);
            });

            $('#resetFilters').on('click', function() {
                Object.keys(filters).forEach(id => $('#' + id).val(''));

                table.search('');
                applyFilters();
            });

            // Apply any saved filters now that `table` and `filters` exist
            restoreFilters();

            // Back/forward cache: the browser restores the page DOM without
            // re-running scripts, so re-sync the selects and the table.
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    restoreFilters();
                }
            });

            /* ---------------------------------------------------------
             * Delete confirmation (delegated so it survives paging/redraws)
             * ------------------------------------------------------- */
            $('#documentsTable').on('submit', 'form.delete', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: "{{ __('Delete?') }}",
                    text: "{{ __('This action cannot be undone.') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: "{{ __('Yes, delete it') }}",
                    cancelButtonText: "{{ __('Cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>
