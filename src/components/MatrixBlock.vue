<template>
    <div class="ui segment matrix-block">

        <input type="hidden" v-bind:name="block.type.name" v-bind:value="block.type.value">
        <input type="hidden" v-bind:name="block.enabled.name" v-bind:value="block.enabled.value">

        <div class="ui top attached label">
            {{ block.name }}

            <div class="actions">

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
                                <template v-if="enabled">
                                    <i class="button toggle off icon"></i>
                                    <span>Disable</span>
                                </template>

                                <template v-else>
                                    <i class="button toggle on icon"></i>
                                    <span>Enable</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="item">
                    <i data-content="Move" class="button move icon"></i>
                </div>

            </div>
        </div>

        <div class="ui form">
            <field v-for="f in block.fields" v-bind:config="f.config"></field>
        </div>

    </div>
</template>

<style lang="scss">
    .matrix-block .ui.form {
        margin-top: 2em;
    }

    .matrix-block .actions {
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

        .ui.dropdown .dropdown {
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
                collapsed: false,
                enabled: true
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
            toggleEnabled: function(event) {
                if (this.enabled) {
                    this.enabled = false;
                } else {
                    this.enabled = true;
                }
            }
        },
        mounted: function() {
            $('.ui.dropdown', this.$el).dropdown({
                action : 'hide'
            });
        }
    }
</script>