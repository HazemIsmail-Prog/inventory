    {{-- New button --}}
    <template x-teleport="#new">
        <x-primary-button x-on:click.prevent="openModal()">{{ __('New Warehouse') }}</x-primary-button>
    </template>

    {{-- Modal --}}
    <x-modal x-data="warehouseForm()" focusable dismissible maxWidth="lg" name="warehouse-form">
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

    <script>
        function warehouseForm() {
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
                    this.$dispatch('open-modal', 'warehouse-form');
                },

                closeModal() {
                    this.$dispatch('close-modal', 'warehouse-form'); // Dispatch the close event for the modal
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
