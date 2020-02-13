

require('./bootstrap'); //carica i css di bootstrap



//* inizializza vue e vuex 
window.Vue = require('vue');
window.Vuex = require('vuex');
window.Vue.use(Vuex);
//inizializza axios
window.axios = require('axios');
import axios from 'axios';
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': window.csrf_token
};

//abilitazione di devtool
Vue.config.devtools = true;


//dichiara i vue components dell'applicazione mycloud
Vue.component('mycloud-component', require('./components/mycloud/mycloudComponent.vue').default);
Vue.component('mycloud-navbar-component', require('./components/mycloud/mycloudNavbarComponent.vue').default);
Vue.component('mycloud-left-menu-component', require('./components/mycloud/mycloudLeftMenuComponent.vue').default);
Vue.component('mycloud-corpo-component', require('./components/mycloud/mycloudCorpoComponent.vue').default);
Vue.component('mycloud-path-component', require('./components/mycloud/mycloudPathComponent.vue').default);
Vue.component('mycloud-tab-header-component', require('./components/mycloud/mycloudTabHeaderComponent.vue').default);
Vue.component('mycloud-tabella-files-component', require('./components/mycloud/mycloudTabellaFilesComponent.vue').default);
Vue.component('mycloud-tabella-row-files-component', require('./components/mycloud/mycloudTabellaRowFilesComponent.vue').default);
Vue.component('mycloud-copier-component', require('./components/mycloud/mycloudCopierComponent.vue').default);
Vue.component('mycloud-wait-component', require('./components/mycloud/mycloudWaitComponent.vue').default);
Vue.component('tec-info-component', require('./components/mycloud/tecInfoComponent.vue').default);

Vue.component('add-folder-modal-component', require('./components/mycloud/modals/addFolderModalComponent.vue').default);
Vue.component('context-menu-component', require('./components/mycloud/modals/contexMenuComponent.vue').default);
Vue.component('my-little-popup-component', require('./components/mycloud/modals/myLittlePopupComponent.vue').default);

require('./layoutApp.js');//carica i componenti vue di layout



//Questo è necessario per far funzionare l'eventbus
export const EventBus = new Vue();


const app = new Vue({
    el: '#app',
});

