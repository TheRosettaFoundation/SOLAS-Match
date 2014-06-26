part of SolasMatchDart;

/**
 * Class containing methods to access [Country]-related data through the API.
 */
class CountryDao
{
  /**
   * Calls the API to get a [List] of all Countries.
   * 
   * Returns a [Future] whose value will be a [List] of [Country] objects on successful completion.
   */
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