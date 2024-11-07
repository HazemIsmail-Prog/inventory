<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($items->isEmpty())
                        <p>No items found.</p>
                    @else
                        <table class="w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="border px-4 py-2">ID</th>
                                    <th class="border px-4 py-2">Name</th>
                                    <th class="border px-4 py-2">Unit</th>
                                    <th class="border px-4 py-2">Available Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="border px-4 py-2">{{ $item->id }}</td>
                                        <td class="border px-4 py-2">{{ $item->name }}</td>
                                        <td class="border px-4 py-2">{{ $item->unit }}</td>
                                        <td class="border px-4 py-2">{{ $item->totalIncome - $item->totalOutgoing == 0 ? '' : $item->totalIncome - $item->totalOutgoing }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
