import '../node_modules/semantic-ui-css/semantic.min.css';
import '../node_modules/semantic-ui-css/semantic.min.js';

import Vue from 'vue';

// Field layout tabs
var fieldLayout = new Vue({
    el: '#field-layout',
    mounted: function() {
        $('.item', this.$el).tab()
    }
});

