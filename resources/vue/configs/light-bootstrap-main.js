import Notifications from '../components/NotificationPlugin'
import SideBar from '../components/SidebarPlugin'

import 'bootstrap/dist/css/bootstrap.css'
import '../../assets/sass/light-bootstrap-dashboard.scss'
import '../../assets/css/demo.css'

import BaseDropdown from '../components/BaseDropdown.vue'
const GlobalComponents = {
    install (Vue) {
        Vue.component(BaseDropdown.name, BaseDropdown)
    }
}

import clickOutside from '../directives/click-ouside.js';
const GlobalDirectives = {
  install (Vue) {
    Vue.directive('click-outside', clickOutside);
  }
}

export default {
  install (Vue) {
    Vue.use(GlobalComponents)
    Vue.use(GlobalDirectives)
    Vue.use(SideBar)
    Vue.use(Notifications)
  }
}
