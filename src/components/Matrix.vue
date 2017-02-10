<template>

    <draggable v-bind:options="options">

        <matrix-block v-for="blk in config.blocks" v-bind:block="blk"></matrix-block>

    </draggable>

</template>

<script>
    import Draggable from 'vuedraggable';

    import MatrixBlock from './MatrixBlock.vue';

    export default {
        name: 'matrix',
        props: {
            config : {},
        },
        components : {
            'draggable'    : Draggable,
            'matrix-block' : MatrixBlock,
        },
        data: function() {
            return {
                options: {
                    handle     : '.move',
                    ghostClass : 'disabled',
                    chosenClass: 'chosen'
                },
            }
        }
    }
</script>


