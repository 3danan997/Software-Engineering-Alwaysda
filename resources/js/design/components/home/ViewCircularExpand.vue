<template>
  <v-row v-show="states.view_all" class="ma-0 circular-page-expand" :style="{backgroundColor: `${item_payload.backgroundColor} !important`, left: `${item_payload.x}px`, top: `${item_payload.y}px`}"  :class="{'circular-page-expand-active': states.view_expanded }">
      <!--<template v-if="states.view_expanded && item_payload.action === 'login' && authUser()">-->
      <!--  <v-col cols="12" class="align-self-center">-->
      <!--    -->
      <!--  </v-col>-->
      <!--</template>-->
  </v-row>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  computed: {
    ...mapGetters({
      authUser: 'authUser',
    }),
  },
  data() {
    return {
      item_payload: {
        x: 0,
        y: '100vh',
        backgroundColor: 'transparent',
        action: "default"
      },
      states: {
        view_all: true,
        view_expanded: false,
        params_received: false,
      },
    }
  },
  watch: {
    '$store.state.utils.transition.circular_page'(payload) {
      let app = this
      app.item_payload.action = payload.action
    },
    'item_payload.action'(newValue){
      let app = this
      if (newValue === "login") {
        /* Wir kehren zum Standard zurück, wenn der Übergang abgeschlossen ist */
        app.item_payload.x = 0
        app.item_payload.y = '100vh'
        /* VERALTET: Die Farbe wird aus .login-view-container in @app.scss übernommen */

        app.setBackground(newValue)
        app.changeAnimation(app.$store.state.utils.transition.circular_page)
      } else if (newValue === "logout") {
        /* Wir kehren zum Standard zurück, wenn der Übergang abgeschlossen ist */
        app.item_payload.x = 0
        app.item_payload.y = '100vh'
        /* VERALTET: Die Farbe wird aus .login-view-container in @app.scss übernommen */
        app.setBackground(newValue)
        app.changeAnimation(app.$store.state.utils.transition.circular_page)
      }
    }
  },
  created(){
    let app = this
    /* Dieser Ansatz sieht schick aus, aber es ist lahm, zu warten und viele Komponenten zu animieren */
    //app.$eventBus.$on('dchvce_params', (payload) => app.onParamsReceived(payload))
    addEventListener('resize', (event) => app.setBackground(null))

    /* Erste Erstellung: Wir erwarten eine Anmeldeausführung, jedoch basierend auf dem authUser-Status */
    app.setBackground()
  },

  beforeDestroy() {
    let app = this

    //app.$eventBus.$off('dchvce_params')
    removeEventListener('resize', app.setBackground())
  },
  methods: {
    setBackground(){
      let app = this

      app.item_payload.backgroundColor = "#272a37"
    },
    onParamsReceived(payload){
      let app = this

      app.states.params_received = true
      app.item_payload.x = payload.x
      app.item_payload.y = payload.y
      app.item_payload.action = payload.action

      setTimeout(function () {
        app.changeAnimation({status: true})
      }, 1000)
    },
    changeAnimation(payload) {
      let app = this
        app.states.view_expanded = payload.status

        if (app.states.view_expanded) {
          setTimeout(function() {
            app.states.view_all = false
            app.states.view_expanded = false
            setTimeout(function () {
              app.states.view_all = true
              // Wechseln Sie die Farben, nachdem der Übergang beendet ist
              app.setBackground(app.item_payload.action === "login" ? "logout" : "login")
            }, 1200);
          }, 1200);
        }

    }
  }
}
</script>

<style lang="scss">

.circular-page-expand {
  position: absolute;
  width: 0;
  height: 0;
  z-index: 12;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  -webkit-transition: all 1.1s;
  transition: all 1.1s;
}

.circular-page-expand-active {
  width: 300vw !important;
  height: 300vh !important;
}

</style>
