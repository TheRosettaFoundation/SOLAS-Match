part of SolasMatchDart;

/**
 * Class containing methods to access [Language]-related data through the API.
 */
class LanguageDao
{
  /**
   * Calls the API to get a list of all Languages.
   * 
   * Returns a [Future] whose value will be a [List] of [Language] objects on successful completion.
   */
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
  
  /**
   * Calls the API to get a list of "active" languages. Active languages are defined as languages for which
   * there are currently tasks available.
   * 
   * Returns a [Future] whose value will be a [List] of [Language] objects representing the active
   * languages on successful completion. 
   */
  //TODO: This doesn't need to be List<dynamic>, does it?
  static Future<List<dynamic>> getActiveLanguages()
  {
    APIHelper client = new APIHelper(".json");
    Future<List<dynamic>> languages = client.call("Language", "v0/languages/getActiveLanguages", 
      "GET", "", new Map())
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
  
  /**
   * Calls the API to get a list of active source languages; languages which are source languages of currently
   * available tasks.
   * 
   * Returns a [Future] whose value will be a [List] of [Language] objects representing the active source
   * languages.
   */
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
  
  /**
   * Calls the API to get a list of active target languages; languages which are target languages of currently
   * available tasks.
   * 
   * Returns a [Future] whose value will be a [List] of [Language] objects representing the active target
   * languages.
   */
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
}