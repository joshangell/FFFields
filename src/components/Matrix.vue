<template>

    <draggable v-bind:blocks="blocks" v-bind:options="options">
        <div v-for="block in blocks" class="ui segment">

            <div class="ui top attached label">
                {{ block.name }}
                <i data-content="Move" class="move icon"></i>
            </div>

            <component v-for="field in block.fields" v-bind:is="field.component.type" v-bind:config="field.component.config" class="field"></component>

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

    import TextInput from './TextInput.vue';
    import TextArea from './TextArea.vue';
    import RichText from './RichText.vue';
    import LightSwitch from './LightSwitch.vue';

    export default {
        name: 'lightswitch',
        props: {
            config : {},
        },
        components : {
            'draggable'   : Draggable,
            'text-area'   : TextArea,
            'text input'  : TextInput,
            'rich-text'   : RichText,
            'lightswitch' : LightSwitch,
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
        }
    }
</script>


