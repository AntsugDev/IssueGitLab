<template>
    <v-snackbar
        v-model="snackbar.show"
        location="top"
        :color="snackbar.color"
        :timeout="isButton ? 9999999999:  8000"
        multi-line
        dense
    >
        <template v-if="isHTML(snackbar.text)" >
            <span v-html="snackbar.text"></span>
        </template>
        <template v-else>
            <v-icon v-if="snackbar.hasOwnProperty('preicon') && snackbar.preicon" :icon="preicon" color="#ffffff"></v-icon> &nbsp;
            {{ snackbar.text }}
        </template>
        <template v-slot:action="{ attrs }">
            <v-btn
                v-if="isButton"
                color="#ffffff"
                fab
                x-small
                dark
                v-bind="attrs"
                @click="useButton"
                class="elevation-6">
                <v-icon icon="mdi-close"></v-icon>
            </v-btn>
        </template>
    </v-snackbar>
</template>
<script setup>
import {useStore} from "vuex";
import {computed,  onUpdated} from "vue";
import {useRoute, useRouter} from "vue-router";
const isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);
const store = useStore();
const snackbar = computed(() => {
    return store.getters['snackbar/get']
})
const route = useRoute();
const router = useRouter();
const isButton = computed(() =>{
    if(snackbar.value.hasOwnProperty('button') )
        return snackbar.value.button;
    return false;
})
const useButton = () => {
    store.commit('snackbar/clear')
}

onUpdated(() => {
    if(route.query.error !== undefined || route.query.logout !== undefined){
        setTimeout(() => {
            router.push({name:route.name})
        },1000)
    }
})

</script>
<style scoped lang="css">

</style>
