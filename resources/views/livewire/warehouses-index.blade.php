<x-slot name="header">
    <h2 class=" flex items-center justify-between font-semibold text-xl text-gray-800 leading-tight">
        <div>{{ __('Warehouses') }}</div>
        <div id="new"></div>
    </h2>
</x-slot>

<div x-data="form()" class="py-12">

    {{-- New button --}}
    <template x-teleport="#new">
        <x-primary-button x-on:click.prevent="openModal()">{{ __('New Warehouse') }}</x-primary-button>
    </template>

    {{-- Table --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 justify-start items-start gap-3">
                    @forelse ($this->warehouses as $warehouse)

                        <x-collapsable-card>

                            <div class="flex w-full h-full items-center justify-between px-4 gap-4">


                                <div class=" select-none flex-1">{{ $warehouse->name }}</div>

                                <div class="flex-1"></div>


                                <button x-show="show" x-transition x-cloak type="button"
                                    x-on:click.prevent="openModal({{ $warehouse }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>

                                </button>

                                <button x-show="show" x-transition x-cloak class="text-red-500"
                                    wire:confirm="Are you sure you want to delete this warehouse?"
                                    wire:click="delete({{ $warehouse->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>


                            </div>

                            <x-slot name="childList">
                                <div class=" w-full flex flex-col divide-y bg-gray-100">

                                    @forelse ($warehouse->available_items as $item)
                                        <div class="flex items-center justify-between py-2 px-4">
                                            <div>
                                                <div class="font-bold">{{ $item->name }}</div>
                                                <div class="text-xs text-red-500">
                                                    {{ $item->expiration_date ? \Carbon\Carbon::parse($item->expiration_date)->format('d-m-Y') : 'non expired' }}

                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="font-bold text-green-500">{{ $item->net_quantity }}</div>
                                                <div class="text-xs">{{ $item->unit }}</div>
                                            </div>
                                        </div>

                                    @empty
                                        <div class="flex items-center justify-center py-2 px-4">No Items</div>
                                    @endforelse

                                </div>
                            </x-slot>
                        </x-collapsable-card>

                    @empty

                        <h1 class=" col-span-full text-center font-bold text-xl">No Warehouses Found</h1>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <x-modal focusable dismissible maxWidth="lg" name="form-modal">
        <form x-on:submit.prevent="submit" class="p-6 flex flex-col gap-3">

            <div class="w-full">
                <label class="block" for="name">Name</label>
                <x-text-input class="w-full" required x-model="form.name" type="text" />
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

<script>
    function form() {
        return {
            submitting: false,

            form: @entangle('form'),

            get isFormReadyToSubmit() {
                if (!this.form.name) {
                    return false;
                }
                return true;
            },

            openModal(warehouse = null) {
                this.resetForm();
                if (warehouse) {
                    this.form.id = warehouse.id;
                    this.form.name = warehouse.name;
                }
                this.$dispatch('open-modal', 'form-modal');
            },

            closeModal() {
                this.$dispatch('close-modal', 'form-modal'); // Dispatch the close event for the modal
            },

            resetForm() {
                this.form = @json($this->form);
                this.submitting = false;
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
