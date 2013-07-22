library SolasMatchDart;

import "dart:async";
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
    Future<String> jsonData = client.call("Project", 
        "v0/projects/" + id.toString(), "GET", "", new Map());
    Future<Project> project = jsonData.then((String jsonText) {
      Project pro = new Project();
      if (jsonText != '') {
        Map jsonParsed = json.parse(jsonText);
        pro = ModelFactory.generateProjectFromMap(jsonParsed);
      }
      return pro;
    });
    return project;
  }
}