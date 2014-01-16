part of SolasMatchDart;

class UserDao
{
  static Future<User> getUser(int id)
  {
    APIHelper client = new APIHelper(".json");
    Future<User> ret = client.call("User", "v0/users/$id", "GET")
        .then((HttpRequest response) {
          User user = null;
          if (response.status < 400) {
            if (response.responseText.length > 0) {
              Map jsonParsed = JSON.decode(response.responseText);
              user = ModelFactory.generateUserFromMap(jsonParsed);
            }
          } else {
            print("Error: getUser returned " + response.status.toString() + " " + response.statusText);
          }
          return user;
        });
    return ret;
  }
  
  static Future<bool> deleteUser(int userId)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/$userId", "DELETE")
        .then((HttpRequest response) {
          if (response.status < 400) {
            return true;
          } else {
            print("Error: deleteUser returned " + response.status.toString() + " " + response.statusText);
            return false;
          }
        });
  }
  
  static Future<UserPersonalInformation> getUserPersonalInfo(int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<UserPersonalInformation> ret = client.call("UserPersonalInformation", "v0/users/$userId/personalInfo", "GET")
        .then((HttpRequest response) {
          UserPersonalInformation userInfo = new UserPersonalInformation();
          userInfo.userId = userId;
          if (response.status < 400) {
            if (response.responseText != '') {
              Map jsonParsed = JSON.decode(response.responseText);
              userInfo = ModelFactory.generateUserInfoFromMap(jsonParsed);
            }
          } else {
            print("Error: getUserPersonalInfo returned " + 
                response.status.toString() + " " + response.statusText);
          }
          return userInfo;
        });
    return ret;
  }
  
  static Future<List<Locale>> getSecondaryLanguages(int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Locale>> ret = client.call("Locale", "v0/users/$userId/secondaryLanguages", "GET")
        .then((HttpRequest response) {
          List<Locale> locales = new List<Locale>();
          if (response.status < 400) {
            if (response.responseText.length > 0) {
              Map parsed = JSON.decode(response.responseText);
              parsed['item'].forEach((String data) {
                Map localeData = JSON.decode(data);
                locales.add(ModelFactory.generateLocaleFromMap(localeData));
              });
            }
          } else {
            print("Error: getSecondaryLanguages returned " + response.status.toString() + " " + response.statusText);
          }
          return locales;
        });
    return ret;
  }
  
  static Future<List<Badge>> getUserBadges(int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Badge>> ret = client.call("Badge", "v0/users/$userId/badges", "GET")
        .then((HttpRequest response) {
          List<Badge> badges = new List<Badge>();
          if (response.status < 400) {
            if (response.responseText.length > 0) {
              Map parsed = JSON.decode(response.responseText);
              parsed['item'].forEach((String data) {
                Map badgeData = JSON.decode(data);
                badges.add(ModelFactory.generateBadgeFromMap(badgeData));
              });
            }
          } else {
            print("Error: getUserBadges returned " + response.status.toString() + " " + response.statusText);
          }
          return badges;
        });
    return ret;
  }
  
  static Future<int> getUserClaimedTasksCount(int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<int> ret = client.call("", "v0/users/getClaimedTasksCount/$userId", "GET")
        .then((HttpRequest response) {
          int count = 0;
          if (response.status < 400) {
            count = int.parse(response.responseText, onError: (String responseText) {
              print("Failed to parse claimed tasks count, $responseText");
              return 0;
            });
          }
          return count;
        });
    return ret;
  }
  
  static Future<List<Task>> getUserTasks(int userId, [int offset = 0, int limit = 0])
  {
    APIHelper client = new APIHelper(".json");
    Map<String, String> queryArgs = new Map<String, String>();
    queryArgs['offset'] = offset.toString();
    queryArgs['limit'] = limit.toString();
    return client.call("", "v0/users/$userId/tasks", "GET", null, queryArgs)
        .then((HttpRequest response) {
          List<Task> tasks = new List<Task>();
          if (response.status < 400 && response.responseText != '') {
            Map jsonParsed = JSON.decode(response.responseText);
            if (jsonParsed.length > 0) {
              jsonParsed['item'].forEach((String data) {
                Map task = JSON.decode(data);
                tasks.add(ModelFactory.generateTaskFromMap(task));
              });
            }
          }
          return tasks;
        });
  }
  
  static Future<bool> saveUserDetails(User user)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("User", "v0/users/" + user.id.toString(), "PUT", JSON.encode(user))
      .then((HttpRequest response) {
        if (response.status < 400) {
          return true;
        } else {
          print("Error: saveUserDetails returned " + response.status.toString() + " " + response.statusText);
          return false;
        }
      });
  }
  
  static Future<bool> saveUserInfo(UserPersonalInformation userInfo)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/" + userInfo.userId.toString() + "/personalInfo", "PUT", JSON.encode(userInfo))
      .then((HttpRequest response) {
        if (response.status < 400) {
          return true;
        } else {
          print("Error: saveUserInfo returned " + response.status.toString() + " " + response.statusText);
          return false;
        }
      });
  }
  
  static Future<bool> addUserBadge(int userId, int badgeId)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/$userId/badges/$badgeId", "PUT")
      .then((HttpRequest response) {
        if (response.status < 400) {
          return true;
        } else {
          print("Error: addUserBadge returned " + response.status.toString() + " " + response.statusText);
          return false;
        }
      });
  }
  
  static Future<bool> removeUserBadge(int userId, int badgeId)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/$userId/badges/$badgeId", "DELETE")
        .then((HttpRequest response) {
          if (response.status < 400) {
            return true;
          } else {
            print("Error: removeUserBadge returned " + response.status.toString() + " " + response.statusText);
            return false;
          }
        });
  }
  
  static Future<bool> addSecondaryLanguage(int userId, Locale locale)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/$userId/secondaryLanguages", "POST", JSON.encode(locale))
      .then((HttpRequest response) {
        if (response.status < 400) {
          return true;
        } else {
          print("Error: addSecondaryLanguage returned " + response.status.toString() + " " + response.statusText);
          return false;
        }
      });
  }
  
  static Future<bool> removeSecondaryLanguage(int userId, String languageCode, String countryCode)
  {
    APIHelper client = new APIHelper(".json");
    return client.call("", "v0/users/removeSecondaryLanguage/$userId/$languageCode/$countryCode", "DELETE")
        .then((HttpRequest response) {
          if (response.status < 400) {
            return true;
          } else {
            print("Error: removeSecondaryLanguage returned " + response.status.toString() + " " + response.statusText);
            return false;
          }
        });
  }
  
  static void destroyUserSession()
  {
    String name = "slim_session";
    String value = "";
    String expires;
    DateTime then = new DateTime.now();
    expires = '; expires=' + then.toString();
    document.cookie = name + '=' + value + expires + '; path=/';
  }
}