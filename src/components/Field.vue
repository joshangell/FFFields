<template>
    
    <div v-bind:class='config.class' v-bind:id='config.fieldId'>

        <label v-bind:id='config.labelId' v-bind:for='config.id'>
            {{ config.label }}
        </label>

        <div class='instructions'>{{ config.instructions }}</div>

        <component v-bind:is="config.field.type" v-bind:config="config.field.config"></component>

    </div>

</template>

<style lang="scss">
    .field {
        position: relative;

        .instructions {
            font-size: 0.85em;
            margin-bottom: 0.5em;
        }
    }
</style>

<script>
    import Field        from './Field.vue';
    import Message      from './Message.vue';
    import TextInput    from './TextInput.vue';
    import TextArea     from './TextArea.vue';
    import RichText     from './RichText.vue';
    import LightSwitch  from './LightSwitch.vue';
    import Matrix       from './Matrix.vue';
    import Dropdown     from './Dropdown.vue';
    import Checkboxes   from './Checkboxes.vue';
    import RadioButtons from './RadioButtons.vue';
    import MultiSelect  from './MultiSelect.vue';

    export default {
        name: 'field',
        props: ['config'],
        components: {
            'message'       : Message,
            'field'         : Field,
            'text-area'     : TextArea,
            'text-input'    : TextInput,
            'rich-text'     : RichText,
            'lightswitch'   : LightSwitch,
            'matrix'        : Matrix,
            'dropdown'      : Dropdown,
            'checkboxes'    : Checkboxes,
            'radio-buttons' : RadioButtons,
            'multi-select'  : MultiSelect
        },
    }
</script>