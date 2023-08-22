<script setup>
import Guest from '@/Layouts/Guest.vue';
import { getFormattedPrice } from '@/Utils/Price';
import { usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const { props } = usePage();

</script>

<template>
    <Guest>
        <v-container class="d-flex justify-center">
            <v-card max-width="500px" width="100%">
                <v-card-title>Customer Detail</v-card-title>
                <v-card-text class="mt-4">
                    <!-- name -->
                    <v-row style="padding: 12px;">
                        <v-col style="padding: 0;" cols="12">Customer Name:</v-col>
                        <v-col class="d-flex justify-end" style="padding: 0;" align-self="end" cols="12">
                            <v-text-field density="compact" variant="filled" readonly
                                :model-value="props.customer.customerName"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row style="padding: 12px;">
                        <v-col style="padding: 0;" cols="12">Customer Phone:</v-col>
                        <v-col class="d-flex justify-end" style="padding: 0;" align-self="end" cols="12">
                            <v-text-field density="compact" variant="filled" readonly
                                :model-value="props.customer.phone"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row style="padding: 12px;">
                        <v-col style="padding: 0;" cols="12">Customer Address:</v-col>
                        <v-col class="d-flex justify-end" style="padding: 0;" align-self="end" cols="12">
                            <v-textarea :auto-grow="false" :no-resize="true" density="compact" variant="filled" readonly
                                :model-value="props.customer.address"></v-textarea>
                        </v-col>
                    </v-row>

                </v-card-text>
            </v-card>
        </v-container>
    </Guest>
</template>

<style scoped></style>
