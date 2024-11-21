<template>

    <v-container class="fill-height justify-center">
        <v-row
            align="center"
            justify="center"
        >
            <v-col
                cols="12"
                sm="8"
                md="4"
            >
                <v-card class="elevation-12">
                    <Title color="error" :title="title"></Title>
                    <v-card-text style="display: flex;flex-direction: column; justify-content: center">
                        <span>{{text}}</span>
                        <br />
                        <br />
                        <v-btn variant="elevated" color="success"  @click="login">Torna alla login
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

</template>
<script setup>

import Title from "../common/Title.vue";
import {useRoute, useRouter} from "vue-router";
import {onBeforeMount, ref} from "vue";
const route = useRoute();
const title = ref(null)
const text = ref(null)
const router = useRouter()
const login = () => {
    router.push({name:'Choose'})
}

onBeforeMount(() =>{
    if(route.query.error !== undefined){
        let error = JSON.parse(atob(route.query.error))
        title.value = error.status;
        text.value = error.text
    }
})


</script>
<style scoped lang="css">

</style>
