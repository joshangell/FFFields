<template>
    <div v-bind:class="{'ui right labeled input': config.showCharsLeft}">
        <input v-bind:value="value"
               v-on:input="updateCharsLeft($event.target.value)"
               v-bind:type="config.type"
               v-bind:id="config.id"
               v-bind:name="config.name"
               v-bind:maxlength="config.maxlength"
               v-bind:autocomplete="config.autocomplete"
               v-bind:disabled="config.disabled"
               v-bind:title="config.title"
               v-bind:placeholder="config.placeholder">

        <div v-if="config.showCharsLeft" class="ui dropdown label">{{ charsLeft }}</div>
    </div>
</template>

<script>
    export default {
        name: 'text-input',
        props: ['config'],
        data: function() {
            return {
                value : this.config.value,
                charsLeft: (this.config.value ? this.config.maxlength - value.length : this.config.maxlength)
            }
        },
        methods: {
            updateCharsLeft: function (value) {
                this.charsLeft = this.config.maxlength - value.length;
                this.value = value;
            }
        }
    }
</script>