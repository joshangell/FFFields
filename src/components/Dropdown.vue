<template>

    <select class="ui dropdown" v-bind:name="config.name" v-bind:id="config.id" v-model="selected">
        <option v-for="option in config.options" v-bind:value="option.value">{{ option.label }}</option>
    </select>

</template>

<style lang="scss">
    .ui.form .field > .selection.dropdown {
        width: auto;
    }
</style>

<script>
    export default {
        name: 'dropdown',
        props: ['config'],
        data: function() {
            return {
                selected : this.config.value
            }
        },
        mounted: function() {
            $(this.$el).dropdown();
        },
    }
</script>