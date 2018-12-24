/**
 * Init components
 */
require('./bootstrap');

import Vue from 'vue';
import VueTimers from 'vue-timers';

/**
 * Components
 */
import MessageList from './components/MessageList.vue';

window.Vue = Vue;
Vue.use(VueTimers);

Vue.component('message-list', MessageList);

new Vue({
    el: '#app'
});
