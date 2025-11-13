<x-filament::page>
    <div class="space-y-6">

        {{-- FLOATING HEADER --}}
        <div class="sticky top-0 z-50 backdrop-blur-md bg-white/70 dark:bg-gray-900/60 
                    border-b border-gray-200 dark:border-gray-700 px-4 py-4 rounded-b-xl shadow-sm">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Google Excel Sheet
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Live editable spreadsheet synced directly with your dashboard.
                    </p>
                </div>

                {{-- Sticky Action Tools --}}
                <div class="flex flex-wrap gap-3">

                    <a href="https://docs.google.com/spreadsheets/d/1MCDA9I-jeJtzITvvNcbE4Aj_BK8wAs9HCFr5s5s_Z_U/export?format=xlsx"
                       target="_blank"
                       class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium 
                              rounded-lg shadow-lg transition-all active:scale-95">
                        Download Excel
                    </a>

                    <a href="https://docs.google.com/spreadsheets/d/1MCDA9I-jeJtzITvvNcbE4Aj_BK8wAs9HCFr5s5s_Z_U/export?format=pdf"
                       target="_blank"
                       class="px-4 py-2 bg-primary-600 hover:bg-gray-700 text-white font-medium 
                              rounded-lg shadow-lg transition-all active:scale-95">
                        PDF
                    </a>
                </div>

            </div>
        </div>

        {{-- Main Sheet Container --}}
        <div class="rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 
                    bg-white dark:bg-gray-900 overflow-hidden">

            {{-- iframe wrapper --}}
            <div class="rounded-lg overflow-hidden">
                <iframe 
                    src="https://docs.google.com/spreadsheets/d/1MCDA9I-jeJtzITvvNcbE4Aj_BK8wAs9HCFr5s5s_Z_U/edit?usp=sharing"
                    width="100%"
                    height="900"
                    class="rounded-lg"
                    style="border:0;">
                </iframe>
            </div>
        </div>

    </div>
</x-filament::page>
