<template>
<div class="modal" tabindex="-1" role="dialog" id="mymodal" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Creazione di una nuova cartella</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="exampleInputEmail1">Nome della nuova cartella:</label>
            <input ref="inpFolderName" type="text" class="form-control" placeholder="Enter folder name ..." v-model="folderName" @keyup="keyup">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" :class="{disabled : isdisabled}" @click="save">Crea la cartella</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import { store } from '../vuex/storeVuex';//carica lo script Vuex
import { mapActions } from 'vuex';//carica lo script Vuex
    export default {
      name : "addFolderModalComponent",
        store,
        data() {
            return {
                folderName:"",
                isdisabled:true,
            }
        },
        components : {
        },
        props:  {

        },
        methods: {
            ...mapActions(['addFolder']),
            mylog(msg){
                console.log(this.$options.name+" => "+msg);
            },
            keyup(){
                if(this.folderName!="") this.isdisabled=false;
                else this.isdisabled=true;
            },
            save(){
              if(!this.isdisabled){
                store.dispatch('addFolder',this.folderName);
                $('#mymodal').modal('hide');
              }
            },
           
        },
        created() {
             
        },
        mounted() {
          var that=this;
          $('#mymodal').on('shown.bs.modal',function (e) {
              //that.mylog("Modal shown");
              Vue.nextTick().then(function(){
                console.log(that.$refs.inpFolderName);
                that.$refs.inpFolderName.focus();
              });

          });
          $('#mymodal').on('hidden.bs.modal',function (e) {
              //that.mylog("Modal hidden");
          });
        }
    }
</script>


