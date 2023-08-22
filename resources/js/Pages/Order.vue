<script setup>
import Guest from '@/Layouts/Guest.vue';
import { Inertia } from '@inertiajs/inertia';
import { nextTick } from 'vue';
import { computed, ref } from 'vue';
import { getFormattedPrice } from '@/Utils/Price'
import { VDataTableServer } from 'vuetify/labs/VDataTable';
import { usePage } from '@inertiajs/inertia-vue3';
import { watch } from 'vue';


const { props } = usePage();
const ordersWithIndex = computed(() => props.value?.orders.map((p, i) => ({ ...p, index: ((props.value.currentPage - 1) * props.value.perPage) + i })))

/** @type {DeepReadonly<DataTableHeader[]>} */
const tableHeaders = ref([
    {
        key: 'no', align: 'start', title: 'No', value: (record, fallback) => {
            return record.index + 1;
        }
    },
    { key: 'customerName', align: 'start', title: 'Customer Name', sortable: false, },
    { key: 'productName', align: 'start', title: 'Product Name', sortable: false, },
    {
        key: 'amount', align: 'start', title: 'Order Amount', sortable: false, value: (record) => {
            return `Rp ${getFormattedPrice(record.amount)}`
        }
    },
    { key: 'quality', align: 'start', title: 'Quality', sortable: false, },
    {
        key: 'orderDate', align: 'start', title: 'Order Date', sortable: false, value: (record) => {
            const date = (new Date(record.orderDate));
            return new Intl.DateTimeFormat('id-ID', {
                dateStyle: 'long',
                timeStyle: 'long',

            }).format(date)
        }
    },
    { title: 'Actions', key: 'actions', align: 'center', sortable: false, width: '10%' },
]);
const dialog = ref(false);
const dialogDelete = ref(false);

const currentItemsPerPage = ref(props.value.perPage);
const currentPage = ref(props.value.currentPage);

const defaultOrderItem = {
    'customerId': '',
    'customerName': '',
    'amount': 0,
    'productId': '',
    'quality': 0,
    'orderDate': new Date().toISOString(),
}
const validateEmptyProps = ['customerId', 'productId', 'orderDate'];

let editedIndex = ref(-1);
let editedItem = ref({ ...defaultOrderItem });
let loading = ref(false);
let isFirstLoad = ref(false);

const formTitle = computed(() => {
    return editedIndex.value === -1 ? 'Add New Order' : 'Edit Order'
})

const editItem = (item) => {
    editedIndex.value = item.index;
    editedItem.value = { ...item, orderDate: new Date(item.orderDate).toISOString() };
    dialog.value = true;
}

const onUpdateOptions = function ({ page, itemsPerPage, search }) {
    if (!isFirstLoad.value) {
        isFirstLoad.value = true;
        return;
    }

    loading.value = true;
    currentPage.value = page;
    Inertia.visit(route('order'), {
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
        editedItem.value = { ...defaultOrderItem };
        editedIndex.value = -1
    })
};

const deleteItem = (item) => {
    editedIndex.value = item.index;
    editedItem.value = { ...item };
    dialogDelete.value = true
}

