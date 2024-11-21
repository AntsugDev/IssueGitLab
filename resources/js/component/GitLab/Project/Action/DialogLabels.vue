<template>
<DialogBase :title="title" route-name="ProjectIndex" max-width="1200">
    <template v-slot:card-text>
        <Table :items="items" :headers="headers" :loading="loading">
            <template v-slot:[`item.text_color`]="{item}">
                <v-btn variant="flat" text=" " :color="item.text_color" :disabled="true"></v-btn>
            </template>
            <template v-slot:[`item.color`]="{item}">
                <v-btn variant="flat" text=" " :color="item.color" :disabled="true"></v-btn>
            </template>
            <template v-slot:[`item.priority`]="{item}">
                <v-btn variant="flat"
                       :icon="getPriority(item.priority).icon"
                       :color="getPriority(item.priority).color"
                       :disabled="true"
                ></v-btn>
            </template>
        </Table>
    </template>
</DialogBase>
</template>
<script setup>

import DialogBase from "../../../common/DialogBase.vue";
import {useRoute} from "vue-router";
import {computed, onBeforeMount, ref} from "vue";
import Table from "../../../common/Table.vue";
import {api} from "../../../../api/index.js";
const route = useRoute();
const loading = ref(false)
const title = computed(() => {
    return "Labels per il progetto "+atob(route.params.title)
})

const items= ref([]);

const headers = ref([
    {title:'Name',key:'name',align:'left'},
    {title:'Description',key:'description',align:'left'},
    {title:'Text Color',key:'text_color',align:'left'},
    {title:'Color',key:'color',align:'left'},
    {title:'Priority',key:'priority',align:'left'},
])
const getPriority = (priority) => {
    switch (parseInt(priority)){
        case 0:
            return {
                icon: 'mdi-playlist-minus',
                color: '#e6e6fa'
            }
        case 1:
            return {
                icon: 'mdi-playlist-check',
                color: '#8fbc8f'
            }
        case 2:
            return {
                icon: 'mdi-playlist-star',
                color: '#6585a4'
            }
        case 3:
            return {
                icon: 'mdi-playlist-plus',
                color: '#dc143c'
            }
        default: return {
            icon: '',
            color: ''
        }
    }
}
const list = () => {
    loading.value = true
    let project = route.params.project
    api('data/labels/'+project,'GET').then(r => {
        items.value = r
        loading.value = false
    }).catch(e => {
        loading.value = true
    })
}

onBeforeMount(() => {
    list()
})
</script>
<style scoped lang="css">

</style>
