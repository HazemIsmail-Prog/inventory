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
                <div x-data="{ showTranscationItems: null }" class="grid grid-cols-3 justify-start items-start gap-3">
                    @forelse ($this->warehouses as $warehouse)
                        <div class="flex flex-col items-center border rounded-lg p-3 shadow-md"
                            x-on:mouseenter="showTranscationItems = {{ $warehouse->id }}"
                            x-on:mouseleave="showTranscationItems = null">
                            <div class="flex w-full items-center justify-between gap-3">
                                <div>{{ $warehouse->name }}</div>
                                <div class="border-s-2 ps-3 text-center">
                                    <button class="text-red-500"
                                        wire:confirm="Are you sure you want to delete this warehouse?"
                                        wire:click="delete({{ $warehouse->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                    <button type="button"
                                        x-on:click.prevent="openModal({{ $warehouse }})">Edit</button>
                                    <div class=" cursor-pointer select-none"
                                        x-on:click="showTranscationItems != {{ $warehouse->id }} ? showTranscationItems = {{ $warehouse->id }} : showTranscationItems = null">
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

                            <div x-cloak x-show="showTranscationItems === {{ $warehouse->id }}" x-transition
                                class="mt-3 w-full border-t-2 flex flex-col divide-y">

                                @forelse ($warehouse->available_items as $item)
                                    <div class="flex items-center justify-between py-3">
                                        <div>
                                            <div class="font-bold">{{ $item->name }}</div>
                                            <div class="text-xs">{{ $item->expiration_date }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="font-bold">{{ $item->net_quantity }}</div>
                                            <div class="text-xs">{{ $item->unit }}</div>
                                        </div>
                                    </div>

                                @empty
                                    <div class=" pt-3 text-center">No Items</div>
                                @endforelse

                            </div>
                        </div>
                    @empty

                        <h1 class=" col-span-full text-center font-bold text-xl">No Warehouses Yet</h1>
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
                this.resetForm();
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
