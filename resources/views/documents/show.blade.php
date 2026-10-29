@section('title', $document->document_name ?? 'Document')
<x-app-layout>

    <style>
        .header {
            display: none;
        }

        .record-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 15px;
        }

        .record-table td {
            border: 1px solid #000;
            padding: 12px 14px;
            vertical-align: middle;
        }

        .record-table td:nth-child(odd) {
            width: 22%;
            font-weight: 600;
            background: #fafafa;
        }

        .record-table td:nth-child(even) {
            width: 28%;
        }

        @page {
            size: A4;
            margin: 15mm;
        }


        @media print {

            aside,
            nav,
            header,
            footer {
                display: none !important;
            }

            main,
            #mainContent,
            .main-content,
            .content-wrapper {
                margin-left: 0 !important;
                padding-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .sm\:ml-64,
            .lg\:ml-64,
            .ml-64,
            .ml-72 {
                margin-left: 0 !important;
            }

            aside,
            nav,
            header,
            footer,
            .print-hide,
            .pdf-section {
                display: none !important;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            .py-24,
            .px-4,
            .sm\:px-6,
            .lg\:px-8,
            .p-8 {
                padding: 0 !important;
            }

            .max-w-5xl,
            .mx-auto {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 auto !important;
            }

            .bg-white {
                box-shadow: none !important;
                border: none !important;
            }

            .header {
                display: block !important;
                margin-bottom: 35px;
                position: relative;
                text-align: center;
            }

            .logo-section img {
                width: 70px;
                height: 70px;
                display: block;
                margin: auto;
            }

            .print-date {
                position: absolute;
                right: 0;
                top: 10px;
                font-size: 14px;
            }

            .record-table {
                width: 100% !important;
                border-collapse: collapse;
                table-layout: fixed;
                margin: auto;
                font-size: 13px;
            }

            .record-table td {
                border: 1px solid #000 !important;
                padding: 12px;
                vertical-align: middle;
                word-break: normal;
                overflow-wrap: break-word;
            }

            /* labels */
            .record-table td:nth-child(1),
            .record-table td:nth-child(3) {
                width: 20%;
                font-weight: 600;
                background: #f5f5f5 !important;
                white-space: nowrap;
            }

            /* values */
            .record-table td:nth-child(2),
            .record-table td:nth-child(4) {
                width: 30%;
                white-space: nowrap;
            }
        }
    </style>

    <div class="py-24">
        <div class="px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
            <div class="bg-white shadow-sm rounded-sm">

                <div class="p-8">

                    <div class="header">

                        <div class="logo-section">
                            <img src="{{ $setting?->website_logo ? Storage::url($setting->website_logo) : asset('assets/logo/logo.png') }}"
                                alt="{{ $setting?->website_title }}">

                            <div>
                                <h2>{{ $setting?->website_title }}</h2>
                                <h3>{{ __('Record Details') }}</h3>
                            </div>
                        </div>

                        <div class="print-date">
                            {{ __('Date') }} :
                            {{ now()->format('Y-m-d') }}
                        </div>

                    </div>

                    <div class="flex items-center justify-between mb-6 print-hide">
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ __('View Record') }}
                        </h2>

                        <div class="flex gap-3">

                            {{-- <button onclick="window.print()"
                                class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">

                                <i class="fa-solid fa-print mr-2"></i>
                                {{ __('Print') }}

                            </button> --}}

                            <a href="{{ route('documents.edit', $document) }}"
                                class="px-5 py-2 bg-[#03396c] text-white rounded-lg hover:bg-[#022d54]">

                                <i class="fa-solid fa-pen-to-square mr-2"></i>
                                {{ __('Edit') }}

                            </a>

                        </div>
                    </div>

                    <table class="record-table">
                        <tr>
                            <td>{{ __('Record Name') }}</td>
                            <td>{{ $document->document_name ?? '-' }}</td>

                            <td>{{ __('File Number') }}</td>
                            <td>{{ $document->file_number ?? '-' }}</td>
                        </tr>

                        <tr>
                            <td>{{ __('Fiscal Year') }}</td>
                            <td>{{ $document->fiscalYear?->name ?? '-' }}</td>

                            <td>{{ __('Government Level') }}</td>
                            <td>{{ $document->governmentLevel?->name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <td>{{ __('Program Type') }}</td>
                            <td>{{ $document->budgetType?->name ?? '-' }}</td>

                            <td>{{ __('Document Category') }}</td>
                            <td>
                                {{ $document->documentCategory
                                    ? (app()->getLocale() === 'ne'
                                        ? $document->documentCategory->name_np ?? $document->documentCategory->name_en
                                        : $document->documentCategory->name_en ?? $document->documentCategory->name_np)
                                    : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <td>{{ __('Month') }}</td>
                            <td>
                                {{ $document->month
                                    ? (app()->getLocale() === 'ne'
                                        ? $document->month->name_np ?? $document->month->name_en
                                        : $document->month->name_en ?? $document->month->name_np)
                                    : '-' }}
                            </td>

                            <td>{{ __('Day') }}</td>
                            <td>
                                {{ $document->day ? \App\Helpers\NepaliHelper::number($document->day) : '-' }}
                            </td>
                        </tr>
                    </table>

                    <div class="mt-6 pdf-section">

                        <label class="block font-semibold mb-2 text-gray-700">
                            {{ __('Document') }}
                        </label>

                        <div class="border rounded-lg p-4 bg-gray-50">

                            @if ($document->pdf_path)
                                <div class="flex items-center justify-between rounded-lg  p-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg ">

                                            <i class="fa-solid fa-file-pdf text-2xl text-red-600"></i>

                                        </div>

                                        <div>

                                            <p class="font-semibold text-gray-800">
                                                {{ $document->document_name }}
                                            </p>

                                            <p class="text-sm text-gray-500">
                                                {{ basename($document->pdf_path) }}
                                            </p>

                                        </div>

                                    </div>

                                    <button type="button"
                                        onclick="window.open('{{ route('repository.recordview', $document) }}', '_blank')"
                                        class="rounded-lg bg-[#03396c] px-4 py-2 text-white hover:bg-[#022d54]">

                                        <i class="fa-solid fa-book-open mr-2"></i>
                                        {{ __('View') }}

                                    </button>

                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-6 print-hide">

                        <a href="{{ route('documents.index') }}"
                            class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">

                            {{ __('Back') }}

                        </a>

                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
