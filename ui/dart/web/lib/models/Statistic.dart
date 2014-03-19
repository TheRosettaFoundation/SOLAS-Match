part of SolasMatchDart;

class Statistic
{
  String name;
  String value;
  
  Statistic()
  {
    name = '';
    value = '';
  }
  
  dynamic toJson()
  {
    dynamic ret = {
      "name": name,
      "value": value
    };
    return ret;
  }
}