// Globals...

var parameters; // Instance of Parameters Class holding data retrieved from Server (e.g. Translations)

// Passed from PHP
var siteLocation;
var siteAPI;
var user_id;

// Errors
var alertError;

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
  if (alertError != null) {
    html += '<p class="alert alert-error">';
      html += '<strong>' + parameters.getTranslation('common_error') + ':</strong>';
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

  var secondaryLanguageCountDatabase = parseInt(getSetting("secondaryLanguageCount"));
  for (var i = 0; i < secondaryLanguageCountDatabase; i++) {
    addSecondaryLanguage(
      getSetting("userSecondaryLanguagesLanguageCode_" + i),
      getSetting("userSecondaryLanguagesCountryCode_" + i),
      getSetting("userSecondaryLanguagesLanguageCodeTarget_" + i),
      getSetting("userSecondaryLanguagesCountryCodeTarget_" + i),
      getSetting("userSecondaryLanguagesQualificationLevel_" + i)
      );
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
function addSecondaryLanguage(
  userSecondaryLanguagesLanguageCode,
  userSecondaryLanguagesCountryCode,
  userSecondaryLanguagesLanguageCodeTarget,
  userSecondaryLanguagesCountryCodeTarget,
  userSecondaryLanguagesQualificationLevel)
{
  if (secondaryLanguageCount < secondaryLanguageLimit) {
    var secondaryLanguageDiv = document.getElementById("secondaryLanguageDiv");
    var locale = document.createElement("div");
    locale.id = "secondary_locale_" + secondaryLanguageCount;

    var text1 = document.createElement("label");
    text1.innerHTML = "<strong>" + parameters.getTranslation("i_can_translate_from") + ":</strong>";
    text1.style.width = "82%";
    locale.appendChild(text1);

    var languageBox = document.createElement("select");
    languageBox.innerHTML = document.getElementById("template_language_options").innerHTML;
    languageBox.name = "secondary_language_" + secondaryLanguageCount;
    languageBox.id = "secondary_language_" + secondaryLanguageCount;
    languageBox.style.width = "82%";
    languageBox.value = userSecondaryLanguagesLanguageCode;
    locale.appendChild(languageBox);

    var countryBox = document.createElement("select");
    countryBox.innerHTML = document.getElementById("template_country_options").innerHTML;
    countryBox.name = "secondary_country_" + secondaryLanguageCount;
    countryBox.id = "secondary_country_" + secondaryLanguageCount;
    countryBox.style.width = "82%";
    countryBox.value = userSecondaryLanguagesCountryCode;
    locale.appendChild(countryBox);

    var text2 = document.createElement("label");
    text2.innerHTML = "<strong>" + parameters.getTranslation("common_to") + ":</strong>";
    text2.style.width = "82%";
    locale.appendChild(text2);

    var languageBoxTarget = document.createElement("select");
    languageBoxTarget.innerHTML = document.getElementById("template_language_options").innerHTML;
    languageBoxTarget.name = "secondary_language_target_" + secondaryLanguageCount;
    languageBoxTarget.id = "secondary_language_target_" + secondaryLanguageCount;
    languageBoxTarget.style.width = "82%";
    languageBoxTarget.value = userSecondaryLanguagesLanguageCodeTarget;
    locale.appendChild(languageBoxTarget);

    var countryBoxTarget = document.createElement("select");
    countryBoxTarget.innerHTML = document.getElementById("template_language_options").innerHTML;
    countryBoxTarget.name = "secondary_country_target_" + secondaryLanguageCount;
    countryBoxTarget.id = "secondary_country_target_" + secondaryLanguageCount;
    countryBoxTarget.style.width = "82%";
    countryBoxTarget.value = userSecondaryLanguagesCountryCodeTarget;
    locale.appendChild(countryBoxTarget);

    var text3 = document.createElement("label");
    text3.innerHTML = "<strong>" + parameters.getTranslation("qualification_level_for_above") + ":</strong>";
    text3.style.width = "82%";
    locale.appendChild(text3);

    var qualificationLevel = document.createElement("select");
    qualificationLevel.innerHTML = document.getElementById("template_qualification_options").innerHTML;
    qualificationLevel.name = "secondary_country_target_" + secondaryLanguageCount;
    qualificationLevel.id = "secondary_country_target_" + secondaryLanguageCount;
    qualificationLevel.style.width = "82%";
    qualificationLevel.value = userSecondaryLanguagesQualificationLevel;
    locale.appendChild(qualificationLevel);

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

  return false;
}

/**
 * This method is used to remove a secondary language selector from the page.
 */
function removeSecondaryLanguage()
{
  if (secondaryLanguageCount > 0) {
    secondaryLanguageCount--;
    var element = document.getElementById("secondary_locale_" + secondaryLanguageCount);
    element.parentNode.removeChild(element);

    button = document.getElementById("addLanguageButton");
    button.disabled = false;

    if (secondaryLanguageCount < 2) {
      button = document.getElementById("removeLanguageButton");
      button.disabled = true;
    }
  }

  return false;
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

  if (document.getElementById("displayName").value == "") {
    alertError = parameters.getTranslation("user_private_profile_2");
    set_all_errors_for_submission();
    return false;
  }

  var nativeLanguageSelect = document.getElementById("nativeLanguageSelect");
  var nativeCountrySelect = document.getElementById("nativeCountrySelect");
  if ((nativeLanguageSelect.value != "" && nativeCountrySelect.value == "") ||
      (nativeLanguageSelect.value == "" && nativeCountrySelect.value != "")) {
    alertError = parameters.getTranslation("user_private_profile_native_language_blanks");
    set_all_errors_for_submission();
    return false;
  }

  // Check if the user has changed their language preference
  if (document.getElementById("langPrefSelect").value != getSetting("langPrefSelectCodeSaved")) {
    window.alert(parameters.getTranslation("user_private_profile_language_preference_updated"));
  }

  if (document.getElementById("receiveCredit").checked) {
    if (document.getElementById("firstName").value == "" || document.getElementById("lastName").value == "") {
      alertError = parameters.getTranslation("user_private_profile_7");
      set_all_errors_for_submission();
      return false;
    }
  }

  for (var i = 0; i < secondaryLanguageCount; i++) {
    var secondaryLanguageSelect = document.getElementById("secondary_language_" + i);
    var secondaryCountrySelect = document.getElementById("secondary_country_" + i);
    if ((secondaryLanguageSelect.value != "" && secondaryCountrySelect.value == "") ||
        (secondaryLanguageSelect.value == "" && secondaryCountrySelect.value != "")) {
      alertError = parameters.getTranslation("user_private_profile_secondary_languages_failed");
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
function DAOdestroyUserSession()
{
  document.cookie = "slim_session=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
}
