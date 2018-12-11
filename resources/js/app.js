/**
 * Init components
 */

require('./bootstrap');

import Vue from 'vue';
import VueTimers from 'vue-timers';

window.Vue = Vue;
Vue.use(VueTimers);

Vue.component('message-list', require('./components/MessageList.vue'));

new Vue({
    el: '#app'
});
