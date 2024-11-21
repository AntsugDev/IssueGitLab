const state = () => ({
    data:{
        show: false
    },
});

const getters = {
    getShow:function (state){
        return state.data.show;
    }
};
const actions = {};
const mutations = {
    update(state,futureState) {
        state.data.show = futureState
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
