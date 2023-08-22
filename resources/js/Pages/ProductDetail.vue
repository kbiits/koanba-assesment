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
                <v-card-title>Product Detail</v-card-title>
                <v-card-text class="mt-4">
                    <!-- name -->
                    <v-row style="padding: 12px;">
                        <v-col style="padding: 0;" cols="12">Product Name:</v-col>
                        <v-col class="d-flex justify-end"  style="padding: 0;" align-self="end" cols="12">
                            <v-text-field density="compact" variant="filled" readonly :model-value="props.product.productName"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row style="padding: 12px;">
                        <v-col style="padding: 0;" cols="12">Product Price:</v-col>
                        <v-col class="d-flex justify-end"  style="padding: 0;" align-self="end" cols="12">
                            <v-text-field density="compact" variant="filled" readonly :model-value="'Rp ' + getFormattedPrice(props.product.productPrice)"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row style="padding: 12px;">
                        <v-col style="padding: 0;" cols="12">Stock:</v-col>
                        <v-col class="d-flex justify-end"  style="padding: 0;" align-self="end" cols="12">
                            <v-text-field density="compact" variant="filled" readonly :model-value="props.product.stock"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row style="padding: 12px;">
                        <v-col style="padding: 0;" cols="12">Product Description:</v-col>
                        <v-col class="d-flex justify-end"  style="padding: 0;" align-self="end" cols="12">
                            <v-textarea :auto-grow="false" :no-resize="true" density="compact" variant="filled" readonly :model-value="props.product.productDescription"></v-textarea>
                        </v-col>
                    </v-row>

                </v-card-text>
            </v-card>
        </v-container>
    </Guest>
</template>

<style scoped></style>
