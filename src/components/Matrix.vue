<template>

    <div class="matrix">

        <draggable v-bind:options="options">
            <matrix-block v-for="blk in config.blocks" v-bind:block="blk"></matrix-block>
        </draggable>

        <div class="ui buttons">
            <template v-for="(blockType, index) in config.blockTypes">
                <template v-if="index == 0">
                    <button class="ui labeled icon button" role="button" v-on:click="addBlock">
                        <i class="left plus icon"></i>
                        {{ blockType.name }}
                    </button>
                </template>
                <template v-else>
                    <button class="ui button" role="button" v-on:click="addBlock">
                        {{ blockType.name }}
                    </button>
                </template>
            </template>
        </div>

    </div>

</template>

<style lang="scss">
    .matrix > .ui.buttons {
        margin-top: 1rem;
    }
</style>

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
        },
        methods: {
            addBlock: function(event) {
                event.preventDefault();
                alert('TODO!');
            }
        }
    }
</script>


