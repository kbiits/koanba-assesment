<script setup>
import Guest from '@/Layouts/Guest.vue';
import { Inertia } from '@inertiajs/inertia';
import { nextTick, watch } from 'vue';
import { computed, ref } from 'vue';
import { getFormattedPrice } from '@/Utils/Price'
import { VDataTableServer } from 'vuetify/labs/VDataTable';
import { usePage } from '@inertiajs/inertia-vue3';

const { props } = usePage();
const productsWithIndex = computed(() => props.value?.products.map((p, i) => ({ ...p, index: ((props.value.currentPage - 1) * props.value.perPage) + i })))

/** @type {DeepReadonly<DataTableHeader[]>} */
const tableHeaders = ref([
    {
        key: 'no', align: 'start', title: 'No', value: (record, fallback) => {
            return record.index + 1;
        }
    },
    { key: 'productName', align: 'start', title: 'Product Name', sortable: false, },
    {
        key: 'productPrice', align: 'start', title: 'Product Price', sortable: false, value: (record) => {
            return `Rp ${getFormattedPrice(record.productPrice)}`
        }
    },
    { key: 'stock', align: 'start', title: 'Stock', sortable: false, },
    { key: 'productDescription', align: 'start', title: 'Product Description', sortable: false, },
    { title: 'Actions', key: 'actions', align: 'center', sortable: false, width: '10%' },
]);
const dialog = ref(false);
const dialogDelete = ref(false);

const currentItemsPerPage = ref(props.value.perPage);
const currentPage = ref(props.value.currentPage);

const defaultProductItem = {
    'productName': '',
    'productPrice': 0,
    'stock': 0,
    'productDescription': '',
}
const validateEmptyProps = ['productName', 'productDescription'];

let editedIndex = ref(-1);
let editedItem = ref({ ...defaultProductItem });
let loading = ref(false);
let isFirstLoad = ref(false);

const formTitle = computed(() => {
    return editedIndex.value === -1 ? 'Add New Product' : 'Edit Product'
})

const editItem = (item) => {
    editedIndex.value = item.index;
    editedItem.value = { ...item };
    dialog.value = true;
}

const onUpdateOptions = function ({ page, itemsPerPage, search }) {
    if (!isFirstLoad.value) {
        isFirstLoad.value = true;
        return;
    }

    loading.value = true;
    currentPage.value = page;
    Inertia.visit(route('product'), {
        data: {
            page,
            itemsPerPage,
            search,
        },
        onFinish: () => {
            loading.value = false;
        }
    })
}

const close = () => {
    dialog.value = false
    nextTick(() => {
        editedItem.value = { ...defaultProductItem };
        editedIndex.value = -1
    })
};

const deleteItem = (item) => {
    editedIndex.value = item.index;
    editedItem.value = { ...item };
    dialogDelete.value = true

}

const deleteItemConfirm = () => {
    const product = editedItem.value.productId;
    Inertia.delete(route('product.delete', {
        product,
    }), {
        data: {
            redirectUrl: window.location.toString(),
        }
    })
    closeDelete();
}

const closeDelete = () => {
    dialogDelete.value = false
    nextTick(() => {
        editedItem.value = { ...defaultProductItem };
        editedIndex.value = -1
    })
};

const save = () => {
    const payloadEdit = editedItem.value;
    if (!payloadEdit.productId) {
        // do create
        Inertia.post(route('product.create'), {
            ...payloadEdit,
            redirectUrl: window.location.toString(),
        });
    } else {
        Inertia.put(route('product.update', {
            product: payloadEdit.productId,
            redirectUrl: window.location.toString(),
        }), payloadEdit, {
            onFinish: () => { }
        })
    }

    close()
};

watch(dialog, async (newVal, old) => {
    if (old === true && newVal === false) {
        editedItem.value = { ...defaultProductItem }
        editedIndex.value = -1
    }
})

watch(dialogDelete, async (newVal, old) => {
    if (old === true && newVal === false) {
        editedItem.value = { ...defaultProductItem }
        editedIndex.value = -1
    }
})


</script>

<template>
    <Guest>
        <v-container>
            <v-data-table-server @update:options="onUpdateOptions" :headers="tableHeaders" :items="productsWithIndex || []"
                :items-length="props.total" class="elevation-1" v-model:items-per-page="currentItemsPerPage"
                item-value="productId" :loading="loading"
                :items-per-page-options="[{ title: '5', value: 5 }, { title: '10', value: 10 }, { title: '20', value: 20 }]"
                v-model:page="currentPage" :items-per-page="itemsPerPage" :must-sort="false">

                <template v-slot:top>
                    <v-toolbar flat>
                        <v-toolbar-title>Products</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{ props }">
                                <v-btn color="primary" dark class="mb-2" v-bind="props">
                                    New Item
                                </v-btn>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="text-h5">{{ formTitle }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="6">
                                                <v-text-field v-model="editedItem.productName"
                                                    label="Product name"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6">
                                                <price-input v-model:price="editedItem.productPrice"
                                                    label="Product Price" />
                                            </v-col>
                                            <v-col cols="12" sm="6">
                                                <v-text-field type="number" v-model="editedItem.stock"
                                                    label="Stock"></v-text-field>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-textarea v-model="editedItem.productDescription"
                                                    label="Product Description"></v-textarea>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue-darken-1" variant="text" @click="close">
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        :disabled="validateEmptyProps.filter(prop => !Boolean(editedItem[prop])).length > 0"
                                        color="blue-darken-1" variant="text" @click="save">
                                        Save
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                        <v-dialog v-model="dialogDelete" max-width="500px">
                            <v-card>
                                <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Cancel</v-btn>
                                    <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">OK</v-btn>
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                        <v-dialog v-model="dialogDelete" max-width="500px">
                            <v-card>
                                <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Cancel</v-btn>
                                    <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">OK</v-btn>
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>


                <template v-slot:item.actions="{ item }">
                    <v-row :dense="true" :style="{
                        paddingTop: '8px',
                        paddingBottom: '8px',
                    }" justify="space-between" align="center" align-content="space-between">
                        <v-col cols="12" :style="{
                            padding: 0,
                        }" sm="12" md="12" lg="4">
                            <v-icon size="small" @click="Inertia.visit(route('product.show', {
                                productId: item.raw.productId,
                            }))">
                                mdi-eye
                            </v-icon>
                        </v-col>
                        <v-col cols="12" :style="{
                            padding: 0,
                        }" sm="12" md="12" lg="4">
                            <v-icon size="small" @click="editItem(item.raw)">
                                mdi-pencil
                            </v-icon>
                        </v-col>
                        <v-col cols="12" :style="{
                            padding: 0,
                        }" sm="12" md="12" lg="4">
                            <v-icon size="small" @click="deleteItem(item.raw)">
                                mdi-delete
                            </v-icon>
                        </v-col>
                    </v-row>
                </template>

                <template v-slot:no-data>
                    <v-chip>No data</v-chip>
                </template>
            </v-data-table-server>
        </v-container>
    </Guest>
</template>

<style scoped></style>
