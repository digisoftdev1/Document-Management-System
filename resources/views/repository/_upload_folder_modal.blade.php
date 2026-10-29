<div id="uploadFolderModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">

        <div class="px-6 py-4 border-b flex items-center justify-between">

            <h2 class="text-xl font-semibold">
                Upload Folder
            </h2>

            <button type="button" onclick="closeUploadFolderModal()" class="text-gray-400 hover:text-gray-700 text-xl">

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>

        <div class="p-6">

            <label id="folderDropZone"
                class="flex flex-col items-center justify-center
                       border-2 border-dashed border-gray-300
                       rounded-xl h-64 cursor-pointer
                       hover:border-[#03396c] transition">

                <i class="fa-solid fa-folder-open text-6xl text-yellow-400 mb-4"></i>

                <p class="text-lg font-medium">
                    Select a folder
                </p>

                <p class="text-gray-500">
                    All nested folders and PDFs will be imported
                </p>

                <p class="text-xs text-gray-400 mt-2">
                    PDF only · Maximum 10 MB per file
                </p>

                <input id="folderInput" type="file" webkitdirectory directory multiple class="hidden">
                <input type="hidden" id="folderPaths" name="folder_paths">
            </label>

            <div id="folderInfo" class="hidden mt-5">

                <div class="border rounded-xl p-4">

                    <div class="flex items-center gap-3">

                        <i class="fa-solid fa-folder text-yellow-400 text-3xl"></i>

                        <div>

                            <div id="folderName" class="font-semibold">
                            </div>

                            <div id="uploadFolderStats" class="text-sm text-gray-500">
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div id="folderProgress" class="hidden mt-5">

                <div class="flex justify-between text-sm mb-2">

                    <span id="uploadStatus">
                        Preparing...
                    </span>

                    <span id="uploadPercent">
                        0%
                    </span>

                </div>

                <div class="w-full bg-gray-200 rounded-full h-3">

                    <div id="uploadProgressBar" class="bg-[#03396c] h-3 rounded-full transition-all" style="width: 0%">
                    </div>

                </div>

                <p id="uploadCurrentFile" class="text-xs text-gray-400 mt-2 truncate">
                </p>

            </div>

        </div>

        <div class="border-t px-6 py-4 flex justify-end gap-3">

            <button type="button" onclick="closeUploadFolderModal()" id="folderCancelBtn"
                class="px-4 py-2 border rounded-lg">

                Cancel

            </button>

            <button type="button" onclick="startFolderUpload()" id="folderUploadBtn" disabled
                class="px-5 py-2 bg-[#03396c] text-white rounded-lg
                       hover:bg-[#022d54] disabled:opacity-50">

                <i class="fa-solid fa-upload mr-2"></i>

                Start Import

            </button>

        </div>

    </div>

</div>
<script>
    let folderFiles = [];

    const folderInput = document.getElementById('folderInput');

    folderInput.addEventListener('change', function() {
        folderFiles = Array.from(this.files);

        const pdfFiles = folderFiles.filter(file => file.type === 'application/pdf');
        const invalidFiles = folderFiles.length - pdfFiles.length;

        folderFiles = pdfFiles;

        const folders = new Set();

        Array.from(this.files).forEach(file => {

            const path = file.webkitRelativePath;

            if (!path) {
                return;
            }

            const parts = path.split('/');

            parts.pop();

            for (let i = 1; i <= parts.length; i++) {
                folders.add(parts.slice(0, i).join('/'));
            }
        });

        const folderPaths = [...folders];
        document.getElementById('folderPaths').value = JSON.stringify(folderPaths);

        const firstPath = this.files[0]?.webkitRelativePath || '';
        const rootFolder = firstPath.split('/')[0] || 'Selected Folder';

        document.getElementById('folderName').textContent = rootFolder;

        document.getElementById('uploadFolderStats').textContent =
            `${folderFiles.length} PDF file(s)` +
            (invalidFiles ? ` · ${invalidFiles} non-PDF file(s) skipped` : '') +
            ` · ${folderPaths.length} folder(s) detected`;

        document.getElementById('folderInfo').classList.remove('hidden');

        document.getElementById('folderUploadBtn').disabled =
            folderPaths.length === 0 && folderFiles.length === 0;
    });

    function openUploadFolderModal() {

        const modal = document.getElementById('uploadFolderModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeUploadFolderModal() {

        const modal = document.getElementById('uploadFolderModal');

        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    async function startFolderUpload() {

        const folderPaths = JSON.parse(document.getElementById('folderPaths').value || '[]');

        if (!folderFiles.length && !folderPaths.length) {
            return;
        }

        const button = document.getElementById('folderUploadBtn');
        const cancelButton = document.getElementById('folderCancelBtn');

        button.disabled = true;
        cancelButton.disabled = true;

        document.getElementById('folderProgress').classList.remove('hidden');

        const batchSize = 20;
        const total = folderFiles.length;
        let uploaded = 0;

        try {

            if (total === 0) {

                const formData = new FormData();

                formData.append('_token', '{{ csrf_token() }}');
                formData.append('folder_paths', JSON.stringify(folderPaths));

                @if ($folder)
                    formData.append('folder_id', '{{ $folder->id }}');
                @endif

                document.getElementById('uploadStatus').textContent = 'Creating folder structure...';
                document.getElementById('uploadPercent').textContent = '100%';
                document.getElementById('uploadProgressBar').style.width = '100%';

                const response = await fetch('{{ route('repository.uploadFolder') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Folder creation failed.');
                }
            }

            for (let i = 0; i < total; i += batchSize) {

                const batch = folderFiles.slice(i, i + batchSize);
                const formData = new FormData();

                formData.append('_token', '{{ csrf_token() }}');
                formData.append('folder_paths', JSON.stringify(folderPaths));

                @if ($folder)
                    formData.append('folder_id', '{{ $folder->id }}');
                @endif

                batch.forEach(file => {

                    formData.append('files[]', file);
                    formData.append('relative_paths[]', file.webkitRelativePath);
                });

                document.getElementById('uploadStatus').textContent =
                    `Uploading ${uploaded + 1}–${Math.min(uploaded + batch.length, total)} of ${total}`;

                document.getElementById('uploadCurrentFile').textContent =
                    batch[0]?.webkitRelativePath || '';

                const response = await fetch('{{ route('repository.uploadFolder') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Folder upload failed.');
                }

                uploaded += batch.length;

                const percent = Math.round((uploaded / total) * 100);

                document.getElementById('uploadPercent').textContent = `${percent}%`;
                document.getElementById('uploadProgressBar').style.width = `${percent}%`;
            }

            document.getElementById('uploadStatus').textContent = 'Import completed successfully.';
            document.getElementById('uploadCurrentFile').textContent = '';

            Swal.fire({
                icon: 'success',
                title: 'Folder Imported',
                text: `${uploaded} PDF file(s) imported successfully.`,
                confirmButtonColor: '#03396c'
            }).then(() => {
                window.location.reload();
            });

        } catch (error) {

            console.error(error);

            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: error.message || 'Something went wrong.'
            });

            button.disabled = false;
            cancelButton.disabled = false;
        }
    }
</script>
