<template>
    <v-dialog persistent min-width="400" :max-width="maxWidth" v-model="dialog">
        <v-card :loading="loading">
            <PageBase :title="title" :icon-back="iconBack" :color="color" :border="true">
                <template v-slot:content>
                    <div class="content">
                        <slot name="card-text"></slot>
                    </div>
                </template>
            </PageBase>
            <v-card-actions>
                <v-btn v-if="routeName"
                       color="primary"
                       variant="text"
                       @click="closed">
                    Chiudi
                </v-btn>
                <slot name="card-action"></slot>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>
import {ref,defineProps} from "vue";
import Title from "./Title.vue";
import {useRouter} from "vue-router";
import PageBase from "./PageBase.vue";

const router = useRouter();

const props = defineProps({
    maxWidth:{
        type:[Number,null],
        default: 800
    },
    loading: {
        type: Boolean,
        default: false
    },
    title:{
        type: [String,null],
    },
    iconBack:{
        type:[String,null]
    },
    color:{
        type:[String,null],
        default:'primary'
    },
    routeName:{
        type:[String,null]
    }
})
const dialog = ref(true)
const closed = () =>{
    router.push({name:props.routeName})
}
</script>
<style scoped lang="css">
.content{
    margin-top: 10px;
    padding: 1vw;
}
</style>
