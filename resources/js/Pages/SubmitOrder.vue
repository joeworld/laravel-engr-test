<template>
    <GuestLayout>

        <Head title="Submit Order" />

        <div class="card-body">
            <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md">
                <div>Submit An Order</div>
                <h1 class="text-2xl font-semibold mb-6">Order Details</h1>

                <!-- Display success message -->
                <div v-if="usePage().props.flash.success_message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ usePage().props.flash.success_message }}
                </div>

                <!-- Display error message -->
                <div v-if="usePage().props.flash.error_message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ usePage().props.flash.error_message }}
                </div>

                <form @submit.prevent="submit" novalidate>
                    <div class="mb-4">
                        <label class="block text-gray-700" for="hmo_code">HMO Code</label>
                        <input v-model="form.code" type="text" id="hmo_code"
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-blue-300"
                            required />
                        <span v-if="form.errors.code" class="text-red-500 text-sm">{{ form.errors.code }}</span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700" for="provider_name">Provider Name</label>
                        <input v-model="form.provider_name" type="text" id="provider_name"
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-blue-300"
                            required />
                        <span v-if="form.errors.provider_name" class="text-red-500 text-sm">{{ form.errors.provider_name
                            }}</span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700" for="encounter_date">Encounter Date</label>
                        <input v-model="form.encounter_date" type="date" id="encounter_date"
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-blue-300"
                            required />
                        <span v-if="form.errors.encounter_date" class="text-red-500 text-sm">{{
                            form.errors.encounter_date }}</span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Order Items</label>
                        <div v-for="(item, index) in form.items" :key="index" class="flex mb-4 border-b pb-4">
                            <input v-model="item.name" type="text" placeholder="Item Name"
                                class="block w-full border border-gray-300 rounded-md p-2 mr-2 focus:outline-none focus:ring focus:ring-blue-300"
                                required />
                            <input v-model.number="item.unit_price" type="number" placeholder="Unit Price"
                                class="block w-1/4 border border-gray-300 rounded-md p-2 mr-2 focus:outline-none focus:ring focus:ring-blue-300"
                                step="0.01" required />
                            <input v-model.number="item.quantity" type="number" placeholder="Quantity"
                                class="block w-1/4 border border-gray-300 rounded-md p-2 mr-2 focus:outline-none focus:ring focus:ring-blue-300"
                                required />
                            <button type="button" @click="removeItem(index)"
                                class="bg-red-500 text-white rounded-md px-4 py-2 hover:bg-red-600">
                                Remove
                            </button>
                        </div>
                        <button type="button" @click="addItem"
                            class="bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600 mb-4">
                            Add Item
                        </button>
                        <span v-if="form.errors.items" class="text-red-500 text-sm">{{ form.errors.items }}</span>
                    </div>

                    <div class="mb-4 -mt-10 w-1/2 float-right">
                        <label class="block text-gray-700" for="total_amount">Total Amount</label>
                        <input v-model="totalAmount" type="text" id="total_amount"
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-100" readonly />
                    </div>

                    <button type="submit" class="bg-green-500 text-white rounded-md px-4 py-2 mt-12 hover:bg-green-600">
                        Submit Order
                    </button>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { Head, useForm, usePage } from '@inertiajs/vue3';
    import GuestLayout from '@/Layouts/GuestLayout.vue';

    // Using useForm to handle form submissions
    const form = useForm({
        provider_name: '',
        code: '',
        encounter_date: '',
        items: [{ name: '', unit_price: null, quantity: null }],
    });

    // Computed property for total amount calculation
    const totalAmount = computed(() => {
        return form.items.reduce((total, item) => {
            const itemTotal = (item.unit_price || 0) * (item.quantity || 0);
            return total + itemTotal;
        }, 0).toFixed(2); // Ensuring two decimal places
    });

    // Method to add an item
    const addItem = () => {
        form.items.push({ name: '', unit_price: null, quantity: null });
    };

    // Method to remove an item by index
    const removeItem = (index) => {
        form.items.splice(index, 1);
    };

    const submit = () => {
        form.post(route('orders.store'), {
            onSuccess: () => {
                form.reset();
            },
            onError: (errors) => {
                console.log(errors);
            },
        });
    };
</script>

<style scoped>
/* Add any additional styles here */
</style>