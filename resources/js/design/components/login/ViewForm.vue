<template>
  <v-col cols="12" md="12" lg="6" class="login-view-form">
    <!-- Wir verwenden ein Formular, um die Eingabe zu validieren -->

    <v-form ref="form" class="row ma-0 justify-content-start fill-height align-content-center">


      <!-- Weiße Karte wird angezeigt, sie wird animiert, wenn das Zurücksetzen des Passworts aufgerufen wird -->
      <div class="login-view-form-circular-view" v-if="visibility.circular_view" :class="{'login-view-form-circular-view-active': visibility.circular_animation_view}"></div>

      <!-- Titel starten -->
      <v-col cols="12" md="12" lg="12" xl="12"  v-show="visibility.views" :class="{'fade-in-up': visibility.text_title, 'text-center': $vuetify.breakpoint.mdAndDown}"
             class="login-view-form-title view-hidden">

      </v-col>
      <!-- Titel beenden -->


      <v-col cols="12" v-show="visibility.views" :class="{'fade-in-up': visibility.text_title_top}">
        <span class="font-weight-medium text-font-body text-color-level-2 text-uppercase">{{getText("text_title_top")}}</span>
      </v-col>

      <v-col cols="12" class="py-1" v-show="visibility.views" :class="{'fade-in-up': visibility.text_title}">
        <span class="font-weight-bold h2 mb-0 text-color-level-0">{{getText('text_title')}}</span><span class="font-weight-bold h1 mb-0 text-primary">.</span>
      </v-col>

      <v-col cols="12" class="mb-5" v-show="visibility.views" :class="{'fade-in-up': visibility.text_title_bottom}">
        <template v-if="item_form.state === 'register_user'">
          <span class="font-weight-medium text-font-body text-color-level-2">Bist Du bereits ein Mitglied? </span><span class="font-weight-semibold text-font-body text-primary clickable" @click="switchView('login_user')">Einloggen</span>
        </template>

        <template v-if="item_form.state === 'login_user'">
          <span class="font-weight-medium text-font-body text-color-level-2">Noch kein Konto? </span><span class="font-weight-semibold text-font-body text-primary clickable" @click="switchView('register_user')">Konto erstellen!</span>
        </template>
      </v-col>

      <v-col cols="12" md="6" class="my-0 py-0" v-if="item_form.state === 'register_user'" v-show="visibility.views" :class="{'fade-in-up': visibility.input_first_name}">
        <v-text-field v-model="item_form.inputFirstName" type='text' :rules="item_form.state === 'register_user' ?[$constants.RULES.required] : []"
                      label="Vorname" flat solo class="text-field"
                      append-icon="mdi-account-circle-outline"></v-text-field>
      </v-col>

      <v-col cols="12" md="6" class="my-0 py-0" v-if="item_form.state === 'register_user'" v-show="visibility.views" :class="{'fade-in-up': visibility.input_last_name}">
      <v-text-field v-model="item_form.inputLastName" type='text' :rules="item_form.state === 'register_user' ? [$constants.RULES.required] : []"
                    label="Nachname" flat solo class="text-field"
                    append-icon="mdi-account-circle-outline"></v-text-field>
      </v-col>

      <v-col cols="12" md="12" lg="12" xl="12" class="my-0 py-0" v-show="visibility.views" :class="{'fade-in-up': visibility.input_email}">
        <v-text-field v-model="item_form.inputEmail" type='email' :rules="[$constants.RULES.email, $constants.RULES.required]"
                      label="E-mail Adresse" flat solo class="text-field"
                      append-icon="mdi-email-outline"></v-text-field>
      </v-col>

      <v-col cols="12" md="12" lg="12" xl="12" class="my-0 py-0" v-if="item_form.state === 'reset_password' || item_form.state === 'request_token'" v-show="visibility.views" :class="{'fade-in-up': visibility.input_token}">
        <v-text-field v-model="item_form.inputToken" :rules="(item_form.state === 'reset_password') ? [$constants.RULES.required] : []"
                      type="text" label="Token" flat
                      solo class="text-field" :class="{'text-field-disabled':item_form.state === 'request_token' }" :disabled="item_form.state === 'request_token'"
                      append-icon="mdi-qrcode"></v-text-field>
      </v-col>

      <v-col cols="12" md="12" lg="12" xl="12" class="my-0 py-0" v-show="visibility.views" :class="{'fade-in-up': visibility.input_password}">
        <v-text-field v-model="item_form.inputPassword" :rules="(item_form.state !== 'request_token') ? [$constants.RULES.required] : []"
                      :type="item_form.passwordShow ? 'text' : 'password'" label="Passwort" flat
                      solo class="text-field" :class="{'text-field-disabled':item_form.state === 'request_token' }" :disabled="item_form.state === 'request_token'"
                      @click:append="item_form.passwordShow = !item_form.passwordShow"
                      :append-icon="item_form.passwordShow ? 'mdi-lock-open-outline' : 'mdi-lock-outline'"></v-text-field>
      </v-col>

      <v-col cols="12" md="12" lg="12" xl="12" class="my-0 py-0" v-show="visibility.views" v-if="item_form.state === 'register_user' || item_form.state === 'reset_password'" :class="{'fade-in-up': visibility.input_password}">
        <v-text-field v-model="item_form.inputPassword2" :rules="[$constants.RULES.required]"
                      :type="item_form.passwordShow ? 'text' : 'password'" label="Passwort wiedereingeben" flat
                      solo class="text-field" :class="{'text-field-disabled':item_form.state === 'request_token' }" :disabled="item_form.state === 'request_token'"
                      @click:append="item_form.passwordShow = !item_form.passwordShow"
                      :append-icon="item_form.passwordShow ? 'mdi-lock-open-outline' : 'mdi-lock-outline'"></v-text-field>
      </v-col>


      <v-col class="flex-grow-0 align-self-center min-width-fit-content"  v-show="visibility.views" :class="{'fade-in-up': visibility.text_optional}" v-if="item_form.state === 'login_user' || item_form.state === 'request_token' || item_form.state === 'reset_password'">
        <v-icon class="text-color-level-2" style="margin-bottom: 2px">mdi-arrow-left-thin</v-icon><span class="clickable text-color-level-2 ml-1 font-weight-medium text-font-body" @click="switchView(item_form.state === 'login_user' ? 'request_token' : 'login_user')"
      >{{item_form.state === "login_user" ? "Passwort vergessen?" : "Einloggen"}}</span>
      </v-col>

      <!-- Anmeldung starten -->
      <v-col class="flex-grow-1 align-self-center min-width-fit-content" v-show="visibility.views" :class="{'fade-in-up': visibility.button_login, 'text-right': $vuetify.breakpoint.mdAndUp || (!$vuetify.breakpoint.mdAndUp && item_form.state !== 'register_user'), 'text-center': !$vuetify.breakpoint.mdAndUp && (item_form.state === 'register_user')}">
        <v-btn large depressed id="view_form_primary_button" color="primary" class="font-weight-medium text-none login-button"
               @click="submitRequest()">{{getText('button')}}
          <v-progress-circular class="mx-2" indeterminate color="white" size="20" width="3"
                               v-show="states.signingIn"></v-progress-circular>
          <span></span>
        </v-btn>
      </v-col>
      <!-- Anmeldung beenden -->

      <!-- Starten Sie das untere Logo -->
      <v-col cols="12" class="text-center login-view-form-logo" v-show="visibility.logo_bottom" :class="{'fade-in': visibility.logo_bottom}">
        <v-img :src="$constants.PATHS.url + $constants.PATHS.brand + 'logo_wide.png'" class="fade-in"
               max-height="30px" contain></v-img>
      </v-col>
      <!-- Beenden Sie das untere Logo -->
    </v-form>
    <!-- Formular beenden -->
  </v-col>
