const state = () => ({
    data:
        {
            drawer:true,
            mini:false,
            routebar:false,
            prj: 473
        }
});

const getters = {
    get:function (state){
        return state.data;
    },
    getBar:function (state){
        return state.data.routebar
    },
    getProject:function(state){
        return state.data.prj
    }

};
const actions = {};
const mutations = {
    update(state, attributes) {
        for (let prop in attributes) {
            if (Object.prototype.hasOwnProperty.call(attributes, prop)) {
                state.data[prop] = attributes[prop]
            }
        }
    },
    changeMini(state){
        if(state.data.mini)
            state.data.mini = false;
        else
            state.data.mini = true;
    },
    changeRouteBar(state){
        state.data.routebar = !state.data.routebar
    },

};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
