$(document).ready(function() {

    $(window).resize(function() {
        // without rerendering, the event blocks get jacked when the window changes size
        $('#profOverviewSchedule').fullCalendar('rerenderEvents');
    });
});




/*  function for handling show/hide of an individual professor schedule
 *
 */
function on_profRowClick(profRowId, sectionObjects){
    $('tr#' + 'profRow_'+ profRowId).toggle();
    var currentDate = 6; //why not

    /* function 'load_indProfRowEvents()' is defined in
     * this file ('calendar.js').  It takes an array of JSON sectionObjects
     * and returns an events array for fullCalendar to read.
     */
    var theEvents = load_indProfRowEvents(sectionObjects);


    //this is what initializes and creates the individual calendar.
    //it's defined in this file (calendar.js)
    displayCalendar(profRowId, theEvents);


    //here the table row containing the calendar is shown or hidden:
    if ($('span#' + 'seeProfCal_' + profRowId).attr('class').includes("menu-up")){
        $('div#' + 'profCalendar_'+ profRowId).hide();
    }else{
        $('div#'+'profCalendar_'+ profRowId).show();
        currentDate = $('#profOverviewSchedule').fullCalendar('getDate');
    }
    $('span#' + 'seeProfCal_' + profRowId).toggleClass('glyphicon-menu-down glyphicon-menu-up');

}



/*
 * takes array of section JSON objects and returns array of events to be passed to
 * fullCalendar for display.
 */
function load_indProfRowEvents(sectionObjects){
    var events = [];
    sectionObjects.forEach(function(section, i){
        addNewEvent(section.title, section.start, section.end, section.location, section.classroom, section.professor,
            section.online ? '#137c33' : '#583372', section.online, events)
    })

    return events;
}




/*  takes a string like '2016-11-07T08:00 AM' and returns
 *  '2016-11-07T08:00:00'.
 *  if the string is like '07:00:00', that's what gets returned.
 *
 *  This function is called in addNewEvent()
 */
function formatTime_fullCalendar(time){
    var timePattern = /[0-9-]{10}T[0-9:]{5} [APM]{2}/;
    if (timePattern.test(time)){
        var timePart = time.substr(time.indexOf('T')+1);
        var hourPart = timePart.substr(0, 2);
        var hour = parseInt(hourPart);
        var minutePart = timePart.substr(timePart.indexOf(':')+1, 2);
        if (timePart.indexOf('PM') != -1){
            if (hour < 12){
                timePart = (hour + 12) + ':' + minutePart + ':00';
            }else{ //hourPart is 12 and it's PM
                timePart = hourPart + ':' + minutePart + ':00';
            }
        }else { // time is AM
            timePart = hourPart + ':' + minutePart + ':00';
        }
        return time.substr(0, time.indexOf('T')+1) + timePart;
    }else{
        return time;
    }

}

/*
 * pushes an event to the events array read by fullCalendar for an individual professor
 * figures out minimum course start time, so that the online courses can be lined up to the side
 *
 * fullCalendar works well with times formatted like '2016-11-14T07:30:00'
 */
function addNewEvent(title, eventstart, eventend, location, classroom, professor, color, isOnline, eventsArray) {
    var timedEvents = [];
    var numOnline = 0;
    eventsArray.forEach(function(event, i){
       if (event.online == true){
           numOnline ++;
       }else{
           timedEvents.push(event);
       }

    });
    var minCourseTime = getMinTime(timedEvents, 0);
    var minHour = moment(minCourseTime, 'hh:mm:ss').hour();
    if (!isOnline){
        eventsArray.push({
            title: title,
            start: formatTime_fullCalendar(eventstart),
            end: formatTime_fullCalendar(eventend),
            color: color,
            location: location,
            classroom: classroom,
            professor: professor,
            online: isOnline
        });
    }else{
        var adj_minHour = minHour + (numOnline);
        var starttime = '2016-11-13T';
        if (adj_minHour < 10) starttime += '0';
        starttime += adj_minHour + minCourseTime.substr(minCourseTime.indexOf(':'));

        var endtime = '2016-11-13T';
        if (adj_minHour + 1 < 10) endtime += '0';
        var minutesMinus2 = parseInt(minCourseTime.substr(minCourseTime.indexOf(':')+1, 2)) - 2;
        endtime += (adj_minHour + 1) + ':' + minutesMinus2  + minCourseTime.substr(minCourseTime.lastIndexOf(':'));

        eventsArray.push({
            title: title,
            start: formatTime_fullCalendar(starttime),
            end: formatTime_fullCalendar(endtime),
            color: color,
            location: "Online",
            professor: professor,
            online: isOnline
        });
    }
}



