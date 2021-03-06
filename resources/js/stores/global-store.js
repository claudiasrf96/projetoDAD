/*jshint esversion: 6 */

import Vue from 'vue';
import Vuex from 'vuex';
import VuexPersistence from 'vuex-persist'
 

Vue.use(Vuex);

const vuexLocalStorage = new VuexPersistence({
    key: 'vuex',
    storage: window.localStorage
  })

export default new Vuex.Store({
    state: { 
        token: "",
        user: null, 
    },  
    mutations: { 
        clearUserAndToken: (state) => {
            state.user = null;
            state.token = "";
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            axios.defaults.headers.common.Authorization = undefined;
        },
        clearUser: (state) => {
            state.user = null;
            localStorage.removeItem('user');
        },
        clearToken: (state) => {
            state.token = "";
            localStorage.removeItem('token');
            axios.defaults.headers.common.Authorization = undefined;
        },
        setUser: (state, user) => {
            state.user =  user;
            localStorage.setItem('user', JSON.stringify(user));
        },
        setToken: (state, token) => {
            state.token= token;
            localStorage.setItem('token', token);
            axios.defaults.headers.common.Authorization = "Bearer " + token;
            
        },
        loadTokenAndUserFromSession: (state) => {
            state.token = "";
            state.user = null;
            let token = localStorage.getItem('token');
            let user = localStorage.getItem('user');
            if (token) {
                 state.token = token;
                axios.defaults.headers.common.Authorization = "Bearer " + token;
            }
            if (user) {
                state.user = JSON.parse(user);
            }
        },
    },
    plugins: [vuexLocalStorage.plugin]
});


