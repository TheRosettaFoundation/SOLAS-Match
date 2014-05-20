function initDeadlinePicker() {
	
	var position = $( "#deadline" ).position();

    $('#deadline').datetimepicker({
        timeFormat: "HH:mm 'UTC'",
        dateFormat: "d MM yy",
        stepHour: 1,
        stepMinute: 10,
        beforeShow: function (input, inst) {
        setTimeout(function () {
            inst.dpDiv.css({
                top: position.top+30
                           });
        }, 0);
    }
    });
}

window.onload = initDeadlinePicker;
