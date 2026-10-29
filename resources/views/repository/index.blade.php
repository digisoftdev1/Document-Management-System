<x-app-layout>
    <div class="py-24">
        <div class="px-6 lg:px-8">

            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ __('Repository Management') }}
                    </h1>

                    <p class="mt-2 text-gray-500">
                        {{ __('Manage digital records and archived documents.') }}
                    </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Digital Repository --}}
                <a href="{{ route('repository.records') }}"
                    class="group bg-white  rounded-2xl
               shadow-sm hover:shadow-md hover:border-[#03396c]/30
               transition overflow-hidden">



                    <div class="p-6">

                        <div
                            class="w-14 h-14 rounded-xl bg-blue-100 text-blue-700
                       flex items-center justify-center mb-5">

                            <i class="fa-solid fa-folder-tree text-2xl"></i>

                        </div>

                        <h2
                            class="text-xl font-semibold text-gray-800
                       group-hover:text-[#03396c] transition">

                            {{ __('Current Records') }}

                        </h2>

                        <p class="mt-3 text-sm text-gray-600 leading-6">

                            {{ __('Browse documents generated within the system.') }}

                        </p>

                        <div class="mt-6 flex items-center text-sm
                       text-[#03396c] font-medium">

                            {{ __('Open Repository') }}

                            <i
                                class="fa-solid fa-arrow-right ml-2
                           transition group-hover:translate-x-1">
                            </i>

                        </div>

                    </div>

                </a>

                {{-- Archive --}}
                <a href="{{ route('repository.index') }}"
                    class="group bg-white rounded-2xl
               shadow-sm hover:shadow-md hover:border-[#03396c]/30
               transition overflow-hidden">



                    <div class="p-6">

                        <div
                            class="w-14 h-14 rounded-xl bg-amber-100 text-amber-600
                       flex items-center justify-center mb-5">

                            <i class="fa-solid fa-box-archive text-2xl"></i>

                        </div>

                        <h2
                            class="text-xl font-semibold text-gray-800
                       group-hover:text-[#03396c] transition">

                            {{ __('Archival Records') }}

                        </h2>

                        <p class="mt-3 text-sm text-gray-600 leading-6">

                            {{ __('Access imported PDFs and archived records stored in folders.') }}

                        </p>

                        <div class="mt-6 flex items-center text-sm
                       text-[#03396c] font-medium">

                            {{ __('Open Repository') }}

                            <i
                                class="fa-solid fa-arrow-right ml-2
                           transition group-hover:translate-x-1">
                            </i>

                        </div>

                    </div>

                </a>

            </div>

        </div>
    </div>
</x-app-layout>
