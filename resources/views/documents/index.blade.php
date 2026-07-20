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

                        <div class="grid grid-cols-1  xl:grid-cols-6 gap-5 items-end">
                            {{-- Fiscal Year --}}
                            <div id="filter-fiscal-container">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Fiscal Year') }}</label>
                                <select id="fiscalYear"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">
                                    <option value="">All</option>
                                    @foreach ($fiscalYears as $year)
                                        <option value="{{ $year->name }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Government (Hidden initially) --}}
                            <div id="filter-gov-container" class="hidden">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Department') }}</label>
                                <select id="government"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">
                                    <option value="">All</option>
                                    @foreach ($governmentLevels as $level)
                                        <option value="{{ $level->name }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Program Type (Hidden initially) --}}
                            <div id="filter-budget-container" class="hidden">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Program Type') }}</label>
                                <select id="budgetType"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">
                                    <option value="">All</option>
                                    @foreach ($budgetTypes as $type)
                                        <option value="{{ $type->name }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Month & Day (Hidden initially) --}}
                            <div id="filter-date-container" class="hidden grid grid-cols-2 gap-5 col-span-2">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ __('Months') }}
                                    </label>

                                    <select id="month"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">

                                        <option value="">All</option>

                                        @foreach ($months as $month)
                                            <option value="{{ $month->id }}">
                                                {{ app()->getLocale() === 'ne' ? $month->name_np : $month->name_en }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Day') }}</label>
                                    <select id="day"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5">
                                        <option value="">All</option>
                                        @foreach ($days as $day)
                                            <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- Reset --}}
                            <div class="items-end">

                                <button id="resetFilters"
                                    class="w-32 rounded-lg border border-gray-300 bg-white px-5 py-2.5 font-medium text-gray-700 shadow-sm transition hover:border-blue-500 hover:bg-blue-50 hover:text-blue-700">

                                    <i class="fa-solid fa-rotate-left mr-2"></i>

                                    {{ __('Reset') }}

                                </button>

                            </div>

                        </div>

                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">

                        <table id="documentsTable" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th>{{ __('SN') }}</th>

                                    <th>{{ __('Fiscal Year') }}</th>

                                    <th>{{ __('Government') }}</th>

                                    <th>{{ __('Program Type') }}</th>

                                    <th>{{ __('File No.') }}</th>

                                    <th>{{ __('Record Name') }}</th>

                                    <th class="hidden">{{ __('Month') }}</th>

                                    <th class="hidden">{{ __('Day') }}</th>

                                    <th class="text-center">{{ __('Actions') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($documents as $document)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ $document->fiscalYear->name }}</td>

                                        <td>{{ $document->governmentLevel->name }}</td>

                                        <td>{{ $document->budgetType->name }}</td>

                                        <td>{{ $document->file_number }}</td>

                                        <td>{{ $document->document_name }}</td>
                                        <td>{{ $document->month->id }}</td>
                                        <td>{{ $document->day }}</td>

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
            const table = new DataTable('#documentsTable', {
                pageLength: 10,
                responsive: true,
                ordering: true,

                info: true,
                language: {
                    search: "",
                    searchPlaceholder: "Search..."
                },
                columnDefs: [{
                    targets: [6, 7],
                    visible: false,
                    searchable: true
                }]
            });

            $('#search').on('keyup', function() {
                table.search(this.value).draw();
            });

            $('#fiscalYear').on('change', function() {
                table.column(1).search(this.value).draw();
            });

            $('#government').on('change', function() {
                table.column(2).search(this.value).draw();
            });

            $('#budgetType').on('change', function() {
                table.column(3).search(this.value).draw();
            });

            $('#month').on('change', function() {
                table.column(6).search(this.value).draw();
            });

            $('#day').on('change', function() {
                table.column(7).search(this.value).draw();
            });

            $('#fiscalYear').on('change', function() {
                table.column(1).search(this.value).draw();
                $('#filter-gov-container').toggleClass('hidden', !this.value);
            });

            $('#government').on('change', function() {
                table.column(2).search(this.value).draw();
                $('#filter-budget-container').toggleClass('hidden', !this.value);
            });

            $('#budgetType').on('change', function() {
                table.column(3).search(this.value).draw();
                $('#filter-date-container').toggleClass('hidden', !this.value);
            });

            $('#month').on('change', function() {
                table.column(6).search(this.value).draw();
            });
            $('#day').on('change', function() {
                table.column(7).search(this.value).draw();
            });

            $('#resetFilters').on('click', function() {
                $('#fiscalYear, #government, #budgetType, #month, #day').val('');
                // Hide containers again
                $('#filter-gov-container, #filter-budget-container, #filter-date-container').addClass('hidden');
                table.search('').columns().search('').draw();
            });
            document.querySelectorAll('.delete').forEach(form => {

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

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

            });
        </script>
    @endpush

</x-app-layout>
