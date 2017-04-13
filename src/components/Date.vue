<template>

    <div class="ui calendar">
        <div class="ui input left icon">
            <i class="calendar icon"></i>
            <input type="text"
                   v-bind:value="config.value"
                   v-bind:id="config.id"
                   v-bind:name="config.name"
                   v-bind:placeholder="config.placeholder">
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
//        data: function() {
//            return {
//
//            };
//        },
        mounted: function() {
            // Wait until on load to setup the pop-up calendars as we need stuff
            // that is in window.FFFields
            $(window).on('load', () => {
                $(this.$el).calendar(this.getCalendarSettings());
            });
        },
        methods: {
            getCalendarSettings: function() {
                return {
                    type : (this.config.showDate && this.config.showTime ? 'datetime' : (this.config.showTime ? 'time' : 'date')),
                    firstDayOfWeek : window.FFFields.datepickerOptions.firstDay,

                    text: {
                        days: window.FFFields.datepickerOptions.dayNamesMin,
                        months: window.FFFields.datepickerOptions.monthNames,
                        monthsShort: window.FFFields.datepickerOptions.monthNamesShort,
                    },

                    formatter: {
                        date: function (date, settings) {
                            if (!date) {
                                return '';
                            }

                            const format = parseFormat(window.FFFields.datepickerOptions.localeDate);

                            return moment(date).format(format);
                        },
                        time: function (date, settings, forCalendar) {
                            if (!date) {
                                return '';
                            }

                            const format = parseFormat(window.FFFields.datepickerOptions.localeTime);

                            return moment(date).format(format);
                        },
                    }


//                    initialDate : this.config.value,

                }
            }
        }

    }
</script>