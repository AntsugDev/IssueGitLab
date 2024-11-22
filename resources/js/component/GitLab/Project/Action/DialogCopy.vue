<template>
    <DialogBase title="Copia Board" routeName="ProjectIndex">
        <template v-slot:card-text>
            <v-progress-linear indeterminate color="warning" v-if="isCheck"></v-progress-linear>
            <v-form ref="formCopy" v-model="valid" v-else>
                <div class="linear-into">
                    <p class="p">Scegli cosa copiare</p>

                    <v-checkbox
                        v-model="select"
                        label="All"
                        value="all"
                        :error-messages="notValid.msg"
                        :error="notValid.error"
                    ></v-checkbox>

                    <v-checkbox
                        v-model="select"
                        label="Labels"
                        value="labels"
                        :error-messages="notValid.msg"
                        :error="notValid.error"
                        :disabled="isDisabled"
                    ></v-checkbox>

                </div>
            </v-form>
            <v-textarea readonly class="note" v-if="isDisabled" variant="outlined" no-resize rows="2" v-model="text"></v-textarea>

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
import {computed, onBeforeMount, ref, watch} from "vue";
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
const isDisabled = ref(false)
const isCheck =ref(false)
const text = ref("Attenzione !Selezionando All, si andrà a creare sia le boards, sia le labels")

watch(select ,(value) => {
    if(value.indexOf('all') !== -1)
        isDisabled.value  =true;
    else
        isDisabled.value  =false;
})

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
})

</script>
<style scoped lang="css">
.note{
    font-style: italic;
    font-size: 12px;
    color:#FF5252;
}
</style>
