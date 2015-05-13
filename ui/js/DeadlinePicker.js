function initDeadlinePicker()
{
  var dUTC = $("#deadline_field").val();
  if (dUTC != "") {
    var year    = dUTC.substring(0, 4);
    var month   = dUTC.substring(5, 7) -1;
    var day     = dUTC.substring(8, 10);
    var hour    = dUTC.substring(11, 13);
    var minutes = dUTC.substring(14, 16);
    var seconds = dUTC.substring(17, 19);

    var d = new Date(Date.UTC(year, month, day, hour, minutes, seconds));

    var m = d.getMonth() + 1;
    if (m < 10) {
      m = "0" + m;
    }
    var da = d.getDate();
    if (da < 10) {
      da = "0" + da;
    }
    var h = d.getHours();
    if (h < 10) {
      h = "0" + h;
    }
    var mi = d.getMinutes();
    if (mi < 10) {
      mi = "0" + mi;
    }
    var s = d.getSeconds();
    if (s < 10) {
      s = "0" + s;
    }
    $("#deadline_field").val(d.getFullYear() + "-" + m + "-" + da + " " + h + ":" + mi + ":" + s);
  }

  var position = $("#deadline_field").position();

  $('#deadline_field').datetimepicker(
    {
      timeFormat: "HH:mm:ss",
      dateFormat: "yy-mm-dd",
      stepHour: 1,
      stepMinute: 10,
      beforeShow: function (input, inst)
        {
          setTimeout(function () {inst.dpDiv.css({top: position.top + 30});}, 0);
        }
    }
  );
}

function validateForm()
{
  var deadline = $('#deadline_field').datetimepicker("getDate");
  if (deadline != null) {
    var m = deadline.getUTCMonth() + 1;
    if (m < 10) {
      m = "0" + m;
    }
    var d = deadline.getUTCDate();
    if (d < 10) {
      d = "0" + d;
    }
    var h = deadline.getUTCHours();
    if (h < 10) {
      h = "0" + h;
    }
    var mi = deadline.getUTCMinutes();
    if (mi < 10) {
      mi = "0" + mi;
    }
    document.getElementById("deadline").value = deadline.getUTCFullYear() + "-" + m + "-" + d + " " + h + ":" + mi + ":00";
  }

  return true;
}

window.onload = initDeadlinePicker;
