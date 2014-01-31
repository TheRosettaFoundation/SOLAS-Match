function initDeadlinePicker() {
    $('#deadline').datetimepicker({
        timeFormat: "HH:mm 'UTC'",
        dateFormat: "d MM yy",
        stepHour: 1,
        stepMinute: 10
    });
}

window.onload = initDeadlinePicker;
