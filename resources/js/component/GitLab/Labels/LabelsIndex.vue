<template>
    <PageBase title="Lista Labels">
        <template v-slot:page-icon-end >
            <IconActioTable icon="mdi-reload"
                            alt="Reload" @click="list"></IconActioTable>
        </template>
        <template v-slot:content>
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
    </PageBase>
</template>
<script setup>

import PageBase from "../../common/PageBase.vue";
import Table from "../../common/Table.vue";
import {inject, onBeforeMount, ref} from "vue";
import {api} from "../../../api/index.js";
import IconActioTable from "../../common/IconActioTable.vue";
const items= ref([]);

const headers = ref([
    {title:'Name',key:'name',align:'left'},
    {title:'Description',key:'description',align:'left'},
    {title:'Text Color',key:'text_color',align:'left'},
    {title:'Color',key:'color',align:'left'},
    {title:'Priority',key:'priority',align:'left'},
])
const project = inject('projectDefault')
const loading = ref(false)
const list = () => {
    loading.value = true
    api('labels','GET').then(r => {
        items.value = r
        loading.value = false
    }).catch(e => {
        loading.value = true
    })
}
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

onBeforeMount(() => {
    list()
})


</script>



<style scoped lang="css">

</style>
