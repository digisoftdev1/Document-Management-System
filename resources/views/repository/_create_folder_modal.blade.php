<div id="folderModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div id="folderModalContent" class="bg-white rounded-xl shadow-xl w-full max-w-2xl">

        <form id="folderForm" action="{{ route('folders.store') }}" method="POST">
            @csrf

            <input type="hidden" id="folderMethod" name="_method">

            <input type="hidden" name="parent_id" value="{{ $folder?->id }}">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b">

                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-[#03396c]/10 flex items-center justify-center">

                        <i class="fa-solid fa-folder-plus text-[#03396c] text-xl"></i>

                    </div>

                    <div>

                        <h2 id="folderModalTitle" class="text-xl font-bold text-gray-800">

                            {{ __('New Folder') }}

                        </h2>

                        <p id="folderModalParagraph" class="text-sm text-gray-500">

                            {{ __('Create a folder to organize your documents.') }}

                        </p>

                    </div>

                </div>

                <button type="button" onclick="closeFolderModal()"
                    class="w-10 h-10 rounded-full hover:bg-gray-100 transition">

                    <i class="fa-solid fa-xmark text-gray-500"></i>

                </button>

            </div>

            {{-- Body --}}
            <div class="p-6 space-y-6">

                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">

                        {{ __('Folder Name') }}

                    </label>

                    <input id="folderName" type="text" name="name" required autofocus
                        placeholder="Example: Finance"
                        class="w-full rounded-xl border-gray-300 focus:border-[#03396c] focus:ring-[#03396c]">

                </div>

                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">

                        {{ __('Description') }}

                    </label>

                    <textarea id="folderDescription" name="description" rows="4" placeholder="Optional description..."
                        class="w-full rounded-xl border-gray-300 resize-none focus:border-[#03396c] focus:ring-[#03396c]"></textarea>

                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-5 border-t bg-gray-50 rounded-b-2xl flex justify-end gap-3">

                <button type="button" onclick="closeFolderModal()"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-100 transition">

                    {{ __('Cancel') }}

                </button>

                <button type="submit" id="folderSubmitBtn"
                    class="px-5 py-2.5 rounded-xl bg-[#03396c] text-white hover:bg-[#022d54] transition">

                    <i class="fa-solid fa-folder-plus mr-2"></i>

                    {{ __('Create Folder') }}

                </button>

            </div>

        </form>

    </div>

</div>

<script>
    function openFolderModal(reset = true) {

        const modal = document.getElementById('folderModal');
        const content = document.getElementById('folderModalContent');

        if (reset) {

            document.getElementById('folderModalTitle').innerText =
                "{{ __('New Folder') }}";

            document.getElementById('folderModalParagraph').innerText =
                "{{ __('Create a folder to organize your documents.') }}";

            document.getElementById('folderForm').action =
                "{{ route('folders.store') }}";

            document.getElementById('folderMethod').value = "";

            document.getElementById('folderName').value = "";

            document.getElementById('folderDescription').value = "";

            document.getElementById('folderSubmitBtn').innerHTML =
                '<i class="fa-solid fa-folder-plus mr-2"></i> {{ __('Create Folder') }}';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');

            document.getElementById('folderName').focus();
        });

    }

    function closeFolderModal() {

        const modal = document.getElementById('folderModal');
        const content = document.getElementById('folderModalContent');

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {

            modal.classList.remove('flex');
            modal.classList.add('hidden');

        }, 150);

    }

    document.getElementById('folderModal').addEventListener('click', function(e) {

        if (e.target === this) {

            closeFolderModal();

        }

    });

    document.addEventListener('keydown', function(e) {

        if (
            e.key === "Escape" &&
            !document.getElementById('folderModal').classList.contains('hidden')
        ) {

            closeFolderModal();

        }

    });
</script>
