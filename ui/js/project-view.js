<script type ="text/javascript">
    window.onload = runStartup;

function runStartup() {
    prepareGraph();
    $("#tabs").tabs();
    select();

}
function select() {
   
    $('#task_options').on('change', function (e) {
        var arr = [];
       
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        $('[type=checkbox]').prop("checked",false);
        
        if(valueSelected == "all_translation_tasks"){

            $(":checkbox[data-task-type='2']").prop("checked",true);
           // $("[name=unpublish_selected_tasks]").val($(":checkbox[data-task-type='2']").val());
           /*
           $(':checkbox:checked').each(function () {
            arr.push($(this).val());
        });*/
        $(':checkbox:checked').each(function () {
            arr.push($(this).val());
             });
           $("[name=unpublish_selected_tasks]").val(arr);
           $("[name=publish_selected_tasks]").val(arr);
           $("[name=tasks_as_paid]").val(arr);
           $("[name=tasks_as_unpaid]").val(arr);
           $("[name=status_as_unclaimed]").val(arr);
           $("[name=status_as_waiting]").val(arr);
        $("[name=unpublish_selected__translation_tasks]").val(arr);
        }
        else if(valueSelected == "all_revision_tasks"){

            $(":checkbox[data-task-type='3']").prop("checked",true);
            $(':checkbox:checked').each(function () {
                arr.push($(this).val());
                 });
               $("[name=unpublish_selected_tasks]").val(arr);
               $("[name=publish_selected_tasks]").val(arr);
               $("[name=tasks_as_paid]").val(arr);
               $("[name=tasks_as_unpaid]").val(arr);
               $("[name=status_as_unclaimed]").val(arr);
               $("[name=status_as_waiting]").val(arr);
            
        }
        else if(valueSelected == "all_tasks"){

           // $(":checkbox[data-task-type='3']").prop("checked",true);
           $('[name=select_task]').prop("checked",true);
           $(':checkbox:checked').each(function () {
            arr.push($(this).val());
             });
           $("[name=unpublish_selected_tasks]").val(arr);
           $("[name=publish_selected_tasks]").val(arr);
           $("[name=tasks_as_paid]").val(arr);
           $("[name=tasks_as_unpaid]").val(arr);
           $("[name=status_as_unclaimed]").val(arr);
           $("[name=status_as_waiting]").val(arr);
        }
        else{
            $(":checkbox").prop("checked",false);

            arr = [];
           $("[name=unpublish_selected_tasks]").val(arr);
           $("[name=publish_selected_tasks]").val(arr);
           $("[name=tasks_as_paid]").val(arr);
           $("[name=tasks_as_unpaid]").val(arr);
           $("[name=status_as_unclaimed]").val(arr);
           $("[name=status_as_waiting]").val(arr);

        }
        console.log(valueSelected);
    });

    $('[name=select_all_tasks]').on('change', function (e) {
        var arr = [];
        if($(this).prop('checked') == true){
            $(':checkbox[data-lang="'+$(this).attr("data-lang")+'"]').prop("checked",true);
            $(':checkbox:checked').each(function () {
                if($(this).val() != "on"){
                    arr.push($(this).val());
                }
                 });
               $("[name=unpublish_selected_tasks]").val(arr);
               $("[name=publish_selected_tasks]").val(arr);
               $("[name=tasks_as_paid]").val(arr);
               $("[name=tasks_as_unpaid]").val(arr);
               $("[name=status_as_unclaimed]").val(arr);
               $("[name=status_as_waiting]").val(arr);
        }else{
            $(':checkbox[data-lang="'+$(this).attr("data-lang")+'"]').prop("checked",false);
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            
        }
    
    });



    

    $('[name=select_task]').on('change', function (e) {
        var arr_select_task = [];
        $(':checkbox:checked').each(function () {
                arr_select_task.push($(this).val());
             });
           $("[name=unpublish_selected_tasks]").val(arr_select_task);
           $("[name=publish_selected_tasks]").val(arr_select_task);
           $("[name=tasks_as_paid]").val(arr_select_task);
           $("[name=tasks_as_unpaid]").val(arr_select_task);
           $("[name=status_as_unclaimed]").val(arr_select_task);
           $("[name=status_as_waiting]").val(arr_select_task);
    });

    var forms = [
        "tasks_as_paid",
        "tasks_as_unpaid",
        "publish_selected_tasks",
        "unpublish_selected_tasks",
        "status_as_unclaimed",
        "status_as_waiting",
    ];

    //validation if user clicks on action without doing a selection

    jQuery.each(forms, function(index, item) {
        console.log(item);
        $(document).on("submit","#"+item, function () {

            console.log($("[name='"+item+"']").val());

            if($("[name='"+item+"']").val() == "" ) {
                alert("No selection done");
                return false;
    
            }
            
    
         });
    });

    //Get project task type to hide approval options

    var task_types = [];

    $(':checkbox').each(function () {
    
            task_types.push($(this).attr("data-task-type"));  
        
    });
    

    //remove undefined
    var data = jQuery.unique( task_types );
    data = data.filter(function( element ) {
        return element !== undefined;
    });

    //show/hide approval selection
    if(jQuery.inArray( "6", data ) == -1){
        
        $("#all_approval_tasks").hide();
        $("#all_approval_tasks_lang").hide();
    }else{
        $("#all_approval_tasks").show();
        $("#all_approval_tasks_lang").show();
    }

    //Language pair dropdown

    $('#language_options').on('change', function (e) {
        var arr = [];
       
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        $(':checkbox[data-lang="'+$.trim($('.language_name').html()).split(" ")[0]+'"]').prop("checked",false);
         console.log($.trim($('.language_name').html()).split(" ")[0]);
         console.log(valueSelected);
        
         if(valueSelected == "all_tasks_"+$.trim($('.language_name').html()).split(" ")[0]){
            $(':checkbox[data-lang="'+$.trim($('.language_name').html()).split(" ")[0]+'"]').prop("checked",true);
            arr = [];
            $(':checkbox:checked').each(function () {
                if($(this).val() != "on"){
                    arr.push($(this).val());
                }
             });
           $("[name=unpublish_selected_tasks]").val(arr);
           $("[name=publish_selected_tasks]").val(arr);
           $("[name=tasks_as_paid]").val(arr);
           $("[name=tasks_as_unpaid]").val(arr);
           $("[name=status_as_unclaimed]").val(arr);
           $("[name=status_as_waiting]").val(arr);

         }else if(valueSelected == "all_translation_tasks_"+$.trim($('.language_name').html()).split(" ")[0]){
            $(':checkbox[data-lang="'+$.trim($('.language_name').html()).split(" ")[0]+'"][data-task-type="2"]').prop("checked",true);
            arr = [];
            $(':checkbox:checked').each(function () {
                if($(this).val() != "on"){
                    arr.push($(this).val());
                }
             });
           $("[name=unpublish_selected_tasks]").val(arr);
           $("[name=publish_selected_tasks]").val(arr);
           $("[name=tasks_as_paid]").val(arr);
           $("[name=tasks_as_unpaid]").val(arr);
           $("[name=status_as_unclaimed]").val(arr);
           $("[name=status_as_waiting]").val(arr);

         }else if(valueSelected == "all_revision_tasks_"+$.trim($('.language_name').html()).split(" ")[0]){
            $(':checkbox[data-lang="'+$.trim($('.language_name').html()).split(" ")[0]+'"][data-task-type="3"]').prop("checked",true);
            arr = [];
            $(':checkbox:checked').each(function () {
                if($(this).val() != "on"){
                    arr.push($(this).val());
                }
             });
           $("[name=unpublish_selected_tasks]").val(arr);
           $("[name=publish_selected_tasks]").val(arr);
           $("[name=tasks_as_paid]").val(arr);
           $("[name=tasks_as_unpaid]").val(arr);
           $("[name=status_as_unclaimed]").val(arr);
           $("[name=status_as_waiting]").val(arr);

        }else if(valueSelected == "all_approval_tasks_"+$.trim($('.language_name').html()).split(" ")[0]){
            $(':checkbox[data-lang="'+$.trim($('.language_name').html()).split(" ")[0]+'"][data-task-type="6"]').prop("checked",true);
            arr = [];
            $(':checkbox:checked').each(function () {
                if($(this).val() != "on"){
                    arr.push($(this).val());
                }
             });
           $("[name=unpublish_selected_tasks]").val(arr);
           $("[name=publish_selected_tasks]").val(arr);
           $("[name=tasks_as_paid]").val(arr);
           $("[name=tasks_as_unpaid]").val(arr);
           $("[name=status_as_unclaimed]").val(arr);
           $("[name=status_as_waiting]").val(arr);

        }else if(valueSelected == "delesect_all_"+$.trim($('.language_name').html()).split(" ")[0]){
            arr = [];

            $(':checkbox[data-lang="'+$.trim($('.language_name').html()).split(" ")[0]+'"]').prop("checked",false);


           $("[name=unpublish_selected_tasks]").val(arr);
           $("[name=publish_selected_tasks]").val(arr);
           $("[name=tasks_as_paid]").val(arr);
           $("[name=tasks_as_unpaid]").val(arr);
           $("[name=status_as_unclaimed]").val(arr);
           $("[name=status_as_waiting]").val(arr);

        }
         else{

            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
 

         }
    });


 



}
</script>