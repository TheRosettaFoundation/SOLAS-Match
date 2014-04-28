part of SolasMatchDart;

class APIHelper
{
  String format;
  static var UserHash;
  static var SolasMatch = null; //JS Proto Context object
  
  APIHelper([String frmt = ".proto"])
  {
    format = frmt;
  }
  
  static Future<bool> init()
  {
    Settings settings = new Settings();
    String url = settings.conf.urls.SiteLocation + "static/getUserHash/";
    Future<bool> finished  = HttpRequest.request(url, method: "GET", withCredentials: true).then((HttpRequest response) {
      if (response.responseText != "") {
        UserHash = response.responseText;
      }
      return true;
    });
    return finished;
  }
  
  Future<HttpRequest> call(String objectType, String url, String method, 
                      [dynamic data = '', Map queryArgs = null])
  {
    Completer<HttpRequest> complete = new Completer<HttpRequest>();
    Settings settings = new Settings();
    url = settings.conf.urls.SOLASMatch + url + format + "/";

    if (queryArgs != null) {
      url += "?";
      queryArgs.keys.forEach((String key) {
        url += key + "=" + queryArgs[key] + "&";
      });
    }
    //start of code to refactor
    HttpRequest request = new HttpRequest();
    request.open(method, url);
    if (UserHash != null) {
      request.setRequestHeader("Authorization", "Bearer " + UserHash);
    }
    request.onLoadEnd.listen((e) {
      complete.complete(request);
    });
    request.send(data);
    
    return complete.future;
    //end
  }
  
  String encodeAsString(dynamic proto)
  {
    String ret = "";
    var message = proto.encode();
    
    for (var i=0; i<message.view.byteLength; i++) {
      var val = message.view.getUint8(i);
      ret +=  new String.fromCharCode(val);
    }
    
    return ret;
  }
  
  dynamic decodeFromString(dynamic objectContext, String data)
  {
    return objectContext.decode(data);
  }
}
