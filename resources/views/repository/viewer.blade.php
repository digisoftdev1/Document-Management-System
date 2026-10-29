<x-app-layout>

    <div class="py-20 px-6" x-data>

        <div class="mb-3 flex justify-between items-center">

            <div class="pt-3">

                <h2 class="text-2xl font-bold">
                    {{ $title }}
                </h2>



            </div>

            <a href="{{ url()->previous() }}" class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200">

                Back

            </a>

        </div>

        <div class="_df_book" source="{{ $pdf }}" style="height:85vh;width:100%;">
        </div>

    </div>

</x-app-layout>
