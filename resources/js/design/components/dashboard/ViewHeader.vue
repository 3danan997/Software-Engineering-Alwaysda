<template>
  <v-row class="ma-0 view-header" :class="{'view-header-expanded': states.dashboard_expanded && $vuetify.breakpoint.lgAndUp}">
    <v-col class="flex-grow-0 min-width-fit-content align-self-center pl-3">
      <v-btn icon @click="changeDashboardState()"  v-show="visibility.view_hamburger" :class="{'fade-in': visibility.view_hamburger}">
        <v-icon class="view-header-hamburger-icon text-color-level-0"  size="28">
          mdi-menu
        </v-icon>
      </v-btn>
    </v-col>

    <v-col class="flex-grow-1 min-width-fit-content align-self-center pl-2">
      <p class="font-weight-semibold text-font-header pb-0 mb-0 text-color-level-0" v-show="visibility.view_title" :class="{'fade-in': visibility.view_title}">
        <span>Homepage</span>
      </p>
    </v-col>
  </v-row>
</template>
<script>
export default {
  data() {
    return {
      visibility: {
        view_hamburger: false,
        view_title: false,
      },
      states: {
        dashboard_expanded: true,
      }
    }
  },
  created() {
    let app = this
    app.animate()
  },
  mounted() {
    let app = this

    app.$eventBus.$on("dashboardState", (state) => {
      app.states.dashboard_expanded = state
    })
  },
  beforeDestroy() {
    let app = this

    app.$eventBus.$off("dashboardState")
  },
  methods: {
    changeDashboardState() {
      let app = this

      app.$eventBus.$emit("dashboardStateChange")
    },
    animate() {
      let app = this

      setTimeout(() => {
        app.visibility.view_hamburger = !app.visibility.view_hamburger
      }, 150);

      setTimeout(() => {
        app.visibility.view_title = !app.visibility.view_title
      }, 150);

    }
  }
}
</script>
<style lang="scss">

.view-header {
  top: 10px;
  position: fixed;
  left: 10px;
  transition: left 0.2s ease-in-out;
}

.view-header-expanded {
  left: 170px !important;
  transition: left 0.2s ease-in-out;
}


</style>