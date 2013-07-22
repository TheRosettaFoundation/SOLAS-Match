library SolasMatchDart;

import "dart:json" as json;
import "dart:async";

import "../lib/APIHelper.dart";
import "../lib/ModelFactory.dart";
import "../lib/models/Badge.dart";
import "../lib/models/User.dart";
import "../lib/models/UserPersonalInformation.dart";
import "../lib/models/Locale.dart";

class UserDao
{
  static Future<User> getUser(int id)
  {
    APIHelper client = new APIHelper(".json");
    Future<User> ret = client.call("User", "v0/users/$id", "GET")
        .then((String jsonText) {
          Map jsonParsed = json.parse(jsonText);
          User user = ModelFactory.generateUserFromMap(jsonParsed);
          return user;
        });
    return ret;
  }
  
  static Future<bool> deleteUser(int userId)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/$userId", "DELETE")
        .then((String data) {
          return true;
        });
  }
  
  static Future<UserPersonalInformation> getUserPersonalInfo(int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<UserPersonalInformation> ret = client.call("UserPersonalInformation", "v0/users/$userId/personalInfo", "GET")
        .then((String jsonText) {
          UserPersonalInformation userInfo = new UserPersonalInformation();
          if (jsonText != '') {
            Map jsonParsed = json.parse(jsonText);
            userInfo = ModelFactory.generateUserInfoFromMap(jsonParsed);
          }
          return userInfo;
        });
    return ret;
  }
  
  static Future<List<Locale>> getSecondaryLanguages(int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Locale>> ret = client.call("Locale", "v0/users/$userId/secondaryLanguages", "GET")
        .then((String jsonText) {
          List<Locale> locales = new List<Locale>();
          Map parsed = json.parse(jsonText);
          parsed['item'].forEach((String data) {
            Map localeData = json.parse(data);
            locales.add(ModelFactory.generateLocaleFromMap(localeData));
          });
          return locales;
        });
    return ret;
  }
  
  static Future<List<Badge>> getUserBadges(int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Badge>> ret = client.call("Badge", "v0/users/$userId/badges", "GET")
        .then((String jsonText) {
          List<Badge> badges = new List<Badge>();
          Map parsed = json.parse(jsonText);
          parsed['item'].forEach((String data) {
            Map badgeData = json.parse(data);
            badges.add(ModelFactory.generateBadgeFromMap(badgeData));
          });
          return badges;
        });
    return ret;
  }
  
  static Future<bool> saveUserDetails(User user)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("User", "v0/users/" + user.id.toString(), "PUT", json.stringify(user))
      .then((String data) {
        return true;
      });
  }
  
  static Future<bool> saveUserInfo(UserPersonalInformation userInfo)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/" + userInfo.userId.toString() + "/personalInfo", "PUT", json.stringify(userInfo))
      .then((String data) {
        return true;
      });
  }
  
  static Future<bool> addUserBadge(int userId, int badgeId)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/$userId/badges/$badgeId", "PUT")
      .then((String data) {
        return true;
      });
  }
  
  static Future<bool> removeUserBadge(int userId, int badgeId)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/$userId/badges/$badgeId", "DELETE")
        .then((String data) {
          return true;
        });
  }
  
  static Future<bool> addSecondaryLanguage(int userId, Locale locale)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/$userId/secondaryLanguages", "POST", json.stringify(locale))
      .then((String data) {
        return true;
      });
  }
  
  static Future<bool> removeSecondaryLanguage(int userId, String languageCode, String countryCode)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/removeSecondaryLanguage/$userId/$languageCode/$countryCode", "DELETE")
        .then((String data) {
          return true;
        });
  }
}