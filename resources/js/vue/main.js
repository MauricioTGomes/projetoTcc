import '../bootstrap';
import Vue from 'vue'
import App from './layout/App.vue'
import VueRouter from 'vue-router'
import router from '../router'

Vue.use (VueRouter);

new Vue({
    router,
    el: '#app',
    render: h => h(App)
})
