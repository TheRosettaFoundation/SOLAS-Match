library SolasMatchDart;

import 'dart:typed_data';
import 'dart:async';
import 'dart:html';
import 'dart:json';
//import 'dart:core';
import 'package:js/js.dart' as js;

import "../lib/Settings.dart";

class APIHelper
{
  String format;
  static var SolasMatch = null; //JS Proto Context object
  
  APIHelper([String frmt = ".proto"])
  {
    format = frmt;
  }
  
  Future<String> call(String objectType, String url, String method, 
                      [String data = '', Map queryArgs = null])
  {
    Settings settings = new Settings();
    url = settings.conf.urls.SOLASMatch + url + format + "/";

    if (queryArgs != null) {
      url += "?";
      queryArgs.keys.forEach((String key) {
        url += key + "=" + queryArgs[key] + "&";
      });
    }
    
    Future<HttpRequest> response = HttpRequest.request(url, method: method, sendData: data);
    Future<String> ret = response.then((HttpRequest resp) {
      return resp.responseText;
    });
    
    return ret;
  }
  
  dynamic getJSProtoContext()
  {
    if (SolasMatch == null) {
      SolasMatch = js.context._root;
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
