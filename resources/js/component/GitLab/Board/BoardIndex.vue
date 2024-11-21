<template>
    <PageBase title="Lista Boards">
        <template v-slot:page-icon-end >
            <IconActioTable icon="mdi-reload"
                            alt="Reload" @click="list"></IconActioTable>
        </template>
        <template v-slot:content>
            <Table :items="items" :headers="headers" :loading="loading">
                <template v-slot:[`item.lists`]="{item}">
                    <div style="display: flex;flex-direction: row;padding: 2vw">
                        <v-sheet v-for="(v,i) in item.lists" :key="i" border
                                 :style="getStyle(v.label.color)">
                            <p style="font-size: 16px;font-weight: 600;color: #ffff">{{v.label.name}}</p>
                        </v-sheet>
                    </div>
                </template>
            </Table>
        </template>
    </PageBase>
</template>
<script setup>

import IconActioTable from "../../common/IconActioTable.vue";
import Table from "../../common/Table.vue";
import PageBase from "../../common/PageBase.vue";
import {inject, onBeforeMount, ref} from "vue";
import {api} from "../../../api/index.js";
import {LoadRelationship} from "../../../utils/LoadRelationship.js";

const items = ref([])
const headers = ref([
    {title:'Name',key:'name',align:'left'},
    {title:'Component',key:'lists',align:'left'},
])
const loading = ref(false)
const project = inject('projectDefault')

const list = () => {
    loading.value = true
    api('boards', 'GET',null,null,false,LoadRelationship.boards).then(r => {
        items.value = r
        loading.value = false
    }).catch(e => {
        loading.value = false
    })
}
onBeforeMount(() => {
    list()
})

const getStyle = (color) => {
    return "border-color: "+color+";border-radius: 10px; width:200px;height:6vw;margin-right: 10px;background:"+color+";padding:1vw; word-wrap: break-word"
}

</script>
<style scoped lang="css">

</style>
