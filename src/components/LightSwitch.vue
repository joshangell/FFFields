<template>

    <div class="ui fitted toggle checkbox">
        <input type="hidden" v-bind:name="config.name" value="">
        <input type="checkbox"
               value="1"
               v-bind:id="config.id"
               v-bind:name="config.name"
               v-bind:checked="checked">
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
        }
    }
</script>