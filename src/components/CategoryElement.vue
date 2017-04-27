<template>
    <div v-bind:class="'item level-'+element.level">
        <i class="angle down icon" v-if="element.level > 1"></i>
        <div v-bind:class="classObject" v-on:click="selectElement">
            {{ element.label }}
            <i class="delete icon" v-on:click="removeElement" v-if="element.context === 'field'"></i>
            <input type="hidden" v-bind:name="element.name" v-bind:value="element.id" v-if="element.context === 'field'">
        </div>
    </div>
</template>

<style lang="scss">
    .category-element {
        user-select: none;
    }
    .ui.label {
        cursor: default;
    }
</style>

<script>
    export default {
        name: 'category-element',
        props: ['element','selectedElementIds'],
        data: function()
        {
            let selected = false;
            if (Array.isArray(this.selectedElementIds)) {
                selected = this.selectedElementIds.indexOf(this.element.id) != -1;
            }

            return {
                selected: selected,
                classObject: {
                    'ui label' : true,
                    'disabled' : this.element.disabled,
                    'category-element' : true,
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
            },
            selectElement: function(event) {
                if (this.element.context === 'index' && !this.element.disabled) {
                    this.selected = !this.selected;
                    this.$emit('elementSelected', {
                        selected: this.selected,
                        elementId: this.element.id
                    });
                }
            }
        }
    }
</script>