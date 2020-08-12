<template>
    <div class="wrapper">
        <side-bar>
            <mobile-menu slot="content"></mobile-menu>

            <sidebar-link to="/pessoas/listar">
                <i class="fa fa-user-circle-o"></i>
                <p>Pessoas</p>
            </sidebar-link>

            <sidebar-link to="/produtos/listar">
                <i class="fa fa-archive"></i>
                <p>Produtos</p>
            </sidebar-link>

             <sidebar-link to="/produtos/listar">
                <i class="fa fa-money"></i>
                <p>Contas receber / pagar</p>
            </sidebar-link>
        </side-bar>

        <div class="main-panel">
            <top-navbar></top-navbar>

             <div class="card">
                <div class="card-header" v-if="$store.state.App.title != undefined">
                    <slot name="header">
                        <h4 class="card-title">{{$store.state.App.title}}</h4>
                        <p class="card-category" v-if="$store.state.App.subTitle != undefined">{{$store.state.App.subTitle}}</p>
                    </slot>
                </div>
                
                <div class="card-body">
                    <dashboard-content @click="toggleSidebar"></dashboard-content>
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright text-center">
                        &copy; Desenvolvido por Mauricio Treviso Gomes
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<script>
  import TopNavbar from './TopNavbar.vue'
  import DashboardContent from './Content.vue'
  import MobileMenu from './MobileMenu.vue'

  export default {
      name: "Dashboard",
      components: {
          TopNavbar,
          DashboardContent,
          MobileMenu,
      },
      methods: {
          toggleSidebar () {
              if (this.$sidebar.showSidebar) {
                  this.$sidebar.displaySidebar(false)
              }
          }
      }
  }

</script>
