<template>
  <v-row class="ma-0">
    <template v-if="!$vuetify.breakpoint.mdAndDown">
      <v-col cols="12">
        <p class="mb-0 font-weight-semibold text-font-title text-color-level-0 fade-in">Zusammenfassung</p>
      </v-col>

    </template>


    <v-col cols="12">
      <div class="view-stats-row view-fade-horizontal">
        <div class="view-stats-col mr-2">
          <v-card class="card card-shadow card-radius view-stats-card fade-in">
            <v-row class="ma-0 align-center fill-height">
              <v-col cols="12" class="align-self-center pa-4">
                <p class="mb-0 font-weight-semibold text-font-body text-color-level-1">Kontakte</p>
                <p class="py-4 mb-0 font-weight-bold text-font-subtitle-more text-color-level-0">{{item_payload.contacts_count}}</p>
                <p class="mb-0 font-weight-semibold text-font-caption text-color-level-2">insgesamt</p>
              </v-col>
            </v-row>
          </v-card>
        </div>

        <div class="view-stats-col mr-2">
          <v-card class="card card-shadow card-radius view-stats-card fade-in">
            <v-row class="ma-0 align-center fill-height">
              <v-col cols="12" class="align-self-center pa-4">
                <p class="mb-0 font-weight-semibold text-font-body text-color-level-1">Einstellungen</p>
                <p class="py-4 mb-0 font-weight-bold text-font-subtitle-more text-color-level-0">{{item_payload.reminders_count}}</p>
                <p class="mb-0 font-weight-semibold text-font-caption text-color-level-2">von Erinnerungen</p>
              </v-col>
            </v-row>
          </v-card>
        </div>

        <div class="view-stats-col">
          <v-card class="card card-shadow card-radius view-stats-card fade-in">
            <v-row class="ma-0 align-center fill-height">
              <v-col cols="12" class="align-self-center pa-4">
                <p class="mb-0 font-weight-semibold text-font-body text-color-level-1">Erinnerungen</p>
                <p class="py-4 mb-0 font-weight-bold text-font-subtitle-more text-color-level-0">{{item_payload.notifications_current_week_count}}</p>
                <p class="mb-0 font-weight-semibold text-font-caption text-color-level-2">dieser Woche</p>
              </v-col>
            </v-row>
          </v-card>
        </div>
      </div>
    </v-col>

  </v-row>

</template>
<script>
export default {
  data() {
    return {
      item_payload: {
        contacts_count: 0,
        reminders_count: 0,
        notifications_current_week_count: 0,
      }
    }
  },
  created() {
    let app = this

    app.$eventBus.$on("refreshStats", () => app.loadStats())

    app.loadStats()
  },
  beforeDestroy() {
    let app = this

    app.$eventBus.$off("refreshStats")
  },
  methods: {
    loadStats() {
      let app = this
      let toast_payload = {}

      app.$api({
        url: `api/stats`,
        method: 'GET'
      }).then((response) => {
        const stats = response.data.stats

        app.item_payload.contacts_count = stats.contacts_count
        app.item_payload.reminders_count = stats.reminders_count
        app.item_payload.notifications_current_week_count = stats.notifications_current_week_count
      }, (error) => {
        toast_payload = {
          status: "failure",
          message: "Fehler: Zusammenfassung konnte nicht aufgerufen werden.",
          error: error
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })
    }
  }
}
</script>