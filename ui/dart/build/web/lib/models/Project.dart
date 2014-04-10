part of SolasMatchDart;

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
  
  Project()
  {
    title = "";
    description = "";
    deadline = "";
    impact = "";
    reference = "";
    createdTime = "";
    status = "";
    tag = new List<Tag>();
  }
  
  dynamic toJson()
  {
    dynamic ret = {
      "id":id,
      "title":title,
      "description" : description,
      "deadline" : deadline,
      "organisationId" : organisationId,
      "impact" : impact,
      "reference" : reference,
      "wordCount" : wordCount,
      "createdTime" : createdTime,
      "status" : status,
      "sourceLocale" : sourceLocale
    };
    if (tag != null && tag.length > 0) {
      ret["tag"] = tag;
    }
    return ret;
  }
}