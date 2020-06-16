<script type="text/javascript">

$(document).ready(documentReady);

function documentReady()
{
  $(".add_click_handler").each.click(function(e) {
      $("#dialog").dialog(
		{
          "modal": true,
          "buttons": {

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
  );
}
</script>
