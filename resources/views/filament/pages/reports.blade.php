<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="generateReport">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit">
                    Generate Report
                </x-filament::button>
            </div>
        </form>

        @if($data)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                @foreach($this->getReportData() as $key => $value)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</h3>
                        <p class="text-2xl font-bold text-blue-600">
                            @if(str_contains($key, 'cost') || str_contains($key, 'revenue') || str_contains($key, 'profit'))
                                ${{ number_format($value, 2) }}
                            @else
                                {{ $value }}
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
