<template>

    <draggable v-bind:blocks="blocks" v-bind:options="options">
        <div v-for="block in blocks" class="ui segment">

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
                blocks : this.config.blocks,
            }
        },
        beforeCreate: function () {
            this.$options.components.field = require('./Field.vue')
        }
    }
</script>


