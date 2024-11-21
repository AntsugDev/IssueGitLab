import dayjs from "dayjs";

const state = () => ({
    data:
        {
            user: null,
            auth: null,
            jwt: null,
            gitLab:null

        },
    /**
     *todo @@@
     * token:null,
     * refresh_token:null,
     * expired:null,
     * login:null,
     *
     */
});

const getters = {
    getUser:function(state){return state.data.user},
    getIsAdmin:function (state){
        //todo @@ check role admin/root
        return true;
    },
    getId:function (state){
        if(state.data.user !== null && state.data.user.hasOwnProperty('id'))
            return state.data.user.id
        return null;
    },
    getToken:function(state){
        return state.data.auth
    },
    // getInit:function (state){
    //     let now = dayjs();
    //     if(state.data.login !== null){
    //         return parseInt(state.data.login.diff(now,'hours')) > 2
    //     }
    // },
    getRefreshToken:function (state){
        if(state.data.auth !== null && state.data.auth.hasOwnProperty('refresh_token'))
            return state.data.auth.refresh_token
        else
            return null;
    },
    getExpires:function (state){
        if(state.data.auth !== null && state.data.auth.hasOwnProperty('expired'))
            return state.data.auth.expired
        else
            return null;
    },
    getIsRoot:function (state){
        if(
            state.data.user.hasOwnProperty('roles')
            && state.data.user.roles !== null
            && state.data.user.roles.hasOwnProperty('label')
        ) {
            return state.data.user.roles.label.toString().toUpperCase() === 'ROOT';
        }
        return undefined;
    },
    getJwt:function (state){
        return state.data.jwt
    },
    getGitLab :function (state){
        return state.data.gitLab
    }

};
const actions = {};
const mutations = {
    update (state,payload){
        state.data.user = payload
    },
    setAccesToken (state,payload){
        state.data.auth = payload
    },
    clear(state){
        state.data.user = null;
        state.data.auth = null;
    },
    updateGitLab(state,token){
        state.data.gitLab = token
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