function getMinTime(eventsArray, hourChange){  //times come in looking like 2016-11-08T09:30 AM
    var minTime = '01/01/2017 23:59:00';
    eventsArray.forEach(function(event, i){
        var eventTime = '01/01/2017 ' + event.start.substr(event.start.indexOf('T')+1);
        if (Date.parse(eventTime) < Date.parse(minTime) )
            minTime = eventTime;
    });
    if (minTime == '01/01/2017 23:59:00') minTime = '01/01/2017 07:30:00'
    var hour = parseInt(minTime.substr(minTime.indexOf(' ')+1,2)) + hourChange;
    if (hour < 10) hour = '0' + hour;
    return hour + minTime.substr(minTime.indexOf(':'));
}



// display an individual professor's schedule
var displayCalendar = function(profRowId, eventsArray){
    $('#' + 'profCalendar_' + profRowId).fullCalendar({
        height: 250,
        header: false,
        defaultDate: '2016-11-07',  // 11/7/16 is a Monday
        allDaySlot: false,  // online courses can show here
        defaultView: 'agendaWeek',
        dayNames: [ 'Online','Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        columnFormat: 'dddd',
        firstDay: '1', //Monday
        navLinks: true, // can click day/week names to navigate views
        editable: false,
        eventLimit: true, // allow "more" link when too many events
        minTime: '06:30:00',
        slotLabelFormat: 'h(:mm) a',
        slotLabelInterval: '00:30',
        slotDuration: '00:60:00',
        events: eventsArray,
        scrollTime: formatTime_fullCalendar(getMinTime(eventsArray, -1)), //defined in this file (calendar.js)
        eventRender: function (event, element) {
            var timeString = event.online ? "" :
                (moment(event.start).format('h:mm A') + ' - ' + moment(event.end).format('h:mm A'));
            var room = event.classroom != undefined ? event.classroom : "";
            element.popover(
                {
                    html: true,
                    title: "<strong>" + event.title + "</strong>",  //some html has to be in the title or it won't work
                    content: event.professor + "<br>" + event.location + " " + room + "<br>" + timeString,
                    trigger: 'hover click',
                    placement: "right",
                    selector: event,
                    container: 'body'  //  THIS NEEDS TO BE HERE SO tooltip is on top of everything
                }
            );
        },
    });

}


/*  This function takes the "set" of professors and their section objects, and forms them into
 *  event blocks to be displayed by fullCalendar.  The events have starting and ending times which
 *  do NOT correlate to class time -- these times are for positioning the professors vertically in
 *  the first column.
 *
 *  To add to a moment object, call .clone() and then .add or .subtract (number, 'option').
 *  Ex:  momentObject.clone().add(5,'m') adds 5 minutes.  'd' is for days.  'h' is for hours.
 */
function createEventsSet(theSet){
    var singleRow = 5;   //row "height" is 5 minutes
    var doubleRow = 10;  //two rows is 10 minutes
    var events = [];
    var rowZeroColumnZero = moment({ years:2016, months:10, date:6, hours:6, minutes:00}); //11/7/16, 6 AM
    var prevDividerStart = rowZeroColumnZero;
    theSet.forEach(function(prof, i){
        var profName = prof.name;
        var theStart = i == 0 ? rowZeroColumnZero : prevDividerStart.clone().add(5, 'm');
        var theEnd = theStart.clone().add(10, 'm');
        events.push(
            {
                title: profName,
                start: theStart,
                end: theEnd,
                className: 'profName',
                order_by: 'A',
                color: '#194d96'
            },
            {
                title: 'MW',
                start: theStart,
                end: theEnd,
                className: 'days',
                order_by: 'B'
            },
            {
                title: " ",
                start: theStart.clone().add(10, 'm'),
                end: theEnd.clone().add(10,'m'),
                className: 'event_placeholder',
                order_by: 'A'
            },
            {
                title: 'TTH',
                start: theStart.clone().add(10, 'm'),
                end: theEnd.clone().add(10,'m'),
                className: 'days',
                order_by: 'B'
            }
        );
        var theCourseTitle;
        var theCourseStart;
        prof.timedCourses.forEach(function(course, j){
            theCourseTitle = course.courseTitle;
            theCourseStart = momentGenerator(course.startTime, course.courseDays, theStart.clone());

            events.push(
                {
                    title: theCourseTitle,
                    start: theCourseStart,
                    end: theCourseStart.clone().add(10,'m'),
                    className: 'classEvent'
                }
            );
        });
        prof.onlineCourses.forEach(function(course, k){
            events.push(
                {
                    title: course.courseTitle + '   -- Online --',
                    start: theStart.clone().add((doubleRow * 2)+(singleRow * k),'m'),
                    end: theStart.clone().add((doubleRow * 2)+(singleRow * k) + singleRow,'m'),
                    className: 'classEvent'
                });
        });
        prof.nonStandardCourses.forEach(function(course, m){
            theCourseStart = momentGenerator(course.startTime, course.courseDays, theStart.clone());

            events.push(
                {
                    title: course.courseTitle + '\n' +
                        course.startTime.substring(0,course.startTime.indexOf(' ')) +
                        '-' + course.endTime.substring(0,course.endTime.indexOf(' ')),
                    start: theCourseStart,
                    end: theCourseStart.clone().add(10, 'm'),
                    color: '#840b38'
                }
            );
        });
        prevDividerStart = theStart.clone()
            .add((10 * 2) + (5 * prof.onlineCourses.length)  /*(10 * prof.nonStandardCourses.length)*/, 'm');

        for (i=0; i < 7; i++) {
            events.push(
                {
                    title: "",
                    start: prevDividerStart.clone().add(i, 'd'),
                    end: prevDividerStart.clone()
                        .add(5, 'm')
                        .add(i,'d'),
                    className: 'profDivider'
                });
        }
    });
    return events;
};



function displayProfOverviewSchedule(theProfSet) {
    var theEvents = createEventsSet(theProfSet);

    $('#profOverviewSchedule').fullCalendar({
        header: false,
        defaultView: 'agendaWeek',
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        dayNames: ['Professor', '7:30 AM', '9:30 AM', '11:30 AM', '1:30 PM', '5:30 PM', '7:30 PM'],
        columnFormat: 'dddd',
        allDaySlot: false,
        defaultDate: '2016-11-07',  // 11/7/16 is a Monday
        firstDay: '0', //Monday
        slotLabelFormat: ' ', //the space makes the slots blank.  First time is 6 AM.
        slotDuration: '00:5:00',
        minTime: '06:00:00',
        eventOrder: 'order_by',
        eventAfterRender: function (event, element, view) {
            if ($(element).hasClass("profName")) {
                $(element).css('width', $(element).width() *.90 + 'px');
                $(element).css('background-color', '#194d96');
                $(element).css('border-color', '#194d96');
            }
            if ($(element).hasClass("days")) {
                $(element).css('width', $(element).width() / 2 + 'px');
                $(element).css('margin-left', '25%');
                $(element).css('background-color', '#137c33');
                $(element).css('border-color', '#137c33');
            }
            if ($(element).hasClass("classEvent")) {
                $(element).css('background-color', '#583372');
                $(element).css('border-color', '#583372');
            }
            if ($(element).hasClass("event_placeholder")) {
                $(element).css('margin-top', '5%');
                $(element).css('margin-bottom', '5%');
                $(element).css('background-color', '#fff');
                $(element).css('border', 'none');
            }
            if ($(element).hasClass("profDivider")) {
                $(element).css('background-color', '#e5deea');//  e5deea light purple-gray
                $(element).css('border-color', '#e5deea');
            }



        },
        events: theEvents
    });

}











/*

 addNewEvent('addNewEvent(1)', '2016-11-11T07:30:00',
 '2016-11-11T09:20:00', 'Main 103C', 'Valle, Hugo','#583372', false);
 addNewEvent('addNewEvent(2)', '2016-11-11T09:30:00',
 '2016-11-11T11:20:00', 'Main 103C', 'Fry, Richard','#583372', false);
 addNewEvent('Online (1)', '2016-11-11T00:00:00',
 '2016-11-11T00:00:00', '', 'Fry, Richard','#137c33', true);
 addNewEvent('Online (2)', '2016-11-11T00:00:00',
 '2016-11-11T00:00:00', '', 'Rague, Brian','#137c33', true);
 */












/*[
 {
 title: 'Brinkerhoff, Delroy',
 start: rowZeroColumnZero,  // date 2016-11-06 is the first column
 end: rowZeroColumnZero.clone().add(10, 'm'),
 className: 'profName'
 },
 {
 title: 'MW',
 start: '2016-11-06T06:00:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:10:00',
 className: 'days'
 },
 {
 title: '',
 start: '2016-11-06T06:10:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:20:00',
 className: 'event_placeholder'
 },
 {
 title: 'TTH',
 start: '2016-11-06T06:10:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:20:00',
 className: 'days'
 },
 {
 title: 'CS 1410',
 start: '2016-11-09T06:00:00', // date 2016-11-09 is 11:30 AM
 end: '2016-11-09T06:10:00',
 className: 'classEvent'
 },
 {
 title: 'CS 1410',
 start: '2016-11-08T06:10:00', // date 2016-11-08 is 9:30 AM
 end: '2016-11-08T06:20:00',
 className: 'classEvent'
 },
 {
 title: 'CS 1410 [Online]',
 start: '2016-11-06T06:20:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:25:00',
 className: 'classEvent'
 },
 {
 title: 'CS 3230 [Online]',
 start: '2016-11-06T06:25:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:30:00',
 className: 'classEvent'
 },
 {
 title: "",
 start: '06:30', // a start time (10am in this example)
 end: '06:35', // an end time (2pm in this example)
 dow: [0, 1, 2, 3, 4, 5, 6],// Repeat monday and thursday
 color: '#e5deea'  //light purple-gray
 },
 {
 title: 'Ball, Bob',
 start: '2016-11-06T06:35:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:45:00',
 className: 'profName'
 },
 {
 title: 'MW',
 start: '2016-11-06T06:35:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:45:00',
 className: 'days'
 },
 {
 title: '',
 start: '2016-11-06T06:45:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:55:00',
 className: 'event_placeholder'
 },
 {
 title: 'TTH',
 start: '2016-11-06T06:45:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T06:55:00',
 className: 'days'
 },
 {
 title: 'CS 3100',
 start: '2016-11-07T06:45:00',  // date 2016-11-07 is 7:30 AM
 end: '2016-11-07T06:55:00',
 className: 'classEvent'
 },
 {
 title: 'CS 1400',
 start: '2016-11-08T06:35:00',  // date 2016-11-08 is 9:30 AM
 end: '2016-11-08T06:45:00',
 className: 'classEvent'
 },
 {
 title: 'CS 2350',
 start: '2016-11-09T06:35:00',  // date 2016-11-09 is11:30 AM
 end: '2016-11-09T06:45:00',
 className: 'classEvent'
 },
 {
 title: 'CS 1400 [Online]',
 start: '2016-11-06T06:55:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T07:00:00',
 className: 'classEvent'
 },
 {
 title: "",
 start: '07:00', // a start time (10am in this example)
 end: '07:05', // an end time (2pm in this example)
 dow: [0, 1, 2, 3, 4, 5, 6],// Repeat monday and thursday
 color: '#e5deea'  //light purple-gray
 },
 {
 title: 'Cowan, Ted',
 start: '2016-11-06T07:05:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T07:15:00',
 className: 'profName'
 },
 {
 title: 'MW',
 start: '2016-11-06T07:05:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T07:15:00',
 className: 'days'
 },
 {
 title: '',
 start: '2016-11-06T07:15:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T07:25:00',
 className: 'event_placeholder'
 },
 {
 title: 'TTH',
 start: '2016-11-06T07:15:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T07:25:00',
 className: 'days'
 },
 {
 title: 'CS 4750',
 start: '2016-11-11T07:05:00',  // date 2016-11-11 is 5:30 PM
 end: '2016-11-11T07:15:00',
 className: 'classEvent'
 },
 {
 title: 'CS 3100',
 start: '2016-11-12T07:05:00',  // date 2016-11-12 is 7:30 PM
 end: '2016-11-12T07:15:00',
 className: 'classEvent'
 },
 {
 title: 'CS 3030 [Online]',
 start: '2016-11-06T07:25:00',  // date 2016-11-06 is the first column
 end: '2016-11-06T07:30:00',
 className: 'classEvent'
 },
 {
 title: "",
 start: '07:30', // a start time (10am in this example)
 end: '07:35', // an end time (2pm in this example)
 dow: [0, 1, 2, 3, 4, 5, 6],// Repeat monday and thursday
 color: '#e5deea'  //light purple-gray
 }

 ]*/



/* $('#'+ 'seeProfCal_'+i).click ( function(){
 $('#' + 'profRow_'+i).toggle();
 addNewEvent('addNewEvent(1)', '2016-11-11T07:30:00',
 '2016-11-11T09:20:00', 'Main 103C', 'Valle, Hugo','#583372', false);
 addNewEvent('addNewEvent(2)', '2016-11-11T09:30:00',
 '2016-11-11T11:20:00', 'Main 103C', 'Fry, Richard','#583372', false);
 addNewEvent('Online (1)', '2016-11-11T00:00:00',
 '2016-11-11T00:00:00', '', 'Fry, Richard','#137c33', true);
 addNewEvent('Online (2)', '2016-11-11T00:00:00',
 '2016-11-11T00:00:00', '', 'Rague, Brian','#137c33', true);
 displayCalendar();

 if ($(this).attr('class').includes("menu-up")){
 $('#' + 'profCalendar_'+i).hide();
 }else{
 $('#'+'profCalendar_'+i).show();
 }
 $(this).toggleClass('glyphicon-menu-down glyphicon-menu-up');
 });*/