<template>
    <centered-div-component>
        <div id='baseCloud'>
            <mycloud-navbar-component  id="barraSup">
            </mycloud-navbar-component>
            <div id="corpo">
                <auto-fixed-div-component style="display:flex">
                    <mycloud-left-menu-component></mycloud-left-menu-component>
                    <mycloud-corpo-component></mycloud-corpo-component>
                    <context-menu-component></context-menu-component>
                </auto-fixed-div-component>
            </div>
        </div>
        <my-little-popup-component></my-little-popup-component>
    </centered-div-component>
</template>

<script>
import { EventBus } from "../../mycloudApp.js";
import { store } from './vuex/storeVuex';//carica lo script Vuex

    export default {
        store,
        mounted() {
            //console.log(store.getters.getInfo);
        },
        created(){
            EventBus.$on("LayoutScreenDimensionModified", data => {
                console.log("myCloudComponent : received screen resize:");
                console.log(data);
                store.commit('SET_SCREEN_ATTR',data);
            });
        }
    }
</script>
<style scoped>
#baseCloud{
    display:flex;
    flex-direction: column;
    position:relative;
    top:0px;
    left:0px;
    width: 100%;
    height: 100%;
}
#barraSup{

}
#corpo{
    flex-grow: 1;
    display:flex;
    flex-direction: row;
    margin-top:4px;
    min-width: 50rem;
}
</style>


