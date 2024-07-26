<script type="text/javascript">
    window.onload = runStartup;

function runStartup() {
    select();
}

function select() {
    $('#task_options').on('change', function (e) {
        var arr = [];
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        $('[type=checkbox]').prop("checked", false);

        if (valueSelected == "all_translation_tasks") {
            $(":checkbox[data-task-type='2']").prop("checked", true);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=unpublish_selected__translation_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_revision_tasks") {
            $(":checkbox[data-task-type='3']").prop("checked", true);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_tasks") {
            $('[name=select_task]').prop("checked", true);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_approval_tasks") {
            $(":checkbox[data-task-type='6']").prop("checked", true);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_revtrans_tasks") {
            $(":checkbox[data-task-type='2']").prop("checked", true);
            $(":checkbox[data-task-type='3']").prop("checked", true);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_paid_tasks") {
            $(":checkbox[data-paid='1']").prop("checked", true);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_tasks_ready_payment") {
            $(":checkbox[data-payment-status='Ready for payment']").prop("checked", true);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        }
        else {
            $(":checkbox").prop("checked", false);
            arr = [];
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        }
    });

    var select_all_tasks = [];
    var select_all_tasks_removed = [];
    var unchecked_items = [];
    $('[name=select_all_tasks]').on('change', function (e) {
        if ($(this).prop('checked') == true) {
            $(':checkbox[data-lang="'+$(this).attr("data-lang")+'"]').prop("checked", true);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on" && !(jQuery.inArray($(this).val(), select_all_tasks) !== -1)) {
                    select_all_tasks.push($(this).val());
                }
            });

        } else {
            $(':checkbox[data-lang="'+$(this).attr("data-lang")+'"]').prop("checked", false);
            $($(':checkbox[data-lang="'+$(this).attr("data-lang")+'"]')).each( function () {
              if($(this).val() != "on") {
                    select_all_tasks.splice( $.inArray($(this).val(), select_all_tasks), 1 );
                }
            });
        }
        $("[name=unpublish_selected_tasks]").val(select_all_tasks);
        $("[name=publish_selected_tasks]").val(select_all_tasks);
        $("[name=tasks_as_paid]").val(select_all_tasks);
        $("[name=tasks_as_unpaid]").val(select_all_tasks);
        $("[name=restrict_native_language_and_variant]").val(select_all_tasks);
        $("[name=restrict_native_language_only]").val(select_all_tasks);
        $("[name=restrict_native_language_none]").val(select_all_tasks);
        $("[name=status_as_unclaimed]").val(select_all_tasks);
        $("[name=status_as_waiting]").val(select_all_tasks);
        $("[name=cancel]").val(select_all_tasks);
        $("[name=complete_selected_tasks]").val(select_all_tasks);
        $("[name=uncomplete_selected_tasks]").val(select_all_tasks);
        $("[name=ponum]").val(select_all_tasks);
        $("[name=ready_payment]").val(select_all_tasks);
        $("[name=pending_documentation]").val(select_all_tasks);
        $("[name=tasks_settled]").val(select_all_tasks);
    });

    $('[name=select_task]').on('change', function (e) {
        var arr_select_task = [];
        $(':checkbox:checked').each(function () {
            if ($(this).val() != "on") {
            arr_select_task.push($(this).val());
            }
        });
        $("[name=unpublish_selected_tasks]").val(arr_select_task);
        $("[name=publish_selected_tasks]").val(arr_select_task);
        $("[name=tasks_as_paid]").val(arr_select_task);
        $("[name=tasks_as_unpaid]").val(arr_select_task);
        $("[name=restrict_native_language_and_variant]").val(arr_select_task);
        $("[name=restrict_native_language_only]").val(arr_select_task);
        $("[name=restrict_native_language_none]").val(arr_select_task);
        $("[name=status_as_unclaimed]").val(arr_select_task);
        $("[name=status_as_waiting]").val(arr_select_task);
        $("[name=cancel]").val(arr_select_task);
        $("[name=complete_selected_tasks]").val(arr_select_task);
        $("[name=uncomplete_selected_tasks]").val(arr_select_task);
        $("[name=ponum]").val(arr_select_task);
        $("[name=ready_payment]").val(arr_select_task);
        $("[name=pending_documentation]").val(arr_select_task);
        $("[name=tasks_settled]").val(arr_select_task);
    });

    var forms = [
        "publish_selected_tasks",
        "unpublish_selected_tasks",
        "tasks_as_paid",
        "tasks_as_unpaid",
        "status_as_unclaimed",
        "status_as_waiting",
        "complete_selected_tasks",
        "uncomplete_selected_tasks",
        "restrict_native_language_and_variant",
        "restrict_native_language_only",
        "restrict_native_language_none"
    ];

    // Validation if user clicks on action without doing a selection
    jQuery.each(forms, function(index, item) {
        $(document).on("submit","#"+item, function () {
            if ($("[name='"+item+"']").val() == "") {
                alert("No selection done");
                return false;
            }
        });
    });

    // Get project task type to hide approval options
    var task_types = [];
    $(':checkbox').each(function () {
        task_types.push($(this).attr("data-task-type"));
    });

    // Remove undefined
    var data = jQuery.unique(task_types);
    data = data.filter(function(element) {
        return element !== undefined;
    });

    // Show/hide approval selection
    if (jQuery.inArray("6", data ) == -1) {
        $("#all_approval_tasks").hide();
        $(".all_approval_tasks_lang").hide();
    } else {
        $("#all_approval_tasks").show();
        $(".all_approval_tasks_lang").show();
    }

    // Language pair dropdown
    $(document).on('change', 'select[name="language_options[]"]', function(){
        var arr = [];
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        $(':checkbox[data-lang="'+$(this).attr("data-select-name")+'"]').prop("checked", false);
        if (valueSelected == "all_tasks_"+$(this).attr("data-select-name")) {
            $(':checkbox[data-lang="'+$(this).attr("data-select-name")+'"]').prop("checked", true);
           // arr = [];
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                    arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_translation_tasks_"+$(this).attr("data-select-name")) {
            $(':checkbox[data-lang="'+$(this).attr("data-select-name")+'"][data-task-type="2"]').prop("checked", true);
           // arr = [];
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                    arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_revision_tasks_"+$(this).attr("data-select-name")) {
            $(':checkbox[data-lang="'+$(this).attr("data-select-name")+'"][data-task-type="3"]').prop("checked", true);
           // arr = [];
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                    arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_approval_tasks_"+$(this).attr("data-select-name")) {
            $(':checkbox[data-lang="'+$(this).attr("data-select-name")+'"][data-task-type="6"]').prop("checked", true);
           // arr = [];
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                    arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else if (valueSelected == "all_revtrans_tasks_"+$(this).attr("data-select-name")) {
            $(':checkbox[data-lang="'+$(this).attr("data-select-name")+'"][data-task-type="2"]').prop("checked", true);
            $(':checkbox[data-lang="'+$(this).attr("data-select-name")+'"][data-task-type="3"]').prop("checked", true);
           // arr = [];
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on") {
                    arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        }
         else if (valueSelected == "delesect_all_"+$(this).attr("data-select-name")) {
            $(':checkbox[data-lang="'+$(this).attr("data-select-name")+'"]').prop("checked", false);
            $(':checkbox:checked').each(function () {
                if ($(this).val() != "on" && !(jQuery.inArray($(this).val(), arr) !== -1)) {
                    arr.push($(this).val());
                }
            });
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        } else {
            $("[name=unpublish_selected_tasks]").val(arr);
            $("[name=publish_selected_tasks]").val(arr);
            $("[name=tasks_as_paid]").val(arr);
            $("[name=tasks_as_unpaid]").val(arr);
            $("[name=restrict_native_language_and_variant]").val(arr);
            $("[name=restrict_native_language_only]").val(arr);
            $("[name=restrict_native_language_none]").val(arr);
            $("[name=status_as_unclaimed]").val(arr);
            $("[name=status_as_waiting]").val(arr);
            $("[name=cancel]").val(arr);
            $("[name=complete_selected_tasks]").val(arr);
            $("[name=uncomplete_selected_tasks]").val(arr);
            $("[name=ponum]").val(arr);
            $("[name=ready_payment]").val(arr);
            $("[name=pending_documentation]").val(arr);
            $("[name=tasks_settled]").val(arr);
        }
    });

    //Cancel Task
    $(document).on('change', 'select[name="cancel_task"]', function() {
        var valueSelected = this.value;

        if (valueSelected == "other" || !parseInt(document.getElementById("isSiteAdmin").innerHTML)) {
            $("[name=reason]").show();
            $("[name=reason_text]").show();
            $("[name=reason]").bind("change paste keyup", function() {
                if($(this).val() == "") {
                    $("#cancelbtn").prop('disabled', true);
                }else {
                    $("#cancelbtn").prop('disabled', false);
                }
             });
        }else if (valueSelected == "") {
            $("#cancelbtn").prop('disabled', true);
        }
         else {
            $("#cancelbtn").prop('disabled', false);
        }
    });

    $('#cancelmodal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        //$("[name=reason]").hide();
       // $("[name=reason_text]").show();
        //$("#cancelbtn").prop('disabled', false);
    });

    //Validation - Hide btn if task is not selected
    $('#cancelmodal').on('shown.bs.modal', function () {
        $("#taskmsg").hide();
        if($("[name=cancel]").val().length == 0) {
            $("#cancelbtn").prop('disabled', true);
            $("#taskmsg").show();
      }

      if($("[name=cancel_task]").val() == "") {
        $("#cancelbtn").prop('disabled', true);
      }
      });

    $('.cancel').on('click', function (e) {
        e.preventDefault();
        $("[name=cancel]").val($(this).attr("data-task-id"));
        $("[name=cancelled]").val($(this).attr("data-cancelled"));
    });

    $('.open-cancel-modal').on('click', function (e) {
        e.preventDefault();
        $("[name=cancelled]").val($(this).attr("data-cancelled"));
    });

    $('#ponummodal').on('shown.bs.modal', function () {
        $("#ponumbtn").prop('disabled', true);
        $("[name=po]").on("focus", function(){
            $(this).on("change paste keyup", function() {
                if($(this).val().length == 0) {
                    $("#ponumbtn").prop('disabled', true);
              }else {
                $("#ponumbtn").prop('disabled', false);
              }
             });
        });
      });
}
</script>
