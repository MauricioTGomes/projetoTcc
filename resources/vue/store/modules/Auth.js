import storage from './services'

const state = {
    check: false,
    user: storage.getObject('user'),
    token: null
}

const mutations = {
    AUTHENTICATED(state) {
        state.check = true
    },

    SET_USER_TOKEN(state, data) {
        state.user = data.user
        state.check = true
        state.token = data.access_token
    }
}

const actions = {
    setUserToken(context, data) {
        context.commit('SET_USER_TOKEN', data)  
    },
    logout(context) {
        
    }
};

export default {
    state,
    mutations,
    actions
}
