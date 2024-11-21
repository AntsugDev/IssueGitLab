import './bootstrap';
import {createApp} from "vue";
import App from "./App.vue";
import {router} from "./router/index.js";
import {vuetify} from "./theme/index.js";
import {store} from "./store/index.js";
import {getParamsToken} from "./utils/index.js";
import {interceptorResponse} from "./api/index.js";
const app = createApp(App);
app.use(router);
app.use(vuetify);
app.use(store);

router.beforeEach((to,from,next) => {
    const auth = store.getters['user/getToken'];
    if (to.matched.some(record => record.meta.requiresNoAuth)) {
            next();
    }
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if(!auth)
            next({name:'Choose'})
        else{
            return getParamsToken(auth,to,next)
        }
    }

})

interceptorResponse;


app.mount("#app");
