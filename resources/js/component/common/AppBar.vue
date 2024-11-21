<template>
    <v-app-bar app :color="getColor.color" :clipped-left="true" class="elevation-1">
        <v-app-bar-nav-icon
            @click="closeMenu"
                             :class="getColor.class"
                             :style="'color:'+getColor.fontColor"
        ></v-app-bar-nav-icon>
        <v-toolbar-title :class="getColor.class+ ' text-uppercase font-weight-bold'"
                         :style="'color:'+getColor.fontColor+';width:350px!important'"
        >Issue Project</v-toolbar-title>
        <div class="imgDiv" v-if="!getColor.role">
            <v-img  width="auto" height="58px" src="/img/logo"></v-img>
        </div>
        <v-menu v-model="userMenu" :close-on-content-click="true" :offset-y="true" bottom>
            <template v-slot:activator="{props }">
                <v-avatar :color="getColor.color" v-bind="props">
                    <v-icon dark icon="mdi-account-circle"></v-icon>
                </v-avatar>
            </template>
            <v-card>
                <v-list>
                    <v-list-item prepend-icon="mdi-account">
                        <v-list-item-title>{{ user.name}}</v-list-item-title>
                        <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
                    </v-list-item>
                </v-list>
                <v-list dense>
                    <v-list-item link @click="logout">
                        <v-list-item-title>{{textLogout}}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-card>
        </v-menu>
    </v-app-bar>
</template>
<script setup>
import {useStore} from "vuex";
import {computed, ref} from "vue";
import {useRouter} from "vue-router";
const router = useRouter();
const store = useStore()
const userMenu = ref(false)
const user = computed(() => {
    let useUser = store.getters['user/getUser']
    return {
        name: useUser.name,
        email: useUser.email
    }
})

const closeMenu = () => {
    store.commit('config/changeMini')
}

const textLogout = ref('Logout')

const getColor = computed(() => {
    if(store.getters['user/getIsRoot']){
        return{
            role:true,
            color: 'primary',
            class : 'white--text',
            fontColor: ""
        }
    }else{
        return{
            role:false,
            color: '#ffffff',
            class : '',
            fontColor: "#696969"
        }
    }
})
const logout = () => {
    router.push({name:"Choose",query:{logout:true}})
}
</script>
<style scoped lang="css">
.imgDiv{
    display: flex;
    justify-content: start;
    width: 82%;
    margin-right: 108px;
}
</style>
