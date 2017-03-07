<template>
    <div>
        <input type="hidden" v-bind:name="config.name" value="">

        <div class="ui labels">
            <asset-element v-for="element in elements" v-bind:element="element"></asset-element>
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
    }
</style>

<script>

    import AssetElement from './AssetElement.vue';

    export default {
        name: 'assets',
        props: {
            config: {},
        },
        data: function() {
            return {
                elements: this.config.elements,
            }
        },
        components: {
            'asset-element': AssetElement,
        },
    }

</script>