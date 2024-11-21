<template>
    <v-navigation-drawer
        :style="(!isRoot ? 'border-right:1px solid #696969' : '')"
        v-model="config.drawer"
        :color="getColor.color"
        :clipped="true"
        :rail="config.mini"
        permanent
        floating >
        <v-divider></v-divider>
        <template v-for="item in drawerItems">
            <v-list density="compact"
                    :opened="opened"
                    @update:opened="newOpened => opened = newOpened.slice(-1)"
                    v-if="item.children.length > 0"
                    nav
            >
                <v-list-group v-model="item.active" >
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            :key="item.text"
                            v-bind="props"
                            :title="item.text"
                            :prepend-icon="item.icon"
                        ></v-list-item>
                    </template>


                    <v-list-item
                        nav
                        link
                        :alt="subMenu.text"
                        v-for="subMenu in item.children"
                        :key="subMenu"
                        :title="subMenu.text"
                        :prepend-icon="subMenu.icon"
                        @click="goTo(subMenu.routeName)"
                    >
                    </v-list-item>
                </v-list-group>
            </v-list>

            <v-list density="compact" v-else nav>
                <v-list-item
                    :alt="item.text"
                    :title="item.text"
                    :prepend-icon="item.icon"
                    @click="goTo(item.routeName)"
                ></v-list-item>
            </v-list>
        </template>
        <template v-slot:append v-if="getColor.role">
            <div class="pa-2 mb-5">
                <v-btn to="/swagger/documentation" icon="mdi-api" target="_blank" alt="Documentation" title="Documentation"></v-btn>
            </div>
        </template>
    </v-navigation-drawer>
</template>
<script setup>
import {computed, inject, ref} from "vue";
import {useStore} from "vuex";
import {MenuList} from "../../utils/MenuList.js";
import {api, refresh} from "../../api/index.js";
import {useRouter} from "vue-router";


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
const drawerItems = computed(() => {
    return MenuList(getColor.value.role)
})

const opened = ref([])
const store = useStore();
const config = computed(() => {
    return store.getters['config/get']
})
const isRoot = inject('isRoot')
const router = useRouter();
const goTo = (routeName) => {
    refresh(routeName, router);
}

</script>
<style scoped lang="css">

</style>
