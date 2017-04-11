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
    export default {
        name: 'date',
        props: ['config'],
        data: function() {
            return {

            };
        },
        mounted: function() {
            $(window).on('load', () => {
                $(this.$el).calendar(this.getCalendarSettings());
            });
        },
        methods: {
            getCalendarSettings: function() {
                return {
                    type : (this.config.showDate && this.config.showTime ? 'datetime' : (this.config.showTime ? 'time' : 'date')),
                    firstDayOfWeek : window.FFFields.datepickerOptions.firstDay,
//                    initialDate : this.config.value,
//                    formatter : {
//                        datetime: function (date, settings) {
//                            //return a formatted string representing the date & time of 'date'
//                        },
//                        date: function (date, settings) {
//                            //return a formatted string representing the date of 'date'
//                        },
//                        time: function (date, settings, forCalendar) {
//                            //return a formatted string representing the time of 'date'
//                        },
//                    }

                }
            }
        }

    }
</script>