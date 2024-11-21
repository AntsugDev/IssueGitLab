<template>
<DialogBase title="Eccezione" route-name="FailedJobs">
    <template v-slot:card-text>
        <v-progress-linear v-if="loading" indeterminate color="success"></v-progress-linear>
        <span v-else style="word-wrap: break-word">
          {{data.exception}}
        </span>
    </template>
</DialogBase>
</template>
<script setup>

import DialogBase from "../../common/DialogBase.vue";
import {onBeforeMount, ref} from "vue";
import {useRoute} from "vue-router";
import {api} from "../../../api/index.js";
const data = ref([])
const route = useRoute();
const loading=ref(false)
const list = () => {
    loading.value = true
    api('failed_jobs/'+route.params.id,'GET').then(r => {
        data.value = r;
        loading.value = false
    }).catch(e => {
        loading.value = false

    })
}
onBeforeMount(() => {
    list();
})

</script>
<style scoped lang="css">

</style>
