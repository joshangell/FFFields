<template>
    <table class="ui single line table seomatic">

        <thead>
            <th></th>
            <th>Source</th>
            <th>Value</th>
        </thead>

        <tbody>

            <tr>
                <th>Title</th>
                <td>
                    <select class="ui dropdown fluid"
                            v-bind:name="config.name + '[seoTitleSource]'"
                            v-bind:id="config.id + '-seoTitleSource'"
                            v-model="seoTitleSource">
                        <option value="custom">Custom</option>
                        <option value="field">From {{ value.seoTitleSourceField }} field</option>
                    </select>
                </td>
                <td>
                    <input type="text"
                           v-bind:value="value.seoTitle"
                           v-bind:id="config.id + '-seoTitle'"
                           v-bind:name="config.name + '[seoTitle]'"
                           v-bind:disabled="seoTitleSource === 'field'">
                </td>
            </tr>

            <tr>
                <th>Description</th>
                <td>
                    <select class="ui dropdown fluid"
                            v-bind:name="config.name + '[seoTitleDescription]'"
                            v-bind:id="config.id + '-seoTitleDescription'"
                            v-model="seoTitleDescription">
                        <option value="custom">Custom</option>
                        <option value="field">From {{ value.seoTitleDescriptionField }} field</option>
                    </select>
                </td>
                <td>
                    <input type="text"
                           v-bind:value="value.seoDescription"
                           v-bind:id="config.id + '-seoDescription'"
                           v-bind:name="config.name + '[seoDescription]'"
                           v-bind:disabled="seoTitleDescription === 'field'">
                </td>
            </tr>

        </tbody>
    </table>
</template>

<script>
    export default {
        name: 'seomatic',
        props: ['config'],
        data: function() {
            return {
                seoTitleSource : this.config.value.seoTitleSource,
                seoTitleDescription : this.config.value.seoTitleDescription,
                value : this.config.value,
            }
        },
        mounted: function() {
            $('.ui.dropdown', this.$el).dropdown();
        },
    }
</script>