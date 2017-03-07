<template>
    <div class="ui image label">
        <input type="hidden" v-bind:name="element.name" v-bind:value="element.id">
        <img v-bind:src="element.thumbUrl">
        {{ element.label }}
        <i class="delete icon"></i>
    </div>
</template>

<script>
    export default {
        name: 'asset-element',
        props: ['element'],
    }
</script>