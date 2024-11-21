import { createVuetify } from "vuetify";
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import 'vuetify/dist/vuetify.min.css';
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { it } from 'vuetify/locale';


const general = {
    dark: false,
    colors: {
        primary: '#248f50',
        secondary: '#fed91f',
        accent: '#82B1FF',
        error: '#FF5252',
        info: '#2196F3',
        success: '#4CAF50',
        warning: '#FFC107',
        memberYellow: '#fed91f',
        memberGreen: '#248f50',
        memberGrey: '#f0f0f0',
        coldirettiLightGrey:'#f0f0f0',
        coldirettiGrey :'#E6E6E6',
        coldirettiGreen:'#248F50',
        fontColorCai:"#696969",
        white:"#ffffff"
    },
}

export const vuetify = createVuetify({
    components,
    directives,
    theme:{
        defaultTheme: "general",
        themes:{ general }
    },
    lang:{
        locales:{ it },
        current: "it"
    },
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        },
    },
});
