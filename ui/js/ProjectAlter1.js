// Globals...

var parameters; // Instance of Parameters Class holding data retrieved from Server (e.g. Translations)

// Passed from PHP
var siteLocation;
var siteAPI;
var maxFileSize;
var imageMaxFileSize;
var supportedImageFormats;
var project_id;
var org_id;
var user_id;
var deadline_timestamp;
var initial_selected_day;
var initial_title;
var userIsAdmin;

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

var projectFile;
var projectFileName;
var projectFileData;

var projectImageFile;
var projectImageFileName;
var projectImageFileData;

var duplicateProjectTitle = null;

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
  siteAPI          = getSetting("siteAPI");
  maxFileSize      = document.getElementById("maxfilesize").innerHTML;
  imageMaxFileSize = parseInt(getSetting("imageMaxFileSize")) * 1024 * 1024;
  project_id       = document.getElementById("project_id").innerHTML;
  org_id           = document.getElementById("org_id").innerHTML;
  user_id          = document.getElementById("user_id").innerHTML;
  initial_title    = document.getElementById("project_title").value;
  userIsAdmin      = document.getElementById("userIsAdmin").innerHTML;
  if (userIsAdmin == 1) {
    userIsAdmin = true;
  } else {
    userIsAdmin = false; // A "0" will not be false!
  }

  supportedImageFormats = getSetting("supportedImageFormats").toString().split(","); // Image format string is comma separated, split it into a list

  deadline_timestamp = new Date(document.getElementById("deadline_timestamp").innerHTML * 1000);
  document.getElementById("selectedYear").value   = deadline_timestamp.getFullYear();
  document.getElementById("selectedMonth").value  = deadline_timestamp.getMonth() + 1;
  document.getElementById("selectedDay").value    = deadline_timestamp.getDate();
  document.getElementById("selectedHour").value   = deadline_timestamp.getHours();
  document.getElementById("selectedMinute").value = deadline_timestamp.getMinutes();

  // Set the options for the day in month select field based on month/year
  initial_selected_day = deadline_timestamp.getDate();
  selectedMonthChanged();
  initial_selected_day = null; // If user changes date after this, do not use the initial value

  parameters = new Parameters(loadingComplete);
}

function loadingComplete()
{
  document.getElementById("image_file_desc").innerHTML =
    parameters.getTranslation("common_maximum_file_size_is").replace("%s", imageMaxFileSize / 1024 / 1024);

  document.getElementById("loading_warning").innerHTML = "";
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
  title          = document.getElementById("project_title").value;
  description    = document.getElementById("project_description").value;
  impact         = document.getElementById("project_impact").value;
  reference      = document.getElementById("project_reference").value;
  tagList        = document.getElementById("tagList").value;
  selectedMonth  = document.getElementById("selectedMonth").value;
  selectedYear   = document.getElementById("selectedYear").value;
  selectedDay    = document.getElementById("selectedDay").value;
  selectedHour   = document.getElementById("selectedHour").value;
  selectedMinute = document.getElementById("selectedMinute").value;
  wordCountInput = document.getElementById("wordCountInput").value;

  projectImageFile = null;
  projectImageFileData = null;

  if (!validateLocalValues() || !validateImageFileInput()) {
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
  } else if (title == duplicateProjectTitle) {
    titleError = parameters.getTranslation("project_create_title_conflict");
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
    } else if (!validateReferenceURL(reference)) {
      // String did not match pattern, it is not a URL
      referenceError = parameters.getTranslation("project_create_error_reference_invalid");
      success = false;
    }
  }

  //if (wordCountInput != null && wordCountInput != '') {
  //  // If word count is set, ensure it is a valid natural number
  //  var newWordCount = parseInt(wordCountInput);
  //  if (isNaN(newWordCount)) {
  //    wordCountError = parameters.getTranslation("project_create_27");
  //    success = false;
  //  } else {
  //    // Only call API for word count iff parse error didn't occur
  //    if (newWordCount != 0 && userIsAdmin) {
  //      DAOupdateProjectWordCount(project_id, newWordCount, successWordCount, inconsistentWordCount, errorUpdatingWordCount);
  //    }
  //  }
  //} else {
  //  // Word count is not set
  //  wordCountError = parameters.getTranslation("project_create_27");
  //  success = false;
  //}

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
      document.getElementById("project_deadline").value = projectDeadline.getUTCFullYear() + "-" + m + "-" + d + " " + h + ":" + mi + ":00";
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

  return success;
}

