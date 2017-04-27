<template>
    <div class="catgeories">
        <input type="hidden" v-bind:name="config.name" value="">

        <div class="ui list">
            <category-element v-for="element in elements" v-bind:element="element" v-on:elementRemoved="onElementRemoved"></category-element>
        </div>

        <button type="button" class="ui small basic labeled icon button" v-if="canAddMore" v-on:click="launchElementSelector">
            <i class="add icon"></i>
            {{ config.selectionLabel }}
        </button>

        <div class="ui large modal catgeories-modal">

            <div class="ui large loader" v-if="!modalElements"></div>

            <div class="content" v-if="modalElements">

                <div class="ui list">
                    <category-element v-for="element in modalElements" v-bind:element="element" v-bind:selectedElementIds="selectedElementIds" v-on:elementSelected="onElementSelected"></category-element>
                </div>

            </div>

            <div class="actions" v-if="modalElements">
                <div class="ui cancel button">Cancel</div>
                <div v-bind:class="selectBtnClasses">Select</div>
            </div>
        </div>

    </div>
</template>

<style lang="scss">
    .catgeories,
    .catgeories-modal {

        .level-2  { margin-left: 0rem;    }
        .level-3  { margin-left: 2.5rem;  }
        .level-4  { margin-left: 5rem;    }
        .level-5  { margin-left: 7.5rem;  }
        .level-6  { margin-left: 10rem;   }
        .level-7  { margin-left: 12.5rem; }
        .level-8  { margin-left: 15rem;   }
        .level-9  { margin-left: 17.5rem; }
        .level-10 { margin-left: 20rem;   }

        .angle.down.icon {
            transform: rotate(45deg);

            float: left;
            font-size: 2em;
            margin-top: 0.2rem !important;

            color: #e8e8e8;
        }

    }
</style>

