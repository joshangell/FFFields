<template>
    <div v-bind:class="classObject">
        <div v-if="config.leftLabel" class="ui label">{{ config.leftLabel }}</div>

        <input v-bind:value="value"
               v-on:input="updateCharsLeft($event.target.value)"
               v-bind:type="config.type"
               v-bind:id="config.id"
               v-bind:name="config.name"
               v-bind:maxlength="config.maxlength"
               v-bind:autocomplete="config.autocomplete"
               v-bind:min="config.min"
               v-bind:max="config.max"
               v-bind:step="config.step"
               v-bind:disabled="config.disabled"
               v-bind:title="config.title"
               v-bind:placeholder="config.placeholder">

        <div v-if="config.showCharsLeft" v-bind:class="labelClasses">{{ charsLeft }}</div>
    </div>
</template>

<script>
    export default {
        name: 'text-input',
        props: ['config'],
        data: function() {
            const value = this.config.value;
            const initialValueLength = value ? value.length : 0;
            const initialCharsLeft = value ? this.config.maxlength - initialValueLength : this.config.maxlength;
            return {
                classObject : {
                    'ui right labeled input' : this.config.showCharsLeft,
                    'ui labeled input' : this.config.leftLabel
                },
                value : value,
                charsLeft : initialCharsLeft,
                percentageUsed : ((this.config.maxlength-initialCharsLeft)/this.config.maxlength)*100
            }
        },
        methods: {
            updateCharsLeft: function (value) {
                this.charsLeft = this.config.maxlength - value.length;
                this.percentageUsed = ((this.config.maxlength-this.charsLeft)/this.config.maxlength)*100;
                this.value = value;
            }
        },
        computed: {
            labelClasses: function () {
                return {
                    'ui label' : true,
                    'basic' : this.percentageUsed < 70,
                    'pink' : this.percentageUsed >= 80 && this.percentageUsed < 90,
                    'red' : this.percentageUsed >= 90,
                }
            }
        }
    }
</script>