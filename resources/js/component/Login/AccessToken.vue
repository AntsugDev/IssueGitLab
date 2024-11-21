<template>
    <DialogBase title="Aggiungi Token GitLab">
        <template v-slot:card-text>
            <v-form ref="formGitLab" v-model="valid">
                <div class="linear-into">
                    <p class="p">Access Token</p>
                        <v-text-field
                        v-model="params"
                        variant="outlined"
                        :disabled="loading"
                        :rules="[rulesRequired.required]"
                        >
                        </v-text-field>
                </div>
            </v-form>
        </template>
        <template v-slot:card-action>
            <v-btn
                   color="primary"
                   variant="text"
                   @click="registra"
                   :disabled="loading"
                   :loading="loading"
            >
                Registra
            </v-btn>
        </template>
    </DialogBase>
</template>
<script setup>

import {ref} from "vue";
import DialogBase from "../common/DialogBase.vue";
import {rulesRequired} from "../../utils/rules.js";
import {api} from "../../api/index.js";
import {useStore} from "vuex";
import {useRouter} from "vue-router";

const valid = ref(false)
const params = ref(null)
const formGitLab = ref(null)
const loading = ref(false)
const store = useStore();
const router = useRouter();

const registra = () => {
    loading.value = true
    formGitLab.value.validate().then(r => {
        if(r.valid){
            api('gitlab/'+params.value,'GET').then(r => {
                store.commit('snackbar/update', {
                    show: true,
                    text: "Token GitLab registrato",
                    color: "success",
                });
                store.commit('user/updateGitLab',params.value)
                router.push({name:'Home'})
            });
            loading.value = false
        }else
            loading.value = false

    })
}

</script>

<style scoped lang="css">

</style>
