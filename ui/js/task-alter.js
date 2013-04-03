
jQuery(selectableUpdate);

function selectableUpdate()
{
    jQuery( "#selectable" ).selectable({
        stop: function() {
            var result = $( "#select-result" ).empty();
            var selectedList = $("#selectedList").val("");
            jQuery( ".ui-selected", this ).each(function() {
                var index = $( "#selectable li" ).index( this );
                var taskId = $( "#selectable li:nth-child(" + (index + 1) + ")").val();
                result.append( " #" + ( index + 1 ) );
                selectedList.val(selectedList.val() + taskId + ",");
            });
        }
    });
}