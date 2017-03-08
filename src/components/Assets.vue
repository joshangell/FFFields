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
            <div class="ui large loader" v-if="!modalElements"></div>

            <div class="content" v-if="modalElements">
                <!-- TODO this will need toggling based on viewMode of field -->
                <div class="ui six doubling cards">
                    <asset-element v-for="element in modalElements" v-bind:element="element" v-on:elementSelected="onElementSelected"></asset-element>
                </div>
            </div>

            <div class="actions" v-if="modalElements">
                <div class="ui cancel button">Cancel</div>
                <div v-bind:class="selectBtnClass">Select</div>
            </div>
        </div>
    </div>
</template>

<script>
    import Draggable from 'vuedraggable';

    import imagesLoaded from 'imagesLoaded';

    import AssetElement from './AssetElement.vue';

    export default {
        name: 'assets',
        props: {
            config: {},
        },
        data: function() {
            const initialElementCount = Object.keys(this.config.elements).length;

            return {
                modal:              null,
                $modal:             null,
                initialized:        false,
                elements:           this.config.elements,
                modalElements:      null,
                selectedElementIds: [],
                canAddMore:         (this.config.limit === '' || initialElementCount < this.config.limit),
                selectBtnClass: {
                    'ui ok positive button' : true,
                    'disabled' : true
                },
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
            this.$modal = $('.ui.modal', this.$el);
            this.modal = this.$modal.modal({
                observeChanges: true,
            });
        },
        methods: {

            onElementRemoved: function(element) {
                delete this.elements[element.id];

                const elementCount = Object.keys(this.elements).length;

                this.$children[0]._sortable.option("disabled", elementCount <= 1);

                if (this.config.limit !== '') {
                    this.canAddMore = elementCount < this.config.limit;
                }
            },

            onElementSelected: function(obj) {
                if (obj.selected) {
                    this.selectedElementIds.push(obj.element.id);
                } else {
                    const i = this.selectedElementIds.indexOf(obj.element.id);
                    if (i != -1) {
                        this.selectedElementIds.splice(i, 1);
                    }
                }

                this.selectBtnClass.disabled = (this.selectedElementIds.length < 1);
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
                this.modal.modal('show');

                const data = {
                    fieldName: this.config.name,
                    sources: this.config.sources,
                    elementType: 'Asset',
                    context: 'index',
                    disabledElementIds: Object.keys(this.elements)
                };

                const _this = this;

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

                        imagesLoaded(_this.$modal, function()
                        {
                            setTimeout(function () {
                                _this.modal.modal('cache sizes');
                                _this.modal.modal('refresh');
                                _this.initialized = true;
                            }, 100)
                        });

                    }
                });

            }

        }
    }
</script>