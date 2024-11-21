<template>
    <AppBar></AppBar>
    <Menu></Menu>
    <v-row>
        <v-col cols="12">
            <v-progress-linear v-if="bar"  indeterminate  style="margin:5px auto;position: absolute;z-index: 1000;top:60px"
                               width="7"
                               size="150"
                               color="warning"></v-progress-linear>
        </v-col>
    </v-row>
    <router-view></router-view>
</template>
<script setup>

import {useStore} from "vuex";
import AppBar from "./common/AppBar.vue";
import Menu from "./common/Menu.vue";
import {onBeforeMount, inject, computed, provide} from "vue";
import {useRouter} from "vue-router";
const router = useRouter();
const store= useStore();
const isRoot = computed(() => {
    let root =  store.getters['user/getIsRoot']
    if(root === undefined) return true;
    return  root;
})
const accessToken = computed(() => {
    return store.getters['user/getGitLab']
})

const bar = computed(() => {
    return store.getters['config/getBar']
})
const projectDefault = computed(() => {
    return store.getters['config/getProject']
})

provide('isRoot',isRoot.value)
provide('projectDefault',projectDefault.value)
onBeforeMount(() => {
    if(!isRoot.value) {
        store.commit('config/changeMini')
    }
    if(accessToken.value === null){
        router.push({name:'AccessToken'})
    }
})
</script>
<style scoped lang="css">

</style>
