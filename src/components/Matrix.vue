<template>

    <draggable v-bind:list="list" v-bind:options="options">
        <div v-for="element in list" class="ui segment">

            <div class="ui top attached label">
                Block
                <i data-content="Move" class="move icon"></i>
            </div>
            <p>{{ element.name }}</p>

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

    export default {
        name: 'lightswitch',
        props: {
            config : {},
        },
        components : {
            'draggable'   : Draggable,
        },
        data: function() {
            return {
                options: {
                    handle     : '.move',
                    ghostClass : 'disabled',
                    chosenClass: 'chosen'
                },
                list : [
                    { name : "One"   },
                    { name : "Two"   },
                    { name : "Three" },
                    { name : "Four"  },
                ]
            }
        }
    }
</script>


