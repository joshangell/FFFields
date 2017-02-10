<template>

    <draggable v-bind:options="options">

        <div v-for="block in config.blocks" class="ui segment">

            <input type="hidden" v-bind:name="block.type.name" v-bind:value="block.type.value">
            <input type="hidden" v-bind:name="block.enabled.name" v-bind:value="block.enabled.value">

            <div class="ui top attached label">
                {{ block.name }}
                <i data-content="Move" class="move icon"></i>
            </div>

            <field v-for="f in block.fields" v-bind:config="f.config"></field>

        </div>
        
    </draggable>

</template>

<style lang="scss">
    .ui.top.attached.label .move {
        float: right;
        margin-right: 0;
        opacity: 0.75;
        cursor: move;

        &:hover {
            opacity: 1;
        }
    }
</style>

<script>
    import Draggable from 'vuedraggable';

    import Field from './Field.vue';

    export default {
        name: 'matrix',
        props: {
            config : {},
        },
        components : {
            'draggable' : Draggable,
            'field'     : Field,
        },
        data: function() {
            return {
                options: {
                    handle     : '.move',
                    ghostClass : 'disabled',
                    chosenClass: 'chosen'
                },
            }
        },
        // This is key, see here: https://vuejs.org/v2/guide/components.html#Circular-References-Between-Components
        beforeCreate: function () {
            this.$options.components.field = require('./Field.vue')
        }
    }
</script>


