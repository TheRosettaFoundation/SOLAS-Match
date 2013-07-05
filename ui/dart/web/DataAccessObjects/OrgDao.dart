library SolasMatchDart;

import "dart:async";
import "dart:json" as json;

import "../lib/models/Org.dart";
import "../lib/ModelFactory.dart";
import "../lib/APIHelper.dart";

class OrgDao
{
  static Future<Organisation> getOrg(int id)
  {
    APIHelper client = new APIHelper(".json");
    Future<String> jsonData = client.call("Organisation", 
        "v0/orgs/" + id.toString(), "GET", "", new Map());
    Future<Organisation> organisation = jsonData.then((String jsonText) {
      Map jsonParsed = json.parse(jsonText);
      Organisation org = ModelFactory.generateOrgFromMap(jsonParsed);
      return org;
    });
    return organisation;
  }
}