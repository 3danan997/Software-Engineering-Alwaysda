import axios from "axios";
import store from "./../store";
import constants from "./constants"
import Cookies from 'js-cookie';
import MomentTimezone from 'moment-timezone'

const APIClient = axios.create({
    baseURL: constants.PATHS.url,
    withCredentials: true, // benötigt, zum benutzten des CRSF Token
});

/**
 * @author: Mohammad Hammado 15.07.2022, 09:45
 * @version 1.0
 * @since 1.0
 * @description   Hört auf die Antworten von Axios. Checkt, ob es 401 oder 419 ist, weil es bei der Authentifizierung
 * dann zu einem Problem gekommen ist, weshalb wir den Benutzer aus Sicherheitsgründen eine Meldung schicken.
 */
APIClient.interceptors.request.use(function (config) {
    // Tue etwas bevor die Anfrage versendet wird
    document.cookie = "XSRF-TOKEN" +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';

    return config;
}, function (error) {
    // Tue etwas mit dem Anfragefehler (request error)
    return Promise.reject(error);
});

APIClient.interceptors.response.use(
    (response) => {
        return response;
    },
    function(error) {
        if (error.response && [401, 419].includes(error.response.status)) {
            store.dispatch("logout", true)
        }
        return Promise.reject(error);
    }
);

/**
 * @author: Mohammad Hammado 15.07.2022, 09:45
 * @version 1.0
 * @since 1.0
 * @description     Exportiert die API's konstant zusammen mit drei Hauptfunktionen zum Navigieren in der API.
 */
export default {
    APIClient,
    async login(payload) {
        document.cookie = "XSRF-TOKEN" +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';

        await APIClient.get("/sanctum/csrf-cookie")
        return APIClient.post("/api/user/login", payload);
    },

    async register(payload) {
        return APIClient.post("/api/user/register", payload);
    },

    async logout() {
        /* löschen des Cookies */
        document.cookie = "XSRF-TOKEN" +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';

        await APIClient.get("/sanctum/csrf-cookie");
        return APIClient.post("/api/user/logout");
    },

    user() {
        return APIClient.get("/api/user/fetch");
    }
};
