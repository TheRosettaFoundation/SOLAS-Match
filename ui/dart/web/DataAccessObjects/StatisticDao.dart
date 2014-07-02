part of SolasMatchDart;

/**
 * Class containing methods to access [Statistic]-related data through the API.
 */
class StatisticDao
{
  /**
   * Calls the API to get a [Statistic] object corresponding to the given [statName].
   * 
   * Returns a [Future] whose value will be a [Statistic] object representing to the statistic corresponding to
   * [statName].
   */
  static Future<Statistic> getStatistic(String statName)
  {
    APIHelper client = new APIHelper(".json");
    Future<Statistic> ret = client.call("", "v0/stats/$statName", "GET")
        .then((HttpRequest response) {
          Statistic stat;
          if (response.status < 400) {
            if (response.responseText != '') {
              Map parsed = JSON.decode(response.responseText);
              Map statData = JSON.decode(parsed['item'][0]);
              stat = ModelFactory.generateStatisticFromMap(statData);
            }
          }
          return stat;
        });
    return ret;
  }
  
  /**
   * Calls the API to get a count of the successful logins from [startDate] to [endDate].
   * 
   * Returns a [Future] whose value will be an [int] storing the login count.
   */
  static Future<int> getLoginCount(String startDate, String endDate)
  {
    APIHelper client = new APIHelper('.json');
    String uri = 'v0/stats/getLoginCount/' + 
        Uri.encodeComponent(startDate.substring(0, startDate.indexOf("."))) +
        '/' + Uri.encodeComponent(endDate.substring(0, endDate.indexOf(".")));
    print("Calling $uri");
    Future<int> ret = client.call('', uri, 'GET')
        .then((HttpRequest response) {
          int count = 0;
          print("Response: " + response.responseText);
          if (response.status < 400) {
            if (response.responseText != '') {
              count = int.parse(response.responseText);
            }
          }
          return count;
        });
    return ret;
  }
}