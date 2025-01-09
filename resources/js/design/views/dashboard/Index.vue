<template>
  <v-container class="pa-0 dashboard-container" fill-height fluid>

    <template v-if="$vuetify.breakpoint.lgAndUp || sideBar() === 'left_sidebar'">

      <v-navigation-drawer v-show="visibility.views" floating v-model="drawer_payload.state" :class="{'fade-in': visibility.view_dashboard}" color="#272a37" fixed left
                           :width="$vuetify.breakpoint.lgAndUp ? '140' : '100'">

        <v-row class="ma-0 align-content-space-between" style="height: 100%">

          <v-col cols="12" class="text-center align-self-center py-5 clickable">
            <v-img :src="$constants.PATHS.url + $constants.PATHS.brand + 'logo.png'" contain style="margin: auto"
                   :width="$vuetify.breakpoint.lgAndUp ? '42' : '36'" :height="$vuetify.breakpoint.lgAndUp ? '42' : '36'"></v-img>
          </v-col>

          <v-col cols="12" class="text-center align-self-center clickable py-5 dashboard-drawer-item-active">
            <v-icon color="primary" v-if="drawer_payload.item.meta.icon" style="margin: auto" :size="$vuetify.breakpoint.lgAndUp ? '38' : '32'">{{ drawer_payload.item.meta.icon }}</v-icon>
          </v-col>

          <v-col cols="12" class="text-center align-self-center py-5 clickable">
            <v-avatar :size="$vuetify.breakpoint.lgAndUp ? '42' : '36'" class="clickable" color="grey" @click="openProfile()" style="margin: auto">
              <template v-if="authUser()">
                <v-img v-if="authUser().avatar"
                       :src="`${$constants.PATHS.url + $constants.PATHS.avatars + authUser().avatar}`" style="margin: auto"></v-img>
                <template v-else>
                  <p class="text-font-caption-more font-weight-semibold mb-0">{{authUser().first_name ? authUser().first_name.charAt(0) : ""}}{{authUser().last_name ? authUser().last_name.charAt() : ""}}</p>
                </template>
              </template>
            </v-avatar>
          </v-col>
        </v-row>
      </v-navigation-drawer>

    <!--      <ViewHeader></ViewHeader>-->
    </template>

    <v-main class="drawer-view " :class="{'drawer-view-expanded': drawer_payload.state && $vuetify.breakpoint.lgAndUp}">
      <AlwaysDa></AlwaysDa>
    </v-main>
  </v-container>
</template>

<script>

import {mapGetters} from 'vuex';
import ViewHeader from "../../components/dashboard/ViewHeader";
import AlwaysDa from "./AlwaysDa";

export default {
  components: {AlwaysDa, ViewHeader},
  computed: {
    ...mapGetters({
      authUser: 'authUser',
      sideBar: 'sideBar'
    }),
  },
  data() {
    return {
      drawer_payload: {
        state: true,
        selected_item: null,
        item: {
          name: 'dashboard',
          path: '/dashboard/homepage',
          meta: {
            text: 'Dashboard',
            icon: 'mdi-view-dashboard-outline',
          }
        }
      },
      visibility: {
        views: false,
        view_dashboard: false,
        view_content: false,
      },
    }
  },
  watch: {
    "drawer_payload.state"(newVal) {
      let app = this
      app.$eventBus.$emit("dashboardState", newVal)
    }
  },
  created() {
    let app = this

    if(app.$vuetify.breakpoint.mdAndDown) {
      app.drawer_payload.state = false
    }

    app.animate()
  },
  mounted() {
    let app = this

    app.$eventBus.$emit("dashboardState", app.drawer_payload.state)
    app.$eventBus.$on("dashboardStateChange", () => {
      console.log("he")
      app.drawer_payload.state = !app.drawer_payload.state
    })
  },
  beforeDestroy() {
    let app = this

    app.$eventBus.$off("dashboardStateChange")
  },
  methods: {
    openProfile(){
      let app = this
      app.$eventBus.$emit("openProfileDialog")
    },
    animate() {
      let app = this

      setTimeout(() => {
        app.visibility.views = true
        app.visibility.view_dashboard = !app.visibility.view_dashboard
      }, 350)

      setTimeout(() => {
        app.visibility.view_content = !app.visibility.view_content
      }, 450)

      setTimeout(() => {
        app.visibility.view_dashboard = !app.visibility.view_dashboard
      }, 1500)
    }
  }
}
</script>

<style lang="scss">
@import '~vuetify/src/styles/settings/_variables';


</style>
