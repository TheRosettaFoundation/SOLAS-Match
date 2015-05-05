<script type="text/javascript">

$(document).ready(documentReady);

/**
 * Called by the DOM when the Document is Ready.
 */
function documentReady()
{
  $(".convert_utc_to_local").each(function ()
    {
      $(this).removeClass("convert_utc_to_local");
      var dUTC = $(this).text();
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
      var d = d.getDate();
      if (d < 10) {
        d = "0" + d;
      }
      var h = d.getHours();
      if (h < 10) {
        h = "0" + h;
      }
      var mi = d.getMinutes();
      if (mi < 10) {
        mi = "0" + mi;
      }
      $(this).html(d.getFullYear() + "-" + m + "-" + d + " " + h + ":" + mi + ":00");
      // Note: d.toLocaleString() works, but too many people have the wrong locale set!
      // Note: d.toString() is explicit about spelling out the Month in case the user's browser locale is wrong, but is longer and I am not sure about how it responds to locale

      $(this).css("visibility", "visible");
    }
  );
}
</script>
