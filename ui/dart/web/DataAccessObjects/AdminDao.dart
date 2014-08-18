part of SolasMatchDart;

/**
 *Contains methods related to "Admins": [User]s who are administrators, either of organisations
 * or the site as a whole.
 */ 
class AdminDao
{
  static Future<bool> isSiteAdmin(int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/admins/isSiteAdmin/$userId", "GET")
        .then((HttpRequest response) {
          bool isAdmin;
          if (response.status < 400) {
            if (response.responseText != '') {
              if (response.responseText == "1") {
                isAdmin = true;
              } else if (response.responseText == "0") {
                isAdmin = false;
              }
            }
          } else {
            throw "Error: getAllCountries returned " + response.status.toString() + " " + response.statusText;
          }
          return isAdmin;
        });
    return ret;
  }
}
