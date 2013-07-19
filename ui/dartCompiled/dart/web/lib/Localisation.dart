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
  
  static Future<bool> loadFile()
  {
    Settings settings = new Settings();
    return HttpRequest.getString(settings.conf.urls.SiteLocation + "static/getStrings/")
      .then((String data) {
        bool ret;
        if (data != "") {
          root = XML.parse(data);
          ret = true;
        } else {
          ret = false;
        }
        return ret;
      });
  }
  
  Localisation._internal();
}