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

  if (this.userLangDoc != null) {
    element = this.userLangDoc.querySelector("[name = " + key + "]");
    if (element != null) {
      data = element.innerHtml;
    }
  }

    if (data == "") {
      element = this.defaultLangDoc.querySelector("[name = " + key + "]");
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
        this.userLangDoc = parser.parseFromString(data, "text/xml");
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
        this.defaultLangDoc = parser.parseFromString(data, "text/xml");
        deferred.resolve();
      }
    }
  );
}
