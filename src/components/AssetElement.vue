<template>
    <div v-bind:class="classObject">
        <template v-if="element.viewMode === 'large'">
            <div class="image" style="">
                <img v-bind:src="element.thumbUrl">
            </div>
            <div class="extra content">
                {{ element.label }}
                <i class="right floated delete icon" v-on:click="removeElement" v-if="element.context === 'field'"></i>
            </div>
        </template>

        <template v-else>
            <input type="hidden" v-bind:name="element.name" v-bind:value="element.id">
            <img v-bind:src="element.thumbUrl">
            {{ element.label }}
            <i class="delete icon" v-on:click="removeElement" v-if="element.context === 'field'"></i>
        </template>
    </div>
</template>

<script>
    export default {
        name: 'asset-element',
        props: ['element'],
        data: function()
        {
            return {
                classObject: {
                    'ui link' : this.element.viewMode === 'large' && this.element.context === 'index',
                    'card' : this.element.viewMode === 'large',
                    'ui image label' : this.element.viewMode !== 'large'
                }
            }
        },
        methods: {
            removeElement: function(event) {
                $(this.$el).transition({
                    animation: 'fade',
                    onHide: function() {
                        $(this).remove();
                    }
                });

                this.$emit('elementRemoved', this.element);
            }
        }
    }
</script>