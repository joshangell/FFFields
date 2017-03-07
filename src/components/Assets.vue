<template>
    <div>
        <input type="hidden" v-bind:name="config.name" value="">

        <div class="ui labels">
            <draggable v-bind:options="options">
                <asset-element v-for="element in elements" v-bind:element="element"></asset-element>
            </draggable>
        </div>

        <button type="button" class="ui small basic labeled icon button">
            <i class="add icon"></i>
            {{ config.selectionLabel }}
        </button>
    </div>
</template>

<style lang="scss">
    .ui.labels .ui.image.label {
        margin-bottom: 0.5rem;
        cursor: default;
        user-select: none;
    }
</style>

<script>
    import Draggable from 'vuedraggable';

    import AssetElement from './AssetElement.vue';

    export default {
        name: 'assets',
        props: {
            config: {},
        },
        data: function() {
            return {
                elements: this.config.elements,
                options: {
                    ghostClass: 'disabled',
                    disabled: this.config.elements.length <= 1
                },
            }
        },
        components: {
            'draggable'     : Draggable,
            'asset-element' : AssetElement,
        },
    }
</script>