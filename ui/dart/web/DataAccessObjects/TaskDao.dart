part of SolasMatchDart;

class TaskDao
{
  static Future<List<Tag>> getTaskTags(int taskId)
  {
    APIHelper client = new APIHelper('.json');
    Future<List<Tag>> ret = client.call("Tag", 'v0/tasks/$taskId/tags', 'GET')
        .then((HttpRequest response) {
      List<Tag> tags = new List<Tag>();
      if (response.status < 400) {
        if (response.responseText != '') {
          Map jsonParsed = JSON.decode(response.responseText);
          if (jsonParsed.length > 0) {
            jsonParsed['item'].forEach((String data) {            
              Map tag = JSON.decode(data);
              tags.add(ModelFactory.generateTagFromMap(tag));
            });
          }
        }
      } else {
        print("Error: getTaskTags returned " + response.status.toString() + " " + response.statusText);
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
      if (response.status < 400) {
        if (response.responseText.length > 0) {
          Map jsonParsed = JSON.decode(response.responseText);
          if (jsonParsed.length > 0) {
            jsonParsed['item'].forEach((String data) {
              Map task = JSON.decode(data);
              userTasks.add(ModelFactory.generateTaskFromMap(task));
            });
          }
        }
      } else {
        print("Error: getUserTopTasks returned: " + response.status.toString() + " " + response.statusText);
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
      if (response.status < 400) {
        if (response.responseText.length > 0) {
          Map jsonParsed = JSON.decode(response.responseText);
          if (jsonParsed.length > 0) {
            jsonParsed['item'].forEach((String data) {
              Map task = JSON.decode(data);
              tasks.add(ModelFactory.generateTaskFromMap(task));
            });
          }
        }
      } else {
        print("Error: getLatestAvailableTasks returned " + 
            response.status.toString() + " " + response.statusText);
      }
      return tasks;
    });
    return ret;
  }
  
  static Future<Task> createTask(Task task)
  {
    APIHelper client = new APIHelper(".json");
    Future<Task> ret = client.call("Task", "v0/tasks", "POST", JSON.encode(task))
        .then((HttpRequest response) {
          Task task = null;
          if (response.status < 400) {
            if (response.responseText.length > 0) {
              Map jsonParsed = JSON.decode(response.responseText);
              if (jsonParsed.length > 0) {
                task = ModelFactory.generateTaskFromMap(jsonParsed);
              }
            }
          } else {
            throw "Error #" + response.status.toString() + " - " + response.statusText;
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
      if (response.status < 400) {
        return true;
      } else {
        throw "Error #" + response.status.toString() + " - " + response.statusText;
      }
    });
    return ret;
  }
  
  static Future<bool> saveTaskFile(int taskId, int userId, String fileData)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/io/upload/task/$taskId/$userId", "PUT", fileData)
        .then((HttpRequest response) {
      if (response.status < 400) {
        return true;
      } else {
        throw "Error #" + response.status.toString() + " - " + response.statusText;
      }
    });
    return ret;
  }
  
  static Future<bool> trackTask(int taskId, int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/users/$userId/trackedTasks/$taskId", "PUT")
        .then((HttpRequest response) {
          bool success = false;
          if (response.status < 400) {
            if (response.responseText == "1") {
              success = true;
            }
          } else {
            throw "Error #" + response.status.toString() + " - " + response.statusText;
          }
          return success;
        });
    return ret;
  }
}