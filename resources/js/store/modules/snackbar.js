const state = () => ({
    data:
        {
            show: false,
            text: null,
            color: null,
            button: false,
            preicon: null
        }
});

const getters = {
    get:function (state){
        return state.data;
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
    clear(state){
        state.data =  {
            show: false,
            text: null,
            color: null,
            button: false,
            preicon: null
        }
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
