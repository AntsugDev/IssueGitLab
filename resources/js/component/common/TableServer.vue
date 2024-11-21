<script setup>
import {ref} from "vue";
import {rulesRequired} from "../../utils/rules.js";

defineProps({

    loading:{
        type:[Boolean,null],
        default: false
    },
    headers:{
        type: Array,
        required: true
    },
    items:{
        type: Array,
        required: true
    },
    totalItems: {
        type:Number,
        required: true,
        default: 0
    },

})
const emit = defineEmits(['load'])
const itemsPerPage = defineModel('itemsPerPage');
const page = defineModel('page');
const loadItems = ({ page, itemsPerPage, sortBy, search }) => {
    emit('load',{ page, itemsPerPage, sortBy, search})
}
const getElement = (items, key) => {
    let split = key.toString().split('.')
    return split.reduce((acc, part) => acc && acc[part], items);

}
const search = defineModel('search')
</script>

<template>
    <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :headers="headers"
        :items="items"
        :items-length="totalItems"
        :loading="loading"
        :search="search"
        @update:options="loadItems"
    >
        <template v-slot:top>
            <div class="linear-into">
                <p class="p">Search</p>
                <v-text-field
                    v-model="search"
                    variant="outlined"
                    placeholder="Digitare almeno 4 carattteri"
                    clearable
                >
                </v-text-field>

            </div>

        </template>
        <template v-for="(field,i) in headers" :key="i" v-slot:[`item.${field.key}`]="{ item }">
            <slot :name="`item.${field.key}`" :item="item">{{getElement(item,field.key)}}</slot>
        </template>
        <template v-slot:bottom v-if="items.length === 0">&nbsp;</template>

    </v-data-table-server>
</template>

<style scoped lang="css">

</style>
