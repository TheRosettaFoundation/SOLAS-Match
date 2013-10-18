library SolasMatchDart;

import "dart:async";
import "dart:convert";
import "dart:html";
//import 'package:js/js.dart' as js;

import "../lib/models/Language.dart";
import "../lib/APIHelper.dart";
import "../lib/ModelFactory.dart";

class LanguageDao
{
  static Future<List<Language>> getAllLanguages()
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Language>> ret = client.call("Language", "v0/languages", "GET")
        .then((HttpRequest response) {
          List<Language> languages = new List<Language>();
          if (response.status < 400) {
            if (response.responseText != "") {
              Map jsonParsed = JSON.decode(response.responseText);
              jsonParsed['item'].forEach((String data) {
                Map lang = JSON.decode(data);
                languages.add(ModelFactory.generateLanguageFromMap(lang));
              });
            }
          } else {
            print("Error: getAllLanguages returned " + response.status.toString() + " " + response.statusText);
          }
          return languages;
        });
    return ret;
  }
  
  static Future<List<dynamic>> getActiveLanguages()
  {
    APIHelper client = new APIHelper(".json");
    Future<List<dynamic>> languages = client.call("Language", "v0/languages/getActiveLanguages", 
      "GET", "", new Map())
      /*.then((String proto) {
      List<dynamic> activeLangs = new List<dynamic>();
      var SolasMatch = client.getJSProtoContext();
      var protoList;
      try {
        protoList = SolasMatch.ProtoList.decode(proto);  //This doesn't work
      } catch (e) {
        print("ERROR: " + e.toString());
      }
      protoList.forEach((String protoLang) {
        activeLangs.add(SolasMatch.Language.decode(protoLang));
      });*/
      .then((HttpRequest response) {
      List<Language> activeLangs = new List<Language>();
      if (response.status < 400) {
        if (response.responseText != '') {
          Map jsonParsed = JSON.decode(response.responseText);
          if (jsonParsed.length > 0) {
            jsonParsed['item'].forEach((String data) {
              Map lang = JSON.decode(data);
              activeLangs.add(ModelFactory.generateLanguageFromMap(lang));
            });
          }
        }
      } else {
        print("Error: getActiveLanguages returned " + response.status.toString() + " " + response.statusText);
      }
      return activeLangs;
    });
    return languages;
  }
  
  static Future<List<Language>> getActiveSourceLanguages()
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Language>> languages = client.call("Language", "v0/languages/getActiveSourceLanguages", "GET")
        .then((HttpRequest response) {
          List<Language> ret = new List<Language>();
          if (response.status < 400) {
            if (response.responseText != '') {
              Map jsonParsed = JSON.decode(response.responseText);
              if (jsonParsed.length > 0) {
                jsonParsed['item'].forEach((String data) {
                  Map lang = JSON.decode(data);
                  ret.add(ModelFactory.generateLanguageFromMap(lang));
                });
              }
            }
          } else {
            print("Error: getActiveSourceLanguages returned " + 
                response.status.toString() + " " + response.statusText);
          }
          return ret;
        });
    return languages;
  }
  
  static Future<List<Language>> getActiveTargetLanguages()
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Language>> languages = client.call("Language", "v0/languages/getActiveTargetLanguages", "GET")
        .then((HttpRequest response) {
          List<Language> ret = new List<Language>();
          if (response.status < 400) {
            if (response.responseText != '') {
              Map jsonParsed = JSON.decode(response.responseText);
              if (jsonParsed.length > 0) {
                jsonParsed['item'].forEach((String data) {
                  Map lang = JSON.decode(data);
                  ret.add(ModelFactory.generateLanguageFromMap(lang));
                });
              }
            }
          } else {
            print("Error: getActiveTargetLanguages returned " + 
                response.status.toString() + " " + response.statusText);
          }
          return ret;
        });
    return languages;
  }
  
  // This works
  /*static Future<dynamic> getLanguage(int id)
  {
    APIHelper client = new APIHelper(".proto");
    Future<dynamic> language = client.call("Language", "v0/languages/$id", "GET")
        .then((String proto)
            {
              var SolasMatch = client.getJSProtoContext();
              var lang = SolasMatch.Language.decode(proto);
              return lang;
            });
    return language;
  }*/
}