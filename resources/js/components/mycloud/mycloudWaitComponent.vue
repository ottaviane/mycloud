<template>          
    <div v-if="getWaiting()" id="waiting">{{ getWaiting() }}</div>           
</template>

<script>
import { store } from './vuex/storeVuex';//carica lo script Vuex
import { mapGetters } from 'vuex';
    export default {
        store,
        name : "mycloudWaitComponent",       
        methods: {            
            ...mapGetters(['getWaiting']),        
           
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
}
</style>


