<template>
<div id='contextMenu'
    ref="contextMenu"
    tabindex="0"
    :class="{cmvisibile : visibile}"
    :style="{ left: x , top : y, width: (w+'rem'), height: (h+'rem')}"
    @blur="myblur"

>

    <div class="menuitem" @click="copia" :class="{disabled : (getSelectedCount<=0)}" ><i class="far fa-copy"></i></i>Copia</div>
    <div class="menuitem" @click="taglia" :class="{disabled : (getSelectedCount<=0)}"><i class="fas fa-cut"></i>Taglia</div>
    <div class="menuitem" @click="incolla" :class="{disabled : (getFilesToCopyOrCut.total<=0)}"><i class="fas fa-clone"></i>Incolla</div>
    <div class="menuline"></div>
    <div class="menuitem" @click="rinomina" :class="{disabled : (getSelectedCount<=0)}"><i class="fas fa-edit"></i>Rinomina</div>
    <div class="menuitem" @click="elimina" :class="{disabled : (getSelectedCount<=0)}"><i class="fas fa-backspace"></i>Elimina</div>
    <div class="menuline"></div>
    <div class="menuitem" @click="condividi" :class="{disabled : (getSelectedCount<=0)}"><i class="fas fa-share-alt"></i>Condividi</div>
</div>
</template>

<script>
import { EventBus } from "../../../mycloudApp.js";
import { store } from '../vuex/storeVuex';//carica lo script Vuex
import { mapGetters } from 'vuex';
    export default {
        name : "contexMenuComponent",
        store,
        computed:{ 
            ...mapGetters(['getSelectedCount','getScreenAttr','getFilesToCopyOrCut'])
        },
        data() {
            return {
                visibile:false,
                x : "0px",
                y : "0px",
                w : "10",
                h : "15",
            }
        },
        components : {
        },
        props:  {

        },
        methods:    {
            copia(){
                console.log("dispatching...");
                store.dispatch('copySelectedFiles');
                console.log("...dispatched");
                this.visibile=false;
            },
            taglia(){
                store.dispatch('cutSelectedFiles');
                this.visibile=false;
            },
            incolla(){
                store.dispatch('pasteFiles');
                this.visibile=false;
            },
            rinomina(){
            },
            elimina(){
            },
            condividi(){
            },
            myblur(){
                console.log(this.$options.name+": event blur hooked");
                this.visibile=false;
            },
        },
        created() {
            let that=this;
            EventBus.$on("contextMenuClicked", data => {
                that.visibile=true;
                var x=parseInt(data.x);
                var y=parseInt(data.y);    
                var screen_h=parseInt(this.getScreenAttr.bodyH);
                var screen_w=parseInt(this.getScreenAttr.bodyW); 
                var scr_ar=parseInt(this.getScreenAttr.remToPixel);//dimensione in pixel di un rem
                var hpx=parseInt(this.h) * scr_ar;//altezza in pixel del context menu
                var wpx=parseInt(this.w) * scr_ar;//larghezza in pixel del context menu
                console.log(this.getScreenAttr);
                console.log(y + " + " + hpx + " = " + (y+hpx));
                console.log("screen_h = "+screen_h);
                if((x+wpx)>(screen_w)) x-=wpx;
                if((y+hpx)>(screen_h)) y=screen_h - hpx;             
                
                that.x=x+"px";
                that.y=y+"px";             
                Vue.nextTick().then(function(){        
                    console.log(that.$refs);
                    that.$refs.contextMenu.focus();
                });
            });

        },
        mounted() {

        }
    }
</script>

<style scoped>
#contextMenu{
    display:flex;
    flex-direction: column;
    position:fixed;
    -webkit-box-shadow: 0px 0px 12px 0px rgba(0,0,0,0.75);
    -moz-box-shadow: 0px 0px 12px 0px rgba(0,0,0,0.75);
    box-shadow: 0px 0px 12px 0px rgba(0,0,0,0.75);
    border-radius: 2px 2px 2px 2px;
    -moz-border-radius: 2px 2px 2px 2px;
    -webkit-border-radius: 2px 2px 2px 2px;
    /* width: 10rem;
    height: 15rem; */
    background: white;
    padding: 1rem;
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}
.cmvisibile{
    opacity:1 !important;
    transition: opacity 0.3s;
    z-index: 2 !important;
}
.menuline{
    border-top: #bfb9b9 solid 1px;
}
.menuitem:hover{
    background: #afdf75;
}
.menuitem{
    cursor:pointer;
}
.disabled{
    color:rgb(211, 211, 211);
}
i {
    margin: 10px;
    color:
    #4a62fe;
}
</style>
