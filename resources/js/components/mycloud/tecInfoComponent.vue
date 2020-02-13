<template>
    <div id="tecInfoContainer">
        <div id="propName">
            <p>path ID:</p>
            <p v-if="getSelectedCount>0">selected files:</p>
            <p v-if="getFilesToCopyOrCut.total>0 ">files to {{ getFilesToCopyOrCut.function }}:</p>
            
        </div>
        <div id="propValues">
            <p>{{ getIdPath }}</p>
            <p v-if="getSelectedCount>0">{{ getSelectedCount }}</p>
            <p v-if="getFilesToCopyOrCut.total>0 ">{{ getFilesToCopyOrCut.total }}</p>
        </div>
    </div>
</template>

<script>
import { store } from './vuex/storeVuex';//carica lo script Vuex
import { mapGetters } from 'vuex';


    export default {
        name : "tecInfoComponent",
        store,
        computed : {
            ...mapGetters(['getSelectedCount','getRecursiveSelectedCount','getFilesToCopyOrCut','getIdPath','getWaiting'])
        },
        methods:{
            wait(){
                console.log("cooo");
                store.commit('SET_WAITING',true);
            }
        }
    }
</script>
<style scoped>
    #tecInfoContainer{        
        width: 100%;
        height: 100%;
        display:flex;
        padding: 1rem;
        font-size: 0.7rem;
    }
    #propName{        
        flex-grow: 10;
        overflow: hidden;
        text-align: right;
        margin-right: 1rem;
    }
    #propValues{        
        flex-grow: 18;
        overflow: hidden;
    }
</style>


