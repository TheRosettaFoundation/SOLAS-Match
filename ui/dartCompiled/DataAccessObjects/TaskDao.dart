library SolasMatchDart;
import "dart:json" as json;
import "dart:async";

import "../lib/APIHelper.dart";
import "../lib/ModelFactory.dart";
import "../lib/models/Task.dart";
import "../lib/models/Tag.dart";

class TaskDao
{
  static Future<List<Tag>> getTaskTags(int taskId)
  {
    APIHelper client = new APIHelper('.json');
    Future<List<Tag>> ret = client.call("Tag", 'v0/tasks/$taskId/tags', 'GET').then((String jsonText) {
      List<Tag> tags = new List<Tag>();
      if (jsonText != '') {
        Map jsonParsed = json.parse(jsonText);
        if (jsonParsed.length > 0) {
          jsonParsed['item'].forEach((String data) {            
            Map tag = json.parse(data);
            tags.add(ModelFactory.generateTagFromMap(tag));
          });
        }
      }
      return tags;
    });
    return ret;
  }
  
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