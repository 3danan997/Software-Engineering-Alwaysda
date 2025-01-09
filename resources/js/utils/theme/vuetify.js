/* Importiert die main Bibliothek und benutzt die Vuetify */
import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify);

/* Wir benutzten mdi (Material Design Icons) als icons Bibliothek und setzten ein Standard Farbschema, welches wir später auch ändern können. */
const vuetifyOptions = {
    rtl: false,
    icons: {
        iconfont: 'mdi',
        values: {

        }
    },
    theme: {
        dark: true,
        options: {
            customProperties: true
        },
        themes: {
            light: {
                primary: '#3a83e7',
                accent: '#e58d09',
            },
            dark: {
                primary: '#3a83e7',
                accent: '#e58d09',
            },
        }
    }
}

export default new Vuetify(vuetifyOptions)
