library SolasMatchDart;

import "Locale.dart";

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
      "password" : password,
      "biography" : biography,
      "nonce" : nonce,
      "created_time" : created_time,
      "nativeLocale" : nativeLocale
    };
  }
}