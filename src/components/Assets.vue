<template>
    <div class="assets">
        <input type="hidden" v-bind:name="config.name" value="">

        <draggable v-bind:options="options" v-bind:class="[config.viewMode === 'large' ? 'ui ten doubling cards' : 'ui labels']">
            <asset-element v-for="element in elements" v-bind:element="element" v-on:elementRemoved="onElementRemoved"></asset-element>
        </draggable>

        <button type="button" class="ui small basic labeled icon button" v-if="canAddMore" v-on:click="launchElementSelector">
            <i class="add icon"></i>
            {{ config.selectionLabel }}
        </button>

        <div class="ui large modal assets-modal">

            <div class="header">

                <div class="ui equal width grid">
                    <div class="column">
                        <file-upload
                            v-addIconToButton
                            v-bind:class="fileUpload.classObject"
                            v-bind:title="fileUpload.title"
                            v-bind:events="fileUpload.events"
                            v-bind:name="fileUpload.name"
                            v-bind:extensions="fileUpload.extensions"
                            v-bind:accept="fileUpload.accept"
                            v-bind:multiple="fileUpload.multiple"
                            v-bind:size="fileUpload.size || 0"
                            v-bind:drop="fileUpload.drop"
                            v-bind:files="fileUpload.files"
                            ref="upload">
                        </file-upload>
                    </div>
                    <div class="column">
                        <div class="ui indicating progress" v-show="fileUpload.batchLength">
                            <div class="bar">
                                <div class="progress"></div>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="ui basic buttons">
                            <div role="button" v-bind:class="[modalViewMode === 'large' ? 'ui labeled icon button disabled' : 'ui labeled icon button']" v-on:click="toggleModalViewMode">
                                <i class="left grid layout icon"></i>
                                Grid
                            </div>
                            <div role="button" v-bind:class="[modalViewMode === 'large' ? 'ui right labeled icon button' : 'ui right labeled icon button disabled']" v-on:click="toggleModalViewMode">
                                List
                                <i class="right list layout icon"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="ui large loader" v-if="!modalElements"></div>

            <div class="content" v-if="modalElements">

                <template v-if="modalViewMode === 'large'">
                    <div class="ui eight doubling cards">
                        <asset-element v-for="element in modalElements" v-bind:element="element" v-bind:selectedElementIds="selectedElementIds" v-on:elementSelected="onElementSelected"></asset-element>
                    </div>
                </template>

                <template v-else>
                    <table class="ui selectable celled table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Filename</th>
                                <th>File Size</th>
                                <th>File Modified Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="element in modalElements" v-bind:class="[element.disabled ? 'disabled' : '', selectedElementIds.indexOf(element.id) != -1 ? 'positive' : '']" v-on:click="toggleSelectModalElement(element)">
                                <td>
                                    <asset-element v-bind:element="element"></asset-element>
                                </td>
                                <td>{{ element.filename }}</td>
                                <td>{{ element.size }}</td>
                                <td>{{ element.dateModified }}</td>
                            </tr>
                        </tbody>
                    </table>
                </template>

            </div>

            <div class="actions" v-if="modalElements">
                <div class="ui cancel button">Cancel</div>
                <div v-bind:class="selectBtnClasses">Select</div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    .assets > .ui.cards {
        margin-bottom: 0.25rem;
    }
    .assets > .ui.labels {
        margin-bottom: 1rem;
    }
    .modal.assets-modal .header {
        overflow: hidden;
    }
    .modal.assets-modal .header .buttons {
        float: right;
    }
</style>

