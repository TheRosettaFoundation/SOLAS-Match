library SolasMatchDart;

import 'dart:html';
import 'dart:async';
import 'package:xml/xml.dart';

import "Settings.dart";

class Localisation
{
  static final Localisation _instance = new Localisation._internal();
  static XmlElement root;
  
  factory Localisation()
  {
    return _instance;
  }
  
  static String getTranslation(String key)
  {
    String data;
    var list = root.query({'name':key});
    if (list != null && list.length > 0) {
      data = list.elementAt(0).text;
    } else {
      print("Unable to find string with name $key");
      data = "";
    }
    return data;
  }
  
  static Future<bool> loadFile([String languageCode = "en"])
  {
    Settings settings = new Settings();
    return HttpRequest.getString(settings.conf.urls.SOLASMatch + "v0/localisation/$languageCode")
        .then((String data) {
          root = XML.parse(data);
          return true;
        });
  }
  
  Localisation._internal();
}