var PENDING_CLAIM  = 2;
var SEGMENTATION   = 1;
var TRANSLATION    = 2;
var PROOFREADING   = 3;
var DESEGMENTATION = 4;

// Globals...

// instance of Parameters Class holding data retrieved from Server (e.g. Translations)
var parameters;

// Passed from PHP
var siteLocation;
var maxfilesize;
var imageMaxFileSize;
var supportedImageFormats;
var org_id;
var user_id;

// Errors
var createProjectError;
var titleError;
var descriptionError;
var wordCountError;
var deadlineError;
var impactError;
var tagsError;
var referenceError;
var taskError;
var duplicateLocale;
var fileError;
var imageError;

// Target Languages
var targetCount = 0;
var maxTargetLanguages = 10;

// Snapshot of Form Values when Submit clicked
var title;
var description;
var impact;
var reference;
var tagList;
var selectedMonth;
var selectedYear;
var selectedDay;
var selectedHour;
var selectedMinute;
var wordCountInput;
var trackProject;
var publish;

var project = new Object();

var projectFile;
var projectFileName;
var projectFileData;

var projectImageFile;
var projectImageFileName;
var projectImageFileData;

var segmentationRequired;
var translationRequired;
var proofreadingRequired;

var targetLanguageCode;
var targetLanguageLanguage;
var targetCountryCode;
var targetCountryCountry;

$(document).ready(documentReady);


function getSetting(text)
{
  return document.getElementById(text).innerHTML;
}

function set_all_errors_for_submission()
{
  set_errors_for_submission("placeholder_for_errors_1", "error-box-top");
  set_errors_for_submission("placeholder_for_errors_2", "error-box-btm");
}

