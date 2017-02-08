<template>
    <div>
        <textarea v-bind:id="config.id"
                  v-bind:name="config.name">{{ config.value }}</textarea>
    </div>
</template>

<style lang="scss">
    @import '../../node_modules/trumbowyg/dist/ui/sass/trumbowyg.scss';
</style>

<script>

    var trumbowygSvgPath = require("../../node_modules/trumbowyg/dist/ui/icons.svg");

    export default {
        name: 'rich-text',
        props: {
            config: {},
            content: {
                type: String,
                default: this.value,
            },
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
//                    resetCss: true,
                    autogrow: true
                })
                .trumbowyg('html', this.content);
        }
    }
</script>