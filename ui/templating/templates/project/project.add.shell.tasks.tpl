{include file="header.tpl"}

    <div class="grid_8">
        <div class="page-header">
            <h1>
                Add Shell Tasks to: {$project->getTitle()|escape:'html':'UTF-8'}<br />
            </h1>
        </div>           
    </div>  

    <div class="well pull-left" style="margin-bottom: 50px">

        {if isset($flash['error'])}
            <p class="alert alert-error">
                {$flash['error']}
            </p>
        {/if}

        <div id="placeholder_for_errors_1"></div>

        <form method="post" action="{urlFor name="project-add-shell-tasks" options="project_id.$project_id"}" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="create_project_button.disabled = true;">
            <table id="myTable" class="container table table-striped" style="overflow-wrap: break-word; word-break:break-all; margin-left: 0%;">
                <thead>
                    <th width="15%">Task Type</th>
                    <th width="20%">Target Language</th>
                    <th width="13%">Source Quantity</th>
                    <th width="13%">Pricing Quantity</th>
                    <th width="15%">Optional Task Name<br />(will default to Project)</th>
                    <th width="18%">Optional Task Work URL</th>
                    <th width="6%"></th>
                </thead>
                <tbody>
<script type="text/javascript">
// Errors
var quantity_error;
var source_quantity_error;
var language_error;

function set_all_errors_for_submission()
{
  set_errors_for_submission("placeholder_for_errors_1", "error-box-top");
  set_errors_for_submission("placeholder_for_errors_2", "error-box-btm");
}

function set_errors_for_submission(id, id_for_div)
{
  html = "";
  if (quantity_error != null || source_quantity_error != null || language_error != null) {
    html += '<div id="' + id_for_div + '" class="alert alert-error pull-left">';
      html += '<h3>Please correct the following errors:</h3>';
      html += '<ol>';
        if (quantity_error != null) {
          html += '<li>' + quantity_error + '</li>';
        }
        if (source_quantity_error != null) {
          html += '<li>' + source_quantity_error + '</li>';
        }
        if (language_error != null) {
          html += '<li>' + language_error + '</li>';
        }
      html += '</ol>';
    html += '</div>';
  }
  document.getElementById(id).innerHTML = html;
}

function validateForm()
{
  // Reset error variables, clearing any previously displayed errors.
  quantity_error = null;
  source_quantity_error = null;
  language_error = null;

  var fail = false;

  for (i=0; i<20; i++) {
    if (document.getElementById("task_type_" + i).value != "0") {
      if (document.getElementById("quantity_" + i).value == "") {
        quantity_error = "You must specify a Quantity if you specify a Task Type";
        fail = true;
      }
      if (document.getElementById("source_quantity_" + i).value == "") {
        source_quantity_error = "You must specify a Source Quantity if you specify a Task Type";
        fail = true;
      }
      var quantity = parseInt(document.getElementById("quantity_" + i).value);
      if (isNaN(quantity) || quantity <= 1) {
        quantity_error = "You must specify a valid (>1) integer Quantity";
        fail = true;
      }
      var source_quantity = parseInt(document.getElementById("source_quantity_" + i).value);
      if (isNaN(source_quantity) || source_quantity < 1) {
        source_quantity_error = "You must specify a valid (>0) integer Source Quantity";
        fail = true;
      }
      if (document.getElementById("target_language_" + i).value == "0") {
        language_error = "You must specify a Target Language if you specify a Task Type";
        fail = true;
      }
    }
  }

  if (fail) {
    set_all_errors_for_submission();
    return false;
  }
  return true;
}

var task_types = [0,
    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
        {$ui['type_enum']},
    {/foreach}
];
var units = ["",
    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
        "{$ui['pricing_and_recognition_unit_text']}",
    {/foreach}
];
var source_units = ["",
    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
        "{$ui['source_unit_for_later_stats']}",
    {/foreach}
];

function duplicate(count) {
    for (i=0; i<20; i++) {
        if (document.getElementById("task_type_" + i).value == "0") {
            document.getElementById("task_type_" + i).value       = document.getElementById("task_type_" + count).value;
            document.getElementById("quantity_" + i).value        = document.getElementById("quantity_" + count).value;
            document.getElementById("target_language_" + i).value = document.getElementById("target_language_" + count).value;
            document.getElementById("source_quantity_" + i).value = document.getElementById("source_quantity_" + count).value;
            document.getElementById("title_" + i).value           = document.getElementById("title_" + count).value;
            document.getElementById("comment_" + i).value         = document.getElementById("comment_" + count).value;
            var task_type = document.getElementById("task_type_" + i).value;
            document.getElementById("unit_" + i).innerHTML = units[task_type];
            document.getElementById("source_unit_" + i).innerHTML = source_units[task_type];
            break;
        }
    }
}
</script>
                    {for $count=0 to 19}
<script type="text/javascript">
function task_type_changed_{$count}() {
    var task_type = parseInt(document.getElementById("task_type_{$count}").value);
    document.getElementById("unit_{$count}").innerHTML = units[task_types.indexOf(task_type)];
    document.getElementById("source_unit_{$count}").innerHTML = source_units[task_types.indexOf(task_type)];
}
</script>
                        <tr>
                            <td>
                                <select name="task_type_{$count}" id="task_type_{$count}" onchange="task_type_changed_{$count}()">
                                    <option value="0"></option>
                                    {assign var="type_category_text" value="Terminology"}
                                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                                        {if $ui['shell_task'] && $task_type != 29}
                                            {if $ui['type_category_text'] != $type_category_text}
                                            <option value="0">=======================</option>
                                            {assign var="type_category_text" value=$ui['type_category_text']}
                                            {/if}
                                            <option value="{$task_type}">{$ui['type_text']}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <select name="target_language_{$count}" id="target_language_{$count}">
                                    <option value="0"></option>
                                    {foreach from=$languages key=codes item=language}
                                        <option value="{$codes}">{$language}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <input type='text' name="source_quantity_{$count}" id="source_quantity_{$count}" value="" /><br />
                                <div id="source_unit_{$count}"></div>
                            </td>
                            <td>
                                <input type='text' name="quantity_{$count}" id="quantity_{$count}" value="" /><br />
                                <div id="unit_{$count}"></div>
                            </td>
                            <td><input type='text' name="title_{$count}" id="title_{$count}" value="" /></td>
                            <td><input type='text' name="comment_{$count}" id="comment_{$count}" value="" /></td>
                            <td>
                                <button onclick="duplicate({$count}); return false;" title="Duplicate">&#8659;</button>
                            </td>
                        </tr>
                    {/for}
                </tbody>
            </table>

            <div id="placeholder_for_errors_2"></div>

            <div class="" style="text-align:center; width:100%">
                <div class="pull-left width-50">
                    <p style="margin-bottom:20px;"></p>
                    <a href="{urlFor name="project-view" options="project_id.$project_id"}" class="btn btn-danger">
                        <i class="icon-ban-circle icon-white"></i>
                        {Localisation::getTranslation('common_cancel')}
                    </a>
                    <p style="margin-bottom:20px;"></p>
                </div>
                <div class="pull-left width-50">
                    <p style="margin-bottom:20px;"></p>
                    <button type="submit" onclick="return validateForm();" class="btn btn-success" name="create_project_button" id="create_project_button">
                        <i class="icon-upload icon-white"></i> Add Shell Tasks to Project
                    </button>
                    <p style="margin-bottom:20px;"></p>
                </div>
            </div>
            <input type="hidden" name="sesskey" value="{$sesskey}" />
        </form>
    </div>

{include file="footer.tpl"}