function set_errors_for_submission(id, id_for_div)
{
  html = "";
  if (titleError != null || descriptionError != null || wordCountError != null ||
    deadlineError != null || impactError != null || createProjectError != null ||
    tagsError != null || referenceError != null || taskError != null || duplicateLocale != null || fileError != null || imageError != null) {
    html += '<div id="' + id_for_div + '" class="alert alert-error pull-left">';
      html += '<h3>' + parameters.getTranslation('common_please_correct_errors') + ':</h3>';
      html += '<ol>';
        if (titleError != null) {
          html += '<li>' + titleError + '</li>';
        }
        if (descriptionError != null) {
          html += '<li>' + descriptionError + '</li>';
        }
        if (createProjectError != null) {
          html += '<li>' + createProjectError + '</li>';
        }
        if (wordCountError != null) {
          html += '<li>' + wordCountError + '</li>';
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
        if (taskError != null) {
          html += '<li>' + taskError + '</li>';
        }
        if (duplicateLocale != null) {
          html += '<li>' + duplicateLocale + '</li>';
        }
        if (fileError != null) {
          html += '<li>' + fileError + '</li>';
        }
        if (imageError != null) {
          html += '<li>' + imageError + '</li>';
        }
      html += '</ol>';
    html += '</div>';
  }
  document.getElementById(id).innerHTML = html;
}

/**
 * Called by the DOM when the Document is Ready.
 */
function documentReady()
{
  siteLocation     = getSetting("siteLocation");
  maxFileSize      = document.getElementById("maxfilesize").innerHTML;
  imageMaxFileSize = parseInt(getSetting("imageMaxFileSize")) * 1024 * 1024;
  org_id           = document.getElementById("org_id").innerHTML;
  user_id          = document.getElementById("user_id").innerHTML;
  supportedImageFormats = getSetting("supportedImageFormats").toString().split(","); // Image format string is comma separated, split it into a list

  // Set the options for the day in month select field based on month/year
  selectedMonthChanged();

  parameters = new Parameters(loadingComplete);
}

function loadingComplete()
{
  document.getElementById("source_text_desc").innerHTML =
    parameters.getTranslation("project_create_6") + " " + parameters.getTranslation("common_maximum_file_size_is").replace("%s", imageMaxFileSize / 1024 / 1024);

  document.getElementById("image_file_desc").innerHTML =
    parameters.getTranslation("project_create_upload_project_image") + " " + parameters.getTranslation("common_maximum_file_size_is").replace("%s", imageMaxFileSize / 1024 / 1024);

  addMoreTargetLanguages();

  document.getElementById("loading_warning").innerHTML = "";
}

/**
 * Add target languages to the form.
 */
function addMoreTargetLanguages()
{
  // Unless the targetCount is less than the maxTargetLanguages, don't do anything.
  // On the UI this shouldn't be an issue anyway because this function will disable the add button when
  // adding a language pushes the targetCount to the max, so this is an extra safeguard.
  if (targetCount < maxTargetLanguages) {
    // Prepare the div elements that will make up the new target language section.
    var targetLanguageRow = document.createElement("div"); // The main div, subdivided into the language/country
    targetLanguageRow.id = "target_row_" + targetCount;    // selects and task type checkboxes
    targetLanguageRow.className = "target-row bottom-line-border";

    var targetLanguageCell = document.createElement("div"); // Sub-div for select elements
    targetLanguageCell.className = "pull-left width-50";

    // Create the select elements
    var targetLanguageSelect = document.createElement("select");
    targetLanguageSelect.style.width = "400px";
    targetLanguageSelect.name = "target_language_" + targetCount;
    targetLanguageSelect.id   = "target_language_" + targetCount;
    targetLanguageSelect.innerHTML = document.getElementById("template_language_options").innerHTML;

    var targetCountrySelect = document.createElement("select");
    targetCountrySelect.style.width = "400px";
    targetCountrySelect.name = "target_country_" + targetCount;
    targetCountrySelect.id   = "target_country_" + targetCount;
    targetCountrySelect.innerHTML = document.getElementById("template_country_options").innerHTML;

    var taskTypesRow = document.createElement("div"); // Sub-div for task type checkboxes, holds individual divs for each checkox
    taskTypesRow.id = "task-type-checkboxes";
    taskTypesRow.className = "pull-left width-50";

    var segmentationRequiredDiv = document.createElement("div");
    segmentationRequiredDiv.className = "pull-left proj-task-type-checkbox";

    var segmentationCheckbox = document.createElement("checkbox");
    segmentationCheckbox.title = parameters.getTranslation("project_create_10");
    segmentationCheckbox.name = "segmentation_" + targetCount;
    segmentationCheckbox.id   = "segmentation_" + targetCount;
    segmentationCheckbox.onclick = "segmentationClicked(this)";

    var translationRequiredDiv = document.createElement("div");
    translationRequiredDiv.className = "pull-left proj-task-type-checkbox";

    var translationCheckbox = document.createElement("checkbox");
    translationCheckbox.title = parameters.getTranslation("common_create_a_translation_task_for_volunteer_translators_to_pick_up");
    translationCheckbox.name = "translation_" + targetCount;
    translationCheckbox.id   = "translation_" + targetCount;
    translationCheckbox.checked = true;

    var proofreadingRequiredDiv = document.createElement("div");
    proofreadingRequiredDiv.className = "pull-left proj-task-type-checkbox";

    var proofreadingCheckbox = document.createElement("checkbox");
    proofreadingCheckbox.title = parameters.getTranslation("common_create_a_proofreading_task_for_evaluating_the_translation_provided_by_a_volunteer");
    proofreadingCheckbox.name = "proofreading_" + targetCount;
    proofreadingCheckbox.id = "proofreading_" + targetCount;
    proofreadingCheckbox.checked = true;

    // Put the Select Elements into their div
    targetLanguageCell.appendChild(targetLanguageSelect);
    targetLanguageCell.appendChild(targetCountrySelect);

    // Put the Select Elements' div into the main div
    targetLanguageRow.appendChild(targetLanguageCell);

    // Put the checkbox Input Elements into their own divs
    segmentationRequiredDiv.appendChild(segmentationCheckbox);
    translationRequiredDiv.appendChild(translationCheckbox);
    proofreadingRequiredDiv.appendChild(proofreadingCheckbox);

    // Put each checkbox div into the div that is to contain them all
    taskTypesRow.appendChild(segmentationRequiredDiv);
    taskTypesRow.appendChild(translationRequiredDiv);
    taskTypesRow.appendChild(proofreadingRequiredDiv);

    // Put the div encompassing the three checkboxes into the main div
    targetLanguageRow.appendChild(taskTypesRow);

    // Add the completed target language "row" to the div containing all previously added target language "rows."
    document.getElementById("targetLangSelectDiv").appendChild(targetLanguageRow);

    targetCount++;
    if (targetCount == 5) {
      window.alert(parameters.getTranslation("project_create_target_language_increase"));
    }

    // If maximum amount of target languages has been reached, display message to notify user.
    if (targetCount >= maxTargetLanguages) {
      var addBtn = document.getElementById("addTargetLanguageBtn");
      addBtn.disabled = true;

      document.getElementById("placeholder_for_maxTargetsReached").innerHTML = '<div class="alert alert-info" style="text-align: center">' + parameters.getTranslation("project_create_11") + '</div>';
    } else {
      document.getElementById("placeholder_for_maxTargetsReached").innerHTML = "";
    }

    var removeButton = document.getElementById("removeBottomTargetBtn");
    if (removeButton != null) {
      // Disable the remove button if there is only 1 target language on the form.
      if (targetCount > 1) {
        removeButton.disabled = false;
      } else {
        removeButton.disabled = true;
      }
    }
  }
}

/**
 * Remove target languages
 */
function removeTargetLanguage()
{
  if (targetCount > 1) {
    targetCount--;
    var targetLanguageRow = document.getElementById("target_row_" + targetCount);
    targetLanguageRow.parentNode.removeChild(targetLanguageRow);

    document.getElementById("placeholder_for_maxTargetsReached").innerHTML = "";
    if (targetCount == 1) {
      var removeButton = document.getElementById("removeBottomTargetBtn");
      removeButton.disabled = true;
    }
    var addBtn = document.getElementById("addTargetLanguageBtn");
    if (addBtn.disabled == true) {
      addBtn.disabled = false;
    }
  }
}

/**
 * Called when "Submit form" button is clicked, triggering validation of all the input.
 */
function validateForm()
{
  // Reset error variables, clearing any previously displayed errors.
  createProjectError = null;
  titleError = null;
  descriptionError = null;
  wordCountError = null;
  deadlineError = null;
  impactError = null;
  tagsError = null;
  referenceError = null;
  taskError = null;
  duplicateLocale = null;
  fileError = null;
  imageError = null;

  // Snapshot of Form Values when Submit clicked
  title          = document.getElementById("project_title").innerHTML
  description    = document.getElementById("project_description").innerHTML
  impact         = document.getElementById("project_impact").innerHTML
  reference      = document.getElementById("project_reference").value;
  tagList        = document.getElementById("tagList").value;
  selectedMonth  = document.getElementById("selectedMonth").value;
  selectedYear   = document.getElementById("selectedYear").value;
  selectedDay    = document.getElementById("selectedDay").value;
  selectedHour   = document.getElementById("selectedHour").value;
  selectedMinute = document.getElementById("selectedMinute").value;
  wordCountInput = document.getElementById("wordCountInput").value;
  trackProject   = document.getElementById("trackProject").value;
  publish        = document.getElementById("publish").value;

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
  sourceLocale.countryName  = $("#sourceCountrySelect option:selected").text();
  sourceLocale.countryCode  = document.getElementById("sourceCountrySelect").value;
  project.sourceLocale = sourceLocale;

  project.tag = [];
  if (tagList.length > 0) {
    var tagListParsed = parseTagsInput(tagList);
  }
  if (tagListParsed.length > 0) {
    project.tag = tagListParsed;
  }

  projectImageFile = null;
  projectImageFileData = null;

  if (!validateLocalValues() || !validateFileInput() || !validateImageFileInput()) {
    set_all_errors_for_submission();
    return false;
  }

  return true;
}

/**
 * Validate the form input and sets various error messages if needed fields are not set or
 * invalid data is given. All these are local tests (no server access).
 */
function validateLocalValues()
{
  var success = true;

  if (title == '') {
    titleError = parameters.getTranslation("project_create_error_title_not_set");
    success = false;
  } else if (title.length > 110) {
    titleError = parameters.getTranslation("project_create_error_title_too_long");
    success = false;
  } else if (title.match(new RegExp('^\\d+$'))) {
    // Is the project title simply a number? Don't allow this, thus avoiding Slim route mismatch,
    // calling route for getProject when it should be getProjectByName
    titleError = parameters.getTranslation("project_create_title_cannot_be_number");
    success = false;
  }
  if (description == '') {
    descriptionError = parameters.getTranslation("project_create_33");
    success = false;
  } else if (description.length > 4096) {
    descriptionError = parameters.getTranslation("project_create_error_description_too_long");
    success = false;
  }
  if (impact == '') {
    impactError = parameters.getTranslation("project_create_26");
    success = false;
  } else if (impact.length > 4096) {
    impactError = parameters.getTranslation("project_create_error_impact_too_long");
    success = false;
  }
  if (reference != null && reference != '') {
    if (reference.length > 128) {
      referenceError = parameters.getTranslation("project_create_error_reference_too_long");
      success = false;
    } else if (validateReferenceURL(reference)) {
      // String did not match pattern, it is not a URL
      referenceError = parameters.getTranslation("project_create_error_reference_invalid");
      success = false;
    }
  }

  if (wordCountInput != null && wordCountInput != '') {
    // If word count is set, ensure it is a valid natural number
    var q = parseInt(wordCountInput);
    if (isNaN(q)) {
      wordCountError = parameters.getTranslation("project_create_27");
      success = false;
    }
    project.wordCount = q;

    // If word count is greater than 5000, and segmentation is not selected, display warning message to user.
    if (project.wordCount > 5000) {
      var i = 0;
      var segmentationMissing = false;
      while (i < targetCount && !segmentationMissing) {
        var segmentationCheckbox = document.getElementById("segmentation_" + i);
        if (!segmentationCheckbox.checked) {
          segmentationMissing = true;
        }
        i++;
      }
      if (segmentationMissing && !window.confirm(parameters.getTranslation("project_create_22"))) {
        success = false;
      }
    }
  } else {
    // Word count is not set
    wordCountError = parameters.getTranslation("project_create_27");
    success = false;
  }

  if (!validateTagList(tagList)) {
    // Invalid tags detected, set error message
    tagsError = parameters.getTranslation('project_create_invalid_tags');
    success = false;
  } else {
    var list = tagList.split(" ");
    for (var i = 0; i < list.length; i++) {
      if (list[i].length > 50) {
        // One of the tags is too long, set error message
        tagsError = parameters.getTranslation("project_create_error_tags_too_long");
        success = false;
        break;
      }
    }
  }

  // Parse project deadline info
  var projectDeadline = new Date(selectedYear, selectedMonth - 1, selectedDay, selectedHour, selectedMinute);
  if (projectDeadline != null) {
    if (projectDeadline > (new Date())) {
      var m = projectDeadline.getUTCMonth() + 1;
      if (m < 10) {
        m = "0" + m;
      }
      var d = projectDeadline.getUTCDate();
      if (d < 10) {
        d = "0" + d;
      }
      var h = projectDeadline.getUTCHours();
      if (h < 10) {
        h = "0" + h;
      }
      var mi = projectDeadline.getUTCMinutes();
      if (mi < 10) {
        mi = "0" + mi;
      }
      project.deadline = projectDeadline.getUTCFullYear() + "-" + m + "-" + d + " " + h + ":" + mi + ":00";
      document.getElementById("project_deadline").value = project.deadline;
    } else {
      // Deadline is not a date in the future, set error message
      deadlineError = parameters.getTranslation("project_create_25");
      success = false;
    }
  } else {
    // Deadline is not set (can this even happen in current code?)
    deadlineError = parameters.getTranslation("project_create_32");
    success = false;
  }

  var encounteredLocales = [];
  segmentationRequired   = [];
  translationRequired    = [];
  proofreadingRequired   = [];
  targetLanguageCode     = [];
  targetLanguageLanguage = [];
  targetCountryCode      = [];
  targetCountryCountry   = [];
  for (var i = 0; i < targetCount; i++) {
    segmentationRequired[i] = document.getElementById("segmentation_" + i).checked;
    translationRequired [i] = document.getElementById("translation_" + i).checked;
    proofreadingRequired[i] = document.getElementById("proofreading_" + i).checked;

    // If no task type is set, display error message
    if (!segmentationRequired[i] && !translationRequired[i] && !proofreadingRequired[i]) {
      taskError = parameters.getTranslation("project_create_29");
      success = false;
    }

    targetLanguageCode    [i] = document.getElementById("target_language_" + i).value;
    targetLanguageLanguage[i] = $("#target_language_" + i + " option:selected").text();
    targetCountryCode     [i] = document.getElementById("target_country_" + i).value;
    targetCountryCountry  [i] = $("#target_country_" + i + " option:selected").text();

    // If a duplicate locale is encountered, display error message
    var encounteredLocale = targetLanguageCode[i] + "_" + targetCountryCode[i];
    if ($.inArray(encounteredLocale, encounteredLocales) >= 0) {
      duplicateLocale = parameters.getTranslation("project_create_28");
      success = false;
    } else {
      encounteredLocales[i] = encounteredLocale;
    }
  }

  return success;
}

/**
 * Validate the details of the project file provided.
 */
function validateFileInput()
{
  var projectFileField = document.getElementById("projectFile");
  var files = projectFileField.files;

  // Ensure projectFileField is not null
  if (projectFileField != null && files.length > 0) {
    projectFile = files[0];
    // Check if file is empty
    if (projectFile.size > 0) {
      // Check that file does not exceed the maximum allowed file size
      if (projectFile.size <= maxFileSize) {
        var extensionStartIndex = projectFile.name.lastIndexOf(".");
        // Check that file has an extension
        if (extensionStartIndex > 0) {
          projectFileName = projectFile.name;
          var extension = projectFileName.substring(extensionStartIndex + 1);
          if (extension != extension.toLowerCase()) {
            extension = extension.toLowerCase();
            projectFileName = projectFileName.substring(0, extensionStartIndex + 1) + extension;
            window.alert(parameters.getTranslation("project_create_18"));
          }

          if (extension == "pdf") {
            // If file is a pdf, warn user that PDFs are difficult to work with
            if (!window.confirm(parameters.getTranslation("project_create_19"))) {
              return false;
            }
          }

          return true;
        } else {
          // File has no extension
          fileError = parameters.getTranslation("project_create_20");
          return false;
        }
      } else {
        // File is too big
        fileError = parameters.getTranslation("project_create_21");
        return false;
      }
    } else {
      // File is empty
      fileError = parameters.getTranslation("project_create_17");
      return false;
    }
  } else {
    // No file provided
    fileError = parameters.getTranslation("project_create_16");
    return false;
  }
}

/**
 * Validate the project image file provided.
 */
function validateImageFileInput()
{
  var projectImageFileField = document.getElementById("projectImageFile");
  var files = projectImageFileField.files;

  // Ensure projectImageFileField is not null
  if (projectImageFileField != null && files.length > 0) {
    projectImageFile = files[0];
    // Check if file is empty
    if (projectImageFile.size > 0) {
      // Check that file does not exceed the maximum allowed file size
      if (projectImageFile.size <= imageMaxFileSize) {
        var extensionStartIndex = projectImageFile.name.lastIndexOf(".");
        // Check that file has an extension
        if (extensionStartIndex > 0) {
          projectImageFileName = projectImageFile.name;
          var extension = projectImageFileName.substring(extensionStartIndex + 1);
          if (extension != extension.toLowerCase()) {
            extension = extension.toLowerCase();
            projectImageFileName = projectImageFileName.substring(0, extensionStartIndex + 1) + extension;
            window.alert(parameters.getTranslation("project_create_18"));
          }

          // Check that the file extension is valid for an image
          if ($.inArray(extension, supportedImageFormats) == -1) {
            imageError = parameters.getTranslation("project_create_please_upload_valid_image_file") + "extension";
            return false;
          }

          return true;
        } else {
          // File has no extension
          imageError = parameters.getTranslation("project_create_image_has_no_extension");
          return false;
        }
      } else {
        // File is too big
        imageError = parameters.getTranslation("project_create_image_is_too_big");
        return false;
      }
    } else {
      // File is empty
      imageError = parameters.getTranslation("project_create_image_file_empty");
      return false;
    }
  } else {
    // No file provided
    return true;
  }
}

/**
 * Called when the segmentation checkbox is clicked for a target language, disabling the
 * translation and proofreading checkboxes.
 */
function segmentationClicked(target)
{
  var index = parseInt(target.id.substring(target.id.indexOf("_") + 1));

  var transCheckbox = document.getElementById("translation_" + index);
  var proofCheckbox = document.getElementById("proofreading_" + index);
  if (target.checked) {
    transCheckbox.checked = false;
    transCheckbox.disabled = true;
    proofCheckbox.checked = false;
    proofCheckbox.disabled = true;
  } else {
    transCheckbox.disabled = false;
    proofCheckbox.disabled = false;
  }
}

/**
 * Called when the year is changed by the user.
 */
function selectedYearChanged()
{
  var month = document.getElementById("selectedMonth").value;
  var year = document.getElementById("selectedYear").value;

  if (month == 2) {
    // in case leap year status changed
    set_options_for_days_in_month(month, year);
  }
}

/**
 * Called when the month is changed by the user.
 */
function selectedMonthChanged()
{
  var month = document.getElementById("selectedMonth").value;
  var year = document.getElementById("selectedYear").value;
  set_options_for_days_in_month(month, year);
}

/**
 * Set the options for the day select to match the number of days in the month.
 */
function set_options_for_days_in_month(month, year)
{
  var monthLengths = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
  if (((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)) {
    monthLengths[2] = 29;
  }

  var options_list = '';
  for (var i = 0; i < monthLengths[month]; i++) {
    options_list += '<option value="' + i.toString() + '">' + i.toString() + '</option>';
  }

  document.getElementById("selectedDay").innerHTML = options_list;
}

/**
 * Validates user input of text for [Tag]s to catch disallowed characters.
 */
function validateTagList(tagList)
{
  var r = new RegExp('[^a-z0-9\\-\\s]');
  return tagList.match(r);
}

/**
 * Parse the project tags from the text input.
 */
function parseTagsInput(tags)
{
  var labels = tags.trim().split(" ");
  var tagArray = [];
  var j = 0;
  for (var i = 0; i < labels.length; i++) {
    if (labels[i].length > 0) {
      tagArray[j++] = {id : null, label: labels[i]};
    }
  }
  return tagArray;
}

/**
 * Uses a regular expression to validate the the reference URL for a project (if one is provided) actually is a URL.
 * Credit to http://stackoverflow.com/a/24058129/1799985
 *
 * Returns true if the provided URL is valid, false otherwise.
 */
function validateReferenceURL(reference)
{
  var r = new RegExp(
    '^(([\\w]+:)?\\/\\/)?(([\\d\\w]|%[a-fA-F\\d]{2,2})+(:([\\d\\w]|%[a-fA-F\\d]{2,2})+)?@)?([\\d\\w][-\\d\\w]{0,' +
    '253}[\\d\\w]\\.)+[\\w]{2,13}(:[\\d]+)?(\\/([-+_~.\\d\\w]|%[a-fA-f\\d]{2,2})*)*(\\?(&?([-+_~.\\d\\w]|%[a-' +
    'fA-f\\d]{2,2})=?)*)?(#([-+_~.\\d\\w]|%[a-fA-f\\d]{2,2})*)?$');
  return reference.match(r);
}
