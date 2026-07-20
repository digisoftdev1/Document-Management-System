<x-app-layout>
    <div class="py-24">
        <div class="px-4 sm:px-6 lg:px-8 max-w-full">
            <div class="bg-white shadow-sm rounded-sm">

                <div class="p-6">

                    <h2 class="text-3xl font-bold text-gray-800 mb-6">
                        {{ __('Edit Record Details') }}
                    </h2>

                    <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf

                        @include('documents._form')

                        <div class="flex justify-end gap-3 mt-6">

                            <a href="{{ route('documents.index') }}"
                                class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                {{ __('Cancel') }}
                            </a>

                            <button type="submit"
                                class="px-5 py-2 bg-[#03396c] text-white rounded-lg hover:bg-[#022d54]">
                                {{ __('Update') }}
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