/**
 * Update Project & Task Word Counts
 */
function updatewordCount()
{
  wordCountInput = document.getElementById("wordCountInput").value;

  if (wordCountInput == null || wordCountInput == '' || !userIsAdmin) {
    window.alert(parameters.getTranslation("project_create_27"));
    return;
  }

  // If word count is set, ensure it is a valid natural number
  var newWordCount = parseInt(wordCountInput);
  if (isNaN(newWordCount) || newWordCount == 0) {
    window.alert(parameters.getTranslation("project_create_27"));
    return;
  }

  DAOupdateProjectWordCount(project_id, newWordCount, successWordCount, inconsistentWordCount, errorUpdatingWordCount);
}

function successWordCount()
{
  window.alert(parameters.getTranslation("common_success"));
}

function inconsistentWordCount()
{
  window.alert(parameters.getTranslation("project_alter_word_count_error_1"));
}

function errorUpdatingWordCount()
{
  window.alert(parameters.getTranslation("project_alter_word_count_error_2"));
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
            imageError = parameters.getTranslation("project_create_please_upload_valid_image_file").replace("%s", extension);
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
  for (var i = 1; i <= monthLengths[month]; i++) {
    selectedString = "";
    if (initial_selected_day != null && i == initial_selected_day) selectedString = ' selected="selected"'
    options_list += '<option value="' + i.toString() + '"' + selectedString + '>' + i.toString() + '</option>';
  }

  document.getElementById("selectedDay").innerHTML = options_list;
}

/**
 * Validates user input of text for [Tag]s to catch disallowed characters.
 */
function validateTagList(tagList)
{
  var r = new RegExp('[^a-z0-9\\-\\s]');
  return !tagList.match(r);
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

function checkTitleNotUsed()
{
  if (userHash != null && document.getElementById("project_title").value != "" && document.getElementById("project_title").value != initial_title) { // Make sure API call will succeed and the field is not empty
    // DAOcheckProjectByNameNotExist(document.getElementById("project_title").value, noTitleConflict, titleConflict, errorFromServer);
    DAOcheckProjectByNameAndOrganisationNotExist(document.getElementById("project_title").value, org_id, noTitleConflict, titleConflict, errorFromServer);
  }
}

function noTitleConflict()
{
}

function titleConflict()
{
  duplicateProjectTitle = document.getElementById("project_title").value;
  window.alert(parameters.getTranslation("project_create_title_conflict"));
}

function errorFromServer(jqXHR, textStatus, errorThrown)
{
  // If the project is not found, we get to here, which is OK
  // (we do not seem to get a normal error response functionExist/noTitleConflict although the response to the POST is 200 OK!)
  // console.log("Error: getProjectByName Failed, returned " + jqXHR.status + " " + jqXHR.statusText);
}

/**
 * Calls the API to verify a project with the given title does not exist.
 *
 * handler functionNotExist called if it does not exist.
 * handler functionExist called if it does exist (this is the unexpected case).
 * handler functionOnFail called on failure of the API call.
 */
function DAOcheckProjectByNameNotExist(title, functionNotExist, functionExist, functionOnFail)
{
  $.ajax(
    {
      url: siteAPI + "v0/projects/getProjectByName",
      method: "POST",
      headers: {
        "Authorization": "Bearer " + userHash
      },
      contentType: 'text/plain; charset=UTF-8',
      processData: false,
      data: title
    }
  )
  .done(function (data, textStatus, jqXHR)
      {
        if (jqXHR.status < 400) {
          if (jqXHR.responseText == "") {
            functionNotExist(); // Expected Response (project not expected to exist)
          } else {
            functionExist(); // Unexpected Response
          }
        } else {
          functionOnFail();
          console.log("Error: getProjectByName returned " + jqXHR.status + " " + jqXHR.statusText);
        }
      }
    )
  .fail(functionOnFail);
}

/**
 * Calls the API to verify a project with the given title and organisation does not exist.
 *
 * handler functionNotExist called if it does not exist.
 * handler functionExist called if it does exist (this is the unexpected case).
 * handler functionOnFail called on failure of the API call.
 */
function DAOcheckProjectByNameAndOrganisationNotExist(title, orgId, functionNotExist, functionExist, functionOnFail)
{
  $.ajax(
    {
      url: siteAPI + "v0/projects/getProjectByNameAndOrganisation/" + title + "/organisation/" + orgId,
      method: "GET",
      headers: {
        "Authorization": "Bearer " + userHash
      }
    }
  )
  .done(function (data, textStatus, jqXHR)
      {
        if (jqXHR.status < 400) {
          if (jqXHR.responseText == "") {
            functionNotExist(); // Expected Response (project not expected to exist)
          } else {
            functionExist(); // Unexpected Response
          }
        } else {
          functionOnFail();
          console.log("Error: getProjectByName returned " + jqXHR.status + " " + jqXHR.statusText);
        }
      }
    )
  .fail(functionOnFail);
}

function deleteImage()
{
  if (window.confirm(parameters.getTranslation("project_alter_confirm_delete_image"))) {
    DAOdeleteProjectImage(project_id, org_id, deleteProjectImageSucceeded, deleteProjectImageFailed);
  }
}

function deleteProjectImageSucceeded()
{
  window.alert(parameters.getTranslation("project_alter_image_successfully_deleted"));
  document.getElementById("proj-image-display").style.display = "none";
}

function deleteProjectImageFailed(jqXHR, textStatus, errorThrown)
{
  console.log("Error: deleteProjectImage Failed");
}

/**
 * Calls the API to delete a project image.
 *
 * handler functionOnSuccess called on success.
 * handler functionOnFail called on failure of the API call.
 */
function DAOdeleteProjectImage(projectId, orgId, functionOnSuccess, functionOnFail)
{
  $.ajax(
    {
      url: siteAPI + "v0/io/projectImage/" + orgId + "/" + projectId,
      method: "DELETE",
      headers: {
        "Authorization": "Bearer " + userHash
      },
      dataType: "text"
    }
  )
  .done(function (data, textStatus, jqXHR)
      {
        if (jqXHR.status < 400) {
          if (jqXHR.status == 200) {
            functionOnSuccess();
          } else {
            functionOnFail();
          }
        } else {
          functionOnFail();
          console.log("Error: deleteProjectImage returned " + jqXHR.status + " " + jqXHR.statusText);
        }
      }
    )
  .fail(functionOnFail);
}

/**
 * Calls the API to update a project wordcount.
 *
 * handler functionOnSuccess called on success.
 * handler functionOnInconsistent called when segmentation/desegementation tasks or inconsistent word counts for its tasks, so cannot save.
 * handler functionOnFail called on failure of the API call.
 */
function DAOupdateProjectWordCount(projectId, newWordCount, functionOnSuccess, functionOnInconsistent, functionOnFail)
{
  $.ajax(
    {
      url: siteAPI + "v0/projects/" + projectId + "/updateWordCount/" + newWordCount,
      method: "PUT",
      headers: {
        "Authorization": "Bearer " + userHash
      }
    }
  )
  .done(function (data, textStatus, jqXHR)
      {
        if (jqXHR.status < 400) {
          if (data == "1") {
            functionOnSuccess();
          } else if (data == "2") {
            functionOnInconsistent();
          } else {
            functionOnFail();
          }
        } else {
          functionOnFail();
          console.log("Error: updateProjectWordCount returned " + jqXHR.status + " " + jqXHR.statusText);
        }
      }
    )
  .fail(functionOnFail);
}
