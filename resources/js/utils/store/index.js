import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from 'vuex-persistedstate'

import * as auth from "./modules/Auth";
import * as utils from "./modules/Utils";

Vue.use(Vuex);

export default new Vuex.Store({
  strict: true,
  plugins: [createPersistedState()],
  modules: {
    auth,
    utils,
  },
});
