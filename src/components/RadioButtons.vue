<template>

    <div v-bind:id="config.id" class="grouped fields">
        <div v-for="option in config.options" class="field">
            <div class="ui radio checkbox">
                <input type="radio" tabindex="0" class="hidden" v-bind:name="config.name" v-bind:value="option.value" v-model="selected">
                <label>{{ option.label }}</label>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: 'radio-buttons',
        props: ['config'],
        data: function() {
            return {
                selected : this.config.value
            }
        },
        mounted: function() {
            $('.ui.radio.checkbox', this.$el).checkbox();
        },
    }
</script>