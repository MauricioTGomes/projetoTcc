import storage from './services'

const state = {
    title: (storage.get('app') || {}).title,
    subTitle: (storage.get('app') || {}).subTitle
};

const mutations = {
    SET_TITLE(state, title) {
        state.title = title;
    },
    SET_SUB_TITLE(state, title) {
        state.subTitle = title;
    }
};

const actions = {
    setTitle(context, title) {
        context.commit('SET_TITLE', title)
    },

    setSubTitle(context, title) {
        context.commit('SET_SUB_TITLE', title)
    }
};

export default {
    state,
    mutations,
    actions
}
