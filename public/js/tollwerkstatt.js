$(document).ready(function () {

    // ##################
    // #### CALENDAR ####
    // ##################

    function checkCalendarSize() {
        var zIndex = parseInt($('html').css('zIndex'));
        if (zIndex >= 300) {
            $(calendar).fullCalendar('changeView', 'month');
        }else{
            $(calendar).fullCalendar('changeView', 'listMonth');
        }
    }



    var $calendar = $('#calendar');
    $($calendar).html('').fullCalendar({
        defaultView: 'listMonth',
        events: '/feed',
        header: {
            left: 'title',
            center: 'listMonth,month',
            right: 'today prev,next'
        },
        eventRender: function (event, element) {
            // Extend event rendering with event.description, which is a custom tollwerkstatt property
            if (event.description.length) {
                $(element).addClass('has-description').css('cursor', 'pointer');
            }
        },
        eventClick: function (calEvent, jsEvent, view) {
            if (!calEvent.description.length) {
                return false;
            }

            var popupElement = $('#calendar-popup');
            $(popupElement).find('#calendar-popup_content').html(calEvent.description);

            $('#calendar-popup').popup({
                autoopen: true,
                scrolllock: true,
                closeelement: '#calendar-popup_close'
            });

        },
        timezone: 'local',
        views: {
            'month': {
                timeFormat: 'hh:mm'
            }
        }
    });
    checkCalendarSize();
});

