<template>
    <v-toolbar
        :color="(rootCheck ? color : 'white')"
        dark
        flat
        dense
        elevation="4"
        class="toolbar"
    >
        <v-toolbar-title :color="(rootCheck ? color : 'white')" class="font-weight-bold" :style="(!rootCheck ? 'color:#696969' : '' )">
            <v-icon icon="mdi-arrow-left" :color="(rootCheck ? '#ffffff' : '#696969' )" @click="back" v-if="iconBack"></v-icon>&nbsp;
            {{title}}
            <span > <slot name="iconEnd"></slot></span>
        </v-toolbar-title>
    </v-toolbar>
</template>
<script setup>
import {computed, defineProps, inject, onMounted} from "vue";
import {useRouter} from "vue-router";
const router = useRouter();
const isRoot = inject('isRoot')

const rootCheck = computed(() => {
    if(isRoot === undefined)
        return true;
    else return isRoot
})

const props  = defineProps({
    title:{
        type:String,
        required:true
    },
    iconBack:{
        type:[String,null]
    },
    color:{
        type:[String,null],
        default:'primary'
    },
})
const back = () => {
    router.push({name:props.iconBack})
}



</script>
<style >
.v-toolbar-title{
    text-transform: capitalize;
    font-size: 22px;
    margin-top:1px;
    align-content: center;
}
span{
    float: right;
    margin-right: 16px;
}
</style>
