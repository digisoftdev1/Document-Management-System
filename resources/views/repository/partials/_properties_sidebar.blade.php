<div id="propertiesSidebar"
    class="fixed top-0 right-0 h-screen w-[430px] bg-gray-50 border-l shadow-2xl z-[9999]
    translate-x-full transition-transform duration-300 flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b bg-white">

        <h2 class="text-xl font-bold text-gray-800">
            {{ __('Details') }}
        </h2>

        <button id="closePropertiesBtn" class="w-10 h-10 rounded-full hover:bg-gray-100 transition">

            <i class="fa-solid fa-xmark text-lg"></i>

        </button>

    </div>

    <div class="flex-1 overflow-y-auto">

        {{-- Hero --}}
        <div class="bg-white border-b px-6 py-8 text-center">

            <div id="propertiesIcon" class="mx-auto w-24 h-24 rounded-3xl bg-gray-100 flex items-center justify-center">

                <i class="fa-solid fa-folder text-yellow-400 text-6xl"></i>

            </div>

            <h3 id="propName" class="mt-5 text-xl font-bold text-gray-800 break-all">
            </h3>

            <p id="propertiesSubtitle" class="text-gray-500 mt-1">
            </p>

        </div>



        {{-- Description (Repository only) --}}
        @unless ($recordsMode)
            <div id="descriptionCard" class="bg-white border-b p-6">

                <h4 class="font-semibold text-gray-800 mb-3">
                    {{ __('Description') }}
                </h4>

                <p id="propDescription" class="text-gray-600 leading-7">
                </p>

            </div>
        @endunless


        {{-- Folder Statistics --}}
        <div id="folderStats" class="hidden bg-white border-b p-6">

            <h4 class="font-semibold text-gray-800 mb-4">
                {{ __('Statistics') }}
            </h4>

            <div class="grid {{ $recordsMode ? 'grid-cols-2' : 'grid-cols-3' }} gap-3">
                @if (!$recordsMode)
                    <div class="rounded-xl border bg-gray-50 p-4 text-center">

                        <i class="fa-solid fa-folder text-yellow-400 text-2xl"></i>

                        <div id="propFolderCount" class="text-2xl font-bold mt-3">
                            0
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            {{ __('Folders') }}
                        </div>

                    </div>
                @endif
                <div class="rounded-xl border bg-gray-50 p-4 text-center">

                    <i class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>

                    <div id="propFileCount" class="text-2xl font-bold mt-3">
                        0
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        {{ __('Documents') }}
                    </div>

                </div>

                <div class="rounded-xl border bg-gray-50 p-4 text-center">

                    <i class="fa-solid fa-hard-drive text-blue-500 text-2xl"></i>

                    <div id="propStorage" class="text-lg font-bold mt-3">
                        —
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        {{ __('Storage') }}
                    </div>

                </div>

            </div>

        </div>


        {{-- Document Information --}}
        <div id="documentInfoCard" class="hidden bg-white border-b p-6">

            <h4 class="font-semibold text-gray-800 mb-4">
                {{ __('Record Details') }}
            </h4>

            <div class="space-y-5">

                <div class="flex justify-between">
                    <div class="text-gray-500">
                        {{ __('File Number') }}
                    </div>

                    <div id="propFileNumber" class="font-medium text-right">
                    </div>
                </div>

                <div class="flex justify-between">
                    <div class="text-gray-500">
                        {{ __('Fiscal Year') }}
                    </div>

                    <div id="propFiscalYear" class="font-medium text-right">
                    </div>
                </div>

                <div class="flex justify-between">
                    <div class="text-gray-500">
                        {{ __('Government Level') }}
                    </div>

                    <div id="propGovernment" class="font-medium text-right">
                    </div>
                </div>

                <div class="flex justify-between">
                    <div class="text-gray-500">
                        {{ __('Program Type') }}
                    </div>

                    <div id="propBudget" class="font-medium text-right">
                    </div>
                </div>

                <div class="flex justify-between">
                    <div class="text-gray-500">
                        {{ __('Month') }}
                    </div>

                    <div id="propMonth" class="font-medium text-right">
                    </div>
                </div>

                <div class="flex justify-between">
                    <div class="text-gray-500">
                        {{ __('Day') }}
                    </div>

                    <div id="propDay" class="font-medium text-right">
                    </div>
                </div>

            </div>

        </div>


        {{-- Information --}}
        <div class="bg-white border-b p-6">

            <h4 class="font-semibold text-gray-800 mb-4">
                {{ __('Properties') }}
            </h4>

            <div class="space-y-5">

                <div class="flex justify-between">

                    <div class="text-gray-500">
                        <i class="fa-solid fa-file mr-2"></i>
                        {{ __('Type') }}
                    </div>

                    <div id="propType" class="font-medium text-right">
                    </div>

                </div>

                <div id="sizeRow" class="flex justify-between">

                    <div class="text-gray-500">
                        <i class="fa-solid fa-weight-hanging mr-2"></i>
                        {{ __('Size') }}
                    </div>

                    <div id="propSize" class="font-medium text-right">
                    </div>

                </div>

                <div class="flex justify-between">

                    <div class="text-gray-500">
                        <i class="fa-solid fa-user mr-2"></i>
                        {{ __('Added By') }}
                    </div>

                    <div id="propOwner" class="font-medium text-right">
                        —
                    </div>

                </div>

                <div class="flex justify-between">

                    <div class="text-gray-500">
                        <i class="fa-solid fa-calendar-plus mr-2"></i>
                        {{ __('Created') }}
                    </div>

                    <div id="propCreated" class="font-medium text-right">
                    </div>

                </div>

                <div class="flex justify-between">

                    <div class="text-gray-500">
                        <i class="fa-solid fa-clock-rotate-left mr-2"></i>
                        {{ __('Modified') }}
                    </div>

                    <div id="propUpdated" class="font-medium text-right">
                    </div>

                </div>

            </div>

        </div>

        {{-- Actions --}}
        @if (!$recordsMode)
            <div id="fileActions" class="hidden bg-white p-6">

                <h4 class="font-semibold text-gray-800 mb-4">
                    {{ __('Actions') }}
                </h4>

                <div class="space-y-3">


                    <a id="propDownload" href="#" download
                        class="flex justify-center items-center gap-2
                        w-full rounded-xl border py-3 hover:bg-gray-50 transition">

                        <i class="fa-solid fa-download"></i>

                        {{ __('Download PDF') }}

                    </a>

                </div>

            </div>
        @else
            <div id="recordActions" class="bg-white p-6">

                {{-- <a id="recordPropOpen" href="#" target="_blank"
                    class="flex justify-center items-center gap-2
                    w-full rounded-xl bg-[#03396c]
                    text-white py-3 hover:bg-[#022d54] transition">

                    <i class="fa-solid fa-folder-open"></i>

                    {{ __('Open') }}

                </a> --}}
                <a id="recordPropDownload" href="#" download
                    class="flex justify-center items-center gap-2
                        w-full rounded-xl border py-3 hover:bg-gray-50 transition">

                    <i class="fa-solid fa-download"></i>

                    {{ __('Download PDF') }}

                </a>

            </div>
        @endif

    </div>

</div>
