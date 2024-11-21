<template>
    <v-icon size="35"
            @click.stop="fnAction"
            :color="color"
            :alt="alt"
            :title="alt"
            :icon="icon" ></v-icon>
</template>
<script setup>
import {defineEmits,defineProps} from "vue";
const emit = defineEmits(['click'])
const props = defineProps({
    icon:{
        type:String,
        required:true,
        validator(value, props) {
            if(value.toString().indexOf('mdi') === -1) {
                console.warn("Icon not valid")
                return false;
            }
            return  true;
        }
    },
    color:{
        type:[String,null],
        default:"success"
    },
    alt:{
        type:[String,null],
        default: ""
    }
})

const fnAction = (event) => {
   emit('click',event)
}
</script>
<style scoped lang="css">

</style>
