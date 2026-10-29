<div class="hidden sm:flex sm:items-center">
    <x-dropdown align="right" width="48">

        <x-slot name="trigger">
            <button
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition">


                <span class="uppercase">
                    {{ app()->getLocale() }}
                </span>

                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>

            </button>
        </x-slot>
        <x-slot name="content">

            <x-dropdown-link :href="route('language.switch', 'en')"
                class="{{ app()->getLocale() === 'en' ? 'bg-gray-50 font-semibold' : '' }}">

                🇬🇧 English

            </x-dropdown-link>

            <x-dropdown-link :href="route('language.switch', 'ne')"
                class="{{ app()->getLocale() === 'ne' ? 'bg-gray-50 font-semibold' : '' }}">

                🇳🇵 नेपाली

            </x-dropdown-link>

        </x-slot>

    </x-dropdown>
</div>
