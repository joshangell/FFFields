<template>
    <div v-bind:class="{ 'ui labeled input': config.showCharsLeft }">
        <textarea v-bind:value="value"
                  v-on:input="updateCharsLeft($event.target.value)"
                  v-bind:rows="config.rows"
                  v-bind:id="config.id"
                  v-bind:name="config.name"
                  v-bind:maxlength="config.maxlength"
                  v-bind:disabled="config.disabled"
                  v-bind:placeholder="config.placeholder">{{ config.value }}</textarea>
        
        <div v-if="config.showCharsLeft" v-bind:class="labelClasses">{{ charsLeft }}</div>
    </div>
</template>

<script>
    export default {
        name: 'text-area',
        props: ['config'],
        data: function() {
            return {
                value : this.config.value,
                charsLeft : (this.config.value ? this.config.maxlength - value.length : this.config.maxlength),
                percentageUsed : (this.config.value ? ((this.config.maxlength-value.length)/this.config.maxlength)*100 : 0)
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
                    'ui bottom right attached label' : true,
                    'basic' : this.percentageUsed < 70,
                    'pink' : this.percentageUsed >= 80 && this.percentageUsed < 90,
                    'red' : this.percentageUsed >= 90,
                }
            }
        }
    }
</script>