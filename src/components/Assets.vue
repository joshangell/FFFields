<template>
    <div>
        <input type="hidden" v-bind:name="config.name" value="">

        <div class="ui labels">
            <draggable v-bind:options="options">
                <asset-element v-for="element in elements" v-bind:element="element" v-on:elementRemoved="onElementRemoved"></asset-element>
            </draggable>
        </div>

        <button type="button" class="ui small basic labeled icon button" v-if="canAddMore" v-on:click="launchElementSelector">
            <i class="add icon"></i>
            {{ config.selectionLabel }}
        </button>

        <div class="ui modal">
            <div class="content">
                <!-- TODO this will need toggling based on viewMode of field -->
                <div class="ui cards">
                    <asset-element v-for="element in modalElements" v-bind:element="element"></asset-element>
                </div>
            </div>
            <div class="actions">
                <div class="ui button">Cancel</div>
                <div class="ui disabled positive button">Select</div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    .ui.labels .ui.image.label {
        margin-bottom: 0.5rem;
        cursor: default;
        user-select: none;
    }
</style>

<script>
    import Draggable from 'vuedraggable';

    import AssetElement from './AssetElement.vue';

    export default {
        name: 'assets',
        props: {
            config: {},
        },
        data: function() {
            const initialElementCount = Object.keys(this.config.elements).length;

            return {
                modal:         null,
                initialized:   false,
                elements:      this.config.elements,
                modalElements: null,
                canAddMore:    (this.config.limit === '' || initialElementCount < this.config.limit),
                options: {
                    ghostClass: 'disabled',
                    disabled: initialElementCount <= 1
                },
            }
        },
        components: {
            'draggable'     : Draggable,
            'asset-element' : AssetElement,
        },
        mounted: function() {
            this.modal = $('.ui.modal', this.$el).modal();
        },
        methods: {

            onElementRemoved: function(element) {
                delete this.elements[element.id];
                this.updateState();
            },

            updateState: function() {
                const elementCount = Object.keys(this.elements).length;

                this.$children[0]._sortable.option("disabled", elementCount <= 1);

                if (this.config.limit !== '') {
                    this.canAddMore = elementCount < this.config.limit;
                }
            },

            launchElementSelector: function()
            {
                if (!this.initialized) {
                    this.initializeModal();
                } else {
                    this.modal.modal('show');
                }
            },

            initializeModal: function()
            {

                const _this = this;

                const data = {
                    fieldName: this.config.name,
                    sources: this.config.sources,
                    elementType: 'Asset',
                    context: 'index',
                };

                if (typeof window.FFFields.csrfTokenName != 'undefined') {
                    data[window.FFFields.csrfTokenName] = window.FFFields.csrfTokenValue;
                }

                $.ajax({
                    url: window.FFFields.actionUrl + '/fffields/elements/getElements',
                    type: 'POST',
                    data: data,
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        alert(textStatus + errorThrown);
                    },
                    complete: function(jqXHR, textStatus)
                    {
                        if (textStatus != 'success') {
                            alert('An unknown error occurred.');
                            return;
                        }

                        _this.modalElements = jqXHR.responseJSON.elements;
                        _this.modal.modal('show');
                        _this.initialized = true;
                    }
                });

            }

        }
    }
</script>