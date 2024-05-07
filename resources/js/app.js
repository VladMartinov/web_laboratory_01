import './bootstrap';
import { createApp } from 'vue';
import router from "./router/router";
import store from "./store/index";

const app = createApp({});

import NavBarComponent from "@/components/NavBar.vue";

import ActionsBarComponent from "@/components/ActionsBar.vue";
import ItemsListComponent from "@/components/ItemsList.vue";

import BaseModalComponent from "@/components/BaseModal.vue";

app.component('NavBar', NavBarComponent);

app.component('ActionsBar', ActionsBarComponent);
app.component('ItemsList', ItemsListComponent);

app.component('BaseModal', BaseModalComponent);

app.config.globalProperties.$axios = axios;

app.use(router).use(store).mount('#app');