<script>

    import _pullAt from 'lodash/pullAt';
    import _extend from 'lodash/assignIn';
    import _uniqBy from 'lodash/uniqBy';

    import CategoryElement from './CategoryElement.vue';

    export default {

        name: 'categories',

        props: {
            config: {},
        },

        components: {
            'category-element' : CategoryElement,
        },

        data: function() {
            return {
                $modal:             null,
                modalInitialized:   false,
                modalApproved:      false,
                modalElements:      [],

                selectedElementIds: [],
                selectBtnClasses: {
                    'ui ok positive button': true,
                    'disabled': true
                },
                elements:           this.config.elements,
                canAddMore:         (this.config.limit === '' || this.config.elements.length < this.config.limit),
            }
        },

        mounted: function() {

            this.$modal = $('.ui.modal', this.$el);
            this.$modal.modal({
                observeChanges: true,
                autofocus: false,
                onApprove: ($element) => {
                    this.modalApproved = true;

                    const newElementsArray = [];

                    for (let i = 0; i < this.modalElements.length; i++) {

                        // Is it in this.elements already? If so, move from this.elements and to the new tree and skip.
                        let currentIndex = this.elements.findIndex(obj => {
                            return obj.id === this.modalElements[i].id;
                        });

                        if (currentIndex != -1) {
                            newElementsArray.push(this.elements[currentIndex]);
                        }


                        // Is is in the newly selected elements array? If so, add it to the tree.
                        if (this.selectedElementIds.indexOf(this.modalElements[i].id) != -1) {

                            // Clone the element
                            let newElement = _extend({}, this.modalElements[i]);

                            // Set up some props on the new element
                            newElement.context = 'field';
                            newElement.disabled = false;

                            // Push it onto the field
                            newElementsArray.push(newElement);
                        }
                    }

                    // Clean out any duplicates
                    this.elements = _uniqBy(newElementsArray, 'id');

                },
                onHidden: () => {
                    if (this.modalApproved) {
                        this.modalApproved = false;
                        this.trashModal();
                    }
                }
            });
        },

        methods: {

            trashModal: function() {
                this.modalElements = [];
                this.selectedElementIds = [];
                this.modalInitialized = false;
                this.selectBtnClasses.disabled = true;

                // Whether we can add more or not
                if (this.config.limit !== '') {
                    this.canAddMore = this.elements.length < this.config.limit;
                }
            },

            onElementRemoved: function(element) {

                // Get the index of this element so we know where to start looking for children from
                const elementIndex = this.elements.findIndex(function(obj) {
                    return obj.id === element.id;
                });

                // Start the array of element indexes we need to remove
                let elementIndexesToRemove = [elementIndex];

                // Look for direct descendants so we can remove them too
                for (let i = elementIndex + 1; i < this.elements.length; i++) {

                    // Stop as soon as we go above or the same as the level of the element clicked
                    if (this.elements[i].level <= element.level) {
                        break;
                    }

                    // Add to the removal array
                    elementIndexesToRemove.push(i);
                }

                // Finally remove them all
                _pullAt(this.elements, elementIndexesToRemove);

                this.trashModal();
            },

            onElementSelected: function(obj) {

                // Get the index of this element
                let elementIndex = this.modalElements.findIndex(function(o) {
                    return o.id === obj.elementId;
                });

                let element = this.modalElements[elementIndex];

                // Add/remove
                if (obj.selected) {
                    this.selectedElementIds.push(element.id);
                } else {
                    let idx = this.selectedElementIds.indexOf(element.id);
                    if (idx != -1) {
                        this.selectedElementIds.splice(idx, 1);
                    }
                }


                // Check its not a top level category, if its not we select
                // up the tree until to the parent
                if (element.parent !== null) {

                    // First reverse the array so we can look down it instead of up
                    this.modalElements.reverse();

                    // Re-find that index
                    let elementIndex = this.modalElements.findIndex(function(o) {
                        return o.id === element.id;
                    });

                    let parentId = element.parent;

                    for (let i = elementIndex; i < this.modalElements.length; i++) {

                        // Check we match the current parent we’re looking for
                        if (this.modalElements[i].id === parentId) {

                            // Add/remove
                            if (obj.selected) {
                                this.selectedElementIds.push(this.modalElements[i].id);
                            } else {
                                let idx = this.selectedElementIds.indexOf(this.modalElements[i].id);
                                if (idx != -1) {
                                    this.selectedElementIds.splice(idx, 1);
                                }
                            }

                            // If there is no new parent to look for then stop
                            if (this.modalElements[i].parent == null) {
                                break;
                            }

                            // Otherwise, store the next parent to look for
                            parentId = this.modalElements[i].parent;

                        }

                    }

                    // Stick the array back the right way around
                    this.modalElements.reverse();

                }

                this.selectBtnClasses.disabled = (this.selectedElementIds.length < 1);
            },

            launchElementSelector: function()
            {
                if (!this.modalInitialized) {
                    this.initializeModal();
                } else {
                    this.$modal.modal('show');
                }
            },

            initializeModal: function()
            {
                // Show the modal
                // TODO: improve this here so it shows a spinner first, then the modal
                this.$modal.modal('show');

                // Work out the disabled elements
                const disabledElementIds = [];

                for (let i = 0; i < this.elements.length; i++) {
                    disabledElementIds.push(this.elements[i].id);
                }

                const data = {
                    fieldName: this.config.name,
                    fieldId: this.config.fieldId,
                    source: this.config.source,
                    elementType: 'Category',
                    context: 'index',
                    disabledElementIds: disabledElementIds
                };

                if (typeof window.FFFields.csrfTokenName != 'undefined') {
                    data[window.FFFields.csrfTokenName] = window.FFFields.csrfTokenValue;
                }

                $.ajax({
                    url: window.FFFields.actionUrl + '/fffields/elements/getCategories',
                    type: 'POST',
                    data: data,
//                    error: function(jqXHR, textStatus, errorThrown)
//                    {
//                        alert(textStatus + errorThrown);
//                    },
                    complete: (jqXHR, textStatus) => {
                        if (textStatus != 'success') {
                            alert('An unknown error occurred.');
                            return;
                        }

                        this.modalElements = jqXHR.responseJSON.elements;

                        this.$modal.modal('cache sizes');
                        this.$modal.modal('refresh');
                        this.modalInitialized = true;
                    }
                });

            },

        }
    }
</script>