<x-app-layout>
    <div class="py-24">
        <div class="px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">

                <div>

                    <h1 class="text-3xl font-bold text-[#03396c]">
                        {{ __('Dashboard') }}
                    </h1>

                    <p class="text-gray-500 mt-1">
                        {{ __('Digital Document Management System Overview') }}
                    </p>

                </div>



            </div>

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                {{-- Total Records --}}
                <div class="rounded-2xl bg-[#eaf2fb] border border-[#c9d9eb] p-6 shadow-sm hover:shadow-md transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-[#4b6b8f] font-medium">
                                {{ __('Total Records') }}
                            </p>

                            <h2 class="text-4xl font-bold text-[#03396c] mt-3">
                                {{ number_format($totalDocuments) }}
                            </h2>
                        </div>

                        <div class="size-14 rounded-xl bg-[#d5e4f3] flex items-center justify-center">

                            <i class="fa-solid fa-file-lines text-2xl text-[#03396c]"></i>

                        </div>

                    </div>

                </div>





                {{-- Uploaded Today --}}
                <div class="rounded-2xl bg-[#fdf0f1] border border-[#f3d3d8] p-6 shadow-sm hover:shadow-md transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-[#9b4a58] font-medium">
                                {{ __('Uploaded Today') }}
                            </p>

                            <h2 class="text-4xl font-bold text-[#b4233c] mt-3">
                                {{ number_format($documentsToday) }}
                            </h2>
                        </div>

                        <div class="size-14 rounded-xl bg-[#f8dde1] flex items-center justify-center">

                            <i class="fa-solid fa-calendar-plus text-2xl text-[#b4233c]"></i>

                        </div>

                    </div>

                </div>


                {{-- Upload Growth --}}
                <div class="rounded-2xl bg-[#f1f4fb] border border-[#d9e0ef] p-6 shadow-sm hover:shadow-md transition">

                    <div class="flex justify-between items-start">

                        <div>

                            <p class="text-[#5f6f91] font-medium">
                                {{ __('Uploaded This Month') }}
                            </p>

                            <div class="flex items-end gap-2 mt-3">

                                <h2 class="text-4xl font-bold text-[#314a7c] leading-none">
                                    {{ number_format($documentsThisMonth) }}
                                </h2>

                                <span
                                    class="text-sm font-medium mb-1
            {{ $documentGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">

                                    @if ($documentGrowth >= 0)
                                        <i class="fa-solid fa-arrow-trend-up"></i>
                                    @else
                                        <i class="fa-solid fa-arrow-trend-down"></i>
                                    @endif

                                    {{ abs($documentGrowth) }}%

                                </span>

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('vs last month') }}
                            </p>

                        </div>

                        <div class="size-14 rounded-xl bg-[#e1e8f7] flex items-center justify-center">

                            <i class="fa-solid fa-chart-line text-2xl text-[#314a7c]"></i>

                        </div>

                    </div>

                </div>
                {{-- Archived Files --}}
                <div class="rounded-2xl bg-[#eef8f0] border border-[#d4e9d8] p-6 shadow-sm hover:shadow-md transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-[#5f7d65] font-medium">
                                {{ __('Archived Files') }}
                            </p>

                            <h2 class="text-4xl font-bold text-[#1f6b36] mt-3">
                                {{ number_format($totalFiles) }}
                            </h2>
                        </div>

                        <div class="size-14 rounded-xl bg-[#dcefe0] flex items-center justify-center">

                            <i class="fa-solid fa-box-archive text-2xl text-[#1f6b36]"></i>

                        </div>

                    </div>

                </div>

            </div>

            {{-- Fiscal Year --}}
            <div class="bg-white rounded-2xl shadow mb-6 p-6">

                <h3 class="font-semibold mb-6">
                    {{ __('Fiscal Year Distribution') }}
                </h3>

                <div class="relative min-h-[350px] h-[40vh] max-h-[500px]">
                    <canvas id="fiscalChart"></canvas>
                </div>

            </div>
            {{-- Document Category Distribution --}}
            <div class="bg-white rounded-2xl shadow mb-6 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-semibold text-gray-800">
                        {{ __('Document Category Distribution') }}
                    </h3>

                    <span class="text-sm text-gray-500">
                        {{ number_format($categoryTotal) }}
                        {{ $categoryTotal === 1 ? __('Document') : __('Documents') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-gray-500 border-b">
                            <tr>
                                <th class="py-3 pr-4">S.N.</th>
                                <th class="py-3 pr-4">{{ __('Category') }}</th>
                                <th class="py-3 px-4 text-right">{{ __('Documents') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($categoryStats as $category)
                                @php
                                    $name =
                                        app()->getLocale() === 'ne'
                                            ? ($category->name_np ?:
                                            $category->name_en)
                                            : ($category->name_en ?:
                                            $category->name_np);

                                    $percentage = $categoryTotal ? ($category->total / $categoryTotal) * 100 : 0;
                                @endphp

                                <tr class="{{ $loop->iteration > 5 ? 'hidden extra-category' : '' }}">
                                    <td class="py-3 pr-4 text-gray-400">{{ $loop->iteration }}</td>
                                    <td class="py-3 pr-4 font-medium text-gray-800">{{ $name }}</td>
                                    <td class="py-3 px-4 text-right">{{ number_format($category->total) }}</td>

                                </tr>
                            @endforeach

                            @if ($uncategorizedDocuments > 0)
                                <tr class="{{ $categoryStats->count() >= 5 ? 'hidden extra-category' : '' }}">
                                    <td class="py-3 pr-4 text-gray-400">—</td>
                                    <td class="py-3 pr-4">{{ __('Uncategorized') }}</td>
                                    <td class="py-3 px-4 text-right">
                                        {{ number_format($uncategorizedDocuments) }}
                                    </td>
                                    <td class="py-3 pl-4">
                                        {{ $categoryTotal ? number_format(($uncategorizedDocuments / $categoryTotal) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($categoryStats->count() + ($uncategorizedDocuments > 0 ? 1 : 0) > 5)
                    <div class="mt-4 text-center">
                        <button type="button" id="toggleCategories"
                            class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            {{ __('Show more') }} ↓
                        </button>
                    </div>
                @endif
            </div>

            <script>
                document.getElementById('toggleCategories')?.addEventListener('click', function() {
                    const rows = document.querySelectorAll('.extra-category');
                    const expanded = this.dataset.expanded === 'true';

                    rows.forEach(row => row.classList.toggle('hidden', expanded));

                    this.dataset.expanded = String(!expanded);
                    this.textContent = expanded ?
                        @json(__('Show more')) + ' ↓' :
                        @json(__('Show less')) + ' ↑';
                });
            </script>

            <div class="grid xl:grid-cols-2 gap-6 my-2">


                {{-- Government --}}
                <div class="bg-white rounded-2xl shadow  p-6">

                    <h3 class="font-semibold mb-6">
                        {{ __('Government Distribution') }}
                    </h3>

                    <div class="relative min-h-[350px] h-[40vh] max-h-[500px]">
                        <canvas id="governmentChart"></canvas>
                    </div>

                </div>

                {{-- Budget --}}
                <div class="bg-white rounded-2xl shadow  p-6">

                    <h3 class="font-semibold mb-6">
                        {{ __('Program Type Distribution') }}
                    </h3>

                    <div class="relative min-h-[350px] h-[40vh] max-h-[500px]">
                        <canvas id="budgetChart"></canvas>
                    </div>

                </div>




            </div>

        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {

                    new window.Chart(
                        document.getElementById('governmentChart'), {
                            type: 'doughnut',
                            data: {
                                labels: @json($governmentStats->pluck('government')),
                                datasets: [{
                                    data: @json($governmentStats->pluck('total'))
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        }
                    );

                    new window.Chart(
                        document.getElementById('budgetChart'), {
                            type: 'pie',
                            data: {
                                labels: @json($budgetStats->pluck('budget')),
                                datasets: [{
                                    data: @json($budgetStats->pluck('total'))
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        }
                    );

                    new window.Chart(
                        document.getElementById('fiscalChart'), {
                            type: 'bar',
                            data: {
                                labels: @json($fiscalYearStats->pluck('name')),
                                datasets: [{
                                    label: 'Documents',
                                    data: @json($fiscalYearStats->pluck('total')),
                                    borderRadius: 10
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        }
                    );

                });
            </script>
        @endpush
</x-app-layout>
