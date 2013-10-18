library SolasMatchDart;

import "dart:async";
import "dart:html";
import "dart:convert";

import "../lib/models/Org.dart";
import "../lib/ModelFactory.dart";
import "../lib/APIHelper.dart";

class OrgDao
{
  static Future<Organisation> getOrg(int id)
  {
    APIHelper client = new APIHelper(".json");
    Future<Organisation> organisation = client.call("Organisation", "v0/orgs/" + id.toString(), "GET", "", new Map())
          .then((HttpRequest response) {
      Organisation org = null;
      if (response.status < 400) {
        if (response.responseText != '') {
          Map jsonParsed = JSON.decode(response.responseText);
          org = ModelFactory.generateOrgFromMap(jsonParsed);
        }
      } else {
        print("Error: getOrg returned " + response.status.toString() + " " + response.statusText);
      }
      return org;
    });
    return organisation;
  }
}