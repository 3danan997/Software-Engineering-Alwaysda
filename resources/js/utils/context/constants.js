// Wird für die Eingabevalidierung verwendet
const RULES = {
    required: value => !!value || 'Erforderlich.',
    email: value => {
        const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        return pattern.test(value) || 'E-mail ist nicht korrekt!'
    },
}

const VALUES = {
    ANIMATION_DELAY: 50
}

const PRIORITIES = ["Familie", "Freunde", "Arbeit", "Bekannte"];
const REMINDERS_FREQUENCIES = ["Jeden Tag", "Jede Woche", "Jeden Monat", "Selbst eingeben..."];
const REMINDERS_TYPES = ["Schreiben", "Treffen", "Telefonieren", "Sonst"];

const PATHS = {
    url: process.env.MIX_APP_URL,
    content: "content/",
    avatars: "content/avatars/",
    settings: "content/settings/",
    login: "content/login/",
    brand: "content/brand/",
}

export default {
    PATHS,
    PRIORITIES,
    REMINDERS_FREQUENCIES,
    REMINDERS_TYPES,
    RULES,
    VALUES
}
