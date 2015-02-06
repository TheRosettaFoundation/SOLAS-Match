part of SolasMatchDart;

/**
 * Class containing methods to modify and access [Task]-related data through the API.
 */
class TaskDao
{
  /**
   * Calls the API to get a [List] of the [Tag]s associated with the [Task] with the id [taskId].
   * 
   * Returns a [Future] whose value will be a [List] of [Tag]s associated with the the [Task] with
   * the id [taskId] if the API sent back a response code less than 400, otherwise prints a [String] 
   * to the browser console showing the response code and status text.
   */
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
  
  /**
   * Calls the API to get the [List] of [Task]s which form the Task Stream for the user corresponding 
   * to [userId].
   * 
   * Returns a [Future] whose value will be a [List] of [Task]s for the Task Stream of the user with id [userId],
   * starting with at position [offset] up to [limit] number of tasks, filtered to only return [Task]s matching
   * the filter string [filter], only used when "Filter Task Stream" button is clicked. If [strict] is true,
   * [Task]s are further filtered such that only those whose languages match the languages the logged in 
   * used has set on their profile. That is, if the API sent back a response code less than 400,
   * otherwise prints a [String] to the browser console showing the response code and status text.
   */
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
  
  /**
   * Calls the API to get a [List] of the most recently created [Task]s. They can be retrieved in batches using
   * [offset] (default 0) and [limit] (default 15).
   * 
   * Returns a [Future] whose value will be a [List] of the most recently created [Task]s if the API sent back
   * a response code less than 400, if the API sent back a response code less than 400, otherwise prints 
   * a [String] to the browser console showing the response code and status text.
   */
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
  
  /**
   * Calls the API to create a task, using the data from the [Task] object [task].
   * 
   * Returns a [Future] whose value will be a [Task] object representing the task that has been created on the
   * database if the API sent back a response code less than 400, otherwise throws a [String] showing the
   * response code and status text.
   */
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
  
  /**
   * Calls the API to make the task corresponding to [preReqId] a prerequisite of the task corresponding
   * to [taskId].
   * 
   * Returns a [Future] whose value will be a [bool], true if the API sent back a response code less than 400,
   * otherwise throws a [String] showing the response code and status text.
   */
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
  
  /**
   * Calls the API to save a task file for the task corresponding to [taskId], recording the userd corresponding
   * to [userId] as the uploader, with [fileData] storing the data of the file.
   * 
   * Returns a [Future] whose value will be a [bool] if the API sent back a response code less than 400,
   * otherwise throws a [String] showing the response code and status text.
   */
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
  
  /**
   * Calls the API to record that the user corresponding to [userId] is tracking the task corresponding to
   * [taskId].
   * 
   * Returns a [Future] whose value will be a [bool]; true if the task was tracked successfully false if not, so
   * long as the API sent back a response code less than 400, otherwise throws a [String] showing the
   * response code and status text.
   */
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
  
  /**
    * Calls the API to get a completed proofread task corresponding to a given translation [Task] with the id [taskId].
    * 
    * Returns a [Future] whose value will be a proofreading [Task], otherwise prints a [String] 
    * to the browser console showing the response code and status text.
    */
   static Future<Task> getProofreadTask(int taskId)
   {
     APIHelper client = new APIHelper('.json');
     Future<Task> ret = client.call("Task", 'v0/tasks/proofreadTask/$taskId', 'GET')
         .then((HttpRequest response) {
       Task task  = null;
       if (response.status < 400) {
         if (response.responseText != '') {
           Map jsonParsed = JSON.decode(response.responseText);
           if (jsonParsed.length > 0) {
             task = ModelFactory.generateTaskFromMap(jsonParsed);
             };
           }
       } else {
         print("Error: getProofreadTask returned " + response.status.toString() + " " + response.statusText);
       }
       return task;
     });
     return ret;
   }
}