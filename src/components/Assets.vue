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

        <div class="ui large modal">

            <div class="header">
                <file-upload
                        v-addIconToButton
                        class="ui labeled icon blue button"
                        v-bind:title="fileUpload.title"
                        v-bind:events="fileUpload.events"
                        v-bind:name="fileUpload.name"
                        v-bind:post-action="fileUpload.postAction"
                        v-bind:extensions="fileUpload.extensions"
                        v-bind:accept="fileUpload.accept"
                        v-bind:multiple="fileUpload.multiple"
                        v-bind:size="fileUpload.size || 0"
                        v-bind:headers="fileUpload.headers"
                        v-bind:data="fileUpload.data"
                        v-bind:drop="fileUpload.drop"
                        v-bind:files="fileUpload.files"
                        ref="upload">
                </file-upload>

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
    .modal .header {
        overflow: hidden;
    }
    .modal .header .buttons {
        float: right;
    }
</style>

<script>
    import Draggable from 'vuedraggable';

    import imagesLoaded from 'imagesLoaded';

    import FileUpload from 'vue-upload-component'

    import remove from 'lodash/remove';
    import extend from 'lodash/assignIn';

    import AssetElement from './AssetElement.vue';

    export default {
        name: 'assets',
        props: {
            config: {},
        },
        directives: {
            addIconToButton: {
                inserted: function (el) {
                    $(el).prepend('<i class="upload icon"></i>');
                }
            }
        },
        data: function() {

            const _this = this;

            return {
                modal:              null,
                $modal:             null,
                modalViewMode:      this.config.viewMode,
                modalInitialized:   false,
                modalElements:      null,

                fileUpload:         {
                    title:         'Upload',
                    name:          'assets-upload',
                    postAction:    null,
                    multiple:      true,
                    extensions:    'gif,jpg,png', // get from window.FFFields
                    accept:        '',
                    size:          1024 * 1024 * 10,
                    drop:          true,
                    files:         [],
                    upload:        {},
                    headers:       {
//                        "X-Csrf-Token": "xxxx", // get from window.FFFields
                    },
                    data: {
//                        "_csrf_token": "xxxxxx", // get from window.FFFields
                    },
                    events: {
                        add(file, component) {
                            console.log('add');
                            component.active = true;
                            file.headers['X-Filename'] = encodeURIComponent(file.name);
                            file.data.finename = file.name;
                            // file.putAction = 'xxx'
                            // file.postAction = 'xxx'

                            this.$parent.fileUpload.upload.active = true;
                        },
                        progress(file, component) {
                            console.log('progress ' + file.progress);
                        },
                        after(file, component) {
                            console.log('after');
                        },
                        before(file, component) {
                            console.log('before');
                        },
                    },
                },

                selectedElementIds: [],
                selectBtnClasses: {
                    'ui ok positive button': true,
                    'disabled': true
                },
                elements:           this.config.elements,
                canAddMore:         (this.config.limit === '' || this.config.elements.length < this.config.limit),
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
            const _this = this;

            window.onload = function() {
                _this.fileUpload.postAction = window.FFFields.actionUrl + '/assets/uploadFile';
            };

            this.fileUpload.upload = this.$refs.upload.$data;

            this.$modal = $('.ui.modal', this.$el);
            this.modal = this.$modal.modal({
                observeChanges: true,
                autofocus: false,
                onApprove: function($element) {
                    for (let i = 0; i < _this.modalElements.length; i++) {
                        if (_this.selectedElementIds.indexOf(_this.modalElements[i].id) != -1) {

                            // Clone the element
                            const newElement = extend({}, _this.modalElements[i]);

                            // Set up some props on the new element
                            newElement.context = 'field';
                            newElement.disabled = false;
                            // TODO do we need this?
//                            newElement.viewMode = _this.elements[0].viewMode;

                            // Push it onto the field
                            _this.elements.push(newElement);
                        }
                    }
                }
            });
        },
        methods: {

            onElementRemoved: function(element) {
                remove(this.elements, function(obj) {
                    return obj.id === element.id;
                });

                this.$children[0]._sortable.option("disabled", this.elements.length <= 1);

                if (this.config.limit !== '') {
                    this.canAddMore = this.elements.length < this.config.limit;
                }
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
                    this.modal.modal('show');
                }
            },

            toggleModalViewMode: function()
            {
                const _this = this;

                if (this.modalViewMode === 'list') {
                    this.modalViewMode = 'large';
                } else if (this.modalViewMode === 'large') {
                    this.modalViewMode = 'list';
                }

                this.modalElements.map(function(el) {
                    el.viewMode = _this.modalViewMode;
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
                this.modal.modal('show');

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
                                _this.modalInitialized = true;
                            }, 100)
                        });

                    }
                });

            }

        }
    }
</script>