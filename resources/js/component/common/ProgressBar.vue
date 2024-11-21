<template>
    <v-progress-linear indeterminate
                       color="coldirettiGreen"
                       v-if="store.getters['progress/getShow']"
    ></v-progress-linear>
</template>
<script setup>
import {useStore} from "vuex";

const store = useStore();
</script>
<style scoped lang="css">

</style>
