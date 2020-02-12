<template>
    <div id="lm">
        
           <div v-if="wait" id="waiting"></div>
           <ul class="nav flex-column">
            <li class="nav-item" style="border-bottom-width: 1px;border-bottom-style: solid;border-bottom-color: #a5aed5;">
                <img src="/img/iconmonstr-weather-12-24.png">
                <a class="nav-link" href="#">My files</a>
            </li>
            <li class="nav-item" @click="addFolderClicked()">
                <img src="/img/iconmonstr-folder-3-24.png">
                <a class="nav-link" href="#">New Folder</a>
            </li>
            <li class="nav-item" @click="newFileClicked">
                <img src="/img/iconmonstr-file-13-24.png">
                <a class="nav-link" href="#">New File</a>
            </li>
            <li class="nav-item">
                <img src="/img/iconmonstr-gear-4-24.png">
                <a class="nav-link" href="#">Settings</a>
            </li>
            <li class="nav-item">
                <img src="/img/iconmonstr-log-out-7-24.png">
                <a class="nav-link" href="#">Logout</a>
            </li>
          </ul>
        
        <form style='display:none' id="fileform">
            <input id="path_doc_input" type='file' name="path_docs[]" multiple @change="fileScelti($event)">
        </form>
        <add-folder-modal-component></add-folder-modal-component>
    </div>
</template>

<script>
import { EventBus } from "../../mycloudApp.js";
import { store } from './vuex/storeVuex';//carica lo script Vuex
import { mapActions } from 'vuex';//carica lo script Vuex
import { mapGetters } from 'vuex';
    export default {
        store,
        name : "mycloudLeftMenuComponent",
        data() {
            return {
                files:[],
                wait:false,
                id_path:"0",
            }
        },
        components : {
        },
        props:  {

        },
        methods:    {
            ...mapActions(['addFolderModalShow']),
            ...mapGetters(['getAddFolderModalShow']),
            mylog(msg){
                console.log(this.$options.name+" => "+msg);
            },
            newFileClicked(){
                $('#path_doc_input').click();
            },
            fileScelti(event){
                this.wait=true;
                let form=new FormData();
                form.append("_token",window.csrf_token);
                for(var item in event.target.files) {
                    var stritem="files["+item+"]";
                    form.append(stritem,event.target.files[item]);
                }
                form.append("id_path",this.id_path);
                axios.post("/upload",form).then(resp => {
                    this.wait=false;
                    this.mylog(resp.data);
                }).catch( err => {
                    this.wait=false;
                    this.mylog("upload fallito: "+err);
                    alert("upload fallito: "+err);
                });
            },
            addFolderClicked(){
                //EventBus.$emit("addFolderClicked",{});//avvia la form che richiede il nome della nuova cartella
                store.dispatch('addFolderModalShow').then(()=> {
                    //this.mylog("store.dispatch('addFolderModalShow')");
                });
            },
        },
        created() {
            // EventBus.$on("addFolderWhithName", data => {
               
            // });
             EventBus.$on("ChangeDirectory", data => {
                this.id_path= data.id_path;

            });
        },
        mounted() {

        }
    }
</script>
<style scoped>
#lm{
    flex-grow: 1;
    max-width: 14rem;
    min-width: 3rem;    

}
#fisso{
    overflow-y: scroll;
    overflow-x: inherit;
    position: relative;
    width: 100%;
    height: 100%;
    top:0px;
    left:0px;
}
ul{
    background: #f8fafc;
    padding-left: 20px;
    margin-top:20px;
}
img {
    float: left;
    margin-right: 0px;
}
li {
    display: flex;
    align-items: center;
    margin-top: 5px;
    margin-bottom: 5px;
}
#waiting {

    position: absolute;
    height: 100%;
    width: 100%;
    background:

    #000000a3;

}
</style>


