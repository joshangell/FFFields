<template>
    <div v-bind:class="'item level-'+element.level">
        <i class="angle down icon" v-if="element.level > 1"></i>
        <div v-bind:class="classObject" v-on:click="selectElement">
            <i class="check icon" v-if="selected"></i>
            {{ element.label }}
            <i class="delete icon" v-on:click="removeElement" v-if="element.context === 'field'"></i>
            <input type="hidden" v-bind:name="element.name" v-bind:value="element.id" v-if="element.context === 'field'">
        </div>
    </div>
</template>

<style lang="scss">
    .ui.label.category-element {
        user-select: none;
        cursor: default;
    }

    .ui.label.category-element.selectable {
        cursor: pointer;

        &:hover {
             background-color: #e0e0e0;
             border-color: #e0e0e0;
             background-image: none;
             color: rgba(0,0,0,.8);
        }
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
                    'selectable' : this.element.context === 'index',
                    'category-element' : true,
                }
            }
        },
        methods: {
            removeElement: function(event) {
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