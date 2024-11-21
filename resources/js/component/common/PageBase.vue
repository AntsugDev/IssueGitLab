<template>
    <v-container
        :class="container ? 'fill-height justify-center' : ''"
        fluid
    >
        <div :class="'row '+(center ? 'row-center' : '')">
            <Title :title="title" :icon-back="iconBack" :color="color" >
                <template v-slot:iconEnd>
                    <slot name="page-icon-end"></slot>
                </template>
            </Title>
            <div class="content row-border">
                <slot name="content"></slot>
            </div>
        </div>
    </v-container>
</template>
<script setup>

import Title from "./Title.vue";
import {useStore} from "vuex";
import {computed} from "vue";
defineProps({
    container:{
        type:[Boolean,null],
        default: false
    },
    center:{
        type:[Boolean,null],
        default: false
    },
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
    border:{
        type: [Boolean,null],
        default:false
    }
})
</script>
<style scoped lang="css">

</style>
