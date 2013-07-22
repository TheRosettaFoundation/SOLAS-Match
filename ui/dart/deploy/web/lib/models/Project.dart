library SolasMatchDart;


import "Tag.dart";
import "Locale.dart";

class Project
{
  int id;
  String title;
  String description;
  String deadline;
  int organisationId;
  String impact;
  String reference;
  int wordCount;
  String createdTime;
  String status;
  Locale sourceLocale;
  List<Tag> tag;
  
  dynamic toJson()
  {
    return {
      "id" : id,
      "title" : title,
      "description" : description,
      "deadline" : deadline,
      "organisationId" : organisationId,
      "impact" : impact,
      "reference" : reference,
      "wordCount" : wordCount,
      "createdTime" : createdTime,
      "status" : status,
      "sourceLocale" : sourceLocale,
    };
  }
}