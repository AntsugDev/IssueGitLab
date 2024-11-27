import dayjs from "dayjs";
import {store} from "../store/index.js";
import {useRoute, useRouter} from "vue-router";

export const getParamsToken = (inStoreToken,to,next) => {
    const expiredToken = inStoreToken?.expires_at;
    if (!expiredToken) {
        next({ name: 'Choose',query: { error: btoa('Token not valid'),logout:true }});
        return;
    }
    const expired = dayjs(expiredToken);
    const now = dayjs();
    console.log('[DIFF]:'+(expired.diff(now, 'minute')))
    if (parseInt(expired.diff(now, 'minute')) > 0) {
        next();
    } else {
        next({
            name: 'Choose',
            query: { error: btoa('Session expired'),logout:true }
        });
        return;
    }
}

