<template>
    <v-app>
        <v-layout  row class="justify-space-between">
            <v-main class="d-flex justify-center">
                <ProgressBar></ProgressBar>
                <SnackBar></SnackBar>
                <router-view></router-view>
            </v-main>
        </v-layout>
    </v-app>
</template>
<script setup>
import ProgressBar from "./component/common/ProgressBar.vue";
import SnackBar from "./component/common/SnackBar.vue";
</script>
<style>
@import "../css/app.css";
@import "../scss/app.scss";
</style>
