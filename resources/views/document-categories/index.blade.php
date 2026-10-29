<x-app-layout>
    <div class="py-24">
        <div class="px-4 sm:px-6 lg:px-8 max-w-full">

            <div class="bg-white shadow-sm rounded-sm overflow-hidden">
                <div class="p-6">

                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">
                            {{ __('Document Categories') }}
                        </h2>

                        <button onclick="openCreateModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                            <i class="fa-solid fa-plus mr-2"></i>
                            {{ __('Add Category') }}
                        </button>
                    </div>

                    <div id="documentsTableWrapper" class="overflow-x-auto invisible">
                        <table id="documentCategoryTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th>{{ __('SN') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th class="text-center">{{ __('Actions') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ session('locale', app()->getLocale()) === 'ne' ? $category->name_np : $category->name_en }}
                                        </td>

                                        <td>
                                            <div class="flex gap-4">
                                                <button onclick='openEditModal(@json($category))'
                                                    class="text-yellow-600 hover:text-yellow-800"
                                                    title="{{ __('Edit') }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                <form action="{{ route('document-categories.destroy', $category) }}"
                                                    method="POST" class="delete">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                                        title="{{ __('Delete') }}">
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

            <form action="{{ route('document-categories.store') }}" method="POST">
                @csrf

                <div class="border-b px-6 py-4">
                    <h3 class="text-xl font-semibold">
                        {{ __('Add Document Category') }}
                    </h3>
                </div>

                <div class="p-6 space-y-5">

                    <div>
                        <label for="create_name_en" class="font-medium">
                            {{ __('Category Name (English)') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>

                        <input id="create_name_en" type="text" name="name_en"
                            class="mt-2 w-full rounded-lg border px-4 py-2" maxlength="255">
                    </div>

                    <div>
                        <label for="create_name_np" class="font-medium">
                            {{ __('श्रेणीको नाम (नेपाली)') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>

                        <input id="create_name_np" type="text" name="name_np"
                            class="mt-2 w-full rounded-lg border px-4 py-2" maxlength="255">
                    </div>

                </div>

                <div class="border-t px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="closeCreateModal()" class="px-5 py-2 rounded-lg bg-gray-200">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white">
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
                        {{ __('Edit Document Category') }}
                    </h3>
                </div>

                <div class="p-6 space-y-5">

                    <div>
                        <label for="edit_name_en" class="font-medium">
                            {{ __('Category Name (English)') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>

                        <input id="edit_name_en" type="text" name="name_en"
                            class="mt-2 w-full rounded-lg border px-4 py-2" maxlength="255">
                    </div>

                    <div>
                        <label for="edit_name_np" class="font-medium">
                            {{ __('श्रेणीको नाम (नेपाली)') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>

                        <input id="edit_name_np" type="text" name="name_np"
                            class="mt-2 w-full rounded-lg border px-4 py-2" maxlength="255">
                    </div>

                </div>

                <div class="border-t px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2 rounded-lg bg-gray-200">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white">
                        {{ __('Update') }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    @push('styles')
        <style>
            #documentCategoryTable td.dt-type-numeric,
            #documentCategoryTable th.dt-type-numeric {
                text-align: left !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script type="module">
            new DataTable('#documentCategoryTable', {
                language: {
                    search: "",
                    searchPlaceholder: "{{ __('Search...') }}"
                },
                responsive: true,
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
                document.getElementById('edit_name_en').value = item.name_en;
                document.getElementById('edit_name_np').value = item.name_np;

                document.getElementById('editForm').action =
                    `/admin/document-categories/${item.id}`;

                editModal.classList.remove('hidden');
            }

            function closeEditModal() {
                editModal.classList.add('hidden');
            }

            document.querySelectorAll('.delete').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: @json(__('Delete?')),
                        text: @json(__('This action cannot be undone.')),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: @json(__('Yes, delete it')),
                        cancelButtonText: @json(__('Cancel'))
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
