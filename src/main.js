import './semantic/dist/semantic.css';

import './semantic/dist/components/form';
import './semantic/dist/components/checkbox';
import './semantic/dist/components/dropdown';
import './semantic/dist/components/tab';
import './semantic/dist/components/transition';
import './semantic/dist/components/state';
import './semantic/dist/components/visibility';

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
