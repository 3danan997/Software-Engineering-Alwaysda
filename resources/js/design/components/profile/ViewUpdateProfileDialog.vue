<template>
  <v-dialog v-model="views_payload.dialog" persistent scrollable width="500px">
    <v-card class="card">
      <v-card-title class="border-bottom">
        <v-row class="ma-0">

          <v-col class="flex-grow-1 min-width-fit-content text-left align-self-center">
            <p class="font-weight-bold text-font-subtitle text-color-level-0 mb-0">Einstellung</p>
          </v-col>

          <v-col class="flex-grow-0 min-width-fit-content align-self-center text-right">
            <v-btn class="text-capitalize font-weight-semibold text-font-caption-more" color="red" text x-small
                   @click="logout()">Ausloggen
            </v-btn>
          </v-col>

        </v-row>
      </v-card-title>
      <v-card-text>
        <v-row class="ma-0 py-2">

          <v-col class="text-left" cols="12">
            <p class="font-weight-bold text-font-body-more mb-0 text-truncate text-color-level-0">Mein Profil</p>
          </v-col>

          <v-col class="ma-0 px-0 pb-1" cols="12">
            <v-row class="ma-0 clickable" @click="onAvatarEvent('click', null)">

              <v-col class="flex-grow-0 min-width-fit-content align-self-center pr-1">
                <v-avatar color="grey" size="55">
                  <v-img :src="item_payload.avatar_display"></v-img>
                </v-avatar>
              </v-col>

              <v-col class="flex-grow-1  align-self-center text-left">
                <span class="font-weight-medium text-font-body-less mb-0  text-color-level-2">Klicken Sie hier um einen Avatar hochzuladen</span>

              </v-col>

            </v-row>

            <v-file-input v-show="false" ref="avatar" v-model="item_payload.avatar" accept="image/*" hide-details
                          @change="onAvatarEvent('change', $event)"></v-file-input>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputFirstName" v-model="item_payload.inputFirstName" class="text-field text-field-bordered" dense
                          flat hide-details
                          label="Vorname" prepend-inner-icon="mdi-account-circle-outline" solo type='text'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field id="inputLastName" v-model="item_payload.inputLastName" class="text-field text-field-bordered" dense
                          flat hide-details
                          label="Nachname" prepend-inner-icon="mdi-account-circle-outline" solo type='text'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12">
            <v-text-field id="inputEmail" v-model="item_payload.inputEmail" :disabled="true" class="text-field text-field-bordered"
                          dense flat
                          hide-details label="Email" prepend-inner-icon="mdi-email-outline" solo type='email'
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>

          <v-col cols="12">
            <v-text-field id="inputPassword" v-model="item_payload.inputPassword" :append-icon="views_payload.passwordShow ? 'mdi-lock-open-outline' : 'mdi-lock-outline'" :type="views_payload.passwordShow ? 'text' : 'password'"
                          class="text-field text-field-bordered"
                          dense flat hide-details label="Passwort"
                          solo
                          @click:append="views_payload.passwordShow = !views_payload.passwordShow"
                          @keydown.enter="onTextFieldEnter($event.target.id)"></v-text-field>
          </v-col>


          <v-col class="text-left" cols="12">
            <p class="font-weight-bold text-font-body-more mb-0 text-truncate text-color-level-0">Theme</p>
          </v-col>

          <v-col class="ma-0 pa-0" cols="12">

            <v-row class="ma-0">

              <v-col v-for="(item, index) in views_payload.bar_options.bottom" :key="index"
                     class="flex-grow-0 min-width-fit-content">
                <v-img :class="{'view-profile-theme-option-active': item.active}" :src="`${$constants.PATHS.url + $constants.PATHS.settings + item.img}.svg`"
                       class="view-profile-theme-option"
                       @click="onBarClicked('bottom', item.img)"></v-img>
                <p class="mt-2 mb-0 text-center font-weight-semibold text-font-caption">{{ item.title }}</p>
              </v-col>

            </v-row>

          </v-col>

          <v-col class="ma-0 pa-0" cols="12">

            <v-row class="ma-0">

              <v-col v-for="(item, index) in views_payload.bar_options.side" :key="index"
                     class="flex-grow-0 min-width-fit-content">
                <v-img :class="{'view-profile-theme-option-active': item.active}" :src="`${$constants.PATHS.url + $constants.PATHS.settings + item.img}.svg`"
                       class="view-profile-theme-option"
                       @click="onBarClicked('side', item.img)"></v-img>
                <p class="mt-2 mb-0 text-center font-weight-semibold text-font-caption">{{ item.title }}</p>
              </v-col>

            </v-row>

          </v-col>


        </v-row>
      </v-card-text>
      <v-card-actions class="border-top">
        <v-spacer></v-spacer>
        <v-btn class="font-weight-bold text-font-caption" text @click="onButtonClicked('negative')">Abbrechen
        </v-btn>
        <v-btn class="font-weight-bold text-font-caption" color="accent" text @click="onButtonClicked('positive')">
          Abschicken
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script>
import {mapGetters} from "vuex";

