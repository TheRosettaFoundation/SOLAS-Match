part of SolasMatchDart;

class Localisation
{
  static final Localisation _instance = new Localisation._internal();
  static Document userLangDoc;
  static Document defaultLangDoc;
  
  factory Localisation()
  {
    return _instance;
  }
  
  String getTranslation(String key)
  {
    String data = "";
    Element element;
    if (userLangDoc != null) {
      element = userLangDoc.querySelector("[name = $key]");
      if (element != null) {
        data = element.innerHtml;
      }
    }
    
    if (data == "") {
      element = defaultLangDoc.querySelector("[name = $key]");
      if (element != null) {
        data = element.innerHtml;
      } else {
        print("Unable to find string with name $key");
        data = '';
      }
    }
    return data;
  }
  
  static Future<bool> loadFile()
  {
    Settings settings = new Settings();
    Future<bool> ret;
    List<Future<bool>> finished = new List<Future<bool>>();
    finished.add(HttpRequest.getString(settings.conf.urls.SiteLocation + "static/getUserStrings/")
      .then((String data) {
        bool ret;
        if (data != "") {
          DomParser parser = new DomParser();
          userLangDoc = parser.parseFromString(data, "text/xml");
          ret = true;
        }
        return true;  //even if the user has not set a language
      }));
    finished.add(HttpRequest.getString(settings.conf.urls.SiteLocation + "static/getDefaultStrings/")
        .then((String data) {
          bool ret;
          if (data != "") {
            DomParser parser = new DomParser();
            defaultLangDoc = parser.parseFromString(data, "text/xml");
            ret = true;
          } else {
            ret = false;
          }
          return ret;
        }));
    ret = Future.wait(finished).then((List<bool> successes) {
      bool successful = true;
      successes.forEach((bool success) {
        if (!success) {
          successful = false;
        }
      });
      return successful;
    });
    return ret;
  }
    
  Localisation._internal();
}