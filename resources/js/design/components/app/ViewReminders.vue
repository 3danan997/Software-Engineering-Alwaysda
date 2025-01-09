<template>
  <v-row class="ma-0 align-content-start view-reminders-row">
    <v-col v-if="$vuetify.breakpoint.lgAndUp" cols="12">
      <!--      Titel -->
      <p class="mb-0 font-weight-semibold text-font-title text-color-level-0 fade-in">Erinnerungen</p>
    </v-col>

    <v-col class="ma-0 pa-0" cols="12">
      <v-row class="ma-0">

        <!-- wenn null/nicht gesetzt oder leer (Länge) === 0, dann haben wir keine Erinnerungen -->
        <v-col v-if="!item_payload.reminders || item_payload.reminders.length === 0" cols="12">
          <p class="font-weight-semibold text-font-body-less pb-0 mb-0 text-color-level-0 fade-in">Keine Erinnerungen
            vorhanden</p>
        </v-col>

        <!-- Normalerweise schleifen wir, wenn wir welche haben -->
        <template v-for="(item, index) in item_payload.reminders">

          <!--          untergeordnete Elemente, die multipliziert werden, sollten einen Index haben, aber wir haben zwei Hauptelemente, daher verwenden wir benannte Indizes -->
          <v-col :key="`${index}_item`" class="align-self-center fade-in" cols="12" xl="6">

            <v-row class="ma-0">

              <v-col class="flex-grow-0 min-width-fit-content align-self-center pr-1">

                <v-badge
                    avatar bordered bottom offset-x="16" offset-y="16" overlap>
                  <template v-slot:badge>
                    <v-avatar :color="getReminderIconColor(item.type)">
                      <v-icon>{{ getReminderIcon(item.type) }}</v-icon>
                    </v-avatar>
                  </template>
                  <!--                  Bedingte Farbe, prüfen wir, ob wir einen Avatar in dieser Nutzlast haben, andernfalls legen wir eine Standardfarbe fest, die grau ist-->
                  <v-avatar :color="item.payload && item.payload.avatar ? '' : 'grey'" rounded size="50">
                    <v-img v-if="item.payload"
                           :src="$constants.PATHS.url + $constants.PATHS.avatars + item.payload.avatar"></v-img>
                  </v-avatar>
                </v-badge>
              </v-col>

              <v-col class="flex-grow-1 align-self-center pr-1">
                <p class="font-weight-semibold text-font-body-less pb-0 mb-0 text-color-level-0">
                  {{ getReminderText(item) }}</p>
                <p class="font-weight-medium text-font-caption pb-0 mb-0 mt-3 text-color-level-2">
                  <v-icon class="mb-1" x-small>mdi-calendar-outline</v-icon>
                  {{ getReminderDate(item.remind_at) }}
                </p>
              </v-col>

            </v-row>

          </v-col>

          <!--          Fügen Sie nach jeder Spalte eine Trennlinie hinzu, mit Ausnahme der letzten in der mobilen Ansicht
          oder in der Desktop-Ansicht fügen Sie nach jeweils 2 Elementen hinzu, da wir lg = 6 haben, was bedeutet, dass die Spalte in der Desktop-Ansicht 6 von 12 einnehmen sollte-->
          <v-col
              v-if="(index + 1) !== item_payload.reminders.length && !($vuetify.breakpoint.lgAndUp && index % 2 === 0)"
              :key="`${index}_divider`" class="py-1"
              cols="12">
            <v-divider class="divider fade-in"></v-divider>
          </v-col>

        </template>
      </v-row>
    </v-col>
  </v-row>
</template>
<script>
export default {
  data() {
    return {
      item_payload: {
        reminders: [],
      }
    }
  },
  mounted() {
    let app = this
    // In case contacts get deleted/updated/created, we refresh the reminders again
    app.$eventBus.$on("contactsRefresh", () => app.laodReminders())

    // first load, load the reminders
    app.laodReminders()
    // load the notifications that we need to send to the user
    app.loadNotifications()

    // Every 5 minutes, notifications will be checked. We don't want to have real-time function, it's too much and an overkill.
    // Reminders get checked by our Kernel.php and saved into database, so any n-minutes won't change the precision of that functionality
    setInterval(() => {
      app.loadNotifications()
    }, 1000 * 60 * 5)
  },
  beforeDestroy() {
    let app = this
    // Avoid memory leak
    app.$eventBus.$off("contactsRefresh")
  },
  methods: {
    getReminderText(item) {
      if (!item) return ""

      // Just like Kernel.php, same functionality, we write our reminder text to load it
      let type = item.type
      let middle_word = ""
      let full_name = item.payload ? item.payload.full_name : ""

      if (type === "Schreiben") {
        middle_word = "an"
        type = "Nachricht"
      } else if (type === "Geburtstag") {
        middle_word = "von"
      } else if (type === "Telefonieren") {
        middle_word = "mit"
        type = "Telefonat"
      } else if (type === "Treffen") {
        middle_word = "mit"
      } else if (type === "Sonst") {
        middle_word = "mit"
        type = "Sonstige Benachrichtigung"
      }

      return `${type} ${middle_word} ${full_name}`
    },
    getReminderIcon(type) {
      if (type === "Schreiben") {
        return "mdi-message-text-outline"
      } else if (type === "Geburtstag") {
        return "mdi-cake-variant-outline"
      } else if (type === "Telefonieren") {
        return "mdi-cake-variant-outline"
      } else if (type === "Treffen") {
        return "mdi-account-group-outline"
      } else if (type === "Sonst") {
        return "mdi-crosshairs-question"
      }
      return "mdi-plus-outline"
    },
    getReminderIconColor(type) {
      if (type === "Schreiben") {
        return "#7542b9"
      } else if (type === "Geburtstag") {
        return "#b34141"
      } else if (type === "Telefonieren") {
        return "#4d9f5a"
      } else if (type === "Treffen") {
        return "#3a83e7"
      } else if (type === "Sonst") {
        return "#889334"
      }
      return "#60605d"
    },
    getReminderDate(remind_at) {
      let app = this

      // Parsen Sie die UTC-Zeit in die lokale Zeitzone und wir erwarten immer, dass es Europa/Berlin sein wird
      let parsed = app.$moment.utc(remind_at).local()
      // Wir wollen nur die Tage vergleichen, nicht die Uhrzeit, denn es könnte 11:00 und 23:00 sein und die Tagesdifferenz wäre 0, aber eigentlich ist es 1
      // z.B. 10.10.2022 23:00 und 11.10.2022 10:00 (es ist weniger als ein Tag, aber es ist eigentlich ein anderer Tag)
      // Deshalb machen wir startOf
      let diff_in_days = parsed.clone().startOf('day').diff(app.$moment().startOf('day'), "day")


      if (diff_in_days === 0) {
        return `Heute ${parsed.format("HH:mm")}`
      } else if (diff_in_days === 1) {
        return `Morgen ${parsed.format("HH:mm")}`
      } else if (diff_in_days === 2) {
        return `Übermorgen ${parsed.format("HH:mm")}`
      }

      return parsed.format("DD.MM.YYYY HH:mm") + " Uhr"
    },
    loadNotifications() {
      let app = this
      let toast_payload = {}

      // Senden Sie eine Get-Anfrage an unsere API, um die verfügbaren Benachrichtigungen zu laden
      app.$api({
        url: `api/reminders/today`,
        method: 'GET'
      }).then((response) => {
        const notifications = response.data.reminders

        // Wir übergeben sie an unsere Sendebenachrichtigungsfunktion
        app.sendNotification(notifications)
      }, (error) => {
        toast_payload = {
          status: "failure",
          message: "Fehler: Benachrichtigungen konnte nicht aufgerufen werden.",
          error: error
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })
    },
    sendNotification(notifications) {
      let app = this

      // wenn leer, zurück
      if (!notifications) return

      // andernfalls durchlaufen Sie das Array
      notifications.forEach((item, index) => {
        // klonen Sie über die Lodash-Bibliothek, damit wir die Haupteigenschaft nicht beeinflussen
        let payload = app.$lodash.cloneDeep(item)
        // payload.user ist null, erstellen Sie ein leeres Objekt, um Fehler in anderen Funktionen zu vermeiden, die danach suchen
        if (!payload.user) {
          payload.user = {}
        }
        payload.payload = payload.user
        payload.type = payload.reminder_type

        // Verwenden Sie die Funktion der Bibliothek
        setTimeout(() => {
          app.$notify({
            title: "Erinnerung",
            text: app.getReminderText(payload),
            duration: 2000,
            type: "warm",
            speed: "500"
          })
        }, 1500 * index)
      })
    },
    laodReminders() {
      let app = this
      let toast_payload = {}

      // Laden Sie die Erinnerungen über unsere API
      app.$api({
        url: `api/reminders`,
        method: 'GET'
      }).then((response) => {
        // Legen Sie den Wert fest, es ist uns egal, ob er null ist, die Vorlage übernimmt bereits das Rendern
        // wenn es null ist, dann setzen wir ein leeres Array
        // doppelt ?? bedeutet, wenn es einen gültigen Wert hat, dann setze es
        // vollständiger Code wäre
        // app.item_payload.reminders = response.data.reminders ? response.data.reminders : []
        app.item_payload.reminders = response.data.reminders ?? []
      }, (error) => {
        toast_payload = {
          status: "failure",
          message: "Fehler: Erinnerungen konnte nicht aufgerufen werden.",
          error: error
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })
    }
  }
}
</script>