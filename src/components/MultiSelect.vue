<template>

    <div>
        <input type="hidden" v-bind:name="config.name + '[]'" value="">
        <select class="ui dropdown" v-bind:name="config.name + '[]'" v-bind:id="config.id" v-model="selected" multiple>
            <option v-for="option in config.options" v-bind:value="option.value">{{ option.label }}</option>
        </select>
    </div>

</template>

<style lang="scss">
    .ui.form .field > .selection.dropdown {
        width: auto;
    }
</style>

<script>
    export default {
        name: 'multi-select',
        props: ['config'],
        data: function() {
            return {
                selected : this.config.value
            }
        },
        mounted: function() {
            $('.ui.dropdown', this.$el).dropdown();
        },
    }
</script>`