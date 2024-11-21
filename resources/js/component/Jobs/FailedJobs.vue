<template>
    <router-view></router-view>
    <PageBase title="Failed jobs" >
        <template v-slot:page-icon-end>
            <v-progress-circular indeterminate v-if="loadingDel" color="primary"></v-progress-circular>
            <template v-else>
                <IconActioTable icon="mdi-delete" alt="Delete" color="error" @click="delList" ></IconActioTable>
                <IconActioTable icon="mdi-reload" alt="Reload" color="primary" @click="list"></IconActioTable>
            </template>
        </template>
        <template v-slot:content>
            <Table :items="items" :headers="headers">
                <template v-slot:[`item.payload`]="{item}">
                    <span>{{JSON.parse(item.payload).displayName}}</span>
                </template>
                <template v-slot:[`item.exception`]="{item}">
                    {{item.exception.toString().substring(0,100)}} <span alt="Vedi altro" style="cursor: pointer" @click="allException(item.id)">...</span>
                </template>

            </Table>
        </template>
    </PageBase>
</template>
<script setup>

import PageBase from "../common/PageBase.vue";
import IconActioTable from "../common/IconActioTable.vue";
import Table from "../common/Table.vue";
import {onBeforeMount, ref} from "vue";
import {api} from "../../api/index.js";
import {useRouter} from "vue-router";
const items = ref([]);

const headers = ref([
    {title:'UUID',key:'uuid',align:'left'},
    {title:'PAYLOAD',key:'payload',align:'left'},
    {title:'EXCEPTION',key:'exception',align:'left'},
    {title:'FAILED AT',key:'failed_at',align:'left'},
])
const loading = ref(false);
const loadingDel = ref(false);
const router = useRouter();
const list = () => {
    loading.value = true
    api('failed_jobs','GET').then(r => {
        items.value = r
        loading.value = false
    }).catch(e => {
        loading.value = false

    })
}
const delList = () => {
    loadingDel.value = true
    api('failed_jobs/1','DELETE').then(r => {
        loadingDel.value = false
        list();
    }).catch(e => {
        loadingDel.value = false

    })
}
const allException = (id) => {
router.push({
    name:'DialogException',
    params:{
        id: id
    }
})
}

onBeforeMount(() => {
    list();
})

</script>
<style scoped lang="css">

</style>
