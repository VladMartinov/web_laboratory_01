import axios from 'axios';

export default {
    namespaced: true,
    state:{
        authenticated: false,
        user: {},
    },
    getters:{
        authenticated: s => s.authenticated,
        user: s => s.user,
    },
    mutations:{
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },
        SET_USER (state, value) {
            state.user = value
        }
    },
    actions:{
        login({commit}){
            return axios.get('api/user').then(({data}) => {
                commit('SET_USER', data);
                commit('SET_AUTHENTICATED', true);
            }).catch((error) => {
                commit('SET_USER', {});
                commit('SET_AUTHENTICATED', false);
            })
        },
        logout({commit}){
            commit('SET_USER', {});
            commit('SET_AUTHENTICATED', false);
        }
    }
}
