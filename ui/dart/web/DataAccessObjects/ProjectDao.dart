part of SolasMatchDart;

class ProjectDao
{
  static Future<List<Project>> getProjects()
  {
    
  }
  
  static Future<Project> getProject(int id)
  {
    APIHelper client = new APIHelper(".json");
    Future<Project> project = client.call("Project", 
        "v0/projects/" + id.toString(), "GET")
          .then((HttpRequest response) {
      Project pro = null;
      if (response.status < 400) {
        if (response.responseText != '') {
          Map jsonParsed = JSON.decode(response.responseText);
          pro = ModelFactory.generateProjectFromMap(jsonParsed);
        }
      } else {
        print("Error: getProject returned " + response.status.toString() + " " + response.statusText);
      }
      return pro;
    });
    return project;
  }
  
  static Future<bool> calculateProjectDeadlines(int projectId)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/projects/$projectId/calculateDeadlines", "POST")
        .then((HttpRequest response) {
          if (response.status < 400) {
            return true;
          } else {
            print("Error: calculateProjectDeadlines returned " + 
                response.status.toString() + " " + response.statusText);
            return false;
          }
    });
    return ret;
  }
  
  static Future<Project> createProject(Project project)
  {
    APIHelper client = new APIHelper(".json");
    Future<Project> ret = client.call("Project", "v0/projects", "POST", JSON.encode(project))
        .then((HttpRequest response) {
          Project pro = null;
          if (response.status < 400) {
            if (response.responseText != '') {
              Map jsonParsed = JSON.decode(response.responseText);
              pro = ModelFactory.generateProjectFromMap(jsonParsed);
            }
          } else {
            print("Error: createProject returned " + response.status.toString() + " " + response.statusText);
          }
          return pro;
        });
    return ret;
  }
  
  static Future<bool> deleteProject(int projectId)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/projects/" + projectId.toString(), "DELETE")
        .then((HttpRequest response) {
          if (response.status < 400) {
            return true;
          } else {
            print("Error: deleteProject returned " + response.status.toString() + " " + response.statusText);
            return false;
          }
        });
    return ret;
  }
  
  static Future<bool> uploadProjectFile(int projectId, int userId, String filename, String data)
  {
    APIHelper client = new APIHelper(".json");
    filename = Uri.encodeComponent(filename);
    Future<bool> ret = client.call("", "v0/projects/$projectId/file/$filename/$userId", "PUT", data)
        .then((HttpRequest response) {
          if (response.status < 400) {
            return true;
          } else {
            print("Error: uploadProjectFile returned " + response.status.toString() + " " + response.statusText);
            return false;
          }
        });
    return ret;
  }
  
  static Future<bool> trackProject(int projectId, int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/users/$userId/projects/$projectId", "PUT")
        .then((HttpRequest response) {
          if (response.status < 400) {
            return true;
          } else {
            print("Error: trackProject returned " + response.status.toString() + " " + response.statusText);
            return false;
          }
        });
    return ret;
  }
}