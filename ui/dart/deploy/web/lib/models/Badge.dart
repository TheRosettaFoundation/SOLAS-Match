library SolasMatchDart;

class Badge
{
  int id;
  String title;
  String description;
  int owner_id;
  
  Badge()
  {
    title = "";
    description = "";
  }
  
  dynamic toJson()
  {
    return {
      "id" : id,
      "title" : title,
      "description" : description,
      "owner_id" : owner_id
    };
  }
}