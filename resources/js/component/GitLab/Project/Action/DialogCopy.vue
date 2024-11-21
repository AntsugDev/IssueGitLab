<template>
    <DialogBase title="Copia Board" routeName="ProjectIndex">
        <template v-slot:card-text>
            <v-progress-linear indeterminate color="warning" v-if="isCheck"></v-progress-linear>
            <v-form ref="formCopy" v-model="valid" v-else>
                <div class="linear-into">
                    <p class="p">Scegli cosa copiare</p>
                    <v-checkbox
                        v-if="view.board"
                        v-model="select"
                        label="Boards"
                        value="boards"
                        :error-messages="notValid.msg"
                        :error="notValid.error"
                    ></v-checkbox>
                    <v-checkbox
                        v-if="view.label"
                        v-model="select"
                        label="Labels"
                        value="labels"
                        :error-messages="notValid.msg"
                        :error="notValid.error"
                    ></v-checkbox>

                </div>
            </v-form>

        </template>
        <template v-slot:card-action>
            <v-btn color="primary"
                   variant="elevated"
                   :loading="loading"
                   @click="save">
                Copia
            </v-btn>
        </template>
    </DialogBase>
</template>
<script setup>

import DialogBase from "../../../common/DialogBase.vue";
import {rulesRequired} from "../../../../utils/rules.js";
import {computed, onBeforeMount, ref} from "vue";
import {api} from "../../../../api/index.js";
import {useRoute, useRouter} from "vue-router";
import {useStore} from "vuex";
const valid = ref(false)
const formCopy = ref(null)
const select = ref([])
const loading = ref(false)
const notValid = ref({
    error: false,
    msg: null
})
const route = useRoute();
const router = useRouter();
const store = useStore();
const view = ref({
    board:true,
    label:true
})
const isCheck =ref(false)
const check = () => {
    isCheck.value = true
    api('data/project/'+route.params.project,'GET').then(r => {
        view.value.board = r.isBoards
        view.value.label = r.isLabels
        isCheck.value = false
    }).catch(e => {
        isCheck.value = false
    })
}

const save = () => {
    loading.value = true
    if(select.value.length === 0){
        notValid.value.error = true
        notValid.value.msg = "Almeno uno dei checkbox deve essere selezionato"
        valid.value = false
        loading.value = false
    }else{
        notValid.value.error = false
        notValid.value.msg = null
        valid.value = true
    }

    formCopy.value.validate().then(r => {
        if(r.valid){
            api('data/duplicate','POST',{
                choice: select.value,
                project_id: route.params.project
            }).then(r => {
                store.commit('snackbar/update',{
                    show: true,
                    text: "Procedura avviata con successo",
                    color: "success",
                })
                router.push({
                    name: 'ProjectIndex',
                    query:{
                        reload:true
                    }
                })
                loading.value = false
            }).catch(() => {
                loading.value = false
            })

        }
    })
}
onBeforeMount(() => {
    check()
})

</script>
<style scoped lang="css">

</style>
