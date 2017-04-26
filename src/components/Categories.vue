<template>
    <div class="catgeories">
        <input type="hidden" v-bind:name="config.name" value="">

        <div class="ui list">
            <template v-for="element in elements">
                <div v-bind:class="'item level-'+element.level">
                    <i class="angle down icon" v-if="element.level > 1"></i>
                    <category-element v-bind:element="element" v-on:elementRemoved="onElementRemoved"></category-element>
                </div>
            </template>
        </div>

        <button type="button" class="ui small basic labeled icon button" v-if="canAddMore" v-on:click="launchElementSelector">
            <i class="add icon"></i>
            {{ config.selectionLabel }}
        </button>

        <div class="ui large modal">

            <div class="ui large loader" v-if="!modalElements"></div>

            <div class="content" v-if="modalElements">

                <div class="ui list">
                    <template v-for="element in modalElements">
                        <div v-bind:class="'item level-'+element.level">
                            <i class="angle down icon" v-if="element.level > 1"></i>
                            <category-element v-bind:element="element" v-bind:selectedElementIds="selectedElementIds" v-on:elementSelected="onElementSelected"></category-element>
                        </div>
                    </template>
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
    .level-2 { margin-left: 0rem; }
    .level-3 { margin-left: 2.5rem; }
    .level-4 { margin-left: 5rem; }
    .level-5 { margin-left: 7.5rem; }
    .level-6 { margin-left: 10rem; }
    .level-7 { margin-left: 12.5rem; }
    .level-8 { margin-left: 15rem; }
    .level-9 { margin-left: 17.5rem; }

    .angle.down.icon {
        transform: rotate(45deg);

        float: left;
        font-size: 2em;
        margin-top: 0.2rem !important;

        color: #e8e8e8;
    }
</style>

<script>

    import remove from 'lodash/remove';
    import extend from 'lodash/assignIn';

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
            console.log(this.config.elements);
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

                    for (let i = 0; i < this.modalElements.length; i++) {
                        if (this.selectedElementIds.indexOf(this.modalElements[i].id) != -1) {

                            // Clone the element
                            const newElement = extend({}, this.modalElements[i]);

                            // Set up some props on the new element
                            newElement.context = 'field';
                            newElement.disabled = false;

                            // Push it onto the field
                            this.elements.push(newElement);
                        }
                    }
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
                remove(this.elements, function(obj) {
                    return obj.id === element.id;
                });

                this.trashModal();
            },

            onElementSelected: function(obj) {
                if (obj.selected) {
                    this.selectedElementIds.push(obj.elementId);
                } else {
                    const i = this.selectedElementIds.indexOf(obj.elementId);
                    if (i != -1) {
                        this.selectedElementIds.splice(i, 1);
                    }
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

            toggleSelectModalElement: function(element)
            {
                const currentStatus = this.selectedElementIds.indexOf(element.id) != -1;

                this.onElementSelected({
                    selected: !currentStatus,
                    elementId: element.id
                });
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
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        alert(textStatus + errorThrown);
                    },
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