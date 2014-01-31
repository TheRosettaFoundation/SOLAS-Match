part of SolasMatchDart;

class Tag
{
  int id;
  String label;
  
  dynamic toJson()
  {
    return {
      "id" : id,
      "label" : label
    };
  }
  
  String print()
  {
    String ret = "{ Tag : { id : " + id.toString() + 
        ", label : " + label + "} }";
    return ret;
  }
}