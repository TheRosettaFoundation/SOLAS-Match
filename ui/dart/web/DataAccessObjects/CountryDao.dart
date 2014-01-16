part of SolasMatchDart;

class CountryDao
{
  static Future<List<Country>> getAllCountries()
  {
    APIHelper client = new APIHelper(".json");
    Future<List<Country>> ret = client.call("Country", "v0/countries", "GET")
        .then((HttpRequest response) {
          List<Country> countries = new List<Country>();
          if (response.status < 400) {
            if (response.responseText != '') {
              Map parsed = JSON.decode(response.responseText);
              parsed['item'].forEach((String data) {
                Map countryMap = JSON.decode(data);
                countries.add(ModelFactory.generateCountryFromMap(countryMap));
              });
            }
          } else {
            print("Error: getAllCountries returned " + response.status.toString() + " " + response.statusText);
          }
          return countries;
        });
    return ret;
  }
}