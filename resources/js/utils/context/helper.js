/**
 * @author: Mohammad Hammado 15.07.2022, 09:10
 * @version 1.0
 * @since 1.0
 * @description   glieder UTC-Zeit in dei Lokale Zeit
 * @param moment, Instanz der moment Bibliothek.
 * @param time, vergangene Zeit, im Zeitstempel Format
 * @return object;
 */
function getLocalTime(moment, time) {
    return moment.utc(time).local()
}

export default {
    getLocalTime,
}
