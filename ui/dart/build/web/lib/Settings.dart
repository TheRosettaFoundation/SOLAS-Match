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
    Future<bool> ret = HttpRequest.getString(querySelector("#ConfFileLocation").attributes['value'])
               .then((String data) {
                 _instance._conf = new JsonObject.fromJsonString(data);
                 return true;
               }).catchError((e) {
                 print("Error loading conf file: $e");
                 return false;
               });
    return ret;
  }
  
  //(not quite working) potential replacement implementation of loadConf.
  //will allow for deletion of hidden field for dart conf in header and
  //of the dart conf file itself
  Future<bool> loadConfFuture()
  {
    var path = 'api/v0/static/dart/conf.json';
    Future<bool> ret = HttpRequest.getString(path)
               .then((String fileContents) {
                 _instance._conf = new JsonObject.fromJsonString(fileContents);
                 return true;
                }).catchError((Error error) {
                  print("Error loading conf file: $error");
                return false;
                });
    
    return ret;
  }
  
  Settings._internal();
}