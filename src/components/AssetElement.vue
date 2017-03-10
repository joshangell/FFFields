<template>
    <div v-bind:class="classObject" v-on:click="selectElement">
        <template v-if="element.viewMode === 'large'">
            <div class="image">
                <img v-bind:src="element.thumbUrl">
            </div>
            <div class="extra content">
                <i class="check icon" v-if="selected"></i>
                {{ element.label }}
                <i class="delete icon" v-on:click="removeElement" v-if="element.context === 'field'"></i>
            </div>
        </template>

        <template v-else>
            <img v-bind:src="element.thumbUrl">
            {{ element.label }}
            <i class="delete icon" v-on:click="removeElement" v-if="element.context === 'field'"></i>
        </template>

        <input type="hidden" v-bind:name="element.name" v-bind:value="element.id" v-if="element.context === 'field'">
    </div>
</template>

<style lang="scss">
    .asset-element {
        user-select: none;

        .image {
            pointer-events: none;
        }
    }

    .ui.card.disabled {
        opacity: 0.25;
    }

    .ui.card .extra.content {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        word-wrap: normal;
    }

    .ui.ten.doubling.cards .ui.card .extra.content {
        padding-right: 1.8rem;
        position: relative;
        font-size: 0.8em;

        .delete.icon {
            margin-right: 0;
            cursor: pointer;
            opacity: 0.5;
            position: absolute;
            right: 0.5rem;
            top: 50%;
            margin-top: -0.65rem;
            font-size: 1rem;

            &:hover {
                opacity: 1;
            }
        }
    }

    .ui.label {
        margin-bottom: 0.5rem;
        cursor: default;
    }
</style>

<script>
    export default {
        name: 'asset-element',
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
                    'ui' : true,
                    'link' : this.element.viewMode === 'large' && this.element.context === 'index' && !this.element.disabled,
                    'disabled' : this.element.disabled,
                    'card' : this.element.viewMode === 'large',
                    'image large label' : this.element.viewMode !== 'large',
                    'asset-element' : true,
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