</template>

<script>
export default {
  data() {
    return {
      item_form: {
        inputFirstName: null,
        inputLastName: null,
        inputEmail: null,
        inputPassword: null,
        inputPassword2: null,
        inputToken: null,
        passwordShow: false,
        state: "login_user"
      },
      states: {
        isRequesting: false,
      },
      /* Wird für Animationszwecke verwendet. */
      visibility: {
        views: true,
        circular_view: false,
        circular_animation_view: false,
        text_title_top: false,
        text_title: false,
        text_title_bottom: false,
        input_first_name: false,
        input_last_name: false,
        input_email: false,
        input_token: false,
        input_password: false,
        text_optional: false,
        button_login: false,
        logo_bottom: false,
      },
      /* Wird verwendet, um Doppelungen durch eine bestimmte Aufgabe zu vermeiden */
      timers: {
        animation: false,
      },
    }
  },
  created() {
    let app = this
    /* Senden Sie an unseren Index, der Index verwaltet die eigentliche Logik. */
    app.$eventBus.$on("dclvf_login_result", (payload) => app.receiveLoginResult(payload))

    app.animate()
  },
  mounted(){
    let app = this
    document.onreadystatechange = () => {
      if (document.readyState === "complete") {
        /* Senden Sie Mitte X, Y der primären (Anmelde-)Schaltfläche an CircularTransition */
        const element = document.getElementById("view_form_primary_button")
        const clientRect = element.getBoundingClientRect();

        const centerX = clientRect.left + document.body.scrollLeft + (clientRect.width / 2)
        const centerY = clientRect.top + document.body.scrollTop + (clientRect.height / 2)

        const payload = {x: centerX, y: centerY, action: "login"}
        app.$eventBus.$emit('dchvce_params', payload)
      }
    }
  },
  beforeDestroy() {
    let app = this
    app.$eventBus.$off("dclvf_login_result")
  },
  methods: {
    getText(view) {
      let app = this
      if(app.item_form.state === "register_user") {
        if(view === "button")
          return "Konto erstellen"
        else if (view === "text_title")
          return "Neues Konto erstellen"
        else if (view === "text_title_top")
          return "Kostenlos starten"
      } else if(app.item_form.state === "login_user") {
        if(view === "button")
          return "Einloggen"
        else if (view === "text_title")
              return "Einloggen"
        else if (view === "text_title_top")
          return "Willkommen zurück"
      } else if(app.item_form.state === "request_token") {
        if(view === "button")
          return "Token anfragen"
        else if (view === "text_title")
          return "Passwort Token"
        else if (view === "text_title_top")
          return "Email anfragen"
      } else if(app.item_form.state === "reset_password") {
        if(view === "button")
          return "Passwort zurücksetzen"
        else if (view === "text_title")
          return "Neues Passwort"
        else if (view === "text_title_top")
          return "Passwort eingeben"
      }

      return "Keine Ahnung"
    },
    submitRequest() {
      let app = this

      if(app.states.isRequesting){
        let toast_payload = {
          status: "pending",
          // todo: zum eigentlichen Satz wechseln
          message: "Eine Anfrage wird schon bearbetet.",
          error: null
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
        return
      }



      if(app.$refs.form.validate()) {
        let payload = {
          action: app.item_form.state
        }

        if(app.item_form.state === "register_user") {

          if(app.item_form.inputPassword !== app.item_form.inputPassword2) {
            app.$eventBus.$emit("dchvt_toast", {
              status: "failure",
              message: "Die Passwörter stimmt nicht überein!",
              error: null
            })
            return
          }

          payload.first_name = app.item_form.inputFirstName
          payload.last_name = app.item_form.inputLastName

          payload.email = app.item_form.inputEmail
          payload.password = app.item_form.inputPassword
        } else if(app.item_form.state === "login_user") {
          payload.email = app.item_form.inputEmail
          payload.password = app.item_form.inputPassword
        } else if(app.item_form.state === "request_token") {
          payload.email = app.item_form.inputEmail
        } else if(app.item_form.state === "reset_password") {
          if(app.item_form.inputPassword !== app.item_form.inputPassword2) {
            app.$eventBus.$emit("dchvt_toast", {
              status: "failure",
              message: "Die Passwörter stimmt nicht überein!",
              error: null
            })
            return
          }

          payload.email = app.item_form.inputEmail
          payload.token = app.item_form.inputToken
          payload.password = app.item_form.inputPassword
        }

        app.$refs.form.resetValidation();
        app.$eventBus.$emit("dvl_login", payload)
      } else {
        let toast_payload = {
          status: "pending",
          message: "Bearbeiten Sie bitte die Eingaben!",
          error: null
        }
        app.$eventBus.$emit("dchvt_toast", toast_payload)
      }
    },
    receiveLoginResult(payload) {
      let app = this

      /* Setzen Sie den Fortschrittskreis zurück, wir haben eine Antwort erhalten. Wir prüfen jetzt den nächsten Schritt. */
      app.states.isRequesting = false

      if(payload.action === "request_token") {
        if(payload.result) app.item_form.state = "reset_password"
      } else if(payload.action === "reset_password") {
        if(payload.result) app.switchView("login_user")
      }
    },
    animate() {
      let app = this
      /* Verzögerung zwischen den Sichtbarkeitsvariablen */
      setTimeout(() => {
        app.visibility.text_title_top = !app.visibility.text_title_top
      }, 50);
      setTimeout(() => {
        app.visibility.text_title = !app.visibility.text_title
      }, 75);
      setTimeout(() => {
        app.visibility.text_title_bottom = !app.visibility.text_title_bottom
      }, 100);

      setTimeout(() => {
        app.visibility.input_first_name = !app.visibility.input_first_name
      }, 125);
      setTimeout(() => {
        app.visibility.input_last_name = !app.visibility.input_last_name
      }, 125);

      setTimeout(() => {
        app.visibility.input_email = !app.visibility.input_email
      }, 150);

      setTimeout(() => {
        app.visibility.input_token = !app.visibility.input_token
      }, 175);

      setTimeout(() => {
        app.visibility.input_password = !app.visibility.input_password
      }, 175);

      setTimeout(() => {
        app.visibility.text_optional = !app.visibility.text_optional
      }, 200);

      setTimeout(() => {
        app.visibility.button_login = !app.visibility.button_login
      }, 200);

      setTimeout(() => {
        app.visibility.logo_bottom = !app.visibility.logo_bottom
      }, 225);
    },
    switchView(targetView){
      let app = this

      if(app.states.isRequesting) {
        return
      }

      /* Vermeiden Sie die Duplizierung der Animation, wenn Sie auf Spam klicken */
      if(app.timers.animation) {
        return
      }

      app.$refs.form.resetValidation()

      app.timers.animation = true

      app.visibility.circular_view = true

      setTimeout(() => {
        app.visibility.circular_animation_view = true
      }, 50)

      /* Setzen Sie die Validierung des Formulars zurück, damit wir vermeiden, dass beim Wechseln der Ansicht rote Regeln angezeigt werden. */
      app.$refs.form.resetValidation();

      setTimeout(function() {
        /* Rundansicht wechseln. */
        app.switchComponentsVisibility()

        app.visibility.circular_view = false
        app.visibility.circular_animation_view = false
        app.timers.animation = false
        app.item_form.state = targetView
      }, app.$vuetify.breakpoint.mdAndDown ? 700 : 500);

      setTimeout(function() {
        app.$refs.form.reset()
        app.animate()
        setTimeout(() => {
          app.$refs.form.resetValidation()
        }, 50)
      }, app.$vuetify.breakpoint.mdAndDown ? 600 : 350);
    },
    switchComponentsVisibility(){
      let app = this
      /* Schalten Sie die Werte von wahr auf falsch und von falsch auf wahr um. */
      app.visibility.views = !app.visibility.views

      app.visibility.text_title_top = !app.visibility.text_title_top
      app.visibility.text_title = !app.visibility.text_title
      app.visibility.text_title_bottom = !app.visibility.text_title_bottom

      app.visibility.input_first_name = !app.visibility.input_first_name
      app.visibility.input_last_name = !app.visibility.input_last_name

      app.visibility.input_email = !app.visibility.input_email
      app.visibility.input_token = !app.visibility.input_token
      app.visibility.input_password = !app.visibility.input_password

      app.visibility.text_optional = !app.visibility.text_optional

      app.visibility.button_login = !app.visibility.button_login
      app.visibility.logo_bottom = !app.visibility.logo_bottom

      setTimeout(() => {
        app.visibility.views = !app.visibility.views
      }, 200)
    }
  }
}
</script>

<style lang="scss" scoped>



.login-view-form-background-blob {
  //background-image: url("/content/images/login/view_login_form_background_blob.svg") !important;
  -webkit-background-size: cover !important;
  -moz-background-size: cover !important;
  -o-background-size: cover !important;
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
}

</style>
