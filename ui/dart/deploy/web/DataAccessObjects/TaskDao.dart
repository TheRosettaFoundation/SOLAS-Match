library SolasMatchDart;
import "dart:json" as json;
import "dart:async";
import "dart:html";

import "../lib/APIHelper.dart";
import "../lib/ModelFactory.dart";
import "../lib/models/Task.dart";
import "../lib/models/Tag.dart";

class TaskDao
{
  static Future<List<Tag>> getTaskTags(int taskId)
  {
    APIHelper client = new APIHelper('.json');
    Future<List<Tag>> ret = client.call("Tag", 'v0/tasks/$taskId/tags', 'GET')
        .then((HttpRequest response) {
      List<Tag> tags = new List<Tag>();
      if (response.responseText != '') {
        Map jsonParsed = json.parse(response.responseText);
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
    Future<List<Task>> tasks = client.call("Task", "v0/users/$userId/topTasks", "GET", "", queryArgs)
        .then((HttpRequest response) {
      List<Task> userTasks = new List<Task>();
      if (response.responseText.length > 0) {
        Map jsonParsed = json.parse(response.responseText);
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
        .then((HttpRequest response) {
      List<Task> tasks = new List<Task>();
      if (response.responseText.length > 0) {
        Map jsonParsed = json.parse(response.responseText);
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
  
  static Future<Task> createTask(Task task)
  {
    APIHelper client = new APIHelper(".json");
    Future<Task> ret = client.call("Task", "v0/tasks", "POST", json.stringify(task))
        .then((HttpRequest response) {
          task = null;
          if (response.responseText.length > 0) {
            Map jsonParsed = json.parse(response.responseText);
            if (jsonParsed.length > 0) {
              task = ModelFactory.generateTaskFromMap(jsonParsed);
            }
          }
          return task;
    });
    return ret;
  }
  
  static Future<bool> addTaskPreReq(int taskId, int preReqId)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/tasks/$taskId/prerequisites/$preReqId", "PUT")
        .then((HttpRequest response) {
      return true;
    });
    return ret;
  }
  
  static Future<bool> saveTaskFile(int taskId, int userId, String fileData)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/tasks/saveFile/$taskId/$userId", "PUT", fileData)
        .then((HttpRequest response) {
      return true;
    });
    return ret;
  }
  
  static Future<bool> trackTask(int taskId, int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/users/$userId/trackedTasks/$taskId", "PUT")
        .then((HttpRequest response) {
          bool success = false;
          if (response.responseText == "1") {
            success = true;
          }
          return success;
        });
    return ret;
  }
}