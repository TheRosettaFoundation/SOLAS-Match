library SolasMatchDart;
import "dart:json" as json;
import "dart:async";

import "../lib/APIHelper.dart";
import "../lib/ModelFactory.dart";
import "../lib/models/Task.dart";

class TaskDao
{
  static Future<List<Task>> getUserTopTasks(int userId, [int offset = 0, int limit = 15, 
                                            String filter = '', bool strict = false])
  {
    Map<String, String> queryArgs = new Map<String, String>();
    queryArgs["offset"] = offset.toString();
    queryArgs['limit'] = limit.toString();
    queryArgs['strict'] = strict ? '1' : '0';
    queryArgs['filter'] = filter;
    APIHelper client = new APIHelper(".json");
    Future<String> ret = client.call("Task", "v0/users/$userId/topTasks", "GET", "", queryArgs);
    Future<List<Task>> tasks;
    tasks = ret.then((String text) {
      List<Task> userTasks = new List<Task>();
      if (text.length > 0) {
        Map jsonParsed = json.parse(text);
        if (jsonParsed.length > 0) {
          jsonParsed['item'].forEach((String data) {
            Map task = json.parse(data);
            userTasks.add(ModelFactory.generateTaskFromMap(task));
          });
        }
      }
      return userTasks;
    });
    return tasks;
  }
  
  static Future<List<Task>> getLatestAvailableTasks([int offset = 0, int limit = 15])
  {
    Map<String, String> queryArgs = new Map<String, String>();
    queryArgs['offset'] = offset.toString();
    queryArgs['limit'] = limit.toString();
    APIHelper client = new APIHelper('.json');
    Future<List<Task>> ret = client.call("Task", "v0/tasks/topTasks", "GET", "", queryArgs)
        .then((String text) {
      List<Task> tasks = new List<Task>();
      if (text.length > 0) {
        Map jsonParsed = json.parse(text);
        if (jsonParsed.length > 0) {
          jsonParsed['item'].forEach((String data) {
            Map task = json.parse(data);
            tasks.add(ModelFactory.generateTaskFromMap(task));
          });
        }
      }
      return tasks;
    });
    return ret;
  }
}