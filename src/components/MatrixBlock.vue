<template>
    <div class="ui segment matrix-block">

        <input type="hidden" v-bind:name="block.type.name" v-bind:value="block.type.value" v-if="block.type !== undefined">
        <input type="hidden" v-bind:name="block.enabled.name" v-bind:value="enabled">

        <div class="ui form">
            <field v-for="f in block.fields" v-bind:config="f.config"></field>
        </div>

        <div class="ui top attached label">
            {{ block.name }}

            <div class="actions">

                <template v-if="enabled === '0'">
                    <div class="item">
                        <i class="button toggle off icon"></i>
                    </div>
                </template>

                <div class="item">
                    <div class="ui icon dropdown">
                        <i class="setting icon"></i>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item" v-on:click="toggleCollapse">
                                <template v-if="collapsed">
                                    <i class="button expand icon"></i>
                                    <span>Expand</span>
                                </template>

                                <template v-else>
                                    <i class="button compress icon"></i>
                                    <span>Collapse</span>
                                </template>
                            </div>
                            <div class="item" v-on:click="toggleEnabled">
                                <template v-if="enabled === '1'">
                                    <i class="button toggle on icon"></i>
                                    <span>Disable</span>
                                </template>

                                <template v-else>
                                    <i class="button toggle off icon"></i>
                                    <span>Enable</span>
                                </template>
                            </div>
                            <div class="item" v-on:click="deleteBlock">
                                <i class="button minus circle icon"></i>
                                <span>Delete</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="item">
                    <i data-content="Move" class="button move icon"></i>
                </div>

            </div>
        </div>




    </div>
</template>

<style lang="scss">
    .matrix-block {
        /*z-index: 1;*/
    }
    .matrix-block .ui.form {
        margin-top: 2em;
    }

    .matrix-block .ui.top.attached.label {
        z-index: 2;
    }

    .matrix-block .actions {
        /*position: absolute;*/
        /*z-index: 2;*/
        /*right: 0.8em;*/
        /*top: 5px;*/
        float: right;

        > .item {
            float: left;
            margin-left: 1em;
            opacity: 0.75;

            &:hover {
                opacity: 1;
            }
        }

        .move {
            margin-right: 0;
            cursor: move;
        }

        .ui.dropdown .dropdown.icon {
            margin-left: 0;
        }
    }
</style>

<script>
    import Field from './Field.vue';

    export default {
        name: 'matrix-block',
        props: ['block'],
        components : {
            'field' : Field,
        },
        data: function() {
            return {
                collapsed: (this.block.enabled.value === '1' ? false : true),
                enabled: this.block.enabled.value
            }
        },
        // This is key, see here: https://vuejs.org/v2/guide/components.html#Circular-References-Between-Components
        beforeCreate: function () {
            this.$options.components.field = require('./Field.vue')
        },
        methods: {
            toggleCollapse: function(event) {
                if (this.collapsed) {
                    this.collapsed = false;
                } else {
                    this.collapsed = true;
                }

                $('.ui.form', this.$el).transition({
                    animation: 'slide down',
                });
            },
            collapse: function() {
                if ($('.ui.form', this.$el).transition('is visible')) {
                    this.collapsed = true;

                    $('.ui.form', this.$el).transition({
                        animation: 'slide down',
                    });
                }
            },
            expand: function() {
                if (!$('.ui.form', this.$el).transition('is visible')) {
                    this.collapsed = false;

                    $('.ui.form', this.$el).transition({
                        animation: 'slide down',
                    });
                }
            },
            toggleEnabled: function(event) {
                if (this.enabled === '1') {
                    this.enabled = '0';

                    this.collapse();

                } else {
                    this.enabled = '1';
                    this.expand();
                }
            },
            deleteBlock: function(event) {
                $(this.$el).transition({
                    animation: 'slide down',
                    onHide: function() {
                        $(this).remove();
                    }
                });
            }
        },
        mounted: function() {
            $('.ui.dropdown', this.$el).dropdown({
                action : 'hide'
            });

            if (this.collapsed && $('.ui.form', this.$el).transition('is visible')) {
                $('.ui.form', this.$el).transition('hide');
            }
        }
    }
</script>