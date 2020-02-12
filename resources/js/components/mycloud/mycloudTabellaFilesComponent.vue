<template>
<div class="mycloudtabellafiles" @contextmenu="rowMouseDxClicked($event)">
        <!-- <span>{{getSelectedItemsArray}}</span> -->
        <mycloud-tabella-row-files-component
            :type="'UP'"
            v-if="getIdPath != '0'"
            :row="uprow"
            :n="0"
        >({{getIdPath}})
        </mycloud-tabella-row-files-component>
        <span v-for="(item,id) in getDirs" :key="id"
            @click="clickStart($event,id + 1)"
        >
            <mycloud-tabella-row-files-component
                :type="'DIR'"                
                :row="item"
                :n="id + 1"
                :selected="getSelectedItem(id + 1)"                                
            >
            </mycloud-tabella-row-files-component>
        </span>
        <span v-for="(item,id) in getFiles" :key="id + 10001"
            @click="clickStart($event,id + 10001)"
        >
            <mycloud-tabella-row-files-component
                :type="'FILE'"                
                :row="item"
                :n="id + 10001"
                :selected="getSelectedItem(id + 10001)"
            >
            </mycloud-tabella-row-files-component>
        </span>
</div>
</template>

<script>
import { EventBus } from "../../mycloudApp.js";
import { store } from './vuex/storeVuex';//carica lo script Vuex
import { mapGetters } from 'vuex';

    export default {
        store,
        data() {
            return {
                wait:false,
                uprow:{
                    id : "0",
                    tipo_record : "",
                    val1 : "",
                    val2 : "",
                    val3 : "",
                    val4 : "",
                    val5 : "",
                    updated_at : "",
                },
            }
        },
        computed:{ 
            ...mapGetters(['getFiles','getDirs','getIdPath','getSelectedItem','getSelectedItemsArray','copySelectedFiles'])
        },
        methods:{
            log(message){
                console.log(this.$options.name+": "+message);
            },
            getList(){
                store.dispatch('getFileInfo');
                
            },
            clickStart(event,id){
                if(event ){
                    if(event.ctrlKey)
                    {
                        if(!event.shiftKey){  //click col control premuto senza shift                        
                            store.dispatch('setSelected',{id: id, val : !this.getSelectedItem(id)});                          
                        }else{ //click col CTRL e shift premuto
                            store.dispatch('rangeSelect',{id:id,desel:false}); 
                        }
                    }
                    else{
                        if(!event.shiftKey){              //click senza control e senza shift
                            store.dispatch('deselectAll');
                            store.dispatch('setSelected',{id: id, val : true});                       
                        }else{ //click col solo shift premuto
                            store.dispatch('rangeSelect',{id:id,desel:true}); 
                        }
                    }
                    
                }
            },
            rowMouseDxClicked(event) //tastp destro del mouse
            {
                if(event  && this.type!="UP"){
                    event.preventDefault();
                    EventBus.$emit("contextMenuClicked",{
                        x : event.x,
                        y : event.y,
                        id: 0,
                    });
                }
            },
        },
        mounted() { 
            this.getList();     
            this.log("ho ottenuto da Vuex i seguenti dati:");
                this.log(this.getFiles);
                this.log(this.getDirs);     
        },
        created(){
            EventBus.$on("contextMenuClicked", data => {
                        store.dispatch('deselectAll');
                        store.dispatch('setSelected',{id: data.id, val : true}); 
                    });
        }
        
    }
</script>
<style scoped>
.mycloudtabellafiles{
    overflow:scroll;
}
#tabella-files{
    flex-grow:1;
    background: white;
    border-width: 1px;
    border-style: solid;
    border-color:#a5aed563;
}
</style>


