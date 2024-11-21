<template>
    <v-data-table
        loading-text="Caricamento in corso..."
        :loading="loading"
        class="elevation-3"
        :headers="headers"
        :items="items"
        hover
        :search="search"
        :server-items="serverItems"
    >
        <template v-for="(field,i) in headers" :key="i" v-slot:[`item.${field.key}`]="{ item }">
            <slot :name="`item.${field.key}`" :item="item">{{getElement(item,field.key)}}</slot>
        </template>
        <template v-slot:bottom v-if="items.length === 0">&nbsp;</template>
    </v-data-table>
</template>
<script setup>
defineProps({
    search:{
      type:[String,null],
      default: ""
    },
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


})
const getElement = (items, key) => {
    let split = key.toString().split('.')
    return split.reduce((acc, part) => acc && acc[part], items);

}

</script>
<style scoped lang="css">

</style>
