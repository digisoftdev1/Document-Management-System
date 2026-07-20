<x-app-layout>
    <div class="py-24">
        <div class="px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-sm shadow-sm">

                <div class="p-6">

                    <div class="flex items-center justify-between mb-6">

                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">
                                {{ __('Fiscal Years') }}
                            </h2>


                        </div>

                        <button onclick="openCreateModal()"
                            class="inline-flex items-center px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">

                            <i class="fa-solid fa-plus mr-2"></i>

                            {{ __('Add Fiscal Year') }}

                        </button>

                    </div>

                    <div class="overflow-x-auto">

                        <table id="fiscalYearTable" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th>{{ __('SN') }}</th>

                                    <th>{{ __('Fiscal Year') }}</th>

                                    <th class="text-center">{{ __('Actions') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($fiscalYears as $fiscalYear)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        <td class="font-medium">

                                            {{ $fiscalYear->name }}

                                        </td>

                                        <td>

                                            <div class="flex  gap-5">

                                                <button
                                                    onclick="openEditModal(
                                                        '{{ route('fiscal-years.update', $fiscalYear) }}',
                                                        '{{ $fiscalYear->name }}'
                                                    )"
                                                    class="text-yellow-600 hover:text-yellow-700">

                                                    <i class="fa-solid fa-pen-to-square"></i>

                                                </button>

                                                <form action="{{ route('fiscal-years.destroy', $fiscalYear) }}"
                                                    method="POST" class="delete">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="text-red-600 hover:text-red-700">

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

    {{-- Modal --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">

        <div class="w-full max-w-lg rounded-xl bg-white shadow-xl">

            <form id="modalForm" method="POST">

                @csrf

                <div id="methodField"></div>

                <div class="border-b px-6 py-5">

                    <h3 id="modalTitle" class="text-2xl font-bold">

                        {{ __('Add Fiscal Year') }}

                    </h3>

                </div>

                <div class="p-6">

                    <label class="block font-semibold mb-2">

                        {{ __('Fiscal Year') }}

                        <span class="text-red-500">*</span>

                    </label>

                    <input id="name" name="name" type="text" placeholder="2082/83" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                </div>

                <div class="flex justify-end gap-3 border-t px-6 py-5">

                    <button type="button" onclick="closeModal()"
                        class="rounded-lg bg-gray-500 px-5 py-2 text-white hover:bg-gray-600">

                        {{ __('Cancel') }}

                    </button>

                    <button id="modalButton" class="rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700">

                        {{ __('Save') }}

                    </button>

                </div>

            </form>

        </div>

    </div>

    @push('styles')
        <style>
            .dataTables_wrapper .dataTables_length select {
                padding-right: 2.5rem;
            }

            #fiscalYearTable td.dt-type-numeric,
            #fiscalYearTable th.dt-type-numeric {
                text-align: left !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script type="module">
            new DataTable('#fiscalYearTable', {
                language: {
                    search: "",
                    searchPlaceholder: "{{ __('Search...') }}"
                },
                responsive: true,


                autoWidth: false

            });
        </script>

        <script>
            const modal = document.getElementById('modal');
            const form = document.getElementById('modalForm');
            const title = document.getElementById('modalTitle');
            const button = document.getElementById('modalButton');
            const nameInput = document.getElementById('name');
            const methodField = document.getElementById('methodField');

            function openCreateModal() {

                title.textContent = '{{ __('Add Fiscal Year') }}';
                button.textContent = '{{ __('Save') }}';

                form.action = "{{ route('fiscal-years.store') }}";

                methodField.innerHTML = '';

                nameInput.value = '';

                modal.classList.remove('hidden');
                modal.classList.add('flex');

            }

            function openEditModal(action, name) {

                title.textContent = '{{ __('Edit Fiscal Year') }}';
                button.textContent = '{{ __('Update') }}';

                form.action = action;

                methodField.innerHTML =
                    '<input type="hidden" name="_method" value="PUT">';

                nameInput.value = name;

                modal.classList.remove('hidden');
                modal.classList.add('flex');

            }

            function closeModal() {

                modal.classList.remove('flex');
                modal.classList.add('hidden');

            }

            modal.addEventListener('click', function(e) {

                if (e.target === modal)
                    closeModal();

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
