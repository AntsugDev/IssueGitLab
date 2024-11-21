import {createStore} from "vuex";
import progress from "./modules/progress.js";
import snackbar from "./modules/snackbar.js";
import user from "./modules/user.js";
import config from "./modules/config.js";
import menu from "./modules/menu.js";

export const store = createStore({
    modules: {
        progress:progress,
        snackbar:snackbar,
        user:user,
        config:config,
        show:progress,
        menu: menu
    }
})
