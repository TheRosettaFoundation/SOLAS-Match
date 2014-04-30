part of SolasMatchDart;

class Settings
{
  static final Settings _instance = new Settings._internal();
  
  JsonObject _conf;
  JsonObject get conf => _conf;
  
  factory Settings()
  {
    return _instance;
  }

  Future<bool> loadConf()
  {
    Future<bool> ret = HttpRequest.getString((querySelector("link[rel=dart-conf]") as LinkElement).href)
       .then((String fileContents) {
        
         _instance._conf = new JsonObject.fromJsonString(fileContents);
         return true;
        }).catchError((error) {
          print("Error loading conf file: $error");
        return false;
        });
    
    return ret;
  }
  
  Settings._internal();
}