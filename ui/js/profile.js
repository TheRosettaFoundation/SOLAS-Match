<script type="text/javascript">

function documentReady()
{
  $(".add_click_handler").each(function ()
    {
//alert("before adding click handler for: " + $(this).getAttribute("name"));
      $(this).click(function(e)
          {
alert("Handler for .click() called");
            $("#dialog_for_verification").dialog(
              {
                resizable: false,
                height:    "auto",
                width:     400,
                modal:     true,
                buttons: {

                  "OK": function()
                  {
alert("before trying submit of parent, action: " +   e.target.parentNode.action);
//                    e.target.parentNode.submit();
                  },

                  "Cancel": function()
                  {
alert("before trying close dialog");

                    $(this).dialog("close");
                  }
                }
              }
            );
alert("after dialog added");

            e.preventDefault();
            return false;
        }
      );
    }
  );
}

$(document).ready(documentReady);
</script>
