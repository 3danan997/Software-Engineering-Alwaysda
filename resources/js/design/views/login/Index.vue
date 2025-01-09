<template>
  <v-container fluid fill-height py-0 class="login-view-container">
    <v-row class="login-view-row" v-show="visibility.views" :class="{'fade-in': visibility.views}">
      <ViewForm :class="{'fade-in-up': visibility.views}"></ViewForm>
      <ViewBackground :class="{'slide-in-left': visibility.views}" v-if="$vuetify.breakpoint.lgAndUp"></ViewBackground>
    </v-row>
  </v-container>
</template>

<script>
import ViewBackground from "./../../components/login/ViewBackground"
import ViewForm from "./../../components/login/ViewForm"

export default {
  components: {
    ViewBackground,
    ViewForm,
  },
  data() {
    return {
      visibility: {
        views: false
      }
    }
  },
  created() {
    let app = this
    app.$eventBus.$on("dvl_login", (payload) => app.handleEvent(payload))

    app.animate()
  },
  beforeDestroy() {
    let app = this

    app.$eventBus.$off("dvl_login")
  },
  methods: {
    async handleEvent(payload){
      let app = this

      let toast_payload = {
        status: null,
        message: null,
        error: null
      }
      /* Überprüfen Sie, welchen Modus wir verwenden. */
      if(payload.action === "register_user") {
        app.registerUser(payload, toast_payload)
      } else if(payload.action === "login_user") {
        app.loginUser(payload, toast_payload)
      } else if(payload.action === "request_token") {
        app.requestToken(payload, toast_payload)
      } else if(payload.action === "reset_password") {
        app.resetPassword(payload, toast_payload)
      }
    },
    async registerUser(payload, toast_payload) {
      let app = this
      let register_result = false

      await app.$auth.register(payload).then(response => {
        register_result = true
      }, (error) => {
        /* todo: zu einer richtigen Nachricht ändern */
        toast_payload = {
          status: "failure",
          message: "Authentication experienced an error.",
          error: error
        }
        payload.result = false
        app.$eventBus.$emit("dclvf_login_result", payload)
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })

      if (!register_result) {
        return
      }

      app.loginUser(payload, toast_payload)
    },
    async loginUser(payload, toast_payload) {
      let app = this
      let login_result = false

      /* Melden Sie den Benutzer an */
      await app.$auth.login(payload).then(response => {
        login_result = true
      }, (error) => {
        /* todo: zu einer richtigen Nachricht ändern */
        toast_payload = {
          status: "failure",
          message: "Ein Fehler ist aufgetreten.",
          error: error
        }
        app.$eventBus.$emit("dclvf_login_result", payload)
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })

      if (!login_result) {
        return
      }

      /* Der Benutzer hat sich angemeldet, aber wir prüfen, ob wir tatsächlich ein Benutzerobjekt in unserer Sanctum-Middleware haben. Wir fordern eine Abrufroute an. */
      await app.$auth.user().then((response) => {
        let data = response.data
        let user = data.user
        app.$store.commit("setUser", user)
        if (user) {
          app.animateDashboard()
          /* ToDo: Konstante Standardseite nach Anmeldung festlegen? */
          setTimeout(function() {
            app.$router.push({ name: "dashboard"})

            setTimeout(function() {
              app.visibility.views = false
            }, 2000)

          }, 1000);
        } else {
          /* todo: zu einer richtigen Nachricht ändern */
          toast_payload = {
            status: "failure",
            message: "Authenticatable user couldn't be found",
            error: null,
          }
          payload.result = false
          app.$eventBus.$emit("dclvf_login_result", payload)
          app.$eventBus.$emit("dchvt_toast", toast_payload)
        }
      }, (error) => {
        toast_payload = {
          status: "failure",
          message: "Authentication experienced an error.",
          error: error
        }
        payload.result = false
        app.$eventBus.$emit("dclvf_login_result", payload)
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })

    },
    async requestToken(payload, toast_payload) {
      let app = this

      await app.$api({
        url: `api/user/token`,
        params: {
          email: payload.email
        },
        method: 'POST'
      }).then((response) => {
        toast_payload = {
          status: response.data.status,
          message: response.data.message,
          error: null
        }
        payload.result = true
        app.$eventBus.$emit("dclvf_login_result", payload)
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      }, (error) => {
        /* todo: zu einer richtigen Nachricht änderne */
        toast_payload = {
          status: "failure",
          message: "Error, could not request password token.",
          error: error
        }
        app.$eventBus.$emit("dclvf_login_result", payload)
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })
    },
    async resetPassword(payload, toast_payload) {
      let app = this

      await app.$api({
        url: `api/user/password`,
        data: {
          email: payload.email,
          password: payload.password,
          token: payload.token
        },
        method: 'POST'
      }).then((response) => {
        toast_payload = {
          status: response.data.status,
          message: response.data.message,
          error: null
        }
        payload.result = true
        app.$eventBus.$emit("dclvf_login_result", payload)
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      }, (error) => {
        /* todo: zu einer richtigen Nachricht ändern */
        toast_payload = {
          status: "failure",
          message: "Error, could not request password token.",
          error: error
        }
        app.$eventBus.$emit("dclvf_login_result", payload)
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      })
    },
    animateDashboard() {
      let app = this
      let payload = {
        status: true,
        backgroundColor: "#fafafa",
        action: "login"
      }
      app.$store.commit("setCircularPage", payload)
    },
    animate() {
      let app = this
      setTimeout(() => {
        app.visibility.views = true
      }, app.$constants.ANIMATION_DELAY * 4);
    }
  }
}
</script>

<style lang="scss" >
@import '~vuetify/src/styles/settings/_variables';

</style>
