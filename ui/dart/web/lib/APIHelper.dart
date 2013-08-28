library SolasMatchDart;

//import 'dart:typed_data';
import "package:crypto/crypto.dart";
import 'dart:async';
import 'dart:html';
import 'dart:json' as json;
//import 'dart:core';
//import 'package:js/js.dart' as js;

import "ModelFactory.dart";
import "Settings.dart";
import "models/User.dart";

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
    Map<String, String> headers = new Map<String, String>();
    if (UserHash != null) {
      headers["Authorization"] = "Bearer "+UserHash;
    }
    Settings settings = new Settings();
    url = settings.conf.urls.SOLASMatch + url + format + "/";

    if (queryArgs != null) {
      url += "?";
      queryArgs.keys.forEach((String key) {
        url += key + "=" + queryArgs[key] + "&";
      });
    }
    
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
  }
  
  dynamic getJSProtoContext()
  {
    if (SolasMatch == null) {
      //SolasMatch = js.context._root;
    }
    return SolasMatch;
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
