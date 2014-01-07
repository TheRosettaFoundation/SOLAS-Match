part of SolasMatchDart;

class Language
{
  int id;
  String code;
  String name;
  
  dynamic toJson()
  {
    return {
      "id" : id,
      "code" : code,
      "name" : name
    };
  }
}