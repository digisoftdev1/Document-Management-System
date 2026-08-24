<div id="uploadModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">

        <form action="{{ route('repository.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <input type="hidden" name="folder_id" value="{{ $folder?->id }}">

            <div class="px-6 py-4 border-b">

                <h2 class="text-xl font-semibold">

                    {{ __('Upload Files') }}

                </h2>

            </div>

            <div class="p-6">

                <label id="dropZone"
                    class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl h-72 cursor-pointer hover:border-[#03396c] transition">

                    <i class="fa-solid fa-cloud-arrow-up text-6xl text-gray-400 mb-4"></i>

                    <p class="text-lg font-medium">

                        Drag & Drop files here

                    </p>

                    <p class="text-gray-500">

                        or click to browse

                    </p>
                    <p class="text-xs text-gray-400 mt-2">
                        Maximum file size: 10 MB per file (PDFs only)
                    </p>

                    <input id="files" type="file" name="files[]" multiple accept=".pdf" class="hidden">

                </label>

                <div id="selectedFiles" class="mt-6 space-y-2"></div>

            </div>

            <div class="border-t px-6 py-4 flex justify-end gap-3">

                <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border rounded-lg">

                    {{ __('Cancel') }}

                </button>

                <button class="px-5 py-2 bg-[#03396c] text-white rounded-lg hover:bg-[#022d54]">

                    {{ __('Upload') }}

                </button>

            </div>

        </form>

    </div>

</div>

<script>
    function openUploadModal() {

        const modal = document.getElementById('uploadModal');

        modal.classList.remove('hidden');

        modal.classList.add('flex');

    }

    function closeUploadModal() {

        const modal = document.getElementById('uploadModal');

        modal.classList.remove('flex');

        modal.classList.add('hidden');

    }

    const input = document.getElementById('files');
    const selected = document.getElementById('selectedFiles');
    const dropZone = document.getElementById('dropZone');
    let selectedFiles = [];
    const allowedTypes = [
        'application/pdf'
    ];

    function renderFiles() {

        selected.innerHTML = '';

        selectedFiles.forEach((file, index) => {

            const icon = file.type === 'application/pdf' ?
                'fa-file-pdf text-red-500' :
                'fa-file-image text-blue-500';

            selected.innerHTML += `
            <div class="flex items-center justify-between border rounded-xl px-4 py-3">

                <div class="flex items-center gap-3">

                    <i class="fa-solid ${icon} text-xl"></i>

                    <div>
                        <div class="font-medium">${file.name}</div>
                        <div class="text-sm text-gray-500">
                            ${(file.size / 1024 / 1024).toFixed(2)} MB
                        </div>
                    </div>

                </div>

                <button
                    type="button"
                    onclick="removeFile(${index})"
                    class="text-red-500 hover:text-red-700">

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>
        `;
        });

    }

    function syncInputFiles() {

        const dt = new DataTransfer();

        selectedFiles.forEach(file => dt.items.add(file));

        input.files = dt.files;

    }
    window.removeFile = function(index) {

        selectedFiles.splice(index, 1);

        syncInputFiles();

        renderFiles();

    }

    function validateFiles(files) {

        const maxSize = 10 * 1024 * 1024;

        for (const file of files) {

            if (!allowedTypes.includes(file.type)) {

                Swal.fire({
                    icon: 'error',
                    title: 'Unsupported File',
                    text: `${file.name} is not a PDF`
                });

                return false;
            }

            if (file.size > maxSize) {

                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: `${file.name} exceeds the 10 MB limit`
                });

                return false;
            }
        }

        return true;
    }

    input.addEventListener('change', function() {

        const files = [...this.files];

        if (!validateFiles(files)) {
            this.value = '';
            return;
        }

        files.forEach(file => {

            const exists = selectedFiles.some(f =>
                f.name === file.name &&
                f.size === file.size
            );

            if (!exists) {
                selectedFiles.push(file);
            }

        });

        syncInputFiles();

        renderFiles();



    });

    dropZone.addEventListener('dragover', function(e) {

        e.preventDefault();

        this.classList.add(
            'border-[#03396c]',
            'bg-blue-50'
        );

    });

    dropZone.addEventListener('dragleave', function() {

        this.classList.remove(
            'border-[#03396c]',
            'bg-blue-50'
        );

    });

    dropZone.addEventListener('drop', function(e) {

        e.preventDefault();

        this.classList.remove(
            'border-[#03396c]',
            'bg-blue-50'
        );

        const files = e.dataTransfer.files;

        if (!validateFiles(files)) {
            return;
        }

        [...files].forEach(file => {

            const exists = selectedFiles.some(f =>
                f.name === file.name &&
                f.size === file.size
            );

            if (!exists) {
                selectedFiles.push(file);
            }

        });

        syncInputFiles();

        renderFiles();

    });
    document.getElementById('uploadModal').addEventListener('click', function(e) {

        if (e.target === this) {

            closeUploadModal();

        }

    });

    document.addEventListener('keydown', function(e) {

        if (e.key === "Escape") {

            closeUploadModal();

        }

    });
</script>
