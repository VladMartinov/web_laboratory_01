import { createRouter, createWebHistory } from "vue-router";
import store from "@/store/index.js";

/* Guest Component */
const Login = () => import('../view/Login.vue')
const Register = () => import('../view/Register.vue')
/* Guest Component */

/* Layouts */
const DefaultLayout = () => import("../components/layouts/Default.vue");
/* Layouts */

/* Authenticated Component */
const LoadFile = () => import( "../view/LoadFile.vue");
const DataInfo = () => import("@/view/DataInfo.vue");
/* Authenticated Component */

const routes = [
    {
        name: "login",
        path: "/login",
        component: Login,
        meta: {
            middleware: "guest",
            title: `Login`
        }
    },
    {
        name: "register",
        path: "/register",
        component: Register,
        meta: {
            middleware: "guest",
            title: `Register`
        }
    },
    {
        path: "/",
        component: DefaultLayout,
        meta: {
            middleware: "auth"
        },
        redirect: "/load-file",
        children: [
            {
                name: "load-file",
                path: '/load-file',
                component: LoadFile,
                meta: {
                    title: `LoadFile`,
                }
            },
            {
                name: "data-info",
                path: "/data-info",
                component: DataInfo,
                meta: {
                    title: `DataInfo`,
                },
            },
        ]
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    document.title = to.meta.title

    await store.dispatch('auth/login');

    if (to.meta.middleware == "guest") {
        next()
    } else {
        if (store.state.auth.authenticated) {
            next()
        } else {
            next({name: "login"})
        }
    }
})

export default router;
