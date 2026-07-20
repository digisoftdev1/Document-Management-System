<div id="contextMenu" class="fixed hidden z-[9999] w-56 bg-white rounded-xl shadow-xl border py-2">

    <a id="menuOpen" href="#" class="flex items-center px-4 py-2 hover:bg-gray-100">

        <i class="fa-solid fa-folder-open w-5"></i>

        {{ __('Open') }}

    </a>

    <button id="menuRename" class="w-full text-left flex items-center px-4 py-2 hover:bg-gray-100">

        <i id="menuRenameIcon" class="fa-solid fa-pen w-5"></i>

        <span id="menuRenameText">{{ __('Edit') }}</span>

    </button>

    <button id="menuProperties" class="w-full text-left flex items-center px-4 py-2 hover:bg-gray-100">

        <i class="fa-solid fa-circle-info w-5"></i>

        {{ __('Properties') }}

    </button>




    <button id="menuDelete" class="w-full text-left flex items-center px-4 py-2 hover:bg-red-50 text-red-600">

        <i class="fa-solid fa-trash w-5"></i>

        {{ __('Delete') }}

    </button>

</div>
