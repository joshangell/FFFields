import '../node_modules/semantic-ui-css/semantic.min.css';
import '../node_modules/semantic-ui-css/semantic.min.js';
import '../node_modules/trumbowyg/dist/trumbowyg.js';

import Vue from 'vue';

import TextInput from './components/TextInput.vue';
import TextArea from './components/TextArea.vue';
import RichText from './components/RichText.vue';
import LightSwitch from './components/LightSwitch.vue';

// Field layout tabs
if (document.querySelector('#field-layout')) {
    new Vue({
        el: '#field-layout',
        components: {
            'text-area' : TextArea,
            'text-input' : TextInput,
            'rich-text' : RichText,
            'lightswitch' : LightSwitch
        },
        mounted: function() {
            $('.tabular .item', this.$el).tab()
        }
    });
}
