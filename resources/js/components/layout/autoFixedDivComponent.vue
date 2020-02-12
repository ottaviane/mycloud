<template>
    <div class="statoInizialeClass"
        ref="fixeddiv"
        :style="{width : window.width , height : window.height, top : window.top, left: window.left}"
        :class="{Hflex : flex == 'c'}"
    >
        <!-- <p> {{ window.width }} x {{ window.height }} {{hflex}} </p> -->
        <slot v-if="visibile"></slot>
    </div>

</template>

<script>
import { EventBus } from "../../mycloudApp.js";

    export default {
        name : 'auto-fixed-div-component',
        data() {
            return {
                id:null,
                window : { height:"100%",
                           width:"100%",
                           top:"0px",
                           left:"0px"},
                visibile: false,
            }
        },
        components : {
        },
        props:  {
            columns:{},
            rows:{},
            flex: {type:String, default:"c"},
        },
        methods:    {
            log(message){
                //console.log(this.$options.name+": "+message);
            },
            fissaLeDimensioni(){
                //legge le dimensioni della finestra del padre
                this.log("adesso le mie dimensioni sono: "+this.window.height+"x"+this.window.width);
                var questo=this.$refs.fixeddiv;
                var padre=questo.parentNode;
                //this.log(padre);
                var h=padre.offsetHeight;
                var w=padre.offsetWidth;

                this.log("Leggo le dimensione di mio padre: "+h+"x"+w);
                this.log("Adesso prendo le sue sembianze...");
                this.window.height=h+"px";
                this.window.width=w+"px";
                this.log("ecco le mie nuove dimensioni: "+this.window.height+"x"+this.window.width);

                this.visibile=true;
                this.log("adesso sono visibile");
                   
                //Quanti pixel è 1 rem?   
                var remToPixel = parseFloat(getComputedStyle(document.documentElement).fontSize);
                //Quanto è alto e largo il document?
                var body = document.body, html = document.documentElement;
                var bodyH = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
                var bodyW = Math.max( body.scrollWidth, body.offsetWidth, html.clientWidth, html.scrollWidth, html.offsetWidth );

                EventBus.$emit("LayoutScreenDimensionModified",{
                    h : h,
                    w : w,
                    remToPixel : remToPixel,
                    bodyH : bodyH,
                    bodyW : bodyW,
                });
            },
            onDimensioniModificate(){                
                this.log("evento del DOM registrato:onDimensioniModificate");
                let that=this;
                this.log("mi rendo invisibile");
                this.visibile=false;
                this.log("setto le mie dimensioni a 0...");
                this.window.height="0px";
                this.window.width="0px";
                Vue.nextTick(function(){that.fissaLeDimensioni();});

            },
            onDocumentLoaded(){
                this.log("evento del DOM registrato:onDocumentLoaded");
                this.onDimensioniModificate();
            },

        },
        created() {
            window.addEventListener('resize', this.onDimensioniModificate);//associa l'evento onresize della window
            window.addEventListener('load', this.onDocumentLoaded);//associa l'evento onload del document
            this.id=this._uid;
        },
       

    }
</script>
<style scoped>
    .statoInizialeClass{
        position: relative;
        overflow: auto;
    }
</style>
