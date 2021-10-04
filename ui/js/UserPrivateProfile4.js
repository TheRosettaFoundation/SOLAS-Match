// Globals...

var parameters; // Instance of Parameters Class holding data retrieved from Server (e.g. Translations)

// Passed from PHP
var siteLocation;
var siteAPI;
var user_id;

// Errors
var alertError;

$(document).ready(documentReady);


function getSetting(text)
{
  return document.getElementById(text).innerHTML;
}

function set_all_errors_for_submission()
{
  set_errors_for_submission("placeholder_for_errors_1");
  set_errors_for_submission("placeholder_for_errors_2");
}

function set_errors_for_submission(id)
{
  html = "";
  if (alertError != null) {
    html += '<p class="alert alert-error">';
      html += '<strong>' + parameters.getTranslation('common_error') + ': </strong>';
      html += alertError;
    html += '</p>';
  }
  document.getElementById(id).innerHTML = html;
}

/**
 * Called by the DOM when the Document is Ready.
 */
function documentReady()
{
  siteLocation = getSetting("siteLocation");
  siteAPI      = getSetting("siteAPI");
  user_id      = getSetting("user_id");

  parameters = new Parameters(loadingComplete);
}

function loadingComplete()
{
  // document.getElementById("loading_warning").innerHTML = "";
  // document.getElementById("loading_warning1").innerHTML = "";
}

function deleteUser()
{
  if (window.confirm(parameters.getTranslation("user_private_profile_6"))) {
    DAOdeleteUser(user_id, deleteUserSuccess, deleteUserFail);
  }

  return false;
}

function deleteUserSuccess()
{
  DAOdestroyUserSession();
  window.location.replace(siteLocation);
}

function deleteUserFail()
{
  // Seems that the call (even though it deletes the user) always calls the "fail" handler
  DAOdestroyUserSession();
  window.location.replace(siteLocation);
}

function validateForm()
{
  alertError = null;

  if (document.getElementById("language_code_source_0").value == "" || document.getElementById("language_code_target_0").value == "") {
      alertError = "You must fill out the languages you can translate from and to.";
      set_all_errors_for_submission();
      return false;
  }

  for (var i = 0; i < userQualifiedPairsCount; i++) {
    var languageCodeSource = document.getElementById("language_code_source_" + i);
    var languageCodeTarget = document.getElementById("language_code_target_" + i);

    if ((languageCodeSource.value == "" && languageCodeTarget.value != "") || (languageCodeTarget.value == "" && languageCodeSource.value != "") ||
        (languageCodeSource.value == languageCodeTarget.value)) {
      alertError = parameters.getTranslation("user_private_profile_secondary_languages_failed");
      set_all_errors_for_submission();
      return false;
    }
  }

  var capabilityCount = parseInt(getSetting("capabilityCount"));
  var checkedCount = 0;
  for (var i = 0; i < capabilityCount; i++) {
    if (document.getElementById("capability" + i).checked) checkedCount++;
  }
  if (!checkedCount) {
    alertError = "You must indicate that you can provide at least one service such as translation.";
    set_all_errors_for_submission();
    return false;
  }

  var expertiseCount = parseInt(getSetting("expertiseCount"));
  checkedCount = 0;
  for (var i = 0; i < expertiseCount; i++) {
    if (document.getElementById("expertise" + i).checked) checkedCount++;
  }
  if (!checkedCount && !parseInt(getSetting("isSiteAdmin"))) {
    alertError = "You must indicate at least one field of expertise.";
    set_all_errors_for_submission();
    return false;
  }

  return true;
}

/**
 * Calls the API to delete the user with the given [userId] from the database.
 *
 * handler functionOnSuccess called on success.
 * handler functionOnFail called on failure of the API call.
 */
function DAOdeleteUser(userId, functionOnSuccess, functionOnFail)
{
  $.ajax(
    {
      url: siteAPI + "v0/users/" + userId,
      method: "DELETE",
      headers: {
        "Authorization": "Bearer " + userHash
      }
    }
  )
  .done(function (data, textStatus, jqXHR)
      {
        if (jqXHR.status < 400) {
          functionOnSuccess();
        } else {
          functionOnFail();
          console.log("Error: deleteUser returned " + jqXHR.status + " " + jqXHR.statusText);
        }
      }
    )
  .fail(functionOnFail);
}

/**
 * Destroys the current user session, logging the user out. This is presently only used in the event that a
 * user decides to delete their account.
 */
function DAOdestroyUserSession()
{
  document.cookie = "slim_session=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
}
