export const state = {
    transition: {
        circular_page: null
    },
    settings: {
        sidebar: "no_bar",
        bottombar: "bottom_margin",
    }
};

export const mutations = {
    setCircularPage(state, payload) {
        state.transition.circular_page = payload
    },
    setSidebar(state, bar) {
        state.settings.sidebar = bar
    },
    setBottombar(state, bar) {
        state.settings.bottombar = bar
    }
};

export const actions = {};

export const getters = {
    circularPage(state) {
        return state.transition.circular_page
    },
    sideBar(state) {
        return function () {
            return state.settings.sidebar;
        }
    },
    bottomBar(state) {
        return function () {
            return state.settings.bottombar;
        }
    },
};
