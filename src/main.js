import '../node_modules/semantic-ui-css/semantic.min.css';
import '../node_modules/semantic-ui-css/semantic.min.js';

import Vue from 'vue';

import TextInput from './components/TextInput.vue';
import TextArea from './components/TextArea.vue';

// Field layout tabs
if (document.querySelector('#field-layout')) {
    new Vue({
        el: '#field-layout',
        components: {
            'text-area' : TextArea,
            'text-input' : TextInput
        },
        mounted: function() {
            $('.tabular .item', this.$el).tab()
        }
    });
}

