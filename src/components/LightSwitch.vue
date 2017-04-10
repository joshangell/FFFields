<template>

    <div class="ui fitted toggle checkbox">
        <input type="hidden" v-bind:name="config.name" value="">
        <input type="checkbox"
               value="1"
               v-bind:id="config.id"
               v-bind:name="config.name"
               v-model="checked"
               v-on:change="handleToggle(checked)">
        <label></label>
    </div>

</template>

<script>
    export default {
        name: 'lightswitch',
        props: ['config'],
        data: function() {
            return {
                checked : this.config.value
            }
        },
        mounted: function() {
            this.handleToggle(this.config.value);
        },
        methods: {
            handleToggle: function(checked) {
                if (this.config.toggle) {
                    let elements = document.querySelectorAll(this.config.toggle.selector)
                    for (let i = 0; i < elements.length; i++) {
                        elements[i][this.config.toggle.attribute] = checked;
                    }
                }
            }
        },
    }
</script>