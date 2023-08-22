require("./bootstrap");

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";

import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import PriceInput from "@/Components/PriceInput.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

const vuetify = createVuetify({
    components,
    directives,
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        const vueApp = createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(vuetify);
        vueApp.component("VueDatePicker", VueDatePicker);
        vueApp.component("PriceInput", PriceInput);
        return vueApp.mixin({ methods: { route } }).mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
