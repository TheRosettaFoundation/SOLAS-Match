part of SolasMatchDart;

class User
{
  int id;
  String display_name;
  String email;
  String password;
  String biography;
  String nonce;
  String created_time;
  Locale nativeLocale;
  
  dynamic toJson()
  {
    return {
      "id" : id, 
      "display_name" : display_name,
      "email" : email,
      "biography" : biography,
      "created_time" : created_time,
      "nativeLocale" : nativeLocale
    };
  }
}