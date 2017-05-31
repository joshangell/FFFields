<template>

    <div class="matrix">

        <div class="matrix-header">
            <div class="ui tiny compact basic buttons">
                <button role="button" class="ui button" v-on:click="expandAll">
                    <i class="left expand icon"></i>
                    Expand All
                </button>
                <button role="button" class="ui button" v-on:click="collapseAll">
                    <i class="left compress icon"></i>
                    Collapse All
                </button>
            </div>
        </div>

        <draggable v-bind:options="options">
            <matrix-block v-for="blk in blocks" v-bind:block="blk" v-bind:is-variant-field="isVariantField" v-on:madeDefault="onMadeDefault" ref="matrixBlock"></matrix-block>
        </draggable>

        <div class="matrix-footer">
            <div class="ui small buttons">
                <template v-for="(blockType, index) in blockTypes">
                    <template v-if="index == 0">
                        <button class="ui labeled icon button" role="button" v-on:click="addBlock(blockType, $event)">
                            <i class="left plus icon"></i>
                            {{ blockType.name }}
                        </button>
                    </template>
                    <template v-else>
                        <button class="ui button" role="button" v-on:click="addBlock(blockType, $event)">
                            {{ blockType.name }}
                        </button>
                    </template>
                </template>
            </div>
        </div>

    </div>

</template>

<style lang="scss">
    .matrix-header {
        height: 1em;
        .ui.buttons {

            position: absolute;
            top: 0;
            right: 0;
        }
    }
    .matrix-footer {
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
                blocks         : this.config.blocks,
                blockTypes     : this.config.blockTypes,
                totalNewBlocks : this.config.totalNewBlocks,
                isVariantField : this.config.isVariantField,
                options: {
                    handle      : '.move',
                    ghostClass  : 'disabled',
                    chosenClass : 'chosen'
                },
            }
        },
        methods: {
            addBlock: function(blockType, event) {
                event.preventDefault();

                this.totalNewBlocks += 1;

                const id = 'new' + this.totalNewBlocks;

                let newBlock = JSON.stringify(blockType);

                newBlock = newBlock.replace(/__BLOCK__/g, id);

                newBlock = JSON.parse(newBlock);

                this.blocks.push(newBlock);
            },
            expandAll: function(event) {
                event.preventDefault();
                for (let i = 0; i < this.$refs.matrixBlock.length; i++) {
                    this.$refs.matrixBlock[i].expand();
                }
            },
            collapseAll: function(event) {
                event.preventDefault();
                for (let i = 0; i < this.$refs.matrixBlock.length; i++) {
                    this.$refs.matrixBlock[i].collapse();
                }
            },
            onMadeDefault: function() {
                for (let i = 0; i < this.$refs.matrixBlock.length; i++) {
                    this.$refs.matrixBlock[i].makeNotDefault();
                }
            }
        }
    }
</script>


