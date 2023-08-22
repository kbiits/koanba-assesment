<script setup>
import { getFormattedPrice } from '@/Utils/Price';
import { ref } from 'vue';

const props = defineProps({
    price: Number | String,
    label: String,
});
const emit = defineEmits(['update:price'])


const newPrice = ref(props.price);
const parsePrice = (value) => {
    value = value.toString().trim().replace(/[,]*/g, '');
    if (!value.match(/^[0-9\.]*$/g)) {
        return Number(value.replace(/\D/g, ''));
    }

    let parsedPrice = Number(value)
    if (Number.isNaN(parsedPrice)) {
        parsedPrice = 0;
    }

    return parsedPrice;
}

const onUpdatePrice = (v) => {
    const parsedPrice = parsePrice(v);
    emit('update:price', parsedPrice);
    newPrice.value = parsedPrice;
}

</script>

<template>
    <v-text-field :model-value="getFormattedPrice(newPrice)" @update:model-value="onUpdatePrice" :label="label" type="text"
        prefix="Rp" validate-on="input lazy" :rules="[(v) => {
            const res = v.toString().match(/^[0-9\,\.]*$/g);
            if (!res) {
                return 'Number only';
            }

            return Boolean(res);
        }]"></v-text-field>
</template>
