import VueRouter from 'vue-router'
import store from "../store";

// Routes
const routes = [

    {
        path: '/',
        name: 'index',
        component: () => import("../../design/views/home/Index"),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import("../../design/views/login/Index"),
    },
    {
        path: "/dashboard/homepage",
        name: "dashboard",
        meta: {
            text: "Dashboard",
            icon: 'mdi-view-dashboard-outline',
            auth: true,
        },
        component: () => import("../../design/views/dashboard/Index"),
        children: [

        ]
    },
    {
        path: '/*',
        redirect: {
            name: "index",
            auth: true,
        }
    },

]

const router = new VueRouter({
    history: true,
    mode: 'history',
    routes,
})


router.beforeEach((to, from, next) => {
    let user = store.getters.authUser()

    if(!user) {
        if(to.name !== "login" || to.path !== "/login") {
            next({name: "login"})
        } else {
            next()
        }
        return
    }

    if((to.name === "index" || to.path === "/") || (to.name === "login" || to.path === "/login")) {
        next({name: "dashboard"})
    } else {
        next()
    }
});


export default router
