<script setup>
import { Inertia } from '@inertiajs/inertia';

if (route().current('home')) {
    Inertia.visit(route('product'));
}

const tabs = [{
    name: "Product",
    icon: "mdi-shopping",
    routeName: 'product',
}, {
    name: "Customer",
    icon: "mdi-account",
    routeName: 'customer',
}, {
    name: "Order",
    icon: "mdi-cart",
    routeName: 'order',
}]

function isTabActive(tab) {
    return route().current(tab);
}


</script>

<template>
    <v-layout class="rounded">
        <v-app-bar :elevation="2">
            <v-app-bar-title>Order App</v-app-bar-title>
        </v-app-bar>

        <v-main>
            <slot />
        </v-main>

        <v-bottom-navigation>
            <v-btn v-for="tab in tabs" :value="tab.name" @click="Inertia.visit(route(tab.routeName))"
                :active="isTabActive(tab.routeName)">
                <v-icon>{{ tab.icon }}</v-icon>
                <span>{{ tab.name }}</span>
            </v-btn>
        </v-bottom-navigation>
    </v-layout>
</template>
