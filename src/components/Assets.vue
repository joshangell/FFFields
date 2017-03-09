<template>
    <div class="assets">
        <input type="hidden" v-bind:name="config.name" value="">

        <draggable v-bind:options="options" v-bind:class="[config.viewMode === 'large' ? 'ui six doubling cards' : 'ui labels']">
            <asset-element v-for="element in elements" v-bind:element="element" v-on:elementRemoved="onElementRemoved"></asset-element>
        </draggable>

        <button type="button" class="ui small basic labeled icon button" v-if="canAddMore" v-on:click="launchElementSelector">
            <i class="add icon"></i>
            {{ config.selectionLabel }}
        </button>

        <div class="ui modal">
            <div class="ui large loader" v-if="!modalElements"></div>

            <div class="content" v-if="modalElements">
                <div v-bind:class="[config.viewMode === 'large' ? 'ui six doubling cards' : 'ui labels']">
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

    import remove from 'lodash/remove';

    import AssetElement from './AssetElement.vue';

    export default {
        name: 'assets',
        props: {
            config: {},
        },
        data: function() {
            return {
                modal:              null,
                $modal:             null,
                initialized:        false,
                elements:           this.config.elements,
                modalElements:      null,
                selectedElementIds: [],
                canAddMore:         (this.config.limit === '' || this.config.elements.length < this.config.limit),
                selectBtnClass: {
                    'ui ok positive button' : true,
                    'disabled' : true
                },
                options: {
                    draggable: '.asset-element',
                    ghostClass: 'disabled',
                    disabled: this.config.elements.length <= 1
                },
            }
        },
        components: {
            'draggable'     : Draggable,
            'asset-element' : AssetElement,
        },
        mounted: function() {
            const _this = this;

            this.$modal = $('.ui.modal', this.$el);
            this.modal = this.$modal.modal({
                observeChanges: true,
                onApprove: function($element) {
                    // TODO: get the actual elements here
//                    console.log(_this.selectedElementIds);
                }
            });
        },
        methods: {

            onElementRemoved: function(element) {
                // TODO
//                delete this.elements[element.id];

                console.log(this.elements);

                remove(this.elements, function(obj) {
                    return obj.id === element.id;
                });

//                this.elements = newElements;

                console.log(this.elements);

                this.$children[0]._sortable.option("disabled", this.elements.length <= 1);

                if (this.config.limit !== '') {
                    this.canAddMore = this.elements.length < this.config.limit;
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

                const disabledElementIds = [];

                for (let i = 0; i < this.elements.length; i++) {
                    disabledElementIds.push(this.elements[i].id);
                }

                const data = {
                    fieldName: this.config.name,
                    sources: this.config.sources,
                    elementType: 'Asset',
                    context: 'index',
                    disabledElementIds: disabledElementIds
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