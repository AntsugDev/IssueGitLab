import axios from "axios";
import {store} from "../store/index.js";
import {router} from "../router/index.js";

const getToken = () => {
    return sessionStorage.getItem('_tk')
}

const setToken = (data) => {
    store.commit('user/setAccesToken',data)
    sessionStorage.setItem('_tk',data.access_token)
}


export const apiGeneral = (url,method,payload = null,queryString= null,isFile = false, loadQuery = null) => {

    return new Promise((resolve,reject) => {

        if (queryString !== null)
            url += '?' + queryString;


        if (loadQuery !== null  && url.toString().indexOf('?') !== -1)
            url += "&load=" + loadQuery
        else if (loadQuery !== null && url.toString().indexOf('?') === -1)
            url += '?load=' + loadQuery;


        let header = {
            "Content-Type": "application/json",
            Accept: "application/json",
        }
        let token = getToken()

        if (url.toString().indexOf('auth') === -1 && token !== null) {
            header.Authorization = `Bearer ${token}`
        }

        if (isFile)
            header["Content-Type"] = "multipart/form-data"

        let baseUri = '/api/'

        let config = {
            baseURL: baseUri,
            url: url,
            method: method,
            headers: header,
            timeout: 180000,
            data: payload !== null ? payload : undefined,
        }
        let response = axios.request(config);
        response.then(ro => {
            let data = ro.data;
            if( data['data-token'] !== undefined){
                setToken(data['data-token'])
            }

            if(data.redirect !== undefined)
                resolve(data.redirect)
            else if(data.length === 0)
                resolve(ro.status);
            else
                resolve(data.data)
        }).catch(e => {
            reject(e)
            if(store.getters['config/getBar'])
                store.commit('config/changeRouteBar')
        })

    })

}

export const refresh = (routeName,router) => {
    store.commit('config/changeRouteBar')
    api('at/refresh','GET').then(ro => {
        router.push({name:routeName})
        store.commit('config/changeRouteBar')
    })
}

export const api = (url,method,payload = null,queryString= null,isFile = false, loadQuery = null) => {
    return apiGeneral(url,method,payload ,queryString,isFile, loadQuery )

}

export const interceptorResponse = axios.interceptors.response.use((response) => {
    return  Promise.resolve(response);
},(error) => {
    let data = error.response.data
    let message = "";
    if(data.data !== undefined && data.data.errors !== undefined) {
        let errors = data.data.errors;
        if(typeof errors === 'object'){
            for(let key of Object.keys(errors)){
                message += '<li>'+errors[key]+'</li>'
            }
        }else
            message = data.data.errors
    }else
        message = e.message;


    store.commit('snackbar/update',{
        show:true,
        color:"error",
        text: message,
        preicon: "mdi-alert-outline"
    })

    if(parseInt(error.response.status) === 401)
        router.push({name:'Choose', query:{
                error: btoa(message),
                logout: true
            }})

    return Promise.reject(error)
})

// export const getMenu = () => {
//     api('menu','GET').then(ro => {
//         store.commit('menu/update',ro)
//     })
// }

