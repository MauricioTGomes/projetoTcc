export default {
    set(key, value){
        window.sessionStorage[key] = value
        return value
    },
    get(key, defaultV){
        return window.sessionStorage[key] || defaultV
    },
    setObject(key, value){
        window.sessionStorage[key] = JSON.stringify(value)
        return this.getObject(key)
    },
    getObject(key){
        return JSON.parse(window.sessionStorage[key] || null)
    },
    remove(key){
        window.sessionStorage.removeItem(key)
    }
}
