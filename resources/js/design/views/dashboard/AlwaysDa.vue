<template>
  <v-row class="ma-0 align-content-start" style="height: 100%;">

    <v-col v-if="$vuetify.breakpoint.mdAndDown" class="view-navigation-bar pa-0 fade-in-up" cols="12" :class="{'view-navigation-bar-margin': bottomBar() && bottomBar() === 'bottom_margin', 'view-navigation-bar-full': bottomBar() && bottomBar() === 'bottom_full'}">
      <ViewNavigationBar></ViewNavigationBar>
    </v-col>

    <v-btn v-if="$vuetify.breakpoint.mdAndDown && bottomBar() && bottomBar() === 'no_bar'" color="primary" fab right bottom fixed @click="showCreateDialog()"><v-icon>mdi-plus-outline</v-icon></v-btn>

    <v-col v-if="$vuetify.breakpoint.mdAndDown" class="pb-0" cols="12">
      <ViewProfile></ViewProfile>
    </v-col>

    <v-col v-if="$vuetify.breakpoint.mdAndDown" class="py-0" cols="12">
      <v-divider></v-divider>
    </v-col>

    <!--  Mobile Version  -->
    <v-col v-if="$vuetify.breakpoint.mdAndDown" class="py-0 my-0" cols="12">
      <v-row class="ma-0">
        <v-col cols="12" class="ma-0 pa-0">
          <ViewStats></ViewStats>
        </v-col>

        <template v-if="views_payload.active_page === 1">
          <v-col cols="12" class="ma-0 pa-0">
            <ViewContacts></ViewContacts>
          </v-col>
        </template>

        <template v-if="views_payload.active_page === 0">
          <v-col cols="12" class="ma-0 pa-0">
            <ViewReminders></ViewReminders>
          </v-col>
        </template>
      </v-row>
    </v-col>

    <!--  Desktop Version  -->
    <v-col v-if="!$vuetify.breakpoint.mdAndDown" class="px-0 pb-0 my-0" cols="12" style="height: 100%;">
      <v-row class="ma-0" style="height: 100%;">

        <!-- linke Seite -->
        <v-col cols="12" lg="8" xl="9" class="ma-0 py-0">
          <v-row class="ma-0">
            <v-col cols="12" class="ma-0 pa-0">
              <ViewStats></ViewStats>
            </v-col>

            <v-col cols="12" class="ma-0 pa-0">
              <ViewContacts></ViewContacts>
            </v-col>

          </v-row>
        </v-col>

        <!-- rechte Seite -->
        <v-col cols="12" lg="4" xl="3" class="ma-0 py-0 border-left">
          <ViewReminders></ViewReminders>
        </v-col>
      </v-row>
    </v-col>

    <ViewManageContact></ViewManageContact>
    <ViewContactDeleteDialog></ViewContactDeleteDialog>
    <ViewUpdateProfileDialog></ViewUpdateProfileDialog>
    <notifications position="bottom right" style="margin-right: 30px; margin-bottom: 30px;"/>

  </v-row>


</template>
<script>
import ViewNavigationBar from "../../components/dashboard/ViewNavigationBar";
import ViewContactDeleteDialog from "../../components/contacts/ViewContactDeleteDialog";
import ViewProfile from "../../components/profile/ViewProfile";
import ViewUpdateProfileDialog from "../../components/profile/ViewUpdateProfileDialog";
import ViewContacts from "../../components/contacts/ViewContacts";
import ViewManageContact from "../../components/contacts/ViewManageContact";
import ViewReminders from "../../components/app/ViewReminders";
import ViewStats from "../../components/app/ViewStats";
import {mapGetters} from "vuex";

export default {
  computed: {
    ...mapGetters({
      sideBar: 'sideBar',
      bottomBar: 'bottomBar',
    }),
  },
  components: {
    ViewStats,
    ViewReminders,
    ViewManageContact, ViewContacts, ViewProfile, ViewNavigationBar, ViewContactDeleteDialog, ViewUpdateProfileDialog},
  data() {
    return {
      views_payload: {
        active_page: 0,
      }
    }
  },
  created() {
    let app = this

    app.$eventBus.$on("pageActive", (active_page) => {
      app.views_payload.active_page = active_page
    })
  },
  beforeDestroy() {
    let app = this

    app.$eventBus.$off("pageActive")
  },
  methods: {
    showCreateDialog() {
      let app = this
      app.$eventBus.$emit("manageContact", {action: "create"})
    }
  }
}
</script>
<style lang="scss">


</style>