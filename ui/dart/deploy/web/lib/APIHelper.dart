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
  
  Future<String> call(String objectType, String url, String method, 
                      [String data = '', Map queryArgs = null])
  {
    Map<String, String> headers = new Map<String, String>();
    if (UserHash != null) {
      headers["Authorization: Bearer"] = UserHash;
    }
    Settings settings = new Settings();
    url = settings.conf.urls.SOLASMatch + url + format + "/";

    if (queryArgs != null) {
      url += "?";
      queryArgs.keys.forEach((String key) {
        url += key + "=" + queryArgs[key] + "&";
      });
    }
    
    Future<HttpRequest> response = HttpRequest.request(url, method: method, sendData: data, requestHeaders: headers);
    Future<String> ret = response.then((HttpRequest resp) {
      return resp.responseText;
    });
    
    return ret;
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