<script>
    import Draggable from 'vuedraggable';

    import imagesLoaded from 'imagesLoaded';

    import FileUpload from 'vue-upload-component'

    import remove from 'lodash/remove';
    import extend from 'lodash/assignIn';
    import findIndex from 'lodash/findIndex';

    import AssetElement from './AssetElement.vue';

    export default {
        name: 'assets',
        props: {
            config: {},
        },
        directives: {
            addIconToButton: {
                bind: function (el) {
                    $(el).prepend('<i class="upload icon"></i>');
                }
            }
        },
        data: function() {

            return {
                $modal:             null,
                modalViewMode:      this.config.viewMode,
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

                // FileUpload settings
                $uploadProgress:    null,
                fileUpload:         {
                    classObject: {
                        'ui labeled icon blue button': true,
                        'disabled': false,
                    },
                    title:         'Upload',
                    name:          'assets-upload',
                    multiple:      true,
                    extensions:    'gif,jpg,png', // TODO get from window.FFFields
                    accept:        '', // TODO
                    size:          1024 * 1024 * 10, // TODO get from window.FFFields
                    drop:          true, // TODO
                    files:         [],
                    batchLength:   0,
                    events: {
                        add(file, component) {
                            // Update the batch length
                            this.$parent.fileUpload.batchLength = component.files.length;

                            // Make sure the uploader is set to upload automatically
                            component.active = true;

                            // Set up the request data
                            file.headers['X-Requested-With'] = 'XMLHttpRequest';
                            file.postAction = window.FFFields.actionUrl + '/assets/uploadFile';
                            file.data = {
                                folderId: 1, // TODO
                            };
                            file.data[window.FFFields.csrfTokenName] = window.FFFields.csrfTokenValue;
                        },
                        progress(file, component) {
                            this.$parent.updateUploadProgress(file.progress);
                        },
                        after(file, component) {

                            // Add to the list of files in the modal
                            // TODO

                            // Remove the file from the files array
                            const fileIndex = findIndex(component.files, { 'id': file.id });
                            if (fileIndex != -1) {
                                component.files.splice(fileIndex, 1);
                            }

                            // Ping the UI update
                            this.$parent.updateUploadUiState();

                        },
                        before(file, component) {
                            this.$parent.updateUploadUiState();
                        },
                    },
                },

                // Draggable options
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
            'file-upload'   : FileUpload,
        },
        mounted: function() {

            this.$uploadProgress = $('.ui.progress', this.$el);
            this.$uploadProgress.progress();

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
                            newElement.viewMode = this.config.viewMode;

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

                // Draggable
                this.$children[0]._sortable.option("disabled", this.elements.length <= 1);

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

            toggleModalViewMode: function()
            {
                if (this.modalViewMode === 'list') {
                    this.modalViewMode = 'large';
                } else if (this.modalViewMode === 'large') {
                    this.modalViewMode = 'list';
                }

                this.modalElements.map((el) => {
                    el.viewMode = this.modalViewMode;
                    return el
                });
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
                    sources: this.config.sources,
                    elementType: 'Asset',
                    context: 'index',
                    disabledElementIds: disabledElementIds
                };

                if (typeof window.FFFields.csrfTokenName != 'undefined') {
                    data[window.FFFields.csrfTokenName] = window.FFFields.csrfTokenValue;
                }

                $.ajax({
                    url: window.FFFields.actionUrl + '/fffields/elements/getAssets',
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

                        imagesLoaded(this.$modal, () => {
                            setTimeout(() => {
                                this.$modal.modal('cache sizes');
                                this.$modal.modal('refresh');
                                this.modalInitialized = true;
                            }, 100)
                        });

                    }
                });

            },

            updateUploadUiState: function()
            {
                const numFiles = this.fileUpload.files.length;

                if (numFiles && this.fileUpload.batchLength) {
                    this.fileUpload.title = 'Uploading ' + numFiles + ' file' + (numFiles === 1 ? '' : 's') + ' …';
                    this.fileUpload.classObject.disabled = true;
                } else {
                    this.fileUpload.title = 'Upload';
                    this.fileUpload.classObject.disabled = false;
                    this.fileUpload.batchLength = 0;
                    this.$uploadProgress.progress('reset');
                    this.initializeModal();
                }
            },

            updateUploadProgress: function(percent)
            {
                if (this.fileUpload.batchLength) {
                    // Work out the current overall percentage of the total batch
                    const filesDone = this.fileUpload.batchLength - this.fileUpload.files.length;
                    const totalValue = this.fileUpload.batchLength * 100;

                    this.$uploadProgress.progress('set total', totalValue);
                    this.$uploadProgress.progress('set progress', parseInt(percent) + (100 * filesDone));
                }
            }

        }
    }
</script>