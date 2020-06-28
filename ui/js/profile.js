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
                  "class": "btn btn-primary",

                  "OK": function()
                  {
                    //alert("action: " + e.target.parentNode.action);
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
      );
    }
  );
}

$(document).ready(documentReady);
</script>
