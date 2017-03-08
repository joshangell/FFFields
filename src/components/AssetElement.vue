<template>
    <div v-bind:class="classObject" v-on:click="selectElement">
        <template v-if="element.viewMode === 'large'">
            <div class="image">
                <img v-bind:src="element.thumbUrl">
            </div>
            <div class="extra content">
                <i class="check icon" v-if="selected"></i>
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

<style lang="scss">
    .ui.card {
        user-select: none;
    }

    .ui.card.disabled {
        opacity: 0.35;
    }

    .ui.card .extra.content {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        word-wrap: normal;
    }

    .ui.label {
        margin-bottom: 0.5rem;
        cursor: default;
        user-select: none;
    }
</style>

<script>
    export default {
        name: 'asset-element',
        props: ['element'],
        data: function()
        {
            return {
                selected: false,
                classObject: {
                    'ui' : true,
                    'link' : this.element.viewMode === 'large' && this.element.context === 'index' && !this.element.disabled,
                    'disabled' : this.element.disabled,
                    'card' : this.element.viewMode === 'large',
                    'image label' : this.element.viewMode !== 'large'
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
                }
            }
        }
    }
</script>