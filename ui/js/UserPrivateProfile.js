// Globals...

var parameters; // Instance of Parameters Class holding data retrieved from Server (e.g. Translations)

// Passed from PHP
var siteLocation;
var siteAPI;
var user_id;

// Errors
var alert;

var secondaryLanguageLimit = 10;
var secondaryLanguageCount = 0;

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
  if (alert != null) {
    html += '<p class="alert alert-error">';
      html += '<strong>' + parameters.getTranslation('common_error') + ':</strong>';
      html += alert;
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

  secondaryLanguageCount = parseInt(getSetting("secondaryLanguageCount"));
  for (var i = 0; i < secondaryLanguageCount; i++) {
    addSecondaryLanguage(getSetting("userSecondaryLanguagesLanguageCode_" + i), getSetting("userSecondaryLanguagesCountryCode_" + i));
  }

  parameters = new Parameters(loadingComplete);
}

function loadingComplete()
{
  document.getElementById("loading_warning").innerHTML = "";
  document.getElementById("loading_warning1").innerHTML = "";
}

/**
 * This method is used to add another secondary language selector to the page.
 */
function addSecondaryLanguage(userSecondaryLanguagesLanguageCode, userSecondaryLanguagesCountryCode)
{
  if (secondaryLanguageCount < secondaryLanguageLimit) {
    var secondaryLanguageDiv = document.getElementById("secondaryLanguageDiv");
    var locale = document.createElement("div");
    locale.id = "secondary_locale_" + secondaryLanguageCount;

    var languageBox = document.createElement("select");
    languageBox.innerHTML = document.getElementById("template_language_options").innerHTML;
    languageBox.name = "secondary_language_" + secondaryLanguageCount;
    languageBox.id = "secondary_language_" + secondaryLanguageCount;
    languageBox.value = userSecondaryLanguagesLanguageCode;
    locale.appendChild(languageBox);

    var countryBox = document.createElement("select");
    countryBox.innerHTML = document.getElementById("template_country_options").innerHTML;
    countryBox.name = "secondary_country_" + secondaryLanguageCount;
    countryBox.id = "secondary_country_" + secondaryLanguageCount;
    countryBox.value = userSecondaryLanguagesCountryCode;
    locale.appendChild(countryBox);

    var hr = document.createElement("hr");
    hr.style.width = "60%";
    locale.appendChild(hr);

    var button = document.getElementById("addLanguageButton");
    secondaryLanguageDiv.insertBefore(locale, button);
    secondaryLanguageCount++;
    if (secondaryLanguageCount >= secondaryLanguageLimit) {
      button.disabled = true;
    }
    button = document.getElementById("removeLanguageButton");
    button.disabled = false;
  }
}

/**
 * This method is used to remove a secondary language selector from the page.
 */
function removeSecondaryLanguage()
{
  if (secondaryLanguageCount > 0) {
    secondaryLanguageCount--;
    var element = document.getElementById("secondary_locale_" + secondaryLanguageCount);
    element.remove();

    button = document.getElementById("addLanguageButton");
    button.disabled = false;

    if (secondaryLanguageCount < 2) {
      button = document.getElementById("removeLanguageButton");
      button.disabled = true;
    }
  }
}

function deleteUser()
{
  if (window.confirm(parameters.getTranslation("user_private_profile_6"))) {
    DAOdeleteUser(user_id, deleteUserSuccess, deleteUserFail);
  }
}

function deleteUserSuccess()
{
  DAOdestroyUserSession();
  window.location.assign(siteLocation);
}

function deleteUserFail()
{
}

function validateForm()
{
  alert = null;

  if (document.getElementById("displayName").value == "") {
    alert = parameters.getTranslation("user_private_profile_2");
    set_all_errors_for_submission();
    return false;
  }

  var nativeLanguageSelect = document.getElementById("nativeLanguageSelect");
  var nativeCountrySelect = document.getElementById("nativeCountrySelect");
  if ((nativeLanguageSelect.value != "" && nativeCountrySelect.value == "") ||
      (nativeLanguageSelect.value == "" && nativeCountrySelect.value != "")) {
    alert = parameters.getTranslation('user_private_profile_native_language_blanks');
    set_all_errors_for_submission();
    return false;
  }

  // Check if the user has changed their language preference
  if (document.getElementById("langPrefSelect") != getSetting("langPrefSelectCodeSaved")) {
    window.alert(parameters.getTranslation('user_private_profile_language_preference_updated'));
  }

  if (document.getElementById("receiveCredit").value) {
    if (document.getElementById("firstName").value == "" || document.getElementById("lastName").value == "") {
      alert = parameters.getTranslation("user_private_profile_7");
      set_all_errors_for_submission();
      return false;
    }
  }

  var nativeLanguageSelect = document.getElementById("nativeLanguageSelect");
  var nativeCountrySelect = document.getElementById("nativeCountrySelect");
  if ((nativeLanguageSelect.value != "" && nativeCountrySelect.value == "") ||
      (nativeLanguageSelect.value == "" && nativeCountrySelect.value != "")) {
  }

  for (var i = 0; i < secondaryLanguageCount; i++) {
    var secondaryLanguageSelect = document.getElementById("secondary_language_" + i);
    var secondaryCountrySelect = document.getElementById("secondary_country_" + i);
    if ((secondaryLanguageSelect.value != "" && secondaryCountrySelect.value == "") ||
        (secondaryLanguageSelect.value == "" && secondaryCountrySelect.value != "")) {
      alert = parameters.getTranslation("user_private_profile_secondary_languages_failed");
      set_all_errors_for_submission();
      return false;
    }
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
function destroyUserSession()
{
  document.cookie = "slim_session=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
}
