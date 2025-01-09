<template>
  <v-row class="ma-0">
    <v-col class="flex-grow-0 min-width-fit-content align-self-center pr-1">

      <v-btn icon @click="changeDashboardState()" size="20" v-if="sideBar() === 'left_sidebar'">
        <v-icon class="view-header-hamburger-icon text-color-level-1" size="20">
          mdi-menu
        </v-icon>
      </v-btn>

      <v-avatar size="35" class="clickable" color="grey" style="margin: auto">
        <v-img v-if="authUser() && authUser().avatar"
               :src="`${$constants.PATHS.url + $constants.PATHS.avatars + authUser().avatar}`" style="margin: auto"></v-img>
      </v-avatar>
    </v-col>

    <v-col class="flex-grow-1 min-width-fit-content align-self-center pr-1">
      <p class="font-weight-bold text-font-body-less pb-0 mb-0 text-truncate text-color-level-0">
        {{ authUser() ? authUser().first_name : "-" }}</p>
      <p class="font-weight-medium text-font-caption-more pb-0 mb-0 mt-1 text-truncate text-color-level-2">
        {{ authUser() ? authUser().email : "-" }}</p>
    </v-col>

    <v-col class="flex-grow-0 min-width-fit-content align-self-center">
      <v-btn @click="openProfile" icon><v-icon class="text-color-level-2">mdi-cog</v-icon></v-btn>
    </v-col>
  </v-row>
</template>
<script>
import {mapGetters} from "vuex";

export default {
  computed: {
    ...mapGetters({
      authUser: 'authUser',
      sideBar: 'sideBar',
    }),
  },
  data(){
    return {

    }
  },
  created(){
    let app = this
  },
  beforeDestroy() {
    let app = this
  },
  methods: {
    changeDashboardState() {
      let app = this

      app.$eventBus.$emit("dashboardStateChange")
    },
    openProfile() {
      let app = this

      app.$eventBus.$emit("openProfileDialog")
    }
  }
}
</script>
<style lang="scss">

</style>