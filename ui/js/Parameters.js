var userLangDoc = null; // Strings for user's language preference
var DefaultLangDoc;     // Strings for default language

function Parameters(functionOnSuccess)
{
  var deferredGetUserLangDoc = $.Deferred();
  var deferredGetDefaultLangDoc = $.Deferred();

  $.when(deferredGetUserLangDoc, deferredGetDefaultLangDoc).done(functionOnSuccess);

  this.getUserLangDoc(deferredGetUserLangDoc);
  this.getDefaultLangDoc(deferredGetDefaultLangDoc);
}

Parameters.prototype.getTranslation = function(key)
{
  var element;
  var data = "";

  if (userLangDoc != null) {
    element = userLangDoc.querySelector("[name = " + key + "]");
    if (element != null) {
      data = element.innerHtml;
    }
  }

  if (data == "") {
    element = defaultLangDoc.querySelector("[name = " + key + "]");
    if (element != null) {
      data = element.innerHtml;
    } else {
      print("Unable to find string with name " + key);
    }
  }
  return data;
}

Parameters.prototype.getUserLangDoc = function(deferred)
{
  $.ajax(
    {
      url: siteLocation + "static/getUserStrings/",
      method: "GET",
      dataType: "text"
    }
  )
  .done(
    function(data) {
      if (data != "") {
        var parser = new DOMParser();
        userLangDoc = parser.parseFromString(data, "text/xml");
      }
      deferred.resolve();
    }
  );
}

Parameters.prototype.getDefaultLangDoc = function(deferred)
{
  $.ajax(
    {
      url: siteLocation + "static/getDefaultStrings/",
      method: "GET",
      dataType: "text"
    }
  )
  .done(
    function(data) {
      if (data != "") {
        var parser = new DOMParser();
        defaultLangDoc = parser.parseFromString(data, "text/xml");
        deferred.resolve();
      }
    }
  );
}
