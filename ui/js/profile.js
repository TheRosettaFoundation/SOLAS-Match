<script type="text/javascript">

function documentReady()
{
  $(".add_click_handler").each(function ()
    {
      $(this).click(function(e)
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
                    //alert("action: " + e.target.parentNode.action);
                    e.target.disabled = true;
                    e.target.parentNode.submit();
                    $(this).dialog("close");
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
      );
    }
  );
}

$(document).ready(documentReady);
</script>
