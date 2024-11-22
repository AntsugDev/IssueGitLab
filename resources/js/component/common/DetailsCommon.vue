<template>
    <template  v-for="(k,i) in Object.keys(item)"  :key="i">
        <div class="linear-into"  v-if="isNull(item[k]) &&  print(k) && getExclude(k)">
            <template  v-if="!isObject(item[k])">
                <p class="p">{{searchLabel(k)}}</p>
                <span class="linear-row" v-if="getContent(k)">{{item[k]}}</span>
                <template v-else>
                    <slot name="content" :content="item[k]" :item="item"></slot>
                </template>
            </template>
            <div v-else v-html="loop(item[k],k)"></div>
        </div>


    </template>
</template>
<script setup>

import {computed} from "vue";

const props = defineProps({
    item:{
        type:Object,
        required:true
    },
    exclude:{
        type:[Array,null],
        default:[]
    },
    label:{
        type:[Object,null],
        default:null
    }
})

const getContent = (key) =>{
    return key.toString().indexOf('content') === -1;
}

const isNumber = (key) =>  {
    const converted = Number(key);
    return !isNaN(converted);
}

const searchLabel = (key) => {
    if(props.label === null)
        return key.toString().toUpperCase().replaceAll('_',' ');
    else{
        if(props.label[key] !== undefined)
            return props.label[key].toString().toUpperCase()
        else {
            if(!isNumber(key))
                return key.toString().toUpperCase().replaceAll('_', ' ');
            else return  "";
        }
    }
}

const print = (key) => {
    if(key.toString().indexOf('id') !== -1)
        return false;
    else if(key.toString().indexOf('password')  !== -1)
        return false;
    else if(typeof key === "number")
        return false;
    return true;
}
const printDate = (key) => {
    if(key.toString().indexOf('created_at') !== -1)
        return false;
    else if(key.toString().indexOf('updated_at')  !== -1)
        return false;
    return true;
}

const isNull = (row) => {
    if(Array.isArray(row))
        return row.length > 0
    else
        return row !== null ;
}

const isObject = (row) => {
    return typeof row === 'object';
}

const getExclude = (key) => {
    if(props.exclude !== null && props.exclude.length > 0)
        return props.exclude.indexOf(key) === -1
    else
        return true
}

const loop = (item,key) => {
    item = item === undefined ? null : item
    if(item !== null) {
        let list = "";
        if(getExclude(key)) {
            list += '<p class="p">' + searchLabel(key) + '</p>'
            for (let k of Object.keys(item)) {
                let row = item[k];
                if (typeof row !== "object") {

                    if (print(k) && printDate(k) && isNull(row) && getExclude(k))
                        list += '<div class="sub" >\n' +
                            '                   <span class="linear-row" >' + row + '</span>\n' +
                            '                </div>'
                } else list += loop(row, k);
            }
        }
        return list;
    }
    return  "";
}

</script>
<style>
.linear-into{
    display: flex;
    flex-direction: column;
    margin-bottom: 5px;
}
.p{
    text-transform: uppercase;
    font-weight: 800;
    font-size: 13px;
    margin-bottom: 3px
}
.sub{
    display: flex;
    width: 100%;
    flex-direction: column;
    padding: 5px;

}
.linear-row{
    border:1px solid #f4f6f6;
    background: #f4f6f6;
    padding: 1vw;
    border-bottom: 1px solid #000;
    margin-top:5px;
    margin-bottom:5px;
}
</style>
