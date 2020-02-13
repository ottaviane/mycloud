<template>
    <div id='tab-row' class="noselect"
        @dblclick="rowdblclicked($event)"
        @contextmenu="rowMouseDxClicked($event)"
        :class="{ rowselected : selected , cutting : cutting , copying : copying}"
    >
        <div id="rn">
            <img src="/img/iconmonstr-folder-1-24.png" v-if="type=='DIR'" style="margin: 14px;">
            <img src="/img/iconmonstr-file-4-24.png" v-if="type=='FILE'" style="margin: 14px;">
            <img src="/img/iconmonstr-arrow-76-24.png" v-if="type=='UP'" style="margin: 14px;">
            <span v-if="type=='UP'"> .. </span>
            {{ row.val1 }} 
        </div>
        <div id="rs">{{ dimensiona(row.val4) }}</div>
        <div id="rlm">{{ row.updated_at }}</div>
    </div>
</template>

<script>
import { EventBus } from "../../mycloudApp.js";
import { store } from './vuex/storeVuex';//carica lo script Vuex
import { mapGetters } from 'vuex';

    export default {
        name: "mycloudTabellaRowFilesComponent",
        store,
        data() {
            return {
                startSelection : "",
                endSelection : "",
                copying : false,
                cutting : false,
            }
        },
        computed:{ 
            ...mapGetters(['getIdPath','getSelectedItem'])
        },
        props:  {
            row:Object,
            type:String,
            selected: false,
            n:0,
        },
        methods:{
            dimensiona(bytes){
                if(this.type=='UP') return "";
                var fattore=1;
                var udm="bytes";
                if(bytes>(1024*1024*1024)){ fattore=1024*1024*1024; udm="Gb"; }
                else if(bytes>(1024*1024)){ fattore=1024*1024; udm="Mb"; }
                else if(bytes>(1024)){ fattore=1024; udm="Kb"; }

                return (bytes/fattore).toFixed(1)+udm;
            },
            rowdblclicked(event){
                store.dispatch('deselectAll');
                if(event) event.preventDefault();
                if(this.type=="DIR") store.dispatch('chdir',this.row.id);
                if(this.type=="UP")  store.dispatch('goUp',this.getIdPath);                    

            },
            rowMouseDxClicked(event) //tastp destro del mouse
            {
                if(event  && this.type!="UP"){
                    event.preventDefault();
                    event.stopPropagation();
                    console.log(this.$options.name+": event hooked on dx mouse click.");
                    EventBus.$emit("contextMenuClicked",{
                        x : event.x,
                        y : event.y,
                        id: this.n,
                    });
                }
            },
            
        },
    }
</script>
<style>
#tab-row{
    max-height:56px;
    min-height:56px;
    display:flex;
    padding: 8px;
    cursor:pointer;
}
#tab-row:hover{
    filter: brightness(45%);
}
#rn{
    flex-grow: 1;
    align-items: center;
    display: flex;
}
#rs, #rlm{
    min-width: 8rem;
    max-width: 20rem;
    display: flex;
    align-items: center;
}
.rowselected{
    background: #e600009e;
    transition: background 0.2s;
}
.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */

    transition: background 0.4s;
}
.cutting{
    text-decoration: line-through;
    color:gray;
}
.copying{
    color:green;
}
</style>



