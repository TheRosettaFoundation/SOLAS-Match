library SolasMatchDart;

class Tag
{
  int id;
  String label;
  
  String print()
  {
    String ret = "{ Tag : { id : " + id.toString() + 
        ", label : " + label + "} }";
    return ret;
  }
}