{include file="header.tpl"}

    <span class="hidden">

        <!-- Parameters... -->
        <div id="siteLocation">{$siteLocation}</div>
        <div id="siteAPI">{$siteAPI}</div>
        <div id="user_id">{$user_id}</div>
        <div id="userIsAdmin">{$isSiteAdmin}</div>

    </span>

    <div class="grid_8">
        <div class="page-header">
            <h1>
                Add Shell Tasks to a Project<br />
                <small>
                    {Localisation::getTranslation('common_denotes_a_required_field')}
                </small>
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
            <table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
                <thead>
                    <th width="25%">Task Type</th>
                    <th width="25%">Unit</th>
                    <th width="25%">Quantity</th>
                    <th width="25%">Target Language</th>
                </thead>
                <tbody>
<script type="text/javascript>
// Errors
var titleError;
var descriptionError;
var deadlineError;
var impactError;
var tagsError;
var referenceError;
var project_create_set_source_language;
var project_create_set_source_country;
var imageError;

function set_all_errors_for_submission()
{
  set_errors_for_submission("placeholder_for_errors_1", "error-box-top");
  set_errors_for_submission("placeholder_for_errors_2", "error-box-btm");
}

function set_errors_for_submission(id, id_for_div)
{
  html = "";
  if (titleError != null || descriptionError != null ||
    deadlineError != null || impactError != null ||
    project_create_set_source_language != null ||
    project_create_set_source_country != null ||
    tagsError != null || referenceError != null || imageError != null) {
    html += '<div id="' + id_for_div + '" class="alert alert-error pull-left">';
      html += '<h3>' + parameters.getTranslation('common_please_correct_errors') + ':</h3>';
      html += '<ol>';
        if (titleError != null) {
          html += '<li>' + titleError + '</li>';
        }
        if (descriptionError != null) {
          html += '<li>' + descriptionError + '</li>';
        }
        if (deadlineError != null) {
          html += '<li>' + deadlineError + '</li>';
        }
        if (tagsError != null) {
          html += '<li>' + tagsError + '</li>';
        }
        if (impactError != null) {
          html += '<li>' + impactError + '</li>';
        }
        if (referenceError != null) {
          html += '<li>' + referenceError + '</li>';
        }
        if (project_create_set_source_language != null) {
          html += '<li>' + project_create_set_source_language + '</li>';
        }
        if (project_create_set_source_country != null) {
          html += '<li>' + project_create_set_source_country + '</li>';
        }
        if (imageError != null) {
          html += '<li>' + imageError + '</li>';
        }
      html += '</ol>';
    html += '</div>';
  }
  document.getElementById(id).innerHTML = html;
}

function validateForm()
{
  // Reset error variables, clearing any previously displayed errors.
  titleError = null;
  descriptionError = null;
  deadlineError = null;
  impactError = null;
  tagsError = null;
  referenceError = null;
  project_create_set_source_language = null;
  project_create_set_source_country = null;
  imageError = null;

  // Snapshot of Form Values when Submit clicked
  title          = document.getElementById("project_title").value
  description    = document.getElementById("project_description").value
  impact         = document.getElementById("project_impact").value
  reference      = document.getElementById("project_reference").value;
  tagList        = document.getElementById("tagList").value;
  selectedMonth  = document.getElementById("selectedMonth").value;
  selectedYear   = document.getElementById("selectedYear").value;
  selectedDay    = document.getElementById("selectedDay").value;
  selectedHour   = document.getElementById("selectedHour").value;
  selectedMinute = document.getElementById("selectedMinute").value;
  // trackProject   = document.getElementById("trackProject").checked;
  // publish        = document.getElementById("publish").checked;

  project.organisationId = org_id;
  project.title = title;
  project.description = description;
  project.impact = impact;
  project.reference = reference;
  project.createdTime = "";
  project.status = "";
  project.imageUploaded = false;
  project.imageApproved = false;

  var sourceLocale = new Object();
  sourceLocale.languageName = $("#sourceLanguageSelect option:selected").text();
  sourceLocale.languageCode = document.getElementById("sourceLanguageSelect").value;
  project.sourceLocale = sourceLocale;

  project.tag = [];
  if (tagList.length > 0) {
    var tagListParsed = parseTagsInput(tagList);
    if (tagListParsed.length > 0) {
      project.tag = tagListParsed;
    }
  }

  projectImageFile = null;
  projectImageFileData = null;

  if (!validateLocalValues() || !validateImageFileInput()) {
    set_all_errors_for_submission();
    return false;
  }

  return true;
}

var units = ["",
    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
        "{$ui['unit_measurement']}",
    {/foreach}
];
</script>
                    {for $count=0 to 19}
<script type="text/javascript>
function task_type_changed_{$count}() {
    var task_type = document.getElementById("task_type_{$count}").value;
    document.getElementById(unit_{$count}).innerHTML = units[task_type];
}
</script>
                        <tr>
                            <td>
                                <select name="task_type_{$count}" id="task_type_{$count}" onchange="task_type_changed_{$count}">
                                    <option value="0"></option>
                                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                                        {if $ui['shell_task']}
                                            <option value="{$task_type}">{$ui['type_text']}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </td>
                            <td id="unit_{$count}"></td>
                            <td><input type='text' name="quantity_{$count}" id="quantity_{$count}" value="" /></td>
                            <td>
                                <select name="target_language_{$count}" id="target_language_{$count}">
                                    <option value="0"></option>
                                    {foreach from=$languages key=codes item=language}
                                        <option value="{$codes}">{$language}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                    {/for}
                </tbody>
            </table>

            <div id="placeholder_for_errors_2"></div>

            <div class="" style="text-align:center; width:100%">
                <div class="pull-left width-50">
                    <p style="margin-bottom:20px;"></p>
                    <a href="{$siteLocation}org/dashboard" class="btn btn-danger">
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
