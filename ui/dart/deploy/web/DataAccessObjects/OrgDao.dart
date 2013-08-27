library SolasMatchDart;

import "dart:async";
import "dart:html";
import "dart:json" as json;

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
      Organisation org = new Organisation();
      if (response.responseText != '') {
        Map jsonParsed = json.parse(response.responseText);
        org = ModelFactory.generateOrgFromMap(jsonParsed);
      }
      return org;
    });
    return organisation;
  }
}