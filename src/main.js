import '../node_modules/semantic-ui-css/semantic.min.css';
import '../node_modules/semantic-ui-css/semantic.min.js';
import '../node_modules/trumbowyg/dist/trumbowyg.js';

import Vue from 'vue';

import Field from './components/Field.vue';

// Field layout tabs
if (document.querySelector('#field-layout')) {
    new Vue({
        el: '#field-layout',
        components: {
            'field' : Field
        },
        mounted: function() {
            $('.tabular .item', this.$el).tab()
        }
    });
}
