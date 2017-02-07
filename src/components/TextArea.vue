<template>
    <textarea v-bind:rows="config.rows"
              v-bind:id="config.id"
              v-bind:name="config.name"
              v-bind:maxlength="config.maxlength"
              v-bind:disabled="config.disabled"
              v-bind:placeholder="config.placeholder">{{ config.value }}</textarea>
</template>

<script>
    export default {
        name: 'text-area',
        props: ['config'],
    }
</script>