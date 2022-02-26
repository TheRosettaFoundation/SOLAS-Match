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
      url: siteAPI + "v0/users/" + userId + "/",
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
