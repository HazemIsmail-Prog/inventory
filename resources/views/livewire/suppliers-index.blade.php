<x-slot name="header">
    <h2 class=" flex items-center justify-between font-semibold text-xl text-gray-800 leading-tight">
        <div>{{ __('Suppliers') }}</div>
        <div id="new"></div>
    </h2>
</x-slot>

<div x-data="form()" class="py-12">

    {{-- New button --}}
    <template x-teleport="#new">
        <x-primary-button x-on:click.prevent="openModal()">{{ __('New Supplier') }}</x-primary-button>
    </template>

    {{-- Table --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 justify-start items-start gap-3">
                    @forelse ($this->suppliers as $supplier)
                        <x-collapsable-card>

                            <div class="flex w-full h-full items-center justify-between px-4 gap-4">


                                <div class="select-none">
                                    <div>{{ $supplier->name }}</div>
                                    @if ($supplier->phone)
                                        <div class="text-xs font-extralight flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-3">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                            </svg>
                                            <span>{{ $supplier->phone }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1"></div>

                                <button x-show="show" x-transition x-cloak type="button"
                                    x-on:click.prevent="openModal({{ $supplier }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>

                                </button>

                                <button x-show="show" x-transition x-cloak class="text-red-500"
                                    wire:confirm="Are you sure you want to delete this supplier?"
                                    wire:click="delete({{ $supplier->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>


                            </div>

                        </x-collapsable-card>
                    @empty
                        <h1 class=" col-span-full text-center font-bold text-xl">No Suppliers Found</h1>
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
            <div class="w-full">
                <label class="block" for="phone">Phone</label>
                <x-text-input class="w-full" x-model="form.phone" type="text" />

            </div>

            {{-- Footer Buttons --}}
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="closeModal">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <template x-if="isFormReadyToSubmit">

                    <x-primary-button x-bind:disabled="submitting" class="ms-3">
                        <span x-show="!submitting">{{ __('Save') }}</span>
                        <span x-show="submitting" class="animate-spin">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>

                        </span>
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

            openModal(item = null) {
                this.resetForm();
                if (item) {
                    this.form.id = item.id;
                    this.form.name = item.name;
                    this.form.phone = item.phone;
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
