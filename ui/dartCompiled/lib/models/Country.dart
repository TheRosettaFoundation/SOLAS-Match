library SolasMatchDart;

class Country
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