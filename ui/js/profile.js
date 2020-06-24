<script type="text/javascript">

$(document).ready(documentReady);

function documentReady()
{
  $(".add_click_handler").each(function ()
    {
alert("1 onclick " + $(this).name);
      $(this).onclick = function(e)
        {
alert("onclick " + $(this).getAttribute("name"));
document.getElementById('buttonLED'+id).setAttribute('onclick','writeLED(1,1)')
var but.setAttribute('onclick', 'my_function( " '+my_string+' " )');
editButton.setAttribute('onclick', "editSection('id');")
EG onclick="return confirm('Are you sure you want to permanently delete this certificate?')"

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
