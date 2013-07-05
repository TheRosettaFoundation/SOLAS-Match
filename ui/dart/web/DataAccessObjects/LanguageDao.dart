library SolasMatchDart;

import "dart:async";
import "dart:json" as json;

import "../lib/models/Language.dart";
import "../lib/APIHelper.dart";
import "../lib/ModelFactory.dart";

class LanguageDao
{
  static Future<List<dynamic>> getActiveLanguages()
  {
    APIHelper client = new APIHelper(".proto");
    Future<List<dynamic>> languages = client.call("Language", "v0/languages/active/languages", 
      "GET", "", new Map())
      .then((String proto) {
      List<dynamic> activeLangs = new List<dynamic>();
      var SolasMatch = client.getJSProtoContext();
      var protoList = SolasMatch.ProtoList.decode(proto);
      protoList.forEach((String languageProto) {
        activeLangs.add(SolasMatch.Language.decode(languageProto));
      });
      /*Map jsonParsed = json.parse(jsonText);
      if (jsonParsed.length > 0) {
        jsonParsed['item'].forEach((String data) {
          Map lang = json.parse(data);
          activeLangs.add(ModelFactory.generateLanguageFromMap(lang));
        });
      }
      return activeLangs;*/
    });
    return languages;
  }
}