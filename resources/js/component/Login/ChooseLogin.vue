<template>
    <PageBase title="Accesso" :border="true" :container="true" :center="true">
        <template v-slot:content>
            <v-btn  variant="plain"  @click="openSSO" :loading="load">Accedi</v-btn>
        </template>
    </PageBase>
</template>
<script setup>
import {useRoute, useRouter} from "vue-router";
import Title from "../common/Title.vue";
import {computed, onMounted, ref} from "vue";
import {useStore} from "vuex";
import {api} from "../../api/index.js";
import PageBase from "../common/PageBase.vue";

const router = useRouter();
const route = useRoute();
const store = useStore();
const load = ref(false)

const openSSO = ()=> {
    load.value = true
    api('auth/redirect','GET').then(r => {
        window.location.href = r
    }).catch(() => {load.value = false})

}
const login = () => {
    router.push({name:'Login'})
}

const roles = computed(() => {
    return store.getters['user/getIsRoot'];
})

const logoutRoot = async () => {
    await api('at/logout','GET')
    store.commit('user/clear')
}

onMounted(() => {

    if(route.query.error !== undefined){
        store.commit('snackbar/update',{
            show:true,
            color:'error',
            text:atob(route.query.error),
            button:true,
            preicon: "mdi-alert-outline"
        })
    }

    if(route.query.logout !== undefined){
        if(route.query.error === undefined)
            store.commit('snackbar/update',{
                show:true,
                color:'success',
                text:"Logout effettuato",
            })
        logoutRoot();
    }

})

</script>
<style scoped lang="css">

</style>
