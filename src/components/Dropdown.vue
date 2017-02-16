<template>

    <select class="ui dropdown" v-bind:name="config.name" v-bind:id="config.id" v-model="selected">
        <option v-for="option in config.options" v-bind:value="option.value">{{ option.label }}</option>
    </select>

</template>

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