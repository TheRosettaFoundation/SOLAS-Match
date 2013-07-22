library SolasMatchDart;

import "dart:async";

import "APIHelper.dart";
import "Localisation.dart";
import "Settings.dart";

class Loader
{
  static Future<bool> load()
  {
    Future<bool> loaded;
    Settings settings = new Settings();
    loaded = settings.loadConf().then((bool success) {
      List<Future<bool>> progressList = new List<Future<bool>>();
      if (success) {
        progressList.add(Localisation.loadFile());
        progressList.add(APIHelper.init());
      }
      Future<bool> complete = Future.wait(progressList).then((List<bool> status) {
        bool worked = true;
        status.forEach((bool stat) {
          if (!stat) {
            worked = false;
          }
        });
        return worked;
      }); 
      return complete;
    });
    return loaded;
  }
}