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

  $('.badge_value').each(function() {

    $(this).prop('counter', 0).animate({
  
      counter: $(this).text()
  
    }, {
  
      duration: 4000,
  
      easing: 'swing',
  
      step: function(now) {
  
        $(this).text(Math.ceil(now));
      }
    });
  });
}

$(document).ready(documentReady);
</script>
