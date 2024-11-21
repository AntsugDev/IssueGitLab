const state = () => ({
    data:{
        menu: []
    },
});

const getters = {
    getShow:function (state){
        return state.data.menu;
    }
};
const actions = {};
const mutations = {
    update(state,menu) {
        state.data.menu = menu
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
