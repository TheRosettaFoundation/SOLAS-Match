<script type="text/javascript">

$(document).ready(documentReady);

function documentReady()
{
  $(".add_click_handler").each(function ()
    {
alert("we got here before adding onclick");
alert("1 onclick " + $(this).name);
      $(this).onclick = function(e)
        {
alert("onclick " + $(this).name);
          $("#dialog_for_verification").dialog(
            {
              resizable: false,
              height:    "auto",
              width:     400,
              modal:     true,
              buttons: {

                "OK": function()
                {
//                  e.target.parentNode.submit();
                },

                "Cancel": function()
                {
//                  $(this).dialog("close");
                }
              }
            }
          );
alert("AFTER DIALOG");

          e.preventDefault();
          return false;
      }
    }
  );
}
</script>
