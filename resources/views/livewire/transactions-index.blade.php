<x-slot name="header">
    <h2 class=" flex items-center justify-between font-semibold text-xl text-gray-800 leading-tight">
        <div>{{ __('Transactions') }}</div>
        <div id="new"></div>
    </h2>
</x-slot>

<div class="py-12">

    {{-- Table --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div x-data="{ showTranscationItems: null }" class="grid grid-cols-3 justify-start items-start gap-3">
                    @forelse ($this->transactions as $transaction)
                        <div class="flex flex-col items-center border rounded-lg p-3 shadow-md"
                            x-on:mouseenter="showTranscationItems = {{ $transaction->id }}"
                            x-on:mouseleave="showTranscationItems = null">
                            <div class="flex w-full items-center gap-3">
                                <div class="flex flex-col flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>{{ $transaction->created_at->format('d-m-Y') }}</div>
                                        <div>{{ ucfirst($transaction->transaction_type) }}</div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            {{ $transaction->source_warehouse_id ? $transaction->sourceWarehouse->name : ($transaction->invoice_id ? $transaction->invoice->supplier->name : '-') }}
                                        </div>
                                        <div>{{ $transaction->destinationWarehouse->name ?? '-' }}</div>
                                    </div>
                                    <div class="text-xs">{{ $transaction->notes }}</div>
                                </div>
                                <div class="border-s-2 ps-3 text-center">
                                    @if (!$transaction->invoice_id)
                                        <button class="text-red-500"
                                            wire:confirm="Are you sure you want to delete this transaction?"
                                            wire:click="deleteTransaction({{ $transaction->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    @endif
                                    <div class=" cursor-pointer select-none"
                                        x-on:click="showTranscationItems != {{ $transaction->id }} ? showTranscationItems = {{ $transaction->id }} : showTranscationItems = null">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div x-cloak x-show="showTranscationItems === {{ $transaction->id }}" x-transition
                                class="mt-3 w-full border-t-2 flex flex-col divide-y">
                                @foreach ($transaction->items as $item)
                                    <div class="flex items-center justify-between py-3">
                                        <div>
                                            <div class="font-bold">{{ $item->name }}</div>
                                            <div class="text-xs">{{ $item->pivot->expiration_date }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="font-bold">{{ $item->pivot->quantity }}</div>
                                            <div class="text-xs">{{ $item->unit }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty

                            <h1 class=" col-span-full text-center font-bold text-xl">No Transactions yet</h1>
                    @endforelse
                </div>


            </div>
        </div>
    </div>

    {{-- Alpine DIV --}}
    <div x-data="form()">
        {{-- New button --}}
        <template x-teleport="#new">
            <x-primary-button x-on:click.prevent="openModal">{{ __('New Transaction') }}</x-primary-button>
        </template>

        {{-- Modal --}}
        <x-modal maxWidth="7xl" name="form-modal">
            <form x-on:submit.prevent="submit" class="p-6 flex flex-col gap-3">

                <!-- Transaction Type Selection (Buttons) -->
                <div class="w-full flex items-center justify-around gap-3">
                    <template x-for="row in transaction_types" :key="row.type">
                        <div>
                            <x-primary-button x-show="form.transaction_type === row.type" type="button">
                                <span x-text="row.label"></span>
                            </x-primary-button>
                            <x-secondary-button x-show="form.transaction_type !== row.type" type="button"
                                x-on:click="handleTypeSelection(row.type)">
                                <span x-text="row.label"></span>
                            </x-secondary-button>
                        </div>
                    </template>
                </div>

                <input type="hidden" name="transaction_type" x-model="form.transaction_type">

                {{-- Warehouses --}}
                <div class="flex w-full gap-3">
                    <!-- Source Warehouse Selection -->
                    <div class="w-full" x-show="isSourceVisible" style="display: none;">
                        <label class="block" for="source_warehouse_id">Source Warehouse:</label>
                        <x-select x-bind:required="isSourceVisible" class="w-full" name="source_warehouse_id"
                            id="source_warehouse_id" x-model="form.source_warehouse_id" x-on:change="loadItems">
                            <option value="">---</option>
                            <template x-for="warehouse in warehouses" :key="warehouse.id">
                                <option :value="warehouse.id" x-text="warehouse.name"></option>
                            </template>
                        </x-select>
                    </div>

                    <!-- Destination Warehouse Selection -->
                    <div class="w-full" x-show="isDestinationVisible" style="display: none;">
                        <label class="block" for="destination_warehouse_id">Destination Warehouse:</label>
                        <x-select x-bind:required="isDestinationVisible" class="w-full" name="destination_warehouse_id"
                            id="destination_warehouse_id" x-model="form.destination_warehouse_id">
                            <option value="">---</option>
                            <template x-for="warehouse in filteredWarehouses" :key="warehouse.id">
                                <option :value="warehouse.id" x-text="warehouse.name"></option>
                            </template>
                        </x-select>
                    </div>
                </div>

                <div class=" flex gap-3" x-show="isItemsVisible">
                    <!-- Display Available Items -->
                    <div class="p-3 border rounded-md flex flex-col gap-3 w-1/3 h-96">
                        <p>Available Items</p>
                        <x-text-input class="w-full" x-model="itemsSearch" type="text" placeholder="Search..." />
                        <p x-show="loading" style="display: none;">Loading items...</p>

                        <p x-show="form.source_warehouse_id && !loading && availableItems.length === 0 && form.items.length === 0"
                            style="display: none;">No items available in this warehouse.</p>
                        <div class="flex flex-col gap-3 flex-1 overflow-y-auto"
                            x-show="!loading && availableItems.length > 0" style="display: none;">
                            <template x-for="(item , index) in filteredAvailableItems" :key="index">
                                <div x-on:click="selectItem(item)"
                                    class="w-full flex gap-3 py-3 items-center justify-between border-b cursor-pointer">
                                    <div>
                                        <div class="font-extrabold" x-text="item.name"></div>
                                        <div class="text-xs font-extralight" x-text="item.expiration_date"></div>
                                    </div>
                                    <div class=" text-end">
                                        <div class="text-xs font-extralight" x-text="item.net_quantity"></div>
                                        <div class="text-xs font-extralight" x-text="item.unit"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Display Form Items -->
                    <template x-if="!loading && form.items.length > 0">
                        <div class="flex flex-col gap-3 flex-1 max-h-[24rem] overflow-y-auto">
                            <template x-for="(item , index) in form.items" :key="index">
                                <div class="w-full flex gap-3 border rounded-lg p-3 items-center">
                                    <div class="w-1/3">
                                        <div class="font-extrabold" x-text="item.name"></div>
                                        <div class="text-xs font-extralight" x-text="item.expiration_date"></div>
                                    </div>
                                    <div class="flex items-center gap-1 flex-1">
                                        <x-text-input class="w-full" x-model="item.quantity" required type="number"
                                            step="0.01" x-bind:max="item.net_quantity"
                                            x-bind:placeholder="form.transaction_type !== 'adjustment' ? item.net_quantity + ' ' + item
                                                .unit +
                                                ' Available' : 'Quantity'"
                                            min="0" />
                                        <x-text-input class="w-full" x-model="item.expiration_date"
                                            x-bind:required="form.transaction_type == 'adjustment'"
                                            x-bind:type="form.transaction_type == 'adjustment' ? 'date' : 'hidden'" />
                                        <input type="hidden" x-model="item.item_id">
                                    </div>
                                    <button type="button" x-on:click="removeItem(index)" class=" text-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <template x-if="isFormReadyToSubmit">
                    <textarea class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="Notes..." x-model="form.notes" rows="2"></textarea>
                </template>

                {{-- Footer Buttons --}}
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="closeModal">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <template x-if="isFormReadyToSubmit">

                        <x-primary-button x-bind:disabled="submitting" class="ms-3">
                            <span x-show="!submitting">{{ __('Save') }}</span>
                            <span x-show="submitting" class="animate-spin">⏳</span> <!-- Loading indicator -->
                        </x-primary-button>

                    </template>
                    <template x-if="!isFormReadyToSubmit">

                        <x-primary-button type="button" disabled class="ms-3">
                            {{ __('Not Ready to Save') }}
                        </x-primary-button>

                    </template>

                </div>
            </form>
        </x-modal>

    </div>


</div>

<script>
    function form() {
        return {
            warehouses: @json($this->warehouses),
            loading: false,
            submitting: false,
            itemsSearch: '',

            transaction_types: [{
                    type: 'transfer',
                    label: 'Transfer'
                },
                {
                    type: 'adjustment',
                    label: 'Adjustment'
                },
                {
                    type: 'write_off',
                    label: 'Write Off'
                }
            ],

            availableItems: [],

            form: @entangle('form'),

            init() {
                // Livewire.on('modalClosed', () => this.closeModal()); // Listen for modalClosed event
            },

            get isFormReadyToSubmit() {
                const {
                    transaction_type,
                    source_warehouse_id,
                    destination_warehouse_id
                } = this.form;

                if (!transaction_type || this.preparedItemsLength === 0) {
                    return false;
                }

                // Check if any item's quantity is empty or zero
                const hasEmptyQuantity = this.form.items.some(item => !item.quantity || !item.expiration_date);
                if (hasEmptyQuantity) {
                    return false;
                }

                const requirements = {
                    transfer: source_warehouse_id && destination_warehouse_id,
                    adjustment: destination_warehouse_id,
                    write_off: source_warehouse_id,
                };

                return requirements[transaction_type] ?? true;
            },

            get isSourceVisible() {
                return this.form.transaction_type === 'transfer' || this.form.transaction_type === 'write_off';
            },

            get isDestinationVisible() {
                return (this.form.transaction_type === 'transfer' && this.form.source_warehouse_id) || this.form
                    .transaction_type ===
                    'adjustment';
            },

            get isItemsVisible() {
                return ((this.form.transaction_type === 'transfer' || this.form.transaction_type === 'write_off') &&
                        this.form.source_warehouse_id) ||
                    (this.form.transaction_type === 'adjustment');
            },

            get filteredWarehouses() {
                return this.warehouses.filter(warehouse => warehouse.id != this.form.source_warehouse_id);
            },

            get filteredAvailableItems() {
                return this.itemsSearch ?
                    this.availableItems.filter(item => item.name.toLowerCase().includes(this.itemsSearch
                        .toLowerCase())) :
                    this.availableItems;
            },

            get preparedItemsLength() {
                const preparedItems = this.form.items.filter(item => item.quantity > 0).map(item => ({
                    item_id: item.item_id,
                    quantity: item.quantity,
                    expiration_date: item.expiration_date
                }));

                return preparedItems.length;
            },

            openModal() {
                this.form.transaction_type = '';
                this.resetForm();
                this.$dispatch('open-modal', 'form-modal'); // Open modal
            },

            closeModal() {
                this.form.transaction_type = '';
                this.resetForm();
                this.$dispatch('close-modal', 'form-modal'); // Dispatch the close event for the modal
            },

            handleTypeSelection(type) {
                this.resetForm();
                this.form.transaction_type = type;
                if (type == 'adjustment') {
                    this.loadItems();
                }

            },

            resetForm() {
                this.form.items = [];
                this.submitting = false;
                this.form.source_warehouse_id = '';
                this.form.destination_warehouse_id = '';
                this.availableItems = [];
                this.loading = false;
                this.itemsSearch = '';
            },

            loadItems() {

                this.loading = true;
                this.availableItems = [];
                this.form.items = [];
                if (!this.form.source_warehouse_id && this.form.transaction_type !== 'adjustment') {
                    this.loading = false;
                    return;
                };

                // Check the transaction type and load items accordingly
                let url = '';
                if (this.form.transaction_type === 'adjustment') {
                    url = `/api/items/getItemsList`; // URL to fetch all items
                } else if (this.form.transaction_type === 'transfer' || this.form.transaction_type === 'write_off') {
                    url =
                        `/api/warehouses/${this.form.source_warehouse_id}/items`; // URL to fetch items for the specific warehouse
                }

                if (url) {
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            this.availableItems = data.data.map(item => ({
                                item_id: item.id,
                                name: item.name,
                                net_quantity: item.net_quantity,
                                unit: item.unit,
                                expiration_date: item.expiration_date,
                            }));
                        })
                        .catch(error => {
                            console.error('Error fetching items:', error);
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                } else {
                    this.loading = false; // No URL, just stop loading
                }
            },

            selectItem(item) {
                const index = this.availableItems.findIndex(i => i.item_id === item.item_id);
                if (index !== -1) {
                    this.form.items.push({
                        ...item,
                        quantity: ''
                    });

                    // Remove the selected item from availableItems if the type is not 'adjustment'
                    if (this.form.transaction_type !== 'adjustment') {
                        this.availableItems.splice(index, 1);
                    }
                }
            },

            removeItem(index) {
                const removedItem = this.form.items.splice(index, 1)[0];
                if (removedItem && this.form.transaction_type !== 'adjustment') {
                    this.availableItems.push(removedItem);
                }
            },

            submit() {


                if (!this.isFormReadyToSubmit) {
                    alert('Please fill the missing fields');
                    return;
                }

                this.submitting = true;
                @this.save().then(() => {
                    this.submitting = false;
                    this.closeModal();
                }).catch(() => {
                    this.submitting = false;
                });
            },
        }
    }
</script>
