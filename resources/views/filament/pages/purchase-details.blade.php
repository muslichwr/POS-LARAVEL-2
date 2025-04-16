<x-filament::page>
    <x-filament::card>
        <div class="flex justify-between mb-4">
            <h2 class="text-2xl font-bold">Detail Pembelian #{{ $record->id }}</h2>
            <div class="flex space-x-2">
                {{ $this->table->getHeaderActions() }}
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500">Supplier</p>
                    <p class="font-semibold">{{ $record->supplier->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Tanggal</p>
                    <p class="font-semibold">{{ $record->purchase_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Total</p>
                    <p class="font-semibold text-xl text-green-600">{{ $record->total_amount }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <span class="px-2 py-1 rounded 
                        @if($record->status === 'completed') bg-green-200 text-green-800 
                        @elseif($record->status === 'pending') bg-yellow-200 text-yellow-800 
                        @else bg-red-200 text-red-800 @endif">
                        {{ $record->status }}
                    </span>
                </div>
            </div>

            {{ $this->table }}
        </div>
    </x-filament::card>
</x-filament::page>