part of SolasMatchDart;

class Organisation
{
  int id;
  String name;
  String biography;
  String homepage;
  String email;
  String address;
  String city;
  String country;
  String regionalFocus;
  
  dynamic toJson()
  {
    return {
      "id" : id,
      "name" : name,
      "biography" : biography,
      "homepage" : homepage,
      "email" : email,
      "address" : address,
      "city" : city,
      "country" : country,
      "regionalFocus" : regionalFocus
    };
  }
}