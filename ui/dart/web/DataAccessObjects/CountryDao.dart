library SolasMatchDart;

import "dart:json" as json;
import "dart:async";
import "dart:html";

import "../lib/models/Country.dart";
import "../lib/APIHelper.dart";
import "../lib/ModelFactory.dart";

class CountryDao
{
  static Future<List<Country>> getAllCountries()
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Country>> ret = client.call("Country", "v0/countries", "GET")
        .then((HttpRequest response) {
          List<Country> countries = new List<Country>();
          if (response.responseText != '') {
            Map parsed = json.parse(response.responseText);
            parsed['item'].forEach((String data) {
              Map countryMap = json.parse(data);
              countries.add(ModelFactory.generateCountryFromMap(countryMap));
            });
          }
          return countries;
        });
    return ret;
  }
}