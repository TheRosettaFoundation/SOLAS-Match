library SolasMatchDart;

import 'dart:html';
import "package:web_ui/web_ui.dart";
import 'package:js/js.dart' as js;
import 'dart:async';
import 'dart:json';
import 'dart:typed_data';

import "DataAccessObjects/LanguageDao.dart";
import "lib/Settings.dart";

void main() 
{
  Settings settings = new Settings();
  settings.loadConf().then((e) {
    LanguageDao.getActiveLanguages()
    .then((List<dynamic> langs) => print("Language list: " + langs.toString()));
  }).catchError((e) {
    print("Error: $e");
  });
}
