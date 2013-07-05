library SolasMatchDart;

import "Locale.dart";

class Task
{
  int id;
  int projectId;
  String title;
  String comment;
  String deadline;
  int wordCount;
  String createdTime;
  Locale sourceLocale;
  Locale targetLocale;
  int taskType;
  int taskStatus;
  bool published;
  
  String print()
  {
    String ret = "{ Task : { id : " + id.toString() + ", projectId : " + projectId.toString() +
        ", title : " + title + ", comment : " + comment + ", deadline : " + deadline + 
        ", wordCount : " + wordCount.toString() + ", created : " + createdTime + ", sourceLocale : "
        + sourceLocale.print() + ", targetLocale : " + targetLocale.print() + ", type : " +
        taskType.toString() + ", status : " + taskStatus.toString() + ", published : " +
        published.toString() + "} }";
    return ret;
  }
}