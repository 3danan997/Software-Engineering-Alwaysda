<template>
  <v-row class="ma-0" style="height: 100%">
    <v-col class="flex-grow-1 min-width-fit-content text-center align-self-center">
      <v-btn icon @click="changeView(0)"><v-icon class="text-color-level-1" :color="views_payload.active_page === 0 ? 'primary' : ''">mdi-bell-outline</v-icon></v-btn>
    </v-col>

    <v-col class="flex-grow-1 min-width-fit-content text-center align-self-center">
      <v-btn icon x-large @click="showCreateDialog()"><v-icon x-large>mdi-plus-circle-outline</v-icon></v-btn>
    </v-col>

    <v-col class="flex-grow-1 min-width-fit-content text-center align-self-center">
      <v-btn icon @click="changeView(1)"><v-icon class="text-color-level-1"  :color="views_payload.active_page === 1 ? 'primary' : ''" :class="{'view-navigation-bar-page-active': views_payload.active_page === 1}">mdi-view-dashboard</v-icon></v-btn>
    </v-col>
  </v-row>
</template>
<script>
export default {
  data() {
    return {
      views_payload: {
        active_page: 0,
      }
    }
  },
  created() {
    let app = this

    app.$eventBus.$emit("pageActive", app.views_payload.active_page)
  },
  methods: {
    showCreateDialog() {
      let app = this

      app.$eventBus.$emit("manageContact", {action: "create"})
    },
    changeView(active_page) {
      let app = this
      app.views_payload.active_page = active_page
      app.$eventBus.$emit("pageActive", active_page)
    }
  }
}
</script>