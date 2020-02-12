
export const store = new Vuex.Store({
    state: {    //insieme di variabili globali
        loadingStatus: 'loading',
        selected: [],                   //array contenente indici di riferimento dei file e dirs selezionati
        startSelection : -1,            //indice di inizio selezione
        endSelection : -1,              //indice di fine selezione
        files : [],                     //array contenente l'elenco dei file della directory corrente
        dirs : [],                      //array contenente l'elenco delle dir della directory corrente
        idPath : "0",                   //if della path corrente
        fullPath : [],                  //array contenente tutto il percorso fino alla root
        filesToCopyOrCut :{   //informazioni tatistiche dei file selezionati tenendo conto dei contenuti delle directory
            total : 0,
            totalFiles : 0,
            totalDirs : 0,    
            totalSize : 0,  
            idSelected : [],  //id dei file da copiare o tagliare
            function : "",    //copy or cut
        },
        selectedItemsArray : [],        //contiene la selezione fatta SENZA TENER CONTO DEI CONTENUTI DELLE DIR
        filesToCopyOrCut : {},
        screenAttr : {},
       
    },
    mutations: {   //insieme di funzioni sincrone. Esse vengono invocate con un commit
        SET_LOADING_STATUS(state, status) {
            state.loadingStatus = status    //setta una variabile globale (vedi sopra: gruppo 'state:')
        },
        SET_FILES_TO_COPY(state, tocopy) {
            state.filesToCopy = tocopy   //setta una variabile globale (vedi sopra: gruppo 'state:')
        },
        SET_FILES_TO_MOVE(state, tomove) {
            state.filesToMove = tomove   //setta una variabile globale (vedi sopra: gruppo 'state:')
        },
        SET_FILES(state, files){
            state.files = files;
            //console.log(files);
        },
        SET_DIRS(state, dirs){           
            state.dirs = dirs;
            //console.log(dirs);
        },
        SET_ID_PATH(state, idPath='0'){           
            state.idPath = idPath;
        },
        SET_FULL_PATH(state, fullPath){
            state.fullPath = fullPath;
        },
        SET_SELECTED(state,valore){
            //console.log(state.selected);
            var v = state.selected.find( (item) => item.id == valore.id );
            var lenarray=state.selected.length;
            //console.log("Lunghezza array selected = "+lenarray);
            //console.log("Cerco ["+valore.id+"] ... hotrovato indice "+v);
            if(v == null || v==-1 ){ //se l'elemento non esiste nell'array
                state.selected.push({id:valore.id , val:valore.val , idref:lenarray});
                //console.log("push di :");
                //console.log(valore);
                v = state.selected.find( (item) => item.id == valore.id );
                //console.log("Cerco id="+valore.id+" ... ho trovato idref = "+v.idref);
            }
            else{
                state.selected[v.idref].val=valore.val;
                //console.log("Setto l'array ["+v.idref+"]");
            }

            //console.log("SETTED selected["+v.idref+"]=>{id:"+v.id+" , val:"+state.selected[v.idref].val+" , idref:"+v.idref+"}");
          
        },
        SET_START_SELECTION(state,valore){
            state.startSelection = valore;
            //console.log("SETTED startSelection=>"+state.startSelection);
        },
        SET_END_SELECTION(state,vid){
            var start=state.startSelection;
            var end=vid;
            if(start<0) { start=0; state.startSelection = 0; }
            if(end < start){ //inverte la selezione
                var temp=end;
                end=start;
                start=temp;
                state.startSelection = start;
            }
            state.endSelection = end;
            //console.log("start="+start+"  end="+end);
        },
        SET_SCREEN_ATTR(state,screenAttr){
            console.log("setting to ");
            console.log(screenAttr);
            state.screenAttr=screenAttr;
        }
       
           
        
    },
    actions: {  //insieme di funzioni di tipo asincrono. si invocano col metodo dispatch        
        getFileInfo({commit,dispatch,getters}){    
            var href='/getList';
            console.log("Vuex: getting info from "+href);
            let form=new FormData();
            form.append('_token',window.csrf_token);
            form.append('id_path',getters.getIdPath);
            axios.post(href,form).then( resp => {
                console.log("Vuex: ricevo i seguenti dati:");
                console.log(resp.data);
                commit('SET_FILES',resp.data.flist);
                commit('SET_DIRS',resp.data.dlist);

                dispatch('requestFullPath');
            }).catch( err => {
                console.log('Errore su richiesta GET a '+href+': '+err);
            });            
        },
        requestFullPath({getters,commit}){
            var href="/getPath"; //ricerchiamo il path completo partendo dall'id_path corrente
            let form=new FormData();
            form.append("_token",window.csrf_token);
            form.append("id_path",getters.getIdPath);
            axios.post(href,form).then( resp => {
                commit('SET_FULL_PATH', resp.data.path);
            }).catch( err => {
                console.log("Errore su richiesta POST a "+href+": "+err);
            });
        },
        chdir({dispatch,commit},id_dest){
            dispatch('deselectAll');
            commit('SET_ID_PATH',id_dest);
            dispatch('getFileInfo');
        },
        goUp({dispatch,getters,commit}){
            var href='/goUp'; //ricerchiamo il padre dell'id corrente
            let form=new FormData();
            form.append('_token',window.csrf_token);
            form.append('id_path',getters.getIdPath);
            axios.post(href,form).then( resp => {
                commit('SET_ID_PATH',resp.data.id_path);
                dispatch('getFileInfo',resp.data.id_path);
            }).catch( err => {
                console.log('Errore su richiesta POST a '+href+': '+err);
            });
        },
        setSelected({commit},valore){
            commit('SET_SELECTED',valore); 
            if(valore.val) commit('SET_START_SELECTION',valore.id);
        },
        rangeSelect({state,commit,getters}, data){
            //console.log("deselector="+deselector);
            commit('SET_END_SELECTION', data.id );
            var start=getters.getStartSelection;
            var end=getters.getEndSelection;
            if(data.desel) state.selected = [];
            //console.log("Eseguo select da "+start+"  a "+end);
            //console.log("Verifico "+getters.getFiles.length+" files...");
            for(var i=0;i<getters.getFiles.length;i++){
                if(i >= start-10001 && i <= end-10001) {
                    //console.log("commit i="+(i+10001)+".");
                    commit("SET_SELECTED",{id: i + 10001 , val:true});
                }
            }
            //console.log("Verifico "+getters.getDirs.length+" directory...");
            for(var i=0;i<getters.getDirs.length;i++){
                if(i >= start-1 && i <= end-1) {
                    //console.log("commit i="+(i+1)+".");
                    commit("SET_SELECTED",{id: i+1 , val:true});
                }
            }
        
        },
        deselectAll({state,commit}){
            state.selected = [];
            //console.log("deselectAll()=>");
            commit('SET_START_SELECTION',-1);
        },

        /************ ADD FOLDER  **************************************************************/
        addFolderModalShow(){
             $('#mymodal').modal('show');
        },   
        addFolderModalHide(){            
            $('#mymodal').modal('hide');
        },
        addFolder({getters,dispatch},folderName){
            var href="/folderCreate";
            let form=new FormData();
            form.append('_token',window.csrf_token);
            form.append('id_path',getters.getIdPath);
            form.append("folderName",folderName);
            console.log(form);
            axios.post(href,form).then( resp => {
                console.log(resp);
                dispatch('getFileInfo');
            }).catch( err => {
                console.log('Errore su richiesta POST a '+href+': '+err);
            });
        },
        /***********************************************************************************/
    },
    getters:{          
        getFiles : state => {
            return state.files;
        },
        getDirs : state => {
            return state.dirs;
        },
        getIdPath : state => {
            return state.idPath;
        },
        getFullPath : state => {
            return state.fullPath;
        },
        getSelectedItem : state => id => {
            //console.log("getSelectedItem => cerco l'id "+id);
            var v = state.selected.find( (item) => item.id == id );
            
            if(v == null || v==-1 ){ //se l'elemento non esiste nell'array
                //console.log("non trovato... ritorno false");
                return false;
            }
            //console.log("Trovato :");
            //console.log(v);
            return state.selected[v.idref].val;
        },
        getStartSelection : state => {
            return state.startSelection;
        },
        getEndSelection : state => {
            return state.endSelection;
        },
        getSelectedCount : state => {
            return state.selected.length;
        },
        getSelectedItemsArray : state => {   //ritorna la selezione fatta SENZA TENER CONTO DEI CONTENUTI DELLE DIR
            state.selectedItemsArray=[];
            for(var i=0;i<state.selected.length;i++){
                var id=state.selected[i].id;
                if(id > 10000) state.selectedItemsArray.push(state.files[id - 10001]);
                else state.selectedItemsArray.push(state.dirs[id - 1]);
            }
            return state.selectedItemsArray;
        },
        copySelectedFiles : (state, getters) => {             //informazioni statistiche dei file selezionati tenendo conto dei contenuti delle directory            
            var href='/getSelectedFilesRecursiveStat';
            axios.post(href,getters.getSelectedItemsArray)
            .then(resp => {
                state.filesToCopyOrCut=resp.data;
                state.filesToCopyOrCut.function = "copy";
                //console.log("copySelectedFiles : ho conservato i seguenti dati :");
                //console.log(state.filesToCopyOrCut);
            })
            .catch(err => {
                console.log('Errore su richiesta POST a '+href+': '+err);
            });               
        },
        cutSelectedFiles : (state, getters) => {             //informazioni statistiche dei file selezionati tenendo conto dei contenuti delle directory            
            var href='/getSelectedFilesRecursiveStat';
            axios.post(href,getters.getSelectedItemsArray)
            .then(resp => {
                state.filesToCopyOrCut=resp.data;
                state.filesToCopyOrCut.function = "cut";
                //console.log("cutSelectedFiles : ho conservato i seguenti dati :");
                //console.log(state.filesToCopyOrCut);
            })
            .catch(err => {
                console.log('Errore su richiesta POST a '+href+': '+err);
            });               
        },
        getFilesToCopyOrCut : (state) => {
            return state.filesToCopyOrCut;
        },
        pasteFiles : (state, getters) => {
            var href='/pasteFiles';
            axios.post(href,getters.getFilesToCopyOrCut)
            .then(resp => {
                console.log(resp);
            })
            .catch(err => {
                console.log('Errore su richiesta POST a '+href+': '+err);
            });
        },
        getAddFolderModalShow : (state) => {
            return state.addFolderModalShow;
        },
        getScreenAttr : (state) => {
            return state.screenAttr;
        }
    }
});
