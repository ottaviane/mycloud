<template>
    <div class="myLittlePopup"  id="myprettypopup" :class="{visibile : visibile}">
       
            <p v-for="(item,id) in testo" :key="id">{{ item }}</p><br>
            <br>
       
    </div>
</template>

<script>
import { EventBus } from "../../../mycloudApp.js";
    export default {
        data() {
            return {
                visibile:false,
                testo:[],
                timeout : Object,
            }
        },
        components : {
        },
        props:  {

        },
        methods:    {
           
        },
        created() {
            EventBus.$on("showThisLittleMessage",data =>{ 
                var el=document.getElementById("myprettypopup")   ;
                this.testo.push(data.message);
                el.scrollTop += 100;
                this.visibile=true;
                let that=this;
                clearTimeout(this.timeout);
                this.timeout = setTimeout(function(){that.visibile=false;},1500);
                if(this.testo.length > 15) this.testo.shift();//se l'array cresce troppo lo riduciamo togliendo gli items più vecchi
            });

        },
        mounted() {

        }
    }
</script>

<style scoped>
.myLittlePopup{
    opacity: 0;
    transition: opacity 1s;
    position: absolute;
    background: #040404a8;
    height: 8rem;
    width: 10rem;
    bottom: 0px;
    right: 0px;
    margin: 8px;
    padding: 1rem;    
    border-style: outset;
    border-color:#040404a8;
    border-width: 1px;
    overflow-x:inherit;
    overflow-y: hidden !important;    
    display:block !important;
}
.visibile{
    opacity:1;
    transition: opacity 1s;
}
p{
    margin:0px;
    font-family: Lucida Console, Lucida Sans Typewriter, monaco, Bitstream Vera Sans Mono, monospace;
    font-size: 0.6rem;
    color:white;
}
</style>
