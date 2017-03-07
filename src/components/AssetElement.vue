<template>
    <div class="ui image label">
        <input type="hidden" v-bind:name="element.name" v-bind:value="element.id">
        <img v-bind:src="element.thumbUrl">
        {{ element.label }}
        <i class="delete icon" v-on:click="removeElement"></i>
    </div>
</template>

<script>
    export default {
        name: 'asset-element',
        props: ['element'],
        methods: {
            removeElement: function(event) {
                $(this.$el).transition({
                    animation: 'fade',
                    onHide: function() {
                        $(this).remove();
                    }
                });
            }
        }
    }
</script>