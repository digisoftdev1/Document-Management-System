<x-app-layout>

    <div class="py-24">

        <div class="px-6">

            {{-- Header --}}
            <div class="flex items-center justify-between">

                <div>

                    <h2 class="text-3xl font-bold text-[#03396c]">
                        {{ __('Archived Repository') }}
                    </h2>

                    <div class="mt-2 flex items-center flex-wrap gap-2 text-sm text-gray-500">

                        <a href="{{ route('repository.index') }}" class="hover:text-[#03396c] font-medium">
                            {{ __('Archived Repository') }}
                        </a>

                        @if ($folder)

                            @foreach ($folder->ancestors() as $ancestor)
                                <i class="fa-solid fa-chevron-right text-xs"></i>

                                <a href="{{ route('repository.index', [
                                    'folder' => $ancestor->id,
                                ]) }}"
                                    class="hover:text-[#03396c]">

                                    {{ $ancestor->name }}

                                </a>
                            @endforeach

                            <i class="fa-solid fa-chevron-right text-xs"></i>

                            <span class="font-medium text-[#03396c]">

                                {{ $folder->name }}

                            </span>

                        @endif

                    </div>

                </div>

                <div>

                    @if ($folder)
                        <a href="{{ $folder->parent_id
                            ? route('repository.index', [
                                'folder' => $folder->parent_id,
                            ])
                            : route('repository.index') }}"
                            class="inline-flex items-center px-5 py-2 border rounded-lg hover:bg-gray-100">

                            <i class="fa-solid fa-arrow-left mr-2"></i>

                            Back

                        </a>
                    @endif

                </div>

            </div>


            {{-- Actions --}}
            <div class="flex items-center justify-between my-4">
                <div class="flex items-center gap-3 my-4">

                    <button onclick="openUploadModal()"
                        class="inline-flex items-center px-5 py-2 bg-[#03396c] hover:bg-[#022d54] text-white rounded-lg transition">

                        <i class="fa-solid fa-upload mr-2"></i>

                        {{ __('Upload') }}

                    </button>

                    <button onclick="openFolderModal()"
                        class="inline-flex items-center px-5 py-2 bg-[#03396c] hover:bg-[#022d54] text-white rounded-lg transition">

                        <i class="fa-solid fa-folder-plus mr-2"></i>

                        {{ __('New Folder') }}

                    </button>

                </div>
                <form id="searchForm" method="GET" class="mb-6">
                    @if (request('folder'))
                        <input type="hidden" name="folder" value="{{ request('folder') }}">
                    @endif

                    <div class="relative max-w-md">
                        <input id="searchInput" type="text" placeholder="{{ __('Search...') }}"
                            class="w-full rounded-lg border pl-10 pr-4 py-2">

                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </form>
            </div>

            {{-- Files/Folders --}}
            <div id="repositoryWrapper">

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

                </div>

                @if ($files->hasPages())
                    <div class="mt-10" id="paginationContainer">
                        {{ $files->links() }}
                    </div>
                @endif

            </div>
        </div>

    </div>

    @include('repository._create_folder_modal')

    @include('repository._upload_modal')
    @include('repository._upload_folder_modal')
    @include('repository.partials._context_menu')
    @include('repository.partials._properties_sidebar', [
        'recordsMode' => false,
    ])



    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                let timer;

                document.getElementById('searchInput')
                    ?.addEventListener('input', function() {

                        clearTimeout(timer);

                        timer = setTimeout(async () => {

                            const url = new URL(window.location);

                            if (this.value.trim()) {
                                url.searchParams.set('search', this.value);
                            } else {
                                url.searchParams.delete('search');
                            }

                            // reset pagination while searching
                            url.searchParams.delete('page');

                            const response = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const html = await response.text();

                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            document.getElementById('repositoryWrapper').innerHTML =
                                doc.getElementById('repositoryWrapper').innerHTML;

                            // update browser url
                            window.history.replaceState({}, '', url);

                            clearSelection();
                            initRepositoryEvents();

                        }, 300);

                    });



                let selectedItem = null;
                let selectedItems = [];
                const contextMenu = document.getElementById('contextMenu');
                const sidebar = document.getElementById('propertiesSidebar');

                const propName = document.getElementById('propName');
                const propDescription = document.getElementById('propDescription');
                const propType = document.getElementById('propType');
                const propSize = document.getElementById('propSize');
                const propCreated = document.getElementById('propCreated');
                const propUpdated = document.getElementById('propUpdated');
                const propOwner = document.getElementById('propOwner');

                const propFolderCount = document.getElementById('propFolderCount');
                const propFileCount = document.getElementById('propFileCount');
                const propStorage = document.getElementById('propStorage');

                const propSubtitle = document.getElementById('propertiesSubtitle');
                const propIcon = document.getElementById('propertiesIcon');


                const descriptionCard = document.getElementById('descriptionCard');
                const folderStats = document.getElementById('folderStats');
                const fileActions = document.getElementById('fileActions');
                const sizeRow = document.getElementById('sizeRow');


                const propDownload = document.getElementById('propDownload');

                function clearSelection() {

                    document.querySelectorAll('.repo-item').forEach(item => {
                        item.classList.remove(
                            'ring-2',
                            'ring-[#03396c]',
                            'bg-blue-50'
                        );
                    });

                    selectedItems = [];
                }

                function selectItem(item) {

                    if (!selectedItems.includes(item)) {
                        selectedItems.push(item);
                    }

                    item.classList.add(
                        'ring-2',
                        'ring-[#03396c]',
                        'bg-blue-50'
                    );

                    selectedItem = item;
                }

                function deselectItem(item) {

                    selectedItems = selectedItems.filter(i => i !== item);

                    item.classList.remove(
                        'ring-2',
                        'ring-[#03396c]',
                        'bg-blue-50'
                    );

                    selectedItem = selectedItems.at(-1) ?? null;
                }

                function initRepositoryEvents() {

                    document.querySelectorAll('.repo-item').forEach(item => {

                        if (item.dataset.init) return;
                        item.dataset.init = true;

                        item.addEventListener('click', function(e) {

                            if (e.ctrlKey || e.metaKey) {

                                e.preventDefault();

                                if (selectedItems.includes(this)) {
                                    deselectItem(this);
                                } else {
                                    selectItem(this);
                                }

                                if (
                                    !sidebar.classList.contains('translate-x-full') &&
                                    selectedItems.length === 1
                                ) {
                                    openProperties(this);
                                }

                                return;
                            }

                            window.location = this.dataset.open;
                        });

                        item.addEventListener('contextmenu', function(e) {

                            e.preventDefault();

                            if (!selectedItems.includes(this)) {
                                clearSelection();
                                selectItem(this);
                            }
                            openContextMenu(e);

                            const openBtn = document.getElementById('menuOpen');
                            const renameBtn = document.getElementById('menuRename');
                            const propBtn = document.getElementById('menuProperties');
                            const deleteBtn = document.getElementById('menuDelete');

                            if (selectedItems.length > 1) {

                                openBtn.classList.add('hidden');
                                renameBtn.classList.add('hidden');
                                propBtn.classList.add('hidden');

                            } else {

                                openBtn.classList.remove('hidden');
                                propBtn.classList.remove('hidden');
                                deleteBtn.classList.remove('hidden');

                                if (this.dataset.type === 'folder') {
                                    renameBtn.classList.remove('hidden');
                                } else {
                                    renameBtn.classList.add('hidden');
                                }
                            }
                        });

                    });
                }

                function openProperties(item) {

                    propName.textContent = item.dataset.name;

                    propCreated.textContent = item.dataset.created ?? "—";
                    propUpdated.textContent = item.dataset.updated ?? "—";
                    propOwner.textContent = item.dataset.owner ?? "—";





                    if (item.dataset.type === "folder") {

                        propSubtitle.textContent = "Folder";

                        propIcon.innerHTML =
                            `<i class="fa-solid fa-folder text-yellow-400 text-6xl"></i>`;

                        propDescription.textContent =
                            item.dataset.description || "No description available.";

                        propType.textContent = "Folder";

                        sizeRow.classList.add('hidden');

                        propFolderCount.textContent = item.dataset.folders ?? 0;

                        propFileCount.textContent = item.dataset.files ?? 0;

                        propStorage.textContent = item.dataset.storage;


                        folderStats.classList.remove('hidden');

                        descriptionCard.classList.remove('hidden');

                        fileActions.classList.add('hidden');

                    } else {

                        propSubtitle.textContent = "PDF Document";

                        propIcon.innerHTML =
                            `<x-pdf-icon class="w-16 h-16 group-hover:scale-105 transition" />`;

                        propDescription.textContent =
                            item.dataset.description || "No description available.";

                        propType.textContent = item.dataset.mime;

                        propSize.textContent = item.dataset.size;



                        propDownload.href = item.dataset.downloadUrl;


                        folderStats.classList.add('hidden');

                        descriptionCard.classList.add('hidden');

                        fileActions.classList.remove('hidden');

                    }

                    sidebar.classList.remove('translate-x-full');

                }

                function closeProperties() {

                    sidebar.classList.add('translate-x-full');


                }
                document.getElementById('closePropertiesBtn').addEventListener('click', closeProperties);
                document.addEventListener('click', function(e) {
                    if (
                        !e.target.closest('.repo-item') &&
                        !e.target.closest('#contextMenu') &&
                        !e.target.closest('#propertiesSidebar')
                    ) {

                        clearSelection();

                    }
                    contextMenu.classList.add('hidden');

                    if (sidebar.classList.contains('translate-x-full')) {
                        return;
                    }

                    if (sidebar.contains(e.target)) {
                        return;
                    }

                    if (e.target.closest('.repo-item')) {
                        return;
                    }

                    if (e.target.closest('#contextMenu')) {
                        return;
                    }

                    closeProperties();

                });

                // Open
                document.getElementById('menuOpen').onclick = function() {

                    if (!selectedItem) return;

                    window.open(selectedItem.dataset.open, "_blank");
                };
                document.getElementById('menuProperties').onclick = function() {

                    if (!selectedItem) return;

                    contextMenu.classList.add('hidden');

                    openProperties(selectedItem);

                };
                document.getElementById('menuRename').onclick = function() {

                    if (!selectedItem) return;

                    if (selectedItem.dataset.type !== "folder") {
                        Swal.fire(
                            "Not supported",
                            "Only folders can currently be renamed.",
                            "info"
                        );
                        return;
                    }

                    contextMenu.classList.add('hidden');

                    openFolderModal(false);

                    document.getElementById('folderModalTitle').innerText = "{{ __('Rename Folder') }}";
                    document.getElementById('folderModalParagraph').innerText =
                        "{{ __('Update the folder name and description.') }}";

                    document.getElementById('folderForm').action =
                        selectedItem.dataset.update;

                    document.getElementById('folderMethod').value = "PUT";

                    document.getElementById('folderName').value =
                        selectedItem.dataset.name;

                    document.getElementById('folderDescription').value =
                        selectedItem.dataset.description ?? "";

                    document.getElementById('folderSubmitBtn').innerHTML =
                        '<i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes';


                };

                document.getElementById('menuDelete').onclick = function() {

                    if (!selectedItems.length) return;

                    Swal.fire({
                        title: '{{ __('Delete?') }}',
                        text: selectedItems.length === 1 ?
                            selectedItems[0].dataset.name : `${selectedItems.length} items`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626'
                    }).then(result => {

                        if (!result.isConfirmed) return;

                        const form = document.createElement('form');

                        form.method = 'POST';
                        form.action = "{{ route('repository.bulkDestroy') }}";

                        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;

                        selectedItems.forEach((item, index) => {

                            form.innerHTML += `
        <input type="hidden" name="items[${index}][id]" value="${item.dataset.id}">
        <input type="hidden" name="items[${index}][type]" value="${item.dataset.type}">
    `;

                        });

                        document.body.appendChild(form);

                        form.submit();

                    });

                };
                document.addEventListener("keydown", function(e) {

                    if (e.key === "Escape") {

                        closeProperties();

                    }

                });
                initRepositoryEvents();
            });
        </script>
    @endpush
</x-app-layout>
