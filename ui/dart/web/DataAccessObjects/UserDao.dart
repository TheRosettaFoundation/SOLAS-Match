part of SolasMatchDart;

/**
 * Class containing methods for modifying and accessing [User]-related data.
 */
class UserDao
{
  /**
   * Calls the API to get the [User] identified by the given [id].
   * 
   * Returns a [Future] whose value will be a [User] object representing the user identified by [id] if the
   * API sent back a response code less than 400, otherwise prints a [String] to the browser console 
   * showing the response code and status text.
   */
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
  
  /**
   * Calls the API to delete the user with the given [userId] from the database.
   * 
   * Returns a [Future] whose value will be a [bool], true if the API sent back a response code less than 400,
   * false otherwise, coupled with a print to the browser console of a [String] showing the response code
   * and status text.
   */
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
  
  /**
   * Calls the API to get the [UserPersonalInformation] of the user identified by [userId].
   * 
   * Returns a [Future] whose value will be the [UserPersonalInformation] of the user identified by [userId]
   * if the API sent back a response code less than 400, otherwise prints a [String] to the browser console
   * showing the response code and status text.
   */
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
  
  /**
   * Calls the API to get a [List] of the secondary languages of the user identified by [userId], as [Locale]
   * objects.
   * 
   * Returns a [Future] whose value will be a [List] of [Locale] objects, representing the user's secondary
   * languages if the API sent back a response code less than 400, otherwise prints a [String] to the 
   * browser console showing the response code and status text.
   */
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
  
  /**
   * Calls the API to get a [List] of the [Badge]s the user identified by [userId] has been assigned.
   * 
   * Returns a [Future] whose value will be a [List] of the user's badges if the API sent back a 
   * response code less than 400, otherwise prints a [String] to the browser console showing the
   * response code and status text.
   */
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
  
  /**
   * Calls the API to get a count of the [Task]s the user identified by [userId] has claimed.
   * 
   * Returns a [Future] whose value will be an [int] storing the count of the user's claimed tasks. If for some
   * reason the value returned from the API cannot be parsed as an [int] then a failure message will be printed
   * to the browser console and the count will be returned as 0.
   */
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
  
  /**
   * Calls the API to get a [List] of [Task]s that have been claimed by the user identified by [userId].
   * 
   * Returns a [Future] whose value will be a [List] of [Task]s, optionally limited by [limit] (default 0), that 
   * the user identified by [userId] has claimed, starting from the specified [offset] (default 0) if the 
   * API sent back a response code less than 400.
   */
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
  
  /**
   * Calls the API to get a [List] of [Task]s that have been claimed by the user identified by [userId],
   * filtered by [taskType] and [taskStatus], ordering determined by [orderBy], starting from specified
   * [offset] (default 0) and optionally limited by [limit] (default 10).
   * 
   * Returns a [Future] whose value will be a [List] of [Task]s that are the tasks the user has claimed which
   * match the values provided for the aforementioned filters and other parameters if the API sent back a 
   * response code less than 400, otherwise prints a [String] to the browser console showing the
   * response code and status text.
   */
  static Future<List<Task>> getFilteredUserClaimedTasks(int userId, [int orderBy = 0, int limit = 10,
                                                        int offset = 0, int taskType = 0, int taskStatus = 0])
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Task>> tasks = client.call(
        "Task",
        "v0/users/$userId/filteredClaimedTasks/$orderBy/$limit/$offset/$taskType/$taskStatus", 
        'GET')
      .then((HttpRequest response) {
        List<Task> userTasks = new List<Task>();
        if (response.status < 400) {
          if (response.responseText.length > 0) {
            Map jsonParsed = JSON.decode(response.responseText);
            if (jsonParsed.length > 0) {
              jsonParsed['item'].forEach((String data) {
                Map task = JSON.decode(data);
                userTasks.add(ModelFactory.generateTaskFromMap(task));
              });
            }
          }
        } else {
          print("Error: getFilteredUserClaimedTasks returned: " + response.status.toString() + " " + response.statusText);
        }
        return userTasks;
      });
    return tasks;
  }
  
  /**
   * Calls the API to update stored information about the [User] represented by [user]; information that is 
   * not stored as part of [UserPersonalInformation], e.g. display name.
   * 
   * Returns a [Future] whose value will be a [bool], true if the API sent back a response code less than 400,
   * false otherwise, coupled with a print to the browser console of a [String] showing the response code
   * and status text.
   */
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
  
  /**
   * Calls the API to save a the data of the [UserPersonalInformation] object [userInfo].
   * 
   * Returns a [Future] whose value will be a [bool], true if the API sent back a response code less than 400,
   * false otherwise, coupled with a print to the browser console of a [String] showing the response code
   * and status text.
   */
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
  
  /**
   * Calls the API to save data recording that the user identified by [userId] has been assigned the [Badge]
   * identified by [badgeId].
   * 
   * Returns a [Future] whose value will be a [bool], true if the API sent back a response code less than 400,
   * false otherwise, coupled with a print to the browser console of a [String] showing the response code
   * and status text.
   */  
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
  
  /**
   * Calls the API to delete the data recording that the user identified by [userId] had been assigned 
   * the [Badge] identified by [badgeId].
   * 
   * Returns a [Future] whose value will be a [bool], true if the API sent back a response code less than 400,
   * false otherwise, coupled with a print to the browser console of a [String] showing the response code
   * and status text.
   */ 
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
  
  /**
   * Calls the API to record that the user identified by [userId] has added the language represented by the
   * [Locale] object [locale] as a secondary language on their profile.
   * 
   * Returns a [Future] whose value will be a [bool], true if the API sent back a response code less than 400,
   * false otherwise, coupled with a print to the browser console of a [String] showing the response code
   * and status text.
   */ 
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
  
  /**
   * Calls the API to delete data, reflecting that the user identified by [userId] has removed the 
   * language represented by the String pair [languageCode] and [countryCode] from their profile.
   * 
   * Returns a [Future] whose value will be a [bool], true if the API sent back a response code less than 400,
   * false otherwise, coupled with a print to the browser console of a [String] showing the response code
   * and status text.
   */ 
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
  
  /**
   * Destroys the current user session, logging the user out. This is presently only used in the event that a
   * user decides to delete their account.
   */
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