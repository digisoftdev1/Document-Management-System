<x-app-layout>
    <div class="py-24 px-6">
        <div class="bg-white rounded-2xl shadow border p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ __('System Information') }}
                    </h1>

                    {{-- <p class="mt-2 text-gray-500">
                        {{ __('Configure your website.') }}
                    </p> --}}
            </div>

            <form action="{{ route('settings.website.update', $setting) }}" method="POST" enctype="multipart/form-data"
                class="space-y-8">

                @csrf
                @method('PUT')

                {{-- Website Title --}}
                <div class="w-1/2 pr-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ __('Website Title') }} <span class="text-red-500">*</span>
                    </label>

                    <input type="text" name="website_title"
                        value="{{ old('website_title', $setting?->website_title) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-[#03396c] focus:ring-[#03396c]">

                    @error('website_title')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    {{-- Website Logo --}}
                    <div class="border rounded-xl bg-gray-50 p-4 flex flex-col justify-between">
                        <h3 class="text-sm font-semibold mb-3">
                            {{ __('Website Logo') }}
                        </h3>

                        <div class="grid grid-cols-12 gap-3 items-stretch">
                            @if ($setting?->website_logo)
                                <div
                                    class="col-span-4 p-2 bg-white border rounded-xl flex flex-col items-center justify-center min-h-[110px] relative">

                                    <div class="absolute top-2 right-2">
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('settings.website.logo.delete') }}', 'website logo')"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                            title="{{ __('Delete logo') }}">

                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>

                                    <img id="currentLogo" src="{{ Storage::url($setting->website_logo) }}"
                                        class="h-10 object-contain">

                                    <span
                                        class="text-[10px] font-semibold text-gray-400 mt-2 uppercase tracking-wider text-center">
                                        {{ __('Current') }}
                                    </span>
                                </div>
                            @endif

                            <label for="website_logo"
                                class="{{ $setting?->website_logo ? 'col-span-8' : 'col-span-12' }} flex flex-col items-center justify-center min-h-[110px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-white hover:border-[#03396c] transition p-2 text-center">
                                <img id="logoPreview"
                                    src="{{ $setting?->website_logo ? Storage::url($setting->website_logo) : '' }}"
                                    class="hidden h-10 object-contain mb-1">
                                <i id="logoIcon"
                                    class="fa-solid fa-image text-xl text-gray-400 mb-1 {{ $setting?->website_logo ? 'hidden' : '' }}"></i>
                                <p class="text-xs font-medium text-gray-700">
                                    {{ __('Click to upload logo') }}
                                </p>
                                <p id="logoName" class="text-gray-400 text-[10px] mt-0.5">
                                    {{ __('PNG, JPG, SVG (Max: 2MB)') }}
                                </p>
                                <input id="website_logo" type="file" name="website_logo" accept="image/*"
                                    class="hidden">
                            </label>
                        </div>

                        @error('website_logo')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Favicon --}}
                    <div class="border rounded-xl bg-gray-50 p-4 flex flex-col justify-between">
                        <h3 class="text-sm font-semibold mb-3">
                            {{ __('Favicon') }}
                        </h3>

                        <div class="grid grid-cols-12 gap-3 items-stretch">
                            @if ($setting?->favicon)
                                <div
                                    class="col-span-4 p-2 bg-white border rounded-xl flex flex-col items-center justify-center min-h-[110px] relative">

                                    <div class="absolute top-2 right-2">
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('settings.website.favicon.delete') }}', 'favicon')"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                            title="{{ __('Delete favicon') }}">

                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>

                                    <img id="currentFavicon" src="{{ Storage::url($setting->favicon) }}"
                                        class="h-8 w-8 object-contain">

                                    <span
                                        class="text-[10px] font-semibold text-gray-400 mt-2 uppercase tracking-wider text-center">
                                        {{ __('Current') }}
                                    </span>
                                </div>
                            @endif

                            <label for="favicon"
                                class="{{ $setting?->favicon ? 'col-span-8' : 'col-span-12' }} flex flex-col items-center justify-center min-h-[110px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-white hover:border-[#03396c] transition p-2 text-center">
                                <img id="faviconPreview"
                                    src="{{ $setting?->favicon ? Storage::url($setting->favicon) : '' }}"
                                    class="hidden h-8 w-8 object-contain mb-1">
                                <i id="faviconIcon"
                                    class="fa-solid fa-icons text-xl text-gray-400 mb-1 {{ $setting?->favicon ? 'hidden' : '' }}"></i>
                                <p class="text-xs font-medium text-gray-700">
                                    {{ __('Click to upload favicon') }}
                                </p>
                                <p id="faviconName" class="text-gray-400 text-[10px] mt-0.5">
                                    {{ __('32×32 PNG (Max: 1MB)') }}
                                </p>
                                <input id="favicon" type="file" name="favicon" accept="image/*" class="hidden">
                            </label>
                        </div>

                        @error('favicon')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div class="pt-4">
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-[#03396c] text-white hover:bg-[#02284c] transition">
                        <i class="fa-solid fa-floppy-disk mr-2"></i>
                        {{ __('Save Settings') }}
                    </button>
                </div>

            </form>

        </div>


    </div>

    @push('scripts')
        <script>
            function setupPreview(inputId, previewId, iconId, textId) {
                document.getElementById(inputId)?.addEventListener('change', function(e) {
                    const file = e.target.files[0];

                    if (!file) return;

                    const reader = new FileReader();

                    reader.onload = function(event) {
                        document.getElementById(previewId).src = event.target.result;
                        document.getElementById(previewId).classList.remove('hidden');
                        document.getElementById(iconId).classList.add('hidden');
                        document.getElementById(textId).textContent = file.name;
                    };

                    reader.readAsDataURL(file);
                });
            }

            setupPreview('website_logo', 'logoPreview', 'logoIcon', 'logoName');
            setupPreview('favicon', 'faviconPreview', 'faviconIcon', 'faviconName');

            function confirmDelete(action, itemName) {
                Swal.fire({
                    title: 'Delete ' + itemName + '?',
                    text: 'This will permanently remove the current ' + itemName + '.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {

                    if (!result.isConfirmed) {
                        return;
                    }

                    const form = document.createElement('form');

                    form.method = 'POST';
                    form.action = action;

                    form.innerHTML = `
            @csrf
            @method('DELETE')
        `;

                    document.body.appendChild(form);
                    form.submit();
                });
            }
        </script>
    @endpush
</x-app-layout>
