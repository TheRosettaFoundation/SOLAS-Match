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
}