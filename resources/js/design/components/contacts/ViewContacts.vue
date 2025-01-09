<template>

  <v-row class="ma-0">

    <template v-if="$vuetify.breakpoint.mdAndDown">
      <v-col class="fade-in" cols="12">
        <v-text-field clearable v-model="item_payload.inputSearch" class="text-field fade-in" dense flat hide-details
                      label="Nach Kontakt suchen..." prepend-inner-icon="mdi-magnify" solo type='text'></v-text-field>
      </v-col>

      <v-col class="fade-in" cols="12">
        <v-chip-group v-model="item_payload.filters" column multiple show-arrows class="fade-in">
          <v-chip v-for="(item, index) in views_payload.filters" :key="index" filter outlined>{{ item }}</v-chip>
        </v-chip-group>
      </v-col>

    </template>

    <template v-else>
      <v-col cols="12" class="pa-0 ma-0">

        <v-row class="ma-0">

          <v-col class="flex-grow-1 min-width-fit-content align-self-center text-left">
            <p class="mb-0 font-weight-semibold text-font-title text-color-level-0 fade-in">Kontakte</p>
          </v-col>

          <v-col class="flex-grow-0  align-self-center text-right" >
            <v-select style="width: 165px !important;" dense prepend-inner-icon="mdi-heart-outline" multiple hide-details v-model="item_payload.filters" :items='views_payload.filters' label="Priorität" flat solo class="text-field fade-in"></v-select>
          </v-col>

          <v-col class="flex-grow-0 min-width-fit-content align-self-center text-right">
            <v-text-field  clearable dense v-model="item_payload.inputSearch" class="text-field fade-in" flat hide-details style="border: none !important;"
                          label="Nach Kontakt suchen..." prepend-inner-icon="mdi-magnify" solo type='text'></v-text-field>

          </v-col>
        </v-row>

      </v-col>
    </template>


    <v-col class="ma-0 pa-0" cols="12">
      <v-row class="ma-0 view-contacts-row" :class="{'pb-11 mb-11': !$vuetify.breakpoint.lgAndUp}">

        <template v-if="$vuetify.breakpoint.lgAndUp">

          <v-col cols="12" lg="3" xl="2">
            <v-card class="card card-radius card-shadow fade-in clickable fade-in" style="height: 100%" @click="showCreateDialog">
              <v-row class="ma-0 pa-2 align-center" style="height: 100%">
                <v-col cols="12" class="text-center">
                  <v-icon class="text-color-level-1">mdi-plus-circle-outline</v-icon>
                  <span class="font-weight-semibold text-font-caption-more ml-2 text-color-level-1">Kontakt Erstellen</span>
                </v-col>
              </v-row>
            </v-card>
          </v-col>

        </template>


        <v-col v-for="(item, index) in item_payload.contacts" :key="index" cols="12" lg="3" xl="2">

          <v-menu
              v-model="views_payload.card_menus[item.id]"
              :close-on-content-click="true" absolute
              max-width="150">
            <template v-slot:activator="{ on, attrs }">
              <v-card class="card card-radius card-shadow fade-in clickable" v-on="on">
                <v-row class="ma-0 pa-2">
                  <!-- Desktop Version-->
                  <template v-if="$vuetify.breakpoint.lgAndUp">
                    <v-col cols="12" class="align-self-center text-center">
                      <v-avatar class="clickable" color="grey" size="42" style="margin: auto">
                        <v-img v-if="item.avatar"
                               :src="`${$constants.PATHS.url + $constants.PATHS.avatars + item.avatar}`"
                               style="margin: auto"></v-img>
                      </v-avatar>
                    </v-col>

                    <v-col cols="12" class="align-self-center text-center pt-1">
                      <p class="font-weight-bold text-font-body-less pb-0 mb-0 text-truncate text-color-level-1">
                        {{ item.full_name }}</p>
                      <p class="font-weight-medium text-uppercase text-font-caption pb-0 mb-0 mt-2 text-truncate text-color-level-2">
                        {{ item.priority }}</p>
                    </v-col>

                    <v-col class="min-width-fit-content align-self-start" style="position:absolute; top:0; left:0">
                      <v-icon class="text-color-level-3" x-small>mdi-bell-outline</v-icon>
                      <span class="font-weight-bold text-font-caption-more pb-0 mb-0 text-truncate text-color-level-3">{{
                          item.notifications ? item.notifications.length : 0
                        }}</span>
                    </v-col>
                  </template>

                  <!-- Mobile Version-->
                  <template v-else>
                    <v-col class="flex-grow-0 min-width-fit-content align-self-center text-center">
                      <v-avatar class="clickable" color="grey" size="42" style="margin: auto">
                        <v-img v-if="item.avatar"
                               :src="`${$constants.PATHS.url + $constants.PATHS.avatars + item.avatar}`"
                               style="margin: auto"></v-img>
                      </v-avatar>
                    </v-col>

                    <v-col class="flex-grow-1 align-self-center text-left text-truncate">
                      <p class="font-weight-bold text-font-body-less pb-0 mb-0 text-truncate text-color-level-1">
                        {{ item.full_name }}</p>
                      <p class="font-weight-medium text-uppercase text-font-caption pb-0 mb-0 mt-2 text-truncate text-color-level-2">
                        {{ item.priority }}</p>
                    </v-col>

                    <v-col class="flex-grow-0 min-width-fit-content align-self-start">
                      <v-icon class="text-color-level-3" x-small>mdi-bell-outline</v-icon>
                      <span class="font-weight-bold text-font-caption-more pb-0 mb-0 text-truncate text-color-level-3">{{
                          item.notifications ? item.notifications.length : 0
                        }}</span>
                    </v-col>

                  </template>
                </v-row>
              </v-card>
            </template>
            <ViewCardMenu :bus="'contactCardMenu'" :items="views_payload.card_menu_items"
                          :menu="views_payload.card_menus[item.id]" :payload="item"></ViewCardMenu>
          </v-menu>

        </v-col>

      </v-row>
    </v-col>


  </v-row>

