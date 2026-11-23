<x-app-layout>
    <div class="py-24 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-semibold">
                    {{ __('Manage Role') }}:
                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Select the permissions this role should have.') }}
                </p>
            </div>

            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
            @endif

            <form action="{{ route('roles.update', $role) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        {{ __('Role Name') }}
                    </label>

                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                        {{ $role->name === 'superadmin' ? 'readonly' : '' }} required
                        class="mt-1 block w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">

                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t pt-5">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-semibold">{{ __('Module Permissions') }}</h3>

                        <button type="button" id="selectAllPermissions"
                            class="text-sm text-blue-600 hover:text-blue-800">
                            {{ __('Select All') }}
                        </button>
                    </div>

                    <div class="space-y-4">
                        @forelse ($permissions as $module => $modulePermissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-semibold text-gray-800">
                                        {{ ucwords(str_replace(['-', '_'], ' ', $module)) }}
                                    </h4>

                                    <label class="text-sm text-blue-600 cursor-pointer">
                                        <input type="checkbox" class="module-checkbox rounded text-blue-600">
                                        {{ __('Select Module') }}
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach ($modulePermissions as $permission)
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                class="permission-checkbox module-permission rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                {{ in_array($permission->name, old('permissions', $assignedPermissions)) ? 'checked' : '' }}>

                                            {{ ucwords(explode(' ', $permission->name, 2)[0]) }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">{{ __('No permissions created yet.') }}</p>
                        @endforelse
                    </div>

                    @error('permissions')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror

                    @error('permissions.*')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('roles.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        {{ __('Cancel') }}
                    </a>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Save Permissions') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
                moduleCheckbox.addEventListener('change', function() {
                    const container = this.closest('.border');
                    container.querySelectorAll('.module-permission').forEach(permission => {
                        permission.checked = this.checked;
                    });
                });
            });

            document.getElementById('selectAllPermissions').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                const shouldCheck = [...checkboxes].some(checkbox => !checkbox.checked);

                checkboxes.forEach(checkbox => checkbox.checked = shouldCheck);
                document.querySelectorAll('.module-checkbox').forEach(checkbox => {
                    checkbox.checked = shouldCheck;
                });
            });
        </script>
    @endpush
</x-app-layout>
