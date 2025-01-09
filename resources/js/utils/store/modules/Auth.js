import router from "../../router";
import api from '../../context/api'
import constants from "../../context/constants";

/**
 * @author: Mohammad Hammado 15.07.2022, 09:45
 * @version 1.0
 * @since 1.0
 * @description  privater Vuex state Objekt
 */
export const state = {
    user: null, preferences: {

    }, logging_out: false
};

/**
 * @author: Mohammad Hammado 15.07.2022, 09:45
 * @version 1.0
 * @since 1.0
 * @description    setzt mutations funktion via Vuex aus.
 */
export const mutations = {
    setUser(state, user) {
        state.user = user
    },
    setAuthLoggingOut(state, logging_out) {
        state.logging_out = logging_out
    },
};
/**
 * @author: Mohammad Hammado 15.07.2022, 09:45
 * @version 1.0
 * @since 1.0
 */
export const actions = {
    /**
     * @author: Mohammad Hammado 15.07.2022, 09:45
     * @version 1.0
     * @since 1.0
     * @description Wenden Sie eine Abmeldeaktion an, es führt eine Abmeldung zum Vuex-Status durch und ruft die API auf.
     * @param state, state Instanz von Vuex.
     * @param commit, commit Instanz von Vuex.
     * @param dispatch, dispatch Instanz von Vuex.
     * @return string;
     */
    logout({state, commit, dispatch, getters}, ignoreRequest) {
        // verhindert eine endlos-schleife (spammed requests).
        if (state.logging_out) {
            return
        }

        let payload = {
            status: true,
            action: "logout"
        }

        // verhindert eine endlos-schleife. Setz den checkup auf true
        commit("setAuthLoggingOut", true)

        /* Logout "locally" danach über Abfrage */
        // Aktuelle Route ist kein Login, wenden Sie den Übergang an.

        setTimeout(() => {
            commit("setUser", null)
        }, 600)

        if (router.currentRoute.name !== "login") {
            commit("setCircularPage", payload)
            setTimeout(function () {
                commit("setAuthLoggingOut", false)
                if(!state.logging_out) {
                    router.push({
                        name: "login"
                    });
                }
            }, 1000);
        }

        if(!ignoreRequest)
            return api.logout().then(() => {
            }).catch((error) => {
                /* todo: log error */
            }).finally(() => {
                setTimeout(() => {
                    commit("setAuthLoggingOut", false)
                }, 1000)
            })
        else {
            setTimeout(() => {
                commit("setAuthLoggingOut", false)
            }, 1000)
            return null
        }
    },

    /**
     * @author: Mohammad Hammado 15.07.2022, 09:50
     * @version 1.0
     * @since 1.0
     * @description Fordern Sie einen Abruf der Benutzerdetails an. Dies könnte nach einer Profilaktualisierung angefordert werden.
     * @param state, state Instanz von Vuex.
     * @param commit, commit Instanz von Vuex.
     * @param dispatch, dispatch Instanz von Vuex.
     * @return string;
     */
    refresh_fetch({
                      state, commit, dispatch
                  }) {

        if (!state.user) {
            dispatch("logout")
            return
        }
        return api.user().then((response) => {
            // versendet("refresh_auth", response.data)
        }).catch((error) => {

        })
    },

};

/**
 * @author: Mohammad Hammado 15.07.2022, 09:45
 * @version 1.0
 * @since 1.0
 * @description    state getters. Wir verwenden es in einer anonymen Funktion, um eine Aktualisierung jeder Getter-Anfrage zu erzwingen.
 * Andernfalls wird es zwischengespeichert
 */
export const getters = {
    authUser(state) {
        return function () {
            return state.user;
        }
    },
};
