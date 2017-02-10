<template>
    <div v-bind:class='config.class' v-bind:id='config.fieldId'>

        <label v-bind:id='config.labelId' v-bind:for='config.id'>
            {{ config.label }}
        </label>

        <div class='instructions'>{{ config.instructions }}</div>

        <component v-bind:is="config.field.type" v-bind:config="config.field.config"></component>
    </div>
</template>

<script>
    import Message from './Message.vue';
    import TextInput from './TextInput.vue';
    import TextArea from './TextArea.vue';
    import RichText from './RichText.vue';
    import LightSwitch from './LightSwitch.vue';
    import Matrix from './Matrix.vue';

    export default {
        name: 'field',
        props: ['config'],
        components: {
            'message'     : Message,
            'text-area'   : TextArea,
            'text-input'  : TextInput,
            'rich-text'   : RichText,
            'lightswitch' : LightSwitch,
            'matrix'      : Matrix
        },
    }
</script>