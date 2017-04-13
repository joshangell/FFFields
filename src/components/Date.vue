<template>

    <div class="ui calendar" v-bind:id="config.id">
        <div class="ui input left icon">
            <i class="calendar icon"></i>
            <input type="text"
                   ref="input"
                   v-bind:value="value"
                   v-on:input="updateValue($event.target.value)"
                   v-bind:placeholder="config.placeholder">
            <input type="hidden"
                   v-if="config.showDate"
                   v-bind:value="getDateValue()"
                   v-bind:name="config.name + '[date]'">
            <input type="hidden"
                   v-if="config.showTime"
                   v-bind:value="getTimeValue()"
                   v-bind:name="config.name + '[time]'">
        </div>
    </div>

</template>

<script>
    import '../../node_modules/semantic-ui-calendar/dist/calendar.css';
    import '../../node_modules/semantic-ui-calendar/dist/calendar.js';

    import moment from 'moment';
    import parseFormat from 'moment-parseformat';

    export default {
        name: 'date',
        props: ['config'],

        data: function() {

            // This could probably be better ...
            let value = '',
                dateValue = '',
                timeValue = '';

            if (this.config.value != null && this.config.value.date != null) {
                dateValue = this.config.value.date;
            }

            if (this.config.value != null && this.config.value.time != null) {
                timeValue = this.config.value.time;
            }

            if (this.config.showDate && this.config.showTime) {
                value = dateValue + ' ' + timeValue;
            } else if (this.config.showDate) {
                value = dateValue;
            } else if (this.config.showTime) {
                value = timeValue;
            }

            return {
                value : value
            };
        },

        mounted: function() {
            // Wait until on load to setup the pop-up calendars as we need stuff
            // that is in window.FFFields
            $(window).on('load', () => {
                $(this.$el).calendar(this.getCalendarSettings());
            });
        },

        methods: {

            updateValue: function (value) {

                // If the value is changed to empty manually by the user then we
                // need to reset a bunch of stuff
                if (value == '') {

                    // Kill the current popup and calendar instances
                    $(this.$el).calendar('popup', 'hide');
                    $(this.$el).calendar('popup', 'destroy');
                    $(this.$el).calendar('destroy');

                    // Get the settings and reset the initial date to null
                    let settings = this.getCalendarSettings();
                    settings.initialDate = null;

                    // Re-init the calendar and show it
                    $(this.$el).calendar(settings);
                    $(this.$el).calendar('focus');

                }

                // Replicate the normal v-model behaviour
                this.$refs.input.value = value;
                this.$emit('input', value);

            },

            getDateValue: function() {

                // If there is something in the value get the moment instance
                // for that value so we can format it to just the date
                if (this.value != '') {
                    let date = this.getMomentFromValue(this.value);
                    return moment(date).format(parseFormat(this.config.localeDate));
                }

                // Fall back to looking at the initial value on render
                if (this.config.value != null && this.config.value.date != null) {
                    return this.config.value.date;
                }

                return '';
            },

            getTimeValue: function() {

                // If there is something in the value get the moment instance
                // for that value so we can format it to just the time
                if (this.value != '') {
                    let date = this.getMomentFromValue(this.value);
                    return moment(date).format(parseFormat(this.config.localeTime));
                }

                // Fall back to looking at the initial value on render
                if (this.config.value != null && this.config.value.time != null) {
                    return this.config.value.time;
                }

                return '';
            },

            getMomentFromValue: function(value) {

                if (this.config.showDate && this.config.showTime) {
                    return moment(value, parseFormat(this.config.localeDate) + ' ' + parseFormat(this.config.localeTime));
                }

                if (this.config.showDate) {
                    return moment(value, parseFormat(this.config.localeDate));
                }

                if (this.config.showTime) {
                    return moment(value, parseFormat(this.config.localeTime));
                }

            },

            getCalendarSettings: function() {
                return {
                    type: (this.config.showDate && this.config.showTime ? 'datetime' : (this.config.showTime ? 'time' : 'date')),
                    firstDayOfWeek: window.FFFields.datepickerOptions.firstDay,

                    initialDate: this.value != '' ? this.value : null,

                    // This is required otherwise the date / time sub-fields get overridden
                    // to the text value of the selected date on blur
                    formatInput: false,

                    text: {
                        days: window.FFFields.datepickerOptions.dayNamesMin,
                        months: window.FFFields.datepickerOptions.monthNames,
                        monthsShort: window.FFFields.datepickerOptions.monthNamesShort,
                    },

                    formatter: {
                        date: (date, settings) => {
                            if (!date) {
                                return '';
                            }

                            return moment(date).format(parseFormat(this.config.localeDate));
                        },
                        time: (date, settings, forCalendar) => {
                            if (!date) {
                                return '';
                            }

                            return moment(date).format(parseFormat(this.config.localeTime));
                        },
                    },

                    onChange: (date, text, mode) => {

                        // Reset the value to whatever the text version of this date is so that
                        // getDateValue() and getTimeValue() methods update
                        this.value = text;

                    }
                };
            },
        },

    }
</script>