<x-app-layout>
    <div class="py-24 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-6">{{ __('Permission Management') }}</h2>

            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
            @endif

            {{-- Create Permission --}}
            <form action="{{ route('permissions.store') }}" method="POST"
                class="border border-gray-200 rounded-lg p-4 mb-8">
                @csrf

                <h3 class="font-semibold mb-4">{{ __('Create Permission') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="action" class="block text-sm font-medium text-gray-700">
                            {{ __('Action') }}
                        </label>
                        <input type="text" name="action" id="action" value="{{ old('action') }}"
                            placeholder="e.g. view" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('action')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="module" class="block text-sm font-medium text-gray-700">
                            {{ __('Module') }}
                        </label>
                        <input type="text" name="module" id="module" value="{{ old('module') }}"
                            placeholder="e.g. documents" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('module')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Create Permission') }}
                        </button>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-3">
                    {{ __('Example: action = view, module = documents. This creates "view documents".') }}
                </p>
            </form>

            {{-- Existing Permissions --}}
            <h3 class="text-lg font-semibold mb-4">{{ __('Existing Permissions') }}</h3>

            <div class="space-y-4">
                @forelse ($permissions as $module => $modulePermissions)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">
                            {{ ucwords(str_replace(['-', '_'], ' ', $module)) }}
                        </h4>

                        <div class="flex flex-wrap gap-3">
                            @foreach ($modulePermissions as $permission)
                                <div class="flex items-center gap-2 bg-gray-50 border rounded-md px-3 py-2">
                                    <span class="text-sm">
                                        {{ ucwords(explode(' ', $permission->name, 2)[0]) }}
                                    </span>

                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST"
                                        class="delete">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" title="{{ __('Delete Permission') }}"
                                            class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 hover:text-red-700 transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">{{ __('No permissions found.') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: "{{ __('Delete permission?') }}",
                        text: "{{ __('This action cannot be undone.') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: "{{ __('Yes, delete it') }}",
                        cancelButtonText: "{{ __('Cancel') }}"
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
