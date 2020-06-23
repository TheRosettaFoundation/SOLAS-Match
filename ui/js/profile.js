<script type="text/javascript">

$(document).ready(documentReady);

function documentReady()
{
  $(".add_click_handler").each(function ()
    {
      $(this).onclick = function(e)
        {
          $("#dialog_for_verification").dialog(
            {
              resizable: false,
              height:    "auto",
              width:     400,
              modal:     true,
              buttons: {

                "OK": function()
                {
                  e.target.parentNode.submit();
                },

                "Cancel": function()
                {
                  $(this).dialog("close");
                }
              }
            }
          );

          e.preventDefault();
          return false;
      }
    }
  );
}
</script>