export default {
  computed: {
    ...mapGetters({
      sideBar: 'sideBar',
      bottomBar: 'bottomBar',
    }),
  },
  data() {
    return {
      item_payload: {
        avatar: null,
        avatar_display: null,
        avatar_changed: false,
        inputFirstName: null,
        inputLastName: null,
        inputEmail: null,
        inputPassword: null,
      },
      views_payload: {
        dialog: false,
        is_submitting: false,
        passwordShow: false,
        bar_options: {
          side: [
            {
              title: "Sidebar",
              img: "left_sidebar",
              active: false,
            },
            {
              title: "Kein Sidebar",
              img: "no_bar",
              active: false,
            }
          ],
          bottom: [
            {
              title: "Full Bar",
              img: "bottom_full",
              active: false,
            },
            {
              title: "Margined Bar",
              img: "bottom_margin",
              active: false,
            },
          ],
        }
      },
    }
  },
  created() {
    let app = this
    app.$eventBus.$on('openProfileDialog', () => app.onPayloadReceive())
  },
  beforeDestroy() {
    this.$eventBus.$off('openProfileDialog')
  },
  methods: {
    onBarClicked(type, img) {
      let app = this

      if (!type || !img) return

      if (type === "bottom") {
        app.views_payload.bar_options.bottom.forEach((item, index) => {
          app.views_payload.bar_options.bottom[index].active = item.img === img
        })
      } else if (type === "side") {
        app.views_payload.bar_options.side.forEach((item, index) => {
          app.views_payload.bar_options.side[index].active = item.img === img
        })
      }
    },
    onPayloadReceive() {
      let app = this

      app.nullifyInput()
      app.loadProfile()

      app.onBarClicked('side', app.sideBar())
      app.onBarClicked('bottom', app.bottomBar())

      app.views_payload.dialog = true
    },
    nullifyInput() {
      let app = this
      app.item_payload.avatar = null
      app.item_payload.avatar_changed = false
      app.item_payload.avatar_display = null
      app.item_payload.inputFirstName = null
      app.item_payload.inputLastName = null
      app.item_payload.inputEmail = null
      app.item_payload.inputPassword = null
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
    onButtonClicked(action) {
      let app = this

      if (action === "positive") {
        app.updateProfile()
      } else {
        app.views_payload.dialog = false
      }
    },
    onTextFieldEnter(id) {
      if (id === "inputFirstName") {
        document.getElementById("inputLastName").click()

      } else if (id === "inputLastName") {
        document.getElementById("inputPassword").click()
      } else if (id === "inputPassword") {
        this.submitPayload()
      }
    },
    loadProfile() {
      let app = this
      let toast_payload = {}

      app.$api({
        url: `api/user/fetch`,
        method: 'GET'
      }).then((response) => {
        const user = response.data.user


        if (user.avatar) {
          fetch(app.$constants.PATHS.url + app.$constants.PATHS.avatars + user.avatar).then(response => response.blob())
              .then((blob) => {
                app.item_payload.avatar = blob
                app.item_payload.avatar_display = URL.createObjectURL(blob)
              })
        }

        app.item_payload.inputFirstName = user.first_name
        app.item_payload.inputLastName = user.last_name
        app.item_payload.inputEmail = user.email

      }, (error) => {
        toast_payload = {
          status: "failure",
          message: "Fehler: Kontakte konnten nicht aufgerufen werden.",
          error: error
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })
    },
    updateProfile() {
      let app = this
      let toast_payload = {}

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

      let data = new FormData()

      if (app.item_payload.avatar)
        data.append("avatar", app.item_payload.avatar)

      data.append("avatar_changed", app.item_payload.avatar_changed)

      if (app.item_payload.inputFirstName)
        data.append("first_name", app.item_payload.inputFirstName)

      if (app.item_payload.inputLastName)
        data.append("last_name", app.item_payload.inputLastName)

      if (app.item_payload.inputPassword)
        data.append("password", app.item_payload.inputPassword)


      app.$api({
        url: `api/user/preferences`,
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


        const user = response.data.user

        app.$eventBus.$emit("userUpdated", user)

        if (app.item_payload.inputPassword) app.logout()
        else {
          app.$store.commit("setUser", user)
          let sideBar = app.views_payload.bar_options.side.find(e => e.active === true)
          let bottomBar = app.views_payload.bar_options.bottom.find(e => e.active === true)
          if (sideBar)
            app.$store.commit("setSidebar", sideBar.img)
          if (bottomBar)
            app.$store.commit("setBottombar", bottomBar.img)
        }


        app.views_payload.dialog = false
      }, (error) => {
        toast_payload = {
          status: "failure",
          message: "Fehler beim Bearbeiten vom Profile.",
          error: error
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      }).finally(() => {
        app.views_payload.is_submitting = false
      })
    },
    logout() {
      let app = this
      app.views_payload.dialog = false
      app.$store.commit("setAuthLoggingOut", false)
      app.$store.dispatch("logout")
    }
  }
}
</script>
<style lang="scss" scoped>


</style>