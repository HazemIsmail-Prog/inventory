<x-slot name="header">
    <h2 class=" flex items-center justify-between font-semibold text-xl text-gray-800 leading-tight">
        <div>{{ __('Invoices') }}</div>
        <div id="new"></div>
    </h2>
</x-slot>

<div class="py-12">

    {{-- Table --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div x-data="{ showTranscationItems: null }" class="grid grid-cols-3 justify-start items-start gap-3">
                    @forelse ($this->invoices as $invoice)
                        <div class="flex flex-col items-center border rounded-lg p-3 shadow-md"
                            x-on:mouseenter="showTranscationItems = {{ $invoice->id }}"
                            x-on:mouseleave="showTranscationItems = null">
                            <div class="flex w-full items-center gap-3">
                                <div class="flex flex-col flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>{{ $invoice->created_at->format('d-m-Y') }}</div>
                                        <div>{{ ucfirst($invoice->invoice_type) }}</div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            {{ $invoice->supplier->name }}
                                        </div>
                                        <div>{{ $invoice->warehouse->name ?? '-' }}</div>
                                    </div>
                                    <div class="text-xs">{{ $invoice->notes }}</div>
                                </div>
                                <div class="border-s-2 ps-3 text-center">
                                    <button class="text-red-500"
                                        wire:confirm="Are you sure you want to delete this invoice?"
                                        wire:click="delete({{ $invoice->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                    <div class=" cursor-pointer select-none"
                                        x-on:click="showTranscationItems != {{ $invoice->id }} ? showTranscationItems = {{ $invoice->id }} : showTranscationItems = null">
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

                            <div x-cloak x-show="showTranscationItems === {{ $invoice->id }}" x-transition
                                class="mt-3 w-full border-t-2 flex flex-col divide-y">
                                @foreach ($invoice->items as $item)
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

                        <h1 class=" col-span-full text-center font-bold text-xl">No Invoices Yet</h1>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine DIV --}}
    <div x-data="form()">
        {{-- New button --}}
        <template x-teleport="#new">
            <x-primary-button x-on:click.prevent="openModal">{{ __('New Invoice') }}</x-primary-button>
        </template>

        {{-- Modal --}}
        <x-modal maxWidth="7xl" name="form-modal">
            <form x-on:submit.prevent="submit" class="p-6 flex flex-col gap-3">

                <div class="flex w-full gap-3">
                    <div class="w-full">
                        <label class="block" for="supplier_id">Invoice Number</label>
                        <x-text-input class="w-full" x-model="form.invoice_number" type="text" />
                    </div>
                    <div class="w-full">
                        <label class="block" for="supplier_id">Date</label>
                        <x-text-input class="w-full" x-model="form.invoice_date" type="date" />
                    </div>

                </div>

                <div class="flex w-full gap-3">
                    <!-- Suuplier Selection -->
                    <div class="w-full">
                        <label class="block" for="supplier_id">Supplier:</label>
                        <x-select required class="w-full" name="supplier_id" id="supplier_id"
                            x-model="form.supplier_id">
                            <option value="">---</option>
                            <template x-for="supplier in suppliers" :key="supplier.id">
                                <option :value="supplier.id" x-text="supplier.name"></option>
                            </template>
                        </x-select>
                    </div>




                    <!-- Warehouse Selection -->
                    <div class="w-full">
                        <label class="block" for="warehouse_id">Warehouse:</label>
                        <x-select required class="w-full" name="warehouse_id" id="warehouse_id"
                            x-model="form.warehouse_id">
                            <option value="">---</option>
                            <template x-for="warehouse in warehouses" :key="warehouse.id">
                                <option :value="warehouse.id" x-text="warehouse.name"></option>
                            </template>
                        </x-select>
                    </div>

                </div>

                <textarea class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    placeholder="Notes..." x-model="form.notes" rows="2"></textarea>

                <div class="text-right font-bold text-lg">
                    Total: <span x-text="formatNumber(grandTotal)"></span>
                </div>

                <div class=" flex gap-3">
                    <!-- Items -->
                    <div class="p-3 border rounded-md flex flex-col gap-3 w-1/3 h-96">
                        <p>Items</p>
                        <x-text-input class="w-full" x-model="itemsSearch" type="text" placeholder="Search..." />
                        <div class="flex flex-col gap-3 flex-1 overflow-y-auto">
                            <template x-for="(item , index) in filteredItems" :key="item.id">
                                <div x-on:click="selectItem(item)"
                                    class="w-full flex gap-3 py-3 items-center justify-between border-b cursor-pointer">
                                    <div>
                                        <div class="font-extrabold" x-text="item.name"></div>
                                    </div>
                                    <div class=" text-end">
                                        <div class="text-xs font-extralight" x-text="item.unit"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Form Items -->
                    <template x-if="form.items.length > 0">
                        <div class="w-full  h-96 overflow-auto">

                        
                        <table class="w-full">
                            <template x-for="(item , index) in form.items" :key="index">
                                <tr>
                                    <td class="px-1">
                                        <div class="font-extrabold" x-text="item.name"></div>
                                        {{-- <div class="text-xs font-extralight" x-text="item.expiration_date"></div> --}}
                                        <input type="hidden" x-model="item.item_id">
                                    </td>
                                    <td class="px-1">
                                        <x-text-input class="w-full py-1" x-model="item.quantity" required type="number"
                                            step="0.01" placeholder="Quantity" min="0" />
                                    </td>
                                    <td class="px-1">
                                        <x-text-input class="w-full py-1" x-model="item.price_per_unit" required
                                            type="number" step="0.001" placeholder="Price" min="0" />
                                    </td>
                                    <td class="px-1">
                                        <x-text-input class="w-full py-1" x-model="item.expiration_date" required
                                            type="date" />
                                    </td>
                                    <td class="px-1 text-right">
                                        <span
                                            x-text="(item.quantity && item.price_per_unit) ? formatNumber(item.quantity * item.price_per_unit) : '0.000'"></span>
                                    </td>
                                    <td class="px-1">
                                        <button type="button" x-on:click="removeItem(index)" class=" text-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </table>
                    </div>
                    </template>
                </div>


                {{-- Footer Buttons --}}
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="closeModal">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <template x-if="isFormReadyToSubmit">

                        <x-primary-button x-bind:disabled="submitting" class="ms-3">
                            <span x-show="!submitting">{{ __('Save') }}</span>
                            <span x-show="submitting" class="animate-spin">⏳</span>
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
            suppliers: @json($this->suppliers),
            warehouses: @json($this->warehouses),
            items: @json($this->items),
            submitting: false,
            itemsSearch: '',

            form: @entangle('form'),

            get isFormReadyToSubmit() {

                if (!this.form.supplier_id || !this.form.warehouse_id) {
                    return false;
                }

                if (this.preparedItemsLength === 0) {
                    return false;
                }

                // Check if any item's quantity is empty or zero
                const hasEmptyQuantity = this.form.items.some(item => !item.quantity || !item.expiration_date || !
                    item.price_per_unit);
                if (hasEmptyQuantity) {
                    return false;
                }

                return true;
            },

            get filteredItems() {
                return this.itemsSearch ?
                    this.items.filter(item => item.name.toLowerCase().includes(this.itemsSearch
                        .toLowerCase())) :
                    this.items;
            },

            get preparedItemsLength() {
                const preparedItems = this.form.items.filter(item => item.quantity > 0).map(item => ({
                    item_id: item.item_id,
                    quantity: item.quantity,
                    expiration_date: item.expiration_date,
                    price_per_unit: item.price_per_unit,
                }));

                return preparedItems.length;
            },

            // Method to format numbers with thousand separators
            formatNumber(value) {
                return Number(value).toLocaleString(undefined, {
                    minimumFractionDigits: 3,
                    maximumFractionDigits: 3
                });
            },

            get grandTotal() {
                return this.form.items.reduce((total, item) => {
                    const itemTotal = item.quantity && item.price_per_unit ? item.quantity * item
                        .price_per_unit : 0;
                    return total + itemTotal;
                }, 0);
            },

            openModal() {
                this.resetForm();
                this.$dispatch('open-modal', 'form-modal'); // Open modal
            },

            closeModal() {
                this.resetForm();
                this.$dispatch('close-modal', 'form-modal'); // Dispatch the close event for the modal
            },

            resetForm() {
                this.form.items = [];
                this.submitting = false;
                this.form.supplier_id = '';
                this.form.warehouse_id = '';
                this.form.invoice_number = '';
                this.form.notes = '';
                this.form.invoice_date = null;
                this.itemsSearch = '';
            },

            selectItem(item) {
                const index = this.items.findIndex(i => i.id === item.id);
                if (index !== -1) {
                    this.form.items.push({
                        item_id: item.id,
                        name: item.name,
                        unit: item.unit,
                        expiration_date: '',
                        price_per_unit: '',
                        quantity: ''
                    });
                }
            },

            removeItem(index) {
                const removedItem = this.form.items.splice(index, 1)[0];
            },

            submit() {


                if (!this.isFormReadyToSubmit) {
                    alert('Please fill the missing fields');
                    return;
                }

                this.form.total_amount = this.grandTotal;

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
