<template>          
    <div :style="{visibility : getDisplay(),opacity : getOpacity()}" id="waiting">{{ getWaiting() }}</div>           
</template>

<script>
import { store } from './vuex/storeVuex';//carica lo script Vuex
import { mapGetters } from 'vuex';
    export default {
        store,
        name : "mycloudWaitComponent",       
        methods: {            
            ...mapGetters(['getWaiting']),        
            getDisplay(){
                if(this.getWaiting()) return 'visible';
                else return 'hidden';
            },        
            getOpacity(){
                if(this.getWaiting()) return '1';
                else return '0';
            },

        },
    }
</script>
<style scoped>

#waiting {
    position: absolute;
    height: 100%;
    width: 100%;
    background: #000000a3;
    top: 0px;
    left: 0px;
    display: block;
    visibility: hidden;
    opacity : 0;
    transition: visibility 2s,opacity 2s;
}
</style>


