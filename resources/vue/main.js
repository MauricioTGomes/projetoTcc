window.$ = window.jQuery = require('jquery')
require("datatables.net");

import '../js/bootstrap';
import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'
import routes from './router'
import store from './store/index'
import LightBootstrap from './configs/light-bootstrap-main'
import './configs/registerServiceWorker'

import VueConfirmDialog from 'vue-confirm-dialog'
import VueTheMask from 'vue-the-mask'
import VueMoney from 'v-money';
import vSelect from "vue-select";
import axios from 'axios'

Vue.use(VueConfirmDialog)
Vue.component('vue-confirm-dialog', VueConfirmDialog.default)
Vue.component("v-select", vSelect);
Vue.use(VueMoney, {decimal: ',', thousands: '.', prefix: '', suffix: '', precision: 2});
Vue.use(VueTheMask);

const http = axios.create({
    baseURL: 'http://localhost:8000/'
})

Vue.http = Vue.prototype.$http = http;
Vue.use(LightBootstrap)
Vue.use (VueRouter);

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
    linkActiveClass: 'nav-item active',
    scrollBehavior: (to) => {
        if (to.hash) {
            return {selector: to.hash}
        } else {
            return { x: 0, y: 0 }
        }
    }
})

new Vue({
    router,
    store,
    el: '#app',
    render: h => h(App)
})
