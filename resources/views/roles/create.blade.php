<x-app-layout>
    <div class="py-24 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-6">{{ __('Create Role') }}</h2>

            <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        {{ __('Role Name') }}
                    </label>

                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        placeholder="e.g. manager" required
                        class="mt-1 block w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">

                    <p class="text-xs text-gray-500 mt-1">
                        {{ __('Use letters, numbers, underscores or hyphens. Example: document_manager') }}
                    </p>

                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('roles.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        {{ __('Cancel') }}
                    </a>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Create Role') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
