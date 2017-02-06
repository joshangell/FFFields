import '../node_modules/semantic-ui-css/semantic.min.css';
import '../node_modules/semantic-ui-css/semantic.min.js'

import Vue from 'vue'

Vue.use(require('vue-semantic'));



var app4 = new Vue({
    el: '#app-4',
    data: {
        todos: [
            { text: 'Learn JavaScript' },
            { text: 'Learn Vue' },
            { text: 'Build something awesome' }
        ]
    }
})