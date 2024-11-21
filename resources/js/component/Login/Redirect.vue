<template>
    <v-container class="fill-height"
                 fluid>
        <div class="main-access-wr">
            <img  width="250" height="150"  src="/img/logo_CAI.png" />
            <h2 class="title">Autenticazione utenza...</h2>
            <v-progress-circular  indeterminate
                                 style="margin:5px auto;" width="7" size="150" color="success"></v-progress-circular>
        </div>
    </v-container>
</template>
<script setup>
import {onMounted, ref} from "vue";
import {api} from "../../api/index.js";
import {LoadRelationship} from "../../utils/LoadRelationship.js";
import {useRouter} from "vue-router";
import {useStore} from "vuex";
const router = useRouter()
const store = useStore();
onMounted(() => {
    let queryString = window.location.search.toString().replace('?', '')
    api('auth/user', 'GET', null, queryString,false,LoadRelationship.user).then(r => {
        let data = r;
        store.commit('user/update', data)
        console.log('data',data)
        store.commit('user/updateGitLab',data.access_token)
        router.push({name:'Home'})
    }).catch(e => {
        router.push({
            name: 'Error',
            query: {
                error: btoa(JSON.stringify({
                    status: 501,
                    text: "Eccezione login con Oauth"
                }))
            }
        })
    })
})
</script>
<style scoped lang="css">
.title{
    color:#777;
    font-weight:400;
}
.main-access-wr{
    align-items: center;
    justify-content: center;
    height:100vh;
    margin:0 auto;
    display: flex;
    flex-direction: column;
}
</style>