</template>
<script>
import ViewCardMenu from "../dashboard/ViewCardMenu";

export default {
  components: {ViewCardMenu},
  data() {
    return {
      item_payload: {
        inputSearch: null,
        filters: [],
        contacts: [],
      },
      views_payload: {
        card_menus: [],
        card_menu_items: [
          {
            title: "Bearbeiten",
            action: "update_item"
          },
          {
            divider: true
          },
          {
            title: "Löschen",
            action: "delete_item"
          }
        ],
        contacts: [],
        filters: [
          "Alle"
        ]
      }
    }
  },
  watch: {
    "item_payload.inputSearch"() {
      let app = this
      app.filterList()
    },
    "item_payload.filters"(newVal, oldVal) {
      let app = this

      if(app.$vuetify.breakpoint.lgAndUp) {
        if (newVal && newVal.includes("Alle") && newVal.length > 1 && oldVal && oldVal.length > 0) {
          /* Es muss Alle also andere abwählen */
          let intersection = newVal.filter(x => oldVal.includes(x));

          if (intersection.includes("Alle")) {
            /* Wenn nicht, entfernen Sie Alle aus der Liste*/
            app.item_payload.filters.splice(app.item_payload.filters.findIndex(e => e === "Alle"), 1)
          } else {
            /* Prüfen Sie, ob der neue Wert Alle ist, wenn ja, dann abwählen */
            app.item_payload.filters = ["Alle"]
          }
        }
      } else {
        if (newVal && newVal.includes(0) && newVal.length > 1 && oldVal && oldVal.length > 0) {
          /* Es muss Alle also andere abwählen */
          let intersection = newVal.filter(x => oldVal.includes(x));

          if (intersection.includes(0)) {
            /* Wenn nicht, entfernen Sie Alle aus der Liste*/
            app.item_payload.filters.splice(app.item_payload.filters.findIndex(e => e === 0), 1)
          } else {
            /* Prüfen Sie, ob der neue Wert Alle ist, wenn ja, dann abwählen */
            app.item_payload.filters = [0]
          }
        }
      }

      if ((newVal && newVal.includes(0)) || !newVal || newVal.length === 0) {
        app.item_payload.contacts = app.views_payload.contacts
      }

      app.filterList()
    }
  },
  created() {
    let app = this

    app.$eventBus.$on("contactsRefresh", () => app.loadContacts())
    app.$eventBus.$on("contactCardMenu", (payload) => app.onCardMenuClicked(payload))
    app.$eventBus.$on("contactDelete", (payload) => app.onContactDelete(payload))

    app.views_payload.filters.push(...app.$constants.PRIORITIES)
    app.loadContacts()
  },
  beforeDestroy() {
    let app = this

    app.$eventBus.$off("contactsRefresh")
    app.$eventBus.$off("contactCardMenu")
    app.$eventBus.$off("contactDelete")
  },
  methods: {
    showCreateDialog() {
      let app = this
      app.$eventBus.$emit("manageContact", {action: "create"})
    },
    filterList() {
      let app = this
      let hasFiltered = false

      let hasFilter = false
      if(app.$vuetify.breakpoint.lgAndUp) {
        hasFilter = app.item_payload.filters && !app.item_payload.filters.includes("Alle") && app.item_payload.filters.length > 0
      } else {
        hasFilter = app.item_payload.filters && !app.item_payload.filters.includes(0) && app.item_payload.filters.length > 0
      }

      if (hasFilter) {
        hasFiltered = true

        let filtersTextArray = []
        app.item_payload.filters.forEach((filterInt) => {
          if(app.$vuetify.breakpoint.lgAndUp) {
            filtersTextArray.push(filterInt)
          } else {
            filtersTextArray.push(app.views_payload.filters[filterInt])
          }
        })

        app.item_payload.contacts = app.views_payload.contacts.filter(e => filtersTextArray.includes(e.priority))
      }

      if (app.item_payload.inputSearch && app.item_payload.inputSearch.length > 0) {
        const searchQuery = app.item_payload.inputSearch.toLowerCase()
        console.log(app.item_payload.contacts)
        app.item_payload.contacts = (hasFiltered ? app.item_payload.contacts : app.views_payload.contacts).filter(e => e.first_name.toLowerCase().includes(searchQuery) || e.last_name.toLowerCase().includes(searchQuery) || e.full_name.toLowerCase().includes(searchQuery))
      } else {
        if (!hasFiltered) app.item_payload.contacts = app.views_payload.contacts
      }
    },
    onCardMenuClicked(payload) {
      let app = this

      if (payload.menu_item_clicked.action === "update_item") {
        payload.action = "update"
        app.$eventBus.$emit("manageContact", payload)
      } else if (payload.menu_item_clicked.action === "delete_item") {
        app.$eventBus.$emit('deleteContact', {
          id: payload.id,
          title: `Lösche ${payload.full_name}`,
          description: `Bitte beachten Sie, dass dies permanent gelöscht wird! Alle Erinnerungen werden somit entfallen.`,
        })
      }
    },
    onContactDelete(payload) {
      let app = this
      let toast_payload = {}

      app.$api({
        url: `api/contacts/${payload.id}`,
        method: 'DELETE'
      }).then((response) => {
        toast_payload = {
          status: "success",
          message: response.data.message,
          error: null
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
        app.$eventBus.$emit("refreshStats")
        app.loadContacts()
      }, (error) => {
        toast_payload = {
          status: "failure",
          message: "Fehler: Kontakt konnte nicht gelöscht werden.",
          error: error
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })
    },
    loadContacts() {
      let app = this
      let toast_payload = {}

      app.$api({
        url: `api/contacts`,
        method: 'GET'
      }).then((response) => {
        app.item_payload.contacts = response.data.contacts
        app.views_payload.contacts = response.data.contacts
      }, (error) => {
        toast_payload = {
          status: "failure",
          message: "Fehler: Kontakte konnten nicht aufgerufen werden.",
          error: error
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })
    }
  }
}
</script>