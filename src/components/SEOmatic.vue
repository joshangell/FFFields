<template>
    <table class="ui definition table seomatic">

        <thead>
            <tr>
                <th></th>
                <th>Source</th>
                <th>Value</th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <td rowspan="2" class="top aligned">Title</td>
                <td>
                    <div class="ui equal width grid">
                        <div class="column">
                            <select class="ui dropdown fluid"
                                    v-bind:name="config.name + '[seoTitleSource]'"
                                    v-bind:id="config.id + '-seoTitleSource'"
                                    v-model="seoTitleSource">
                                <option value="custom">Custom Text</option>
                                <option value="field">From Field</option>
                            </select>
                        </div>
                        <div v-show="seoTitleSource === 'field'" class="column field-list">
                            <select class="ui dropdown fluid"
                                    v-bind:name="config.name + '[seoTitleSourceField]'"
                                    v-bind:id="config.id + '-seoTitleSourceField'"
                                    v-model="seoTitleSourceField">
                                <option v-for="option in config.fieldList" v-bind:value="option.value">{{ option.label }}</option>
                            </select>
                        </div>
                    </div>
                </td>
                <td class="top aligned">
                    <input type="text"
                           v-bind:value="value.seoTitle"
                           v-bind:id="config.id + '-seoTitle'"
                           v-bind:name="config.name + '[seoTitle]'"
                           v-bind:disabled="seoTitleSource === 'field'">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="instructions ignored"><a href="http://blog.woorank.com/2014/07/15-title-tag-optimization-guidelines-usability-seo/" target="_blank">The SEO Title</a> should be between 10 and 70 characters (spaces included). Make sure your title tag is explicit and contains your most important keywords. Be sure that each page has a unique title tag. The <code>siteSeoName</code> length is subtracted from the 70 character limit automatically, since it is appended to the seoTitle.</td>
            </tr>

            <tr>
                <td rowspan="2" class="top aligned">Description</td>
                <td>
                    <div class="ui equal width grid">
                        <div class="column">
                            <select class="ui dropdown fluid"
                                    v-bind:name="config.name + '[seoDescriptionSource]'"
                                    v-bind:id="config.id + '-seoDescriptionSource'"
                                    v-model="seoDescriptionSource">
                                <option value="custom">Custom Text</option>
                                <option value="field">From Field</option>
                            </select>
                        </div>
                        <div v-show="seoDescriptionSource === 'field'" class="column field-list">
                            <select class="ui dropdown fluid"
                                    v-bind:name="config.name + '[seoDescriptionSourceField]'"
                                    v-bind:id="config.id + '-seoDescriptionSourceField'"
                                    v-model="seoDescriptionSourceField">
                                <option v-for="option in config.fieldList" v-bind:value="option.value">{{ option.label }}</option>
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="text"
                           v-bind:value="value.seoDescription"
                           v-bind:id="config.id + '-seoDescription'"
                           v-bind:name="config.name + '[seoDescription]'"
                           v-bind:disabled="seoDescriptionSource === 'field'">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="instructions ignored"><a href="http://blog.woorank.com/2013/03/the-importance-of-meta-descriptions/" target="_blank">The SEO Description</a> should be between 70 and 160 characters (spaces included). Meta descriptions allow you to influence how your web pages are described and displayed in search results. Ensure that all of your web pages have a unique meta description that is explicit and contains your most important keywords.</td>
            </tr>

        </tbody>
    </table>
</template>

<style lang="scss">
    .seomatic td.instructions {
        border-top: 0 !important;
        border-left: 1px solid rgba(34,36,38,.15);
    }
    .seomatic .field-list {
        padding-left: 0.5em !important;
    }
</style>

<script>
    export default {
        name: 'seomatic',
        props: ['config'],
        data: function() {
            return {
                seoTitleSource : this.config.value.seoTitleSource ? this.config.value.seoTitleSource : 'custom',
                seoTitleSourceField : this.config.value.seoTitleSourceField,
                seoDescriptionSource : this.config.value.seoDescriptionSource ? this.config.value.seoDescriptionSource : 'custom',
                seoDescriptionSourceField : this.config.value.seoDescriptionSourceField,
                value : this.config.value,
            }
        },
        mounted: function() {
            $('.ui.dropdown', this.$el).dropdown();
        },
    }
</script>