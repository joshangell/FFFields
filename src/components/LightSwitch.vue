<template>

    <div class="ui fitted toggle checkbox">
        <input type="hidden" v-bind:name="config.name" value="0">
        <input type="checkbox"
               value="1"
               v-bind:id="config.id"
               v-bind:name="config.name"
               v-bind:checked="checked"
               v-bind:true-value="1"
               v-bind:false-value="0">
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
//        methods: {
//            updateValue: function (value) {
//                console.log(value);
////                this.checked = value ? '1' : '0';
//                this.value = value ? '1' : '0';
//                console.log(this.value);
//            }
//        },
    }
</script>