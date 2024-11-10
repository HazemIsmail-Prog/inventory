<x-slot name="header">
    <h2 class=" flex items-center justify-between font-semibold text-xl text-gray-800 leading-tight">
        <div>{{ __('Transactions') }}</div>
        <div id="new"></div>
    </h2>
</x-slot>

<div class="py-12">

    {{-- List --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 justify-start items-start gap-3">
                    @forelse ($this->transactions as $transaction)
                        <x-collapsable-card>

                            <div class="flex w-full h-full items-center justify-between px-4 gap-4">
                                <div class="grid grid-cols-2">
                                    <div>Date</div>
                                    <div class="">{{ $transaction->created_at->format('d-m-Y') }}</div>
                                    <div>From</div>
                                    <div class=" font-extrabold text-red-500">{{ $transaction->source_warehouse_id ? $transaction->sourceWarehouse->name : ($transaction->invoice_id ? $transaction->invoice->supplier->name : '-') }}</div>
                                    <div>To</div>
                                    <div class=" font-extrabold text-green-500">{{ $transaction->destinationWarehouse->name ?? '-' }}</div>
                                    <div class=" col-span-full text-xs">{{ $transaction->notes }}</div>
                                </div>

                                <div class="flex-1"></div>

                                <button x-show="show" x-transition x-cloak type="button"
                                    x-on:click.prevent="openModal({{ $transaction }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>

                                </button>

                                <button x-show="show" x-transition x-cloak class="text-red-500"
                                    wire:confirm="Are you sure you want to delete this transaction?"
                                    wire:click="delete({{ $transaction->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>

                            </div>

                            <x-slot name="childList">
                                <div class=" w-full flex flex-col divide-y bg-gray-100">
                                    @foreach ($transaction->items as $item)
                                        <div class="flex items-center justify-between py-2 px-4">
                                            <div>
                                                <div class="font-bold">{{ $item->name }}</div>
                                                <div class="text-xs text-red-500">
                                                    {{ $item->pivot->expiration_date ? \Carbon\Carbon::parse($item->pivot->expiration_date)->format('d-m-Y') : 'non expired' }}

                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="font-bold text-green-500">{{ $item->pivot->quantity }}
                                                </div>
                                                <div class="text-xs">{{ $item->unit }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-slot>
                        </x-collapsable-card>
                    @empty
                        <h1 class=" col-span-full text-center font-bold text-xl">No Transactions Found</h1>
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
                <div class="w-full flex flex-col md:flex-row items-center justify-around gap-3">
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
                <div class="flex flex-col md:flex-row w-full gap-3">
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

                <div class=" flex flex-col md:flex-row gap-3" x-show="isItemsVisible">
                    <!-- Display Available Items -->
                    <div class="p-3 border rounded-md flex flex-col gap-3 w-full md:w-1/3 h-96">
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

                    <!-- Form Items -->
                    <template x-if="!loading && form.items.length > 0">
                        <div class="w-full  max-h-[24rem] overflow-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="w-[200px] min-w-[200px]"></th>
                                        <th class="w-[200px] min-w-[200px]">Expiration Date</th>
                                        <th class="w-[50px] min-w-[50px]"></th>
                                    </tr>
                                </thead>
                                <template x-for="(item , index) in form.items" :key="index">
                                    <tr>
                                        <td class="px-3">
                                            <div class="font-extrabold" x-text="item.name"></div>
                                            <input type="hidden" x-model="item.item_id">
                                        </td>
                                        <td class="px-1">
                                            <x-text-input class="w-full py-1" x-model="item.quantity" required
                                                type="number" step="0.01" x-bind:max="item.net_quantity"
                                                x-bind:placeholder="form.transaction_type !== 'adjustment' ? item.net_quantity + ' ' + item
                                                    .unit +
                                                    ' Available' : 'Quantity'"
                                                min="0" />
                                        </td>
                                        <td class="px-1 text-center">
                                            <x-text-input class="w-full py-1" x-model="item.expiration_date"
                                                x-show="form.transaction_type == 'adjustment'"
                                                x-bind:disabled="form.transaction_type != 'adjustment'"
                                                type="date" />
                                            <span x-show="form.transaction_type != 'adjustment'"
                                                x-text="item.expiration_date ? item.expiration_date : '-'"></span>
                                        </td>
                                        <td class="px-1 text-center">
                                            <div class=" cursor-pointer select-none text-red-500" tabindex="-1"
                                                x-on:click="removeItem(index)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </table>
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
                            <span x-show="submitting" class="animate-spin">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                  </svg>
                                  </span> <!-- Loading indicator -->
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
                const hasEmptyQuantity = this.form.items.some(item => !item.quantity);
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
                // this.resetForm();
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
                        quantity: '',
                        expiration_date: item.expiration_date ? item.expiration_date : null,
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
