part of SolasMatchDart;

class Loader
{
  static Future<bool> load()
  {
    Settings settings = new Settings();
    // First load config.
    return settings.loadConf().then((bool success) {
      if (success) {
        // Then load localisation and initialize APIHelper in parallel.
        return Future.wait([Localisation.loadFile(), APIHelper.init()])
            .then((List<bool> status)
                => status.every((bool success) => success == true)); 
      }else {
        return false;
      }
    });
  }
}