<template>
    <div v-bind:class="[element.viewMode === 'large' ? 'ui card' : 'ui image label']">
        <template v-if="element.viewMode === 'large'">
            <div class="image">
                <img v-bind:src="element.thumbUrl">
            </div>
            <div class="content">
                <i class="right floated delete icon" v-on:click="removeElement" v-if="element.context === 'field'"></i>
                <div class="header">{{ element.label }}</div>
                <div class="meta">
                    <span class="date">Create in Sep 2014</span>
                </div>
            </div>
        </template>

        <template v-else>
            <input type="hidden" v-bind:name="element.name" v-bind:value="element.id">
            <img v-bind:src="element.thumbUrl">
            {{ element.label }}
            <i class="delete icon" v-on:click="removeElement" v-if="element.context === 'field'"></i>
        </template>
    </div>
</template>

<script>
    export default {
        name: 'asset-element',
        props: ['element'],
        methods: {
            removeElement: function(event) {
                $(this.$el).transition({
                    animation: 'fade',
                    onHide: function() {
                        $(this).remove();
                    }
                });

                this.$emit('elementRemoved', this.element);
            }
        }
    }
</script>