<template>
  <v-app>
    <v-main>
      <ViewLoadingView v-if="views_payload.should_wait"></ViewLoadingView>
      <router-view v-else></router-view>
      <ViewCircularExpand></ViewCircularExpand>
      <ViewToastbar></ViewToastbar>
    </v-main>
  </v-app>
</template>

<script>
import ViewToastbar from "./../../components/home/ViewToastbar"
import ViewCircularExpand from "./../../components/home/ViewCircularExpand"
import ViewLoadingView from "../../components/home/ViewLoadingView";

export default {
  components: {
    ViewLoadingView,
    ViewToastbar,
    ViewCircularExpand,
  },
  data() {
    return {
      views_payload: {
        should_wait: true,
      }
    }
  },
  created() {
    let app = this

    /* Wenn unser Benutzer in unserem Vuex null ist und er die Route "Login" nicht besucht oder sich bereits auf dieser Route befindet, leiten Sie ihn zur Route "Login" um. */
    /* Führen Sie andernfalls eine Authentifizierungsprüfung im Hintergrund durch*/
    if (app.$store.getters.authUser()) {
      app.checkAuthentication()
    } else {
      app.views_payload.should_wait = false
    }
  },
  methods: {
    async checkAuthentication() {
      let app = this
      let toast_payload = {}
      /* Der Benutzer ist in unserem Vuex gültig. Lassen Sie uns einen Benutzerabruf von unserer API anfordern. */
      if (app.$store.getters.authUser()) {
        app.$store.commit("setAuthLoggingOut", true)
        await app.$auth.user().then((response) => {
          let data = response.data
          let user = data.user
          if(user) {
            app.$store.commit("setUser", user)
          } else {
            app.$store.dispatch("logout")
          }
        }, (error) => {
          toast_payload = {
            status: "failure",
            message: "Couldn't validate user",
            error: error,
          }

          app.$store.commit("setUser", null)
          // app.$store.dispatch("logout")

          app.$router.push({
            name: "login"
          })
        }).finally(() => {
          setTimeout(() => {
            app.views_payload.should_wait = false
          }, 250)
        })
      } else {
        /* Wenn ein Fehler aufgetreten ist und der Benutzer vor der Ausführung dieser Funktion gelöscht wurde, leiten wir einfach zur Anmeldung um */
        if (app.$route.path !== "/login") {
          app.$router.push({
            name: "login"
          })

          app.views_payload.should_wait = false

          console.log(app.views_payload.should_wait)
        }
      }
    },
  }
}
</script>

<style lang="scss">
@import '~vuetify/src/styles/settings/_variables';

$body-font-family: 'Nunito', sans-serif;

.v-application {
  .body-1,
  .body-2,
  .caption,
  .display-1,
  .display-2,
  .display-3,
  .display-4,
  .h1,
  .h2,
  .h3,
  .h4,
  .h5,
  .headline,
  .overline,
  .subtitle-1,
  .subtitle-2,
  .title,
  .v-label {
    font-family: $body-font-family !important;
  }
}

.v-application {
  font-family: $body-font-family !important;
}



</style>
