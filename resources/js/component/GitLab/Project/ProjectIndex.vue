<template>
    <router-view :items="items"></router-view>
    <PageBase title="Lista Projects">
        <template v-slot:page-icon-end>
            <IconActioTable icon="mdi-reload" alt="Reload" color="primary" @click="reload"></IconActioTable>
        </template>
        <template v-slot:content>
            <TableServer
                :items="items"
                :headers="headers"
                :loading="loading"
                @load="loadList"
                :total-items="options.totalItems"
                v-model:search="search"
                :page="options.page">
                <template v-slot:[`item.name`]="{item}">
                    <strong>{{item.name}}</strong>
                </template>
                <template v-slot:[`item.labels`]="{item}">

                    <IconActioTable :icon="item.labels.length > 0 ? 'mdi-check-bold' : 'mdi-close'"
                                    :alt="item.labels.length > 0 ? 'Labels caricati' : 'Labels non caricati'"
                                    :color="item.labels.length > 0  ? 'success' : 'error'"
                    >

                    </IconActioTable>

                </template>
                <template v-slot:[`item.boards`]="{item}">
                    <IconActioTable :icon="item.boards.length > 0 ? 'mdi-check-bold' : 'mdi-close'"
                                    :alt="item.boards.length > 0 ? 'Boards caricati' : 'Boards non caricati'"
                                    :color="item.boards.length > 0  ? 'success' : 'error'"
                    >

                    </IconActioTable>

                </template>
                <template v-slot:[`item.gitlab_url`]="{item}">
                    <IconActioTable color="primary" @click="openHref(item.web_url,1)"  icon="mdi-link"></IconActioTable>
                </template>
                <template v-slot:[`item.boards_link`]="{item}">
                    <IconActioTable color="warning" @click="openHref(item.web_url,2)"  icon="mdi-link"></IconActioTable>
                </template>
                <template v-slot:[`item.issue_link`]="{item}">
                    <IconActioTable color="fontColorCai" @click="openHref(item.web_url,3)"  icon="mdi-link"></IconActioTable>
                </template>
                <template v-slot:[`item.create`]="{item}">
                    <IconActioTable icon="mdi-folder-plus"
                                    color="fontColorCai"
                                    alt="Crea un issue" @click="createIssue(item.id)"></IconActioTable>
                </template>

                <template v-slot:[`item.action`]="{item}">

<!--                    <IconActioTable icon="mdi-information" color="info" alt="Info Progetto" @click="infoProgetto(item.id)"></IconActioTable>-->

                    <IconActioTable
                        icon="mdi-content-copy"
                        alt="Copia Labels"
                        color="error"
                        @click="copia(item.id)"
                    >

                    </IconActioTable>



                </template>
            </TableServer>
        </template>
    </PageBase>
</template>
<script setup>

import IconActioTable from "../../common/IconActioTable.vue";
import Table from "../../common/Table.vue";
import PageBase from "../../common/PageBase.vue";
import {onBeforeMount, ref, watch} from "vue";
import {api} from "../../../api/index.js";
import {useRoute, useRouter} from "vue-router";
import TableServer from "../../common/TableServer.vue";
const items = ref([]);
const headers = ref([
    {title:'Name',key:'name',align:'left'},
    {title:'Description',key:'description',align:'left'},
    {title:'Labels',key:'labels',align:'center'},
    {title:'Boards',key:'boards',align:'center'},
    {title:'GitLab url',key:'gitlab_url',align:'center'},
    {title:'Board Project',key:'boards_link',align:'center'},
    {title:'Issue List',key:'issue_link',align:'center'},
    {title:'Create issue',key:'create',align:'center'},
    {title:'',key:'action',align:'center'},
]);
const loading = ref(false)
const options = ref({
    totalItems: 0,
    sortBy: {
        orderBy: 'created_at',
        order:'desc'
    },
    page:1,
})
const search = ref(null)

const infoProgetto = (id) => {
    router.push({
        name:'DialogInfo',
        params:{
            id: id
        }
    })
}

const openHref = (href, isBoard) => {
    if(isBoard === 2)
        href += "/-/boards/"
    else if(isBoard === 3)
        href += "/-/issues/"

    window.open(href, '_blank');
}

const router = useRouter();
const route = useRoute();

const loadList = ({ page, itemsPerPage, sortBy,search }) => {

    let path = 'data/project';
    if(Array.isArray(sortBy) && sortBy.length === 0)
        path += '?per_page='+itemsPerPage+'&order_by=created_at&page='+page+'&sort=desc'
    else
        path += '?per_page='+itemsPerPage+'&order_by='+sortBy.key+"&page="+page+'&sort='+sortBy.order
    loading.value = true

    if(search !== null){
        if(search === "") list(path)
        else {
            path += "&search=" + search
            if (search.length >= 4)
                setTimeout(() => {
                    list(path)
                }, 1500)
        }
    }else {
        list(path)
    }
}
const reload = () => {
    loadList({
        page: 1,
        itemsPerPage:5,
        sortBy:{
            key:'created_at',
            order:'desc'
        },
        search: null
    })
}

const list =(path) => {
    api(path,'GET').then(r => {
        items.value = r.content
        options.value.totalItems = r.totalElement
        options.value.page = r.page
        options.value.sortBy = r.sort
        loading.value = false
    }).catch(() => {
        loading.value = false
    })
}

const labels = (item) => {
    router.push({
        name:'DialogLabels',
        params:{
            title: btoa(item.name),
            project: item.id
        }
    })
}

const copia = (select)=> {

    router.push({
        name: 'DialogCopy',
        params: {
            project: select,
        }
    })
}
const createIssue = (select) => {
    router.push({
        name: 'CreateIssue',
        params: {
            project: select,
        }
    })
}

watch(() => route.query.reload, (value)=>{
    if(value !== undefined && value) {
        search.value =null;
        loadList({
            page: 1,
            itemsPerPage: 5,
            sortBy: {
                key: 'created_at',
                order: 'desc'
            },
            search: null
        })
    }
})

onBeforeMount(() => {
})
</script>
<style scoped lang="css">

</style>
