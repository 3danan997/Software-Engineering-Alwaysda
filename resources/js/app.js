require('./bootstrap');

/* Importiert Bibliotheken */
import Vue from 'vue'
import VueRouter from 'vue-router'
import { ContentLoader } from "vue-content-loader"
import MomentTimezone from 'moment-timezone'
import VueTimeago from 'vue-timeago'
import VueClipboard from 'vue-clipboard2'
import VueMask from 'v-mask'
import Notifications from 'vue-notification'

/* Importiert Globale Komponenten */
import ViewHome from "./design/views/home/Index"

/* Importiert die helper */
import api from "./utils/context/api"
import store from "./utils/store";
import router from "./utils/router"
import helper from "./utils/context/helper"
import constants from "./utils/context/constants"
import vuetify from "./utils/theme/vuetify"


/* die Lodash helpers */
import orderBy from 'lodash.orderBy';
import values from 'lodash.values';
import map from 'lodash.map';
import cloneDeep from 'lodash.cloneDeep';
import uniqBy from 'lodash.uniqBy';
import pluck from 'lodash.pluck';

/* Setzt die lodash konstant für den globalen Zugang */
const lodash = {
    orderBy: orderBy,
    values: values,
    cloneDeep: cloneDeep,
    uniqBy: uniqBy,
    map: map,
    pluck: pluck
}

/* Benutzt Bibliotheken */
Vue.use(VueMask);
Vue.use(Notifications)
Vue.use(VueRouter)
Vue.use(VueTimeago, {
    name: 'Timeago',
    locale: 'en',
})
Vue.use(VueClipboard)

/* installation Globaler Konstanten */
Vue.component('home', ViewHome)
Vue.component('content-loader', ContentLoader)

/* Setzt globale Variablen */
window.Vue = Vue
Vue.prototype.$eventBus = new Vue()
//Vue.prototype.$pusher = pusher
Vue.prototype.$auth = api;
Vue.prototype.$api = api.APIClient;
Vue.prototype.$lodash = lodash
Vue.prototype.$helper = helper
Vue.prototype.$constants = constants
Vue.prototype.$moment = MomentTimezone
Vue.router = router

/* Installation von app */
const app = new Vue({
    vuetify: vuetify,
    el: '#app',
    router,
    store
});
