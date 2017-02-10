<template>

    <div v-bind:class="config.class">
        {{ config.message }}
    </div>

</template>

<script>
    export default {
        name: 'message',
        props: ['config'],
    }
</script>