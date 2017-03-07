<template>
    <div>
        <asset-element v-for="element in elements" v-bind:element="element"></asset-element>
    </div>
</template>

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