const deleteItemConfirm = () => {
    const order = editedItem.value.orderId;
    Inertia.delete(route('order.delete', {
        order,
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
        editedItem.value = { ...defaultOrderItem };
        editedIndex.value = -1
    })
};

const save = () => {
    const payloadEdit = editedItem.value;
    if (!payloadEdit.orderId) {
        // do create
        Inertia.post(route('order.create'), {
            ...payloadEdit,
            redirectUrl: window.location.toString(),
        });
    } else {
        Inertia.put(route('order.update', {
            order: payloadEdit.orderId,
            redirectUrl: window.location.toString(),
        }), payloadEdit, {
            onFinish: () => { }
        })
    }

    close()
};

const customers = ref({
    lastId: '',
    customers: [...ordersWithIndex.value.map(order => ({ customerId: order.customerId, customerName: order.customerName }))],
});
const searchCustomer = ref('');
const customerLoading = ref(false);

const endIntersectCustomer = async (isIntersecting, entries, observer) => {
    customerLoading.value = 'blue';
    if (isIntersecting) {
        const rawResp = await fetch(`${route('customer.loadByCursor')}?cursor=${customers.value.lastId || 0}&search=${searchCustomer.value}`)
        const resp = await rawResp.json();

        if (resp.customers?.length > 0) {
            customers.value = {
                customers: [...customers.value.customers, ...resp.customers],
                lastId: resp.lastId,
            };
        }
    }
    customerLoading.value = false;
}

const onSelectCustomer = (customerId) => {
    editedItem.value.customerId = customerId;
}

const products = ref({
    lastId: '',
    products: [...ordersWithIndex.value.map(order => ({ productId: order.productId, productName: order.productName }))],
});
const productLoading = ref(false);
const searchProduct = ref('');
const onSelectProduct = (productId) => {
    editedItem.value.productId = productId;
}

const endIntersectProduct = async (isIntersecting, entries, observer) => {
    productLoading.value = 'blue';
    if (isIntersecting) {
        const rawResp = await fetch(`${route('product.loadByCursor')}?cursor=${products.value.lastId || 0}&search=${searchProduct.value}`)
        const resp = await rawResp.json();

        if (resp.products?.length > 0) {
            products.value = {
                products: [...products.value.products, ...resp.products],
                lastId: resp.lastId,
            };
        }
    }
    productLoading.value = false;
}

const onChangeOrderDate = (date) => {
    editedItem.value = {
        ...editedItem.value,
        orderDate: date?.toISOString() || null,
    }
}

watch(dialog, async (newVal, old) => {
    if (old === true && newVal === false) {
        editedItem.value = { ...defaultOrderItem }
        editedIndex.value = -1
    }
})

watch(dialogDelete, async (newVal, old) => {
    if (old === true && newVal === false) {
        editedItem.value = { ...defaultOrderItem }
        editedIndex.value = -1
    }
})

</script>

<template>
    <Guest>
        <v-container>
            <v-alert v-for="err in Object.values(props.errors)" v-if="Object.keys(props.errors).length > 0" type="error"
                title="Error" :text="err"></v-alert>

            <v-alert v-if="props.saveError" type="error" title="Error" :text="props.saveError"></v-alert>

            <v-data-table-server @update:options="onUpdateOptions" :headers="tableHeaders" :items="ordersWithIndex || []"
                :items-length="props.total" class="elevation-1" v-model:items-per-page="currentItemsPerPage"
                item-value="productId" :loading="loading"
                :items-per-page-options="[{ title: '5', value: 5 }, { title: '10', value: 10 }, { title: '20', value: 20 }]"
                v-model:page="currentPage" :items-per-page="itemsPerPage" :must-sort="false">

                <template v-slot:top>
                    <v-toolbar flat>
                        <v-toolbar-title>Orders</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{ props }">
                                <v-btn color="primary" dark class="mb-2" v-bind="props">
                                    Add new order
                                </v-btn>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="text-h5">{{ formTitle }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12">
                                                <v-autocomplete :loading="customerLoading" :items="customers.customers"
                                                    :model-value="editedItem.customerId"
                                                    @update:model-value="onSelectCustomer" item-text="customerName"
                                                    item-title="customerName" item-value="customerId"
                                                    label="Select a customer">
                                                    <template v-slot:append-item>
                                                        <div v-intersect="endIntersectCustomer" />
                                                    </template>
                                                </v-autocomplete>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-autocomplete :loading="productLoading" :items="products.products"
                                                    :model-value="editedItem.productId"
                                                    @update:model-value="onSelectProduct" item-text="productName"
                                                    item-title="productName" item-value="productId"
                                                    label="Select a product">
                                                    <template v-slot:append-item>
                                                        <div v-intersect="endIntersectProduct" />
                                                    </template>
                                                </v-autocomplete>
                                            </v-col>
                                            <v-col cols="12" md="6">
                                                <v-text-field type="number" label="Quality"
                                                    v-model="editedItem.quality"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" md="6">
                                                <price-input label="Amount" v-model:price="editedItem.amount" />
                                            </v-col>
                                            <v-col cols="12">
                                                <vue-date-picker :utc="false" :model-value="new Date(editedItem.orderDate)"
                                                    @update:model-value="onChangeOrderDate" placeholder="Order Date"
                                                    :maxDate="new Date()" nowButtonLabel="Set Current Time"
                                                    locale="id-ID" />
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue-darken-1" variant="text" @click="close">
                                        Cancel
                                    </v-btn>
                                    <v-btn :disabled="validateEmptyProps.filter(prop => !Boolean(editedItem[prop])).length > 0" color="blue-darken-1" variant="text" @click="save">
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
