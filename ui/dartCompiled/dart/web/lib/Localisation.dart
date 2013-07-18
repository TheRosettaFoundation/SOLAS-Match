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
    if (languageCode.compareTo("en")==0) {
      print("Calling " + settings.conf.urls.SiteLocation + "ui/localisation/strings.xml");
      return HttpRequest.getString(settings.conf.urls.SiteLocation + "ui/localisation/strings.xml")
        .then((String data) {
          root = XML.parse(data);
          return true;
        });
    } else {
      return HttpRequest.getString(settings.conf.urls.SiteLocation + "ui/localisation/strings_$languageCode.xml")
        .then((String data) {
          root = XML.parse(data);
          return true;
        });
    }
  }
  
  Localisation._internal();
}