<template>
    <div>
       
    </div>
</template>

<script>
import { store } from './vuex/storeVuex';//carica lo script Vuex
import { mapGetters } from 'vuex';

    export default {
        mounted() {
            console.log(store.getters.getInfo);
        }
    }
</script>
<style scoped>

</style>


