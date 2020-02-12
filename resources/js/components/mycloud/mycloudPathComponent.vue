<template>
        <div id='path-bar'>
            <img src="/img/iconmonstr-home-6-24.png" style="height: 18px;width: 18px;cursor:pointer;" @click="itemClicked('0')" @contextmenu="rowMouseDxClicked($event,0)">
            <span v-for="(item,id) in getFullPath" :key="id" @contextmenu="rowMouseDxClicked($event,item.id)">
                <img class="arrowr" src="/img/iconmonstr-arrow-24-12.png">
                <p @click="itemClicked(item.id)">{{ item.val1 }}</p>
            </span>

        </div>
</template>

<script>
import { EventBus } from "../../mycloudApp.js";
import { store } from './vuex/storeVuex';//carica lo script Vuex
import { mapGetters } from 'vuex';
    export default {        
        store,
        computed:{ 
            ...mapGetters(['getFullPath','getIdPath'])
        },
        methods:    {
            itemClicked(id_to){
                store.dispatch('chdir',id_to);
            },
            rowMouseDxClicked(event,id_to) //tastp destro del mouse
            {
                if(event){
                    event.preventDefault();
                    this.itemClicked(id_to);
                    EventBus.$emit("contextMenuClicked",{
                        x : event.x,
                        y : event.y,
                        id: 0,
                    });
                }
            },
        },
        mounted() {

        }
    }
</script>
<style scoped>
span {
    display: flex;
    flex-direction: row;
    align-content: center;
    align-items: center;
}
p{
    margin:0px;
    cursor:pointer;
}
p:hover{
    filter: brightness(45%);
    color:rgb(185, 185, 185);
}
#path-bar{
    max-height: 32px;
    min-height: 32px;
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color:#a5aed563;
    display:flex;
    flex-direction: row;
    align-items: center;
}
.arrowr{
    margin-left: 8px;
    margin-right: 8px;
}
</style>



