<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    {{-- Fiscal Year --}}
    <div>
        <label class="block font-semibold mb-2">{{ __('Fiscal Year') }} <span class="text-red-500">*</span></label>
        <select name="fiscal_year_id"
            class="w-full border rounded-lg px-4 py-2 @error('fiscal_year_id') border-red-500 @enderror">
            <option value="">Select Fiscal Year</option>
            @foreach ($fiscalYears as $year)
                <option value="{{ $year->id }}" @selected(old('fiscal_year_id', $document->fiscal_year_id ?? '') == $year->id)>
                    {{ $year->name }}
                </option>
            @endforeach
        </select>
        @error('fiscal_year_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Government --}}
    <div>
        <label class="block font-semibold mb-2">{{ __('Government') }} <span class="text-red-500">*</span></label>
        <select name="government_level_id"
            class="w-full border rounded-lg px-4 py-2 @error('government_level_id') border-red-500 @enderror">
            <option value="">Select Government</option>
            @foreach ($governmentLevels as $government)
                <option value="{{ $government->id }}" @selected(old('government_level_id', $document->government_level_id ?? '') == $government->id)>
                    {{ $government->name }}
                </option>
            @endforeach
        </select>
        @error('government_level_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Program Type --}}
    <div>
        <label class="block font-semibold mb-2">{{ __('Program Type') }} <span class="text-red-500">*</span></label>
        <select name="budget_type_id"
            class="w-full border rounded-lg px-4 py-2 @error('budget_type_id') border-red-500 @enderror">
            <option value="">Select Program Type</option>
            @foreach ($budgetTypes as $type)
                <option value="{{ $type->id }}" @selected(old('budget_type_id', $document->budget_type_id ?? '') == $type->id)>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
        @error('budget_type_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Month --}}
    <div>
        <label class="block font-semibold mb-2">{{ __('Month') }} <span class="text-red-500">*</span></label>
        <select name="month_id" class="w-full border rounded-lg px-4 py-2 @error('month_id') border-red-500 @enderror">
            <option value="">Select Month</option>
            @foreach ($months as $month)
                <option value="{{ $month->id }}" @selected(old('month_id', $document->month_id ?? '') == $month->id)>
                    {{ $month->name }}
                </option>
            @endforeach
        </select>
        @error('month_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Day --}}
    <div>
        <label class="block font-semibold mb-2">
            {{ __('Day') }} <span class="text-red-500">*</span>
        </label>

        <select name="day" class="w-full border rounded-lg px-4 py-2 @error('day') border-red-500 @enderror">
            <option value="">{{ __('Select Day') }}</option>

            @foreach ($days as $day)
                <option value="{{ $day['value'] }}" @selected(old('day', $document->day ?? '') == $day['value'])>
                    {{ $day['label'] }}
                </option>
            @endforeach
        </select>

        @error('day')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- File Number --}}
    <div>
        <label class="block font-semibold mb-2">{{ __('File Number') }} <span class="text-red-500">*</span></label>
        <input type="text" name="file_number" value="{{ old('file_number', $document->file_number ?? '') }}"
            class="w-full border rounded-lg px-4 py-2 @error('file_number') border-red-500 @enderror">
        @error('file_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Document Name --}}
<div class="grid md:grid-cols-2">
    <div class="mt-6">
        <label class="block font-semibold mb-2">{{ __('Record Name') }} <span class="text-red-500">*</span></label>
        <input type="text" name="document_name" value="{{ old('document_name', $document->document_name ?? '') }}"
            class="w-full border rounded-lg px-4 py-2 @error('document_name') border-red-500 @enderror">
        @error('document_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
{{-- File Upload --}}
<div class="mt-8 border rounded-lg bg-gray-50 p-6">
    <h3 class="text-lg font-semibold mb-4">📄 {{ __('Document Upload') }}</h3>

    @if (!empty($document?->pdf_path))
        <div class="mb-4 p-4 bg-white border rounded-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>
                    <div>
                        <div class="font-semibold">{{ __('Current Document') }}</div>
                        <div class="text-sm text-gray-500">{{ basename($document->pdf_path) }}</div>
                    </div>
                </div>
                <a href="{{ Storage::url($document->pdf_path) }}" target="_blank"
                    class="px-4 py-2 bg-[#1b4f72] text-white rounded-lg hover:bg-[#154360] transition-colors">
                    <i class="fa-solid fa-eye mr-2"></i> {{ __('View PDF') }}
                </a>
            </div>
        </div>
    @endif

    <label id="dropZone"
        class="flex flex-col items-center justify-center border-2 border-dashed @error('file') border-red-500 @else border-gray-300 @enderror rounded-xl h-72 cursor-pointer hover:border-[#03396c] transition bg-white">
        <i class="fa-solid fa-cloud-arrow-up text-6xl text-gray-400 mb-4"></i>
        <p class="text-lg font-medium">Drag & Drop document here</p>
        <p class="text-gray-500">or click to browse</p>
        <p class="text-xs text-gray-400 mt-2">
            {{ __('Maximum file size: 10 MB') }}
        </p>
        <input id="file" type="file" name="file" accept=".pdf" class="hidden">
    </label>
    @error('file')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    <div id="selectedFile" class="mt-6"></div>
</div>


<script>
    const input = document.getElementById('file');
    const dropZone = document.getElementById('dropZone');
    const selected = document.getElementById('selectedFile');

    const allowedTypes = [
        'application/pdf',
    ];

    let selectedFile = null;

    function renderFile() {
        selected.innerHTML = '';
        if (!selectedFile) {
            return;
        }

        const icon = selectedFile.type === 'application/pdf' ?
            'fa-file-pdf text-red-500' :
            'fa-file-image text-blue-500';

        selected.innerHTML = `
        <div class="flex items-center justify-between border rounded-xl px-4 py-3 bg-white">
            <div class="flex items-center gap-3">
                <i class="fa-solid ${icon} text-xl"></i>
                <div>
                    <div class="font-medium">
                        ${selectedFile.name}
                    </div>
                    <div class="text-sm text-gray-500">
                        ${(selectedFile.size / 1024 / 1024).toFixed(2)} MB
                    </div>
                </div>
            </div>

            <button
                type="button"
                onclick="removeFile()"
                class="text-red-500 hover:text-red-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        `;
    }

    function validateFile(file) {
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Unsupported File',
                text: 'Only PDF files are allowed.'
            });

            return false;
        }

        return true;
    }

    function setFile(file) {
        if (!validateFile(file)) {
            return;
        }

        selectedFile = file;

        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;

        renderFile();
    }

    input.addEventListener('change', function() {
        if (this.files.length) {
            setFile(this.files[0]);
        }
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

        if (e.dataTransfer.files.length) {
            setFile(e.dataTransfer.files[0]);
        }
    });

    window.removeFile = function() {
        selectedFile = null;
        input.value = '';
        renderFile();
    }
</script>
