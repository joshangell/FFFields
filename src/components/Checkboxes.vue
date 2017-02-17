<template>

    <div v-bind:id="config.id" class="grouped fields">
        <input type="hidden" v-bind:name="config.name + '[]'" value="">
        <div v-for="option in config.options" class="field">
            <div class="ui checkbox">
                <input type="checkbox" tabindex="0" class="hidden" v-bind:name="config.name + '[]'" v-bind:value="option.value" v-model="checkedValues">
                <label>{{ option.label }}</label>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: 'checkboxes',
        props: ['config'],
        data: function() {
            return {
                checkedValues : this.config.value
            }
        },
        mounted: function() {
            $('.ui.checkbox', this.$el).checkbox();
        },
    }
</script>