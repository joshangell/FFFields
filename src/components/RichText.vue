<template>
    <div>
        <textarea v-bind:value="value"
                  v-bind:id="config.id"
                  v-bind:name="config.name">{{ config.value }}</textarea>
    </div>
</template>

<style lang="scss">
    @import '../../node_modules/trumbowyg/dist/ui/sass/trumbowyg.scss';
</style>

<script>

    const trumbowygSvgPath = require("../../node_modules/trumbowyg/dist/ui/icons.svg");

    export default {
        name: 'rich-text',
        props: {
            config: {},
            svgPath: {
                type: String,
                default: trumbowygSvgPath,
            },
        },
        data: function() {
            return {
                value : this.config.value,
            }
        },
        mounted: function() {
            $.trumbowyg.svgPath = this.svgPath;
            $('textarea', this.$el)
                .trumbowyg({
                    autogrow: true
                })
                .on('tbwchange', this.onChange)
                .trumbowyg('html', this.value);
        },
        methods: {
            onChange() {
                this.value = $('textarea', this.$el).trumbowyg('html');
            },
        },
    }

</script>