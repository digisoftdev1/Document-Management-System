<x-app-layout>
    <div class="py-24">
        <div class="px-4 sm:px-6 lg:px-8 max-w-full">

            <div class="bg-white shadow-sm rounded-sm overflow-hidden">

                <div class="p-6">

                    <div class="flex justify-between items-center mb-6">

                        <h2 class="text-2xl font-bold text-gray-800">
                            {{ __('Program Types') }}
                        </h2>

                        <button onclick="openCreateModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                            <i class="fa-solid fa-plus mr-2"></i>

                            {{ __('Add Program Type') }}

                        </button>

                    </div>

                    <div id="documentsTableWrapper" class="overflow-x-auto invisible">

                        <table id="budgetTypeTable" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th>{{ __('SN') }}</th>

                                    <th>{{ __('Program Type') }}</th>

                                    <th class="text-center">{{ __('Actions') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($budgetTypes as $budgetType)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ $budgetType->name }}</td>



                                        <td>

                                            <div class="flex gap-4">

                                                <button onclick='openEditModal(@json($budgetType))'
                                                    class="text-yellow-600 hover:text-yellow-800">

                                                    <i class="fa-solid fa-pen-to-square"></i>

                                                </button>

                                                <form action="{{ route('budget-types.destroy', $budgetType) }}"
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

    {{-- Create Modal --}}
    <div id="createModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">

        <div class="bg-white rounded-xl w-full max-w-lg">

            <form action="{{ route('budget-types.store') }}" method="POST">

                @csrf

                <div class="border-b px-6 py-4">

                    <h3 class="text-xl font-semibold">
                        {{ __('Add Program Type') }}
                    </h3>

                </div>

                <div class="p-6 space-y-5">

                    <div>

                        <label class="font-medium">
                            {{ __('Program Type') }}
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="text" name="name" class="mt-2 w-full rounded-lg border px-4 py-2" required>

                    </div>



                </div>

                <div class="border-t px-6 py-4 flex justify-end gap-3">

                    <button type="button" onclick="closeCreateModal()" class="px-5 py-2 rounded-lg bg-gray-200">

                        {{ __('Cancel') }}

                    </button>

                    <button class="px-5 py-2 rounded-lg bg-blue-600 text-white">

                        {{ __('Save') }}

                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">

        <div class="bg-white rounded-xl w-full max-w-lg">

            <form id="editForm" method="POST">

                @csrf
                @method('PUT')

                <div class="border-b px-6 py-4">

                    <h3 class="text-xl font-semibold">
                        {{ __('Edit Program Type') }}
                    </h3>

                </div>

                <div class="p-6 space-y-5">

                    <div>

                        <label class="font-medium">
                            {{ __('Program Type') }}
                            <span class="text-red-500">*</span>
                        </label>

                        <input id="edit_name" type="text" name="name"
                            class="mt-2 w-full rounded-lg border px-4 py-2" required>

                    </div>



                </div>

                <div class="border-t px-6 py-4 flex justify-end gap-3">

                    <button type="button" onclick="closeEditModal()" class="px-5 py-2 rounded-lg bg-gray-200">

                        {{ __('Cancel') }}

                    </button>

                    <button class="px-5 py-2 rounded-lg bg-blue-600 text-white">

                        {{ __('Update') }}

                    </button>

                </div>

            </form>

        </div>

    </div>

    @push('styles')
        <style>
            #budgetTypeTable td.dt-type-numeric,
            #budgetTypeTable th.dt-type-numeric {
                text-align: left !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script type="module">
            new DataTable('#budgetTypeTable', {
                language: {
                    search: "",
                    searchPlaceholder: "Search..."
                },
                responsive: true,

                // paging: false,

                // info: false,

                autoWidth: false,
                initComplete() {
                    document
                        .getElementById('documentsTableWrapper')
                        ?.classList.remove('invisible');
                }

            });
        </script>

        <script>
            const createModal = document.getElementById('createModal');
            const editModal = document.getElementById('editModal');

            function openCreateModal() {

                createModal.classList.remove('hidden');

            }

            function closeCreateModal() {

                createModal.classList.add('hidden');

            }

            function openEditModal(item) {

                document.getElementById('edit_name').value = item.name;


                document.getElementById('editForm').action =
                    `/admin/budget-types/${item.id}`;

                editModal.classList.remove('hidden');

            }

            function closeEditModal() {

                editModal.classList.add('hidden');

            }
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
