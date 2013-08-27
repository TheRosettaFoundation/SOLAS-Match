library SolasMatchDart;

import "dart:async";
import "dart:html";
import "dart:json" as json;

import "../lib/models/Project.dart";
import "../lib/ModelFactory.dart";
import "../lib/APIHelper.dart";

class ProjectDao
{
  static Future<List<Project>> getProjects()
  {
    
  }
  
  static Future<Project> getProject(int id)
  {
    APIHelper client = new APIHelper(".json");
    Future<Project> project = client.call("Project", 
        "v0/projects/" + id.toString(), "GET", "", new Map())
          .then((HttpRequest response) {
      Project pro = new Project();
      if (response.responseText != '') {
        Map jsonParsed = json.parse(response.responseText);
        pro = ModelFactory.generateProjectFromMap(jsonParsed);
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
      return true;
    });
    return ret;
  }
  
  static Future<Project> createProject(Project project)
  {
    APIHelper client = new APIHelper(".json");
    Future<Project> ret = client.call("Project", "v0/projects", "POST", json.stringify(project))
        .then((HttpRequest response) {
          Project pro = new Project();
          if (response.responseText != '') {
            Map jsonParsed = json.parse(response.responseText);
            pro = ModelFactory.generateProjectFromMap(jsonParsed);
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
          return true;
        });
    return ret;
  }
  
  static Future<bool> uploadProjectFile(int projectId, int userId, String filename, String data)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/projects/$projectId/file/$filename/$userId", "PUT", data)
        .then((HttpRequest response) {
          return true;
        });
    return ret;
  }
  
  static Future<bool> trackProject(int projectId, int userId)
  {
    APIHelper client = new APIHelper(".json");
    Future<bool> ret = client.call("", "v0/users/$userId/projects/$projectId", "PUT")
        .then((HttpRequest response) {
          return true;
        });
    return ret;
  }
}