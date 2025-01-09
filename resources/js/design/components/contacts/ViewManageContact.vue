<template>
  <v-bottom-sheet v-model="views_payload.sheet" inset persistent scrollable>

    <v-card
        class="text-center" style="background-color: #323644 !important; overflow-y: scroll">

      <v-card-title class="border-bottom">
        <v-row class="ma-0">
          <v-col class="text-left align-self-center" cols="12">
            <p class="font-weight-bold text-font-subtitle mb-0 text-truncate text-color-level-0">
              {{ item_payload.action === "create" ? "Kontakt erstellen" : "Kontakt bearbeiten" }}</p>
          </v-col>
        </v-row>
      </v-card-title>

      <v-card-text class="py-2 view-fade card-manage-contact">
        <v-form ref="form" class="row ma-0" style="overflow-y: scroll;">

          <v-col class="text-left" cols="12">
            <p class="font-weight-bold text-font-body-more mb-0 text-truncate text-color-level-0">Details</p>
          </v-col>

          <v-col class="ma-0 pa-0" cols="12">
            <v-row class="ma-0 clickable" @click="onAvatarEvent('click', null)">

              <v-col class="flex-grow-0 min-width-fit-content align-self-center pr-1">
                <v-avatar color="grey" size="50">
                  <v-img :src="item_payload.avatar_display"></v-img>
                </v-avatar>
              </v-col>

              <v-col class="flex-grow-1 min-width-fit-content align-self-center text-left">
                <p class="font-weight-medium text-font-body-less mb-0 text-truncate text-color-level-2">Klicken Sie hier
                  um einen Avatar hochzuladen</p>

              </v-col>

            </v-row>

            <v-file-input v-show="false" ref="avatar" v-model="item_payload.avatar" accept="image/*" hide-details
                          @change="onAvatarEvent('change', $event)"></v-file-input>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputFirstName" v-model="item_payload.inputFirstName"
                          :rules="[$constants.RULES.required]" class="text-field text-field-bordered"
                          flat hide-details
                          label="Vorname" prepend-inner-icon="mdi-account-circle-outline" solo type='text'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputLastName" v-model="item_payload.inputLastName"
                          :rules="[$constants.RULES.required]" class="text-field text-field-bordered"
                          flat hide-details
                          label="Nachname" prepend-inner-icon="mdi-account-circle-outline" solo type='text'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputMobileNumber" v-model="item_payload.inputMobileNumber" class="text-field text-field-bordered"
                          flat hide-details
                          label="Handynummer" prepend-inner-icon="mdi-phone-outline" solo type='number'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputBirthday" v-model="item_payload.inputBirthday" v-mask="'##.##.####'"
                          :rules="[$constants.RULES.required]" class="text-field text-field-bordered"
                          flat hide-details
                          label="Geburtstag (DD.MM.YYYY)" prepend-inner-icon="mdi-cake-variant-outline" solo type='text'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-select id="inputPriority" v-model="item_payload.inputPriority" :items='$constants.PRIORITIES'
                      :rules="[$constants.RULES.required]" class="text-field text-field-bordered" flat
                      hide-details label="Priorität" prepend-inner-icon="mdi-heart-outline" solo
                      @change="onTextFieldEnter('inputPriority')"></v-select>
          </v-col>

          <v-col cols="12">
            <v-divider class="divider"></v-divider>
          </v-col>

          <v-col class="align-self-center pa-0 ma-0" cols="12">
            <v-row class="ma-0">
              <v-col class="flex-grow-0 min-width-fit-content align-self-center text-left">
                <p class="font-weight-bold text-font-body-more mb-0 text-truncate text-color-level-0">Erinnerungen</p>
              </v-col>
              <v-col class="flex-grow-0 min-width-fit-content align-self-center text-right pl-0">
                <v-btn icon small @click="addReminder()">
                  <v-icon color="accent" size="24">mdi-plus-circle-outline</v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-col>

          <v-col v-for="(item, index) in item_payload.reminders" :key="index" class="fade-in align-self-center pa-0 ma-0"
                 cols="12">
            <v-row class="ma-0">

              <v-col class="flex-grow-0 min-width-fit-content align-self-center">
                <v-btn :disabled="!item_payload.reminders || !(item_payload.reminders.length > 1)" icon @click="deleteReminder(index)"><v-icon color="red darken--3">mdi-close</v-icon></v-btn>
              </v-col>

              <v-col class="flex-grow-1 pa-0 ma-0 align-self-center">
                <v-row class="card-manage-contact-reminder-row" :class="{'ma-0': $vuetify.breakpoint.lgAndUp, 'mx-0 mt-0': $vuetify.breakpoint.mdAndDown}">
                  <v-col cols="12" lg="3">
                    <v-select :id="`inputReminderType_${index}`" v-model="item.type"
                              :items='$constants.REMINDERS_TYPES' :rules="[$constants.RULES.required]"
                              class="text-field text-field-bordered text-field-bordered-full" flat
                              hide-details label="Erinnerungstyp" prepend-inner-icon="mdi-shape-outline" solo
                              @change="onTextFieldEnter(`inputReminderType_${index}`)"></v-select>
                  </v-col>

                  <v-col cols="12" lg="3">
                    <v-select :id="`inputReminderFrequencySelect_${index}`" v-model="item.frequency"
                              :items='$constants.REMINDERS_FREQUENCIES' :rules="[$constants.RULES.required]"
                              class="text-field text-field-bordered text-field-bordered-full" flat
                              hide-details label="Erinnerungsintervall" prepend-inner-icon="mdi-calendar-outline" solo
                              @change="validateFrequencySelect(index);onTextFieldEnter(`inputReminderFrequencySelect_${index}`)"></v-select>
                  </v-col>

                  <v-col cols="12" lg="3">
                    <v-menu
                        :ref="`menus_${index}`"
                        v-model="views_payload.menus[index]"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        max-width="290px"
                        min-width="290px"
                        offset-y
                        transition="scale-transition"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                            :id="`inputReminderFrequencyTime_${index}`"
                            v-model="item.time"
                            label="Uhrzeit"
                            class="text-field text-field-bordered text-field-bordered-full" flat hide-details solo
                            prepend-inner-icon="mdi-clock-time-four-outline"
                            @change="onTextFieldEnter(`inputReminderFrequencyTime_${index}`)"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                        ></v-text-field>
                      </template>
                      <v-time-picker
                          v-if="views_payload.menus[index]"
                          v-model="item.time" format="24hr"
                          full-width
                      ></v-time-picker>
                    </v-menu>
                  </v-col>

                  <v-col cols="12" lg="3">
                    <v-text-field :id="`inputReminderFrequencyText_${index}`"
                                  v-model="item.frequency_value"
                                  :class="{'text-field-disabled text-field-disabled-full text-field-bordered-disabled': item.frequency !== 'Selbst eingeben...'}" :disabled="item.frequency !== 'Selbst eingeben...'"
                                  :rules="item.frequency === 'Selbst eingeben...' ?  [$constants.RULES.required] : []"
                                  aria-valuemin="1" class="text-field text-field-bordered" flat
                                  hide-details
                                  label="Alle X Tagen"
                                  prepend-inner-icon="mdi-numeric" solo type='number'
                                  @change="item.frequency_value < 2 ? item.frequency_value = 2 : ''"
                                  @keydown.enter="onTextFieldEnter(`inputReminderFrequencyText_${index}`)"></v-text-field>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-col>

          <v-col cols="12">
            <v-divider class="divider"></v-divider>
          </v-col>

          <v-col class="text-left" cols="12">
            <p class="font-weight-bold text-font-body-more mb-0 text-truncate text-color-level-0">Social Media</p>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputSocialMediaFacebook" v-model="item_payload.inputSocialMedia['facebook']" class="text-field text-field-bordered"
                          flat hide-details
                          label="Facebook" prepend-inner-icon="mdi-facebook" solo type='text'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputSocialMediaInstagram" v-model="item_payload.inputSocialMedia['instagram']" class="text-field text-field-bordered"
                          flat hide-details
                          label="Instagram" prepend-inner-icon="mdi-instagram" solo type='text'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputSocialMediaTelegram" v-model="item_payload.inputSocialMedia['telegram']" class="text-field text-field-bordered"
                          flat hide-details
                          label="Telegram" prepend-inner-icon="mdi-send" solo type='text'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>
        </v-form>
      </v-card-text>

      <v-card-actions class="border-top">
        <v-row class="ma-0 justify-center align-center">
          <v-col class="flex-grow-0 min-width-fit-content text-right">
            <v-btn class="text-capitalize text-font-caption font-weight-semibold" depressed large outlined
                   @click="onButtonClicked('negative')">Abbrechen
            </v-btn>
          </v-col>

          <v-col class="flex-grow-0 min-width-fit-content text-right">
            <v-btn class="text-capitalize text-font-caption font-weight-semibold" color="primary" depressed large
                   @click="onButtonClicked('positive')">{{item_payload.action === "create" ? "Erstellen" : "Bearbeiten"}}
            </v-btn>
          </v-col>
        </v-row>
      </v-card-actions>

    </v-card>
  </v-bottom-sheet>
</template>
<script>
export default {
  data() {
    return {
      item_payload: {
        action: "create",
        id: null,
        avatar_display: null,
        avatar: null,
        avatar_changed: false,
        inputFirstName: null,
        inputLastName: null,
        inputMobileNumber: null,
        inputBirthday: null,
        inputPriority: null,
        inputSocialMedia: {
          facebook: null,
          instagram: null,
          telegram: null,
        },
        reminders: [
          {
            frequency: null,
            frequency_value: null,
            type: null,
            time: null,
          }
        ],
      },
      views_payload: {
        sheet: false,
        is_submitting: false,
        birthday_valid: false,
        menus: [],
      }
    }
  },
  created() {
    let app = this

    app.$eventBus.$on("manageContact", (payload) => app.onPayloadReceive(payload))

  },
  beforeDestroy() {
    let app = this

    app.$eventBus.$off("manageContact")
  },
  methods: {
    addReminder() {
      let app = this

      app.item_payload.reminders.push({
        frequency: null,
        frequency_value: null,
        time: null,
        type: null,
      })
    },
    deleteReminder(index) {
      let app = this

      if(app.item_payload.reminders.length > 1) {
        app.item_payload.reminders.splice(index, 1)
      } else {
        const toast_payload = {
          status: "pending",
          message: "Es muss eine Erinnerung geben!",
          error: null
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      }
    },
    validateFrequencySelect(index) {
      let app = this

      /* Frequenzwert zurücksetzen, wenn die Auswahl geändert wird */
      if(app.item_payload.reminders[index]) {
        if(app.item_payload.reminders[index].frequency !== "Selbst eingeben...") {
          app.item_payload.reminders[index].frequency_value = null
        }
      }
    },
    onAvatarEvent(action, e) {
      let app = this
      if (action === "click") {
        app.$refs.avatar.$refs.input.click()
      } else if (action === "change") {
        let reader = new FileReader();
        try {
          reader.onload = function (ef) {
            app.item_payload.avatar_changed = true
            app.item_payload.avatar_display = ef.target.result
            app.item_payload.avatar = e
          }
          reader.readAsDataURL(e);
        } catch (error) {
          app.item_payload.avatar_changed = false
        }
      }
    },
    onTextFieldEnter(id) {
      if(!id) return

      let app = this

      if (id === "inputFirstName") {
        document.getElementById("inputLastName").click()

      } else if (id === "inputLastName") {
        document.getElementById("inputMobileNumber").click()

      } else if (id === "inputMobileNumber") {
        document.getElementById("inputBirthday").click()

      } else if (id === "inputBirthday") {
        document.getElementById("inputPriority").click()

      } else if (id === "inputPriority") {
        if(document.getElementById("inputReminderType_0")) {
          document.getElementById("inputReminderType_0").click()
        } else {
          document.getElementById("inputSocialMediaFacebook").click()
        }

        // inputReminderFrequencySelect_ inputReminderType_ inputReminderFrequencyText_

      } else if (id.match(/inputReminderType_\d/g)) {
        /* Holen Sie sich den Index und prüfen Sie, ob er in der übereinstimmenden Regex enthalten ist */
        let index = id.match(/_\d+/g)
        if(index && index[0]) {
          index = parseInt(index[0].replace("_", ""))

          if(document.getElementById(`inputReminderFrequencySelect_${index}`)) {
            document.getElementById(`inputReminderFrequencySelect_${index}`).click()
          } else {
            document.getElementById("inputSocialMediaFacebook").click()
          }
        }
      } else if (id.match(/inputReminderFrequencySelect_\d/g)) {
        // same as above
        let index = id.replace("inputReminderFrequencySelect_", "")
        if (index && index[0]) {
          index = index[0]
          if (document.getElementById(`inputReminderFrequencyTime_${index}`)) {
            document.getElementById(`inputReminderFrequencyTime_${index}`).click()
          } else {
            document.getElementById("inputSocialMediaFacebook").click()
          }
        }
      } else if (id.match(/inputReminderFrequencyTime_\d/g)) {
        let index = id.replace("inputReminderFrequencyTime_", "")
        if (index && index[0]) {
          index = index[0]
          let indexNext = index + 1
          if (app.item_payload.reminders[index]) {
            /* prüfen, ob es selbst eingeben hat, dann markieren wir das richtige Element, ansonsten springen wir zur nächsten Indexprüfung */
            /* Wir verwenden ein Timeout, da es X Millis dauern würde, bis das Eingabefeld verfügbar ist. Grund? Es ist standardmäßig deaktiviert und die Eingabe kann nicht hervorgehoben werden, wenn das Textfeld zuvor einige Zeit gebraucht hat, um aktiv zu sein */
            if (app.item_payload.reminders[index].frequency === "Selbst eingeben...") {
              setTimeout(() => {
                if (document.getElementById(`inputReminderFrequencyText_${index}`)) {
                  document.getElementById(`inputReminderFrequencyText_${index}`).click()
                } else {
                  document.getElementById("inputSocialMediaFacebook").click()
                }
              }, 100)
            } else {
              if (document.getElementById(`inputReminderType_${indexNext}`)) {
                document.getElementById(`inputReminderType_${indexNext}`).click()
              } else {
                document.getElementById("inputSocialMediaFacebook").click()
              }
            }
          }
        }
      }else if (id.match(/inputReminderFrequencyText_\d/g)) {
        let index = id.match(/_\d+/g)
        if(index && index[0]) {
          index = parseInt(index[0].replace("_", ""))
          let indexNext = index + 1

          if(document.getElementById(`inputReminderType_${indexNext}`)) {
            document.getElementById(`inputReminderType_${indexNext}`).click()
          } else {
            document.getElementById("inputSocialMediaFacebook").click()
          }
        }
      }


      else if (id === "inputSocialMediaFacebook") {
        document.getElementById("inputSocialMediaInstagram").click()

      } else if (id === "inputSocialMediaInstagram") {
        document.getElementById("inputSocialMediaTelegram").click()

      } else if (id === "inputSocialMediaTelegram") {
        this.submitPayload()
      }
    },
    onButtonClicked(action) {
      let app = this
      if (action === "negative") {
        app.views_payload.sheet = false
      } else {
        app.submitPayload()
      }
    },
    onPayloadReceive(payload) {
      let app = this

      app.nullifyInput()

      if (payload.action === "update") {
        app.item_payload.action = payload.action
        app.item_payload.id = payload.id
        if (payload.avatar) {
          fetch(app.$constants.PATHS.url + app.$constants.PATHS.avatars + payload.avatar).then(response => response.blob())
              .then((blob) => {
                app.item_payload.avatar = blob
                app.item_payload.avatar_display = URL.createObjectURL(blob)
              })
        }

        app.item_payload.inputFirstName = payload.first_name
        app.item_payload.inputLastName = payload.last_name
        app.item_payload.inputMobileNumber = payload.mobile_number
        app.item_payload.inputBirthday = app.$moment(payload.birthday, "YYYY-MM-DD").format("DD.MM.YYYY")
        app.item_payload.inputPriority = payload.priority
        app.item_payload.inputSocialMedia = payload.social_media ? JSON.parse(payload.social_media) : app.item_payload.inputSocialMedia
        app.item_payload.reminders = payload.reminders && payload.reminders.length > 1 ? JSON.parse(payload.reminders) : app.item_payload.reminders
      }

      app.views_payload.sheet = true

      /* Verzögerung, da das Blatt nicht verfügbar ist, bis es gerendert wird. Der Aufruf von $refs erfordert, dass das Element gerendert wird.*/
      setTimeout(() => {
        app.$refs.form.resetValidation()
      }, 50)
    },
    nullifyInput() {
      let app = this
      app.item_payload.id = null
      app.item_payload.avatar = null
      app.item_payload.avatar_changed = false
      app.item_payload.avatar_display = null
      app.item_payload.inputFirstName = null
      app.item_payload.inputLastName = null
      app.item_payload.inputMobileNumber = null
      app.item_payload.inputBirthday = null
      app.item_payload.inputPriority = null
      app.item_payload.inputSocialMedia = {
        facebook: null,
        instagram: null,
        telegram: null,
      }
      app.item_payload.reminders = [{
        frequency: null,
        frequency_value: null,
        type: null,
        time: null,
      }
      ]
    },
    submitPayload() {
      let app = this
      let toast_payload = {}

      let isBirthDayValid = app.$moment(app.item_payload.inputBirthday, "DD.MM.YYYY").isValid()
      if(!isBirthDayValid) {
        toast_payload = {
          status: "pending",
          message: "Bitte den Geburtstag korrekt eingeben!",
          error: null
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
        return
      }


      let birthday = app.$moment(app.$moment.now()).diff(app.$moment(app.item_payload.inputBirthday, "DD.MM.YYYY"), "years");
      if(birthday < 0) {
        toast_payload = {
          status: "pending",
          message: "Bitte den Geburtstag nicht in der Zukunft eingeben!",
          error: null
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
        return
      }

      if (app.$refs.form.validate()) {

        if (app.views_payload.is_submitting) {
          toast_payload = {
            status: "pending",
            message: "Eine Anfrage wird bearbeitet. Bitte warten Sie!",
            error: null
          }
          app.$eventBus.$emit("dchvt_toast", toast_payload)
          return
        }

        app.views_payload.is_submitting = true

        /* es ist aktualisieren */
        let substring = ""
        if (app.item_payload.id && app.item_payload.id > 0) {
          substring = `/${app.item_payload.id}`
        }


        let data = new FormData()

        if (app.item_payload.avatar)
          data.append("avatar", app.item_payload.avatar)

        data.append("avatar_changed", app.item_payload.avatar_changed)

        if (app.item_payload.inputFirstName)
          data.append("first_name", app.item_payload.inputFirstName)

        if (app.item_payload.inputLastName)
          data.append("last_name", app.item_payload.inputLastName)

        if (app.item_payload.inputMobileNumber)
          data.append("mobile_number", app.item_payload.inputMobileNumber)

        if (app.item_payload.inputBirthday)
          data.append("birthday", app.$moment(app.item_payload.inputBirthday, "DD.MM.YYYY").format("YYYY-MM-DD"))

        if (app.item_payload.inputPriority)
          data.append("priority", app.item_payload.inputPriority)

        if (app.item_payload.inputSocialMedia)
          data.append("social_media", JSON.stringify(app.item_payload.inputSocialMedia))


        let reminders = null

        if(app.item_payload.reminders) {
          reminders = app.$lodash.cloneDeep(app.item_payload.reminders)
          reminders = reminders.filter(e => e => e.type && (e.frequency !== "Selbst eingeben..." || (e.frequency === "Selbst eingeben..." && e.frequency_value && e.frequency_value > 0)))

          reminders = app.$lodash.uniqBy(reminders, function (elem) {
            return [elem.type, elem.frequency, elem.frequency_value, elem.time].join()
          })

          reminders.forEach((item) => {
            if(!item.time) {
              item.time = "00:00"
            }
          })
        }

        if (reminders)
          data.append("reminders", JSON.stringify(reminders))


        app.$api({
          url: `api/contacts${substring}`,
          data: data,
          headers: {
            "Content-Type": "multipart/form-data"
          },
          method: 'POST'
        }).then((response) => {
          toast_payload = {
            status: "success",
            message: response.data.message,
            error: null
          }
          app.$eventBus.$emit("dchvt_toast", toast_payload)

          app.views_payload.sheet = false

          app.$eventBus.$emit("contactsRefresh")
          app.$eventBus.$emit("refreshStats")
        }, (error) => {
          toast_payload = {
            status: "failure",
            message: "Fehler beim Bearbeiten vom Kontakt.",
            error: error
          }
          app.$eventBus.$emit("dchvt_toast", toast_payload)
        }).finally(() => {
          app.views_payload.is_submitting = false
        })

      } else {
        toast_payload = {
          status: "pending",
          message: "Bitte die Eingaben vollständig bearbeiten!",
          error: null
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
        app.$refs.form.resetValidation()
      }

    }
  }
}
</script>