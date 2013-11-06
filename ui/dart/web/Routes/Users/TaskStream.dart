library SolasMatchDart;

import "package:polymer/polymer.dart";
import "dart:async";
import "dart:html";

import '../../DataAccessObjects/TaskDao.dart';
import '../../DataAccessObjects/ProjectDao.dart';
import '../../DataAccessObjects/OrgDao.dart';
import '../../DataAccessObjects/LanguageDao.dart';

import '../../lib/models/Task.dart';
import '../../lib/models/Tag.dart';
import '../../lib/models/Project.dart';
import '../../lib/models/Org.dart';
import '../../lib/models/Language.dart';
import '../../lib/Settings.dart';
import '../../lib/Localisation.dart';

@CustomTag("task-stream")
class TaskStream extends PolymerElement
{
  static const int limit = 10;
  
  String siteAddress;
  int taskCount = 0;
  String filter = '';
  DateTime currentDateTime;
  @published int userid = 0;
  @observable bool loaded = false;
  @observable Localisation localisation;
  @observable bool moreTasks = true;
  @observable List<Task> tasks;
  @observable Map<int, String> taskAges;
  @observable Map<int, Project> projectMap;
  @observable Map<int, Organisation> orgMap;
  @observable int selectedTaskTypeFilter = 0;
  @observable int selectedSourceFilter = 0;
  @observable int selectedTargetFilter = 0;
  @observable List<Language> activeSourceLanguages;
  @observable List<Language> activeTargetLanguages;
  @observable Map<int, String> taskTypes;
  @observable Map<int, String> taskColours;
  @observable List<int> taskTypeIndexes;
  @observable Map<int, List<Tag>> taskTags;
  
  TaskStream.created() : super.created()
  {
    var root = getShadowRoot("task-stream");
    root.applyAuthorStyles = true;
    
    currentDateTime = new DateTime.now();
    tasks = toObservable(new List<Task>());
    taskAges = toObservable(new Map<int, String>());
    projectMap = toObservable(new Map<int, Project>());    
    orgMap = toObservable(new Map<int, Organisation>());
    activeSourceLanguages = toObservable(new List<Language>());
    activeTargetLanguages = toObservable(new List<Language>());
    taskTypes = toObservable(new Map<int, String>());
    taskColours = toObservable(new Map<int, String>());
    taskTypeIndexes = toObservable(new List<int>());
    taskTags = toObservable(new Map<int, List<Tag>>());
  }
  
  void enteredView()
  {
    localisation = new Localisation();
    loadActiveLanguages();
    addTasks();
    Settings settings = new Settings();
    siteAddress = settings.conf.urls.SiteLocation;
    taskTypeIndexes.add(0);
    taskTypes[0] = localisation.getTranslation("index_any");
    taskTypeIndexes.add(1);
    taskTypes[1] = localisation.getTranslation("common_segmentation");
    taskColours[1] = settings.conf.task_colours.colour_1;
    taskTypeIndexes.add(2);
    taskTypes[2] = localisation.getTranslation("common_translation");
    taskColours[2] = settings.conf.task_colours.colour_2;
    taskTypeIndexes.add(3);
    taskTypes[3] = localisation.getTranslation("common_proofreading");
    taskColours[3] = settings.conf.task_colours.colour_3;
    taskTypeIndexes.add(4);
    taskTypes[4] = localisation.getTranslation("common_desegmentation");
    taskColours[4] = settings.conf.task_colours.colour_4;
  }
  
  void loadActiveLanguages()
  {
    Language any = new Language();
    any.name = localisation.getTranslation("index_any").toString();
    any.code = "";
    
    activeSourceLanguages.add(any);
    activeTargetLanguages.add(any);
    LanguageDao.getActiveSourceLanguages().then((List<Language> langs) {
      activeSourceLanguages.addAll(langs);
    });
    LanguageDao.getActiveTargetLanguages().then((List<Language> langs) {
      activeTargetLanguages.addAll(langs);
    });
  }
  
  void addTasks()
  {
    int offset = taskCount;
    if (userid > 0) {
      TaskDao.getUserTopTasks(userid, offset, limit, filter)
              .then((List<Task> userTasks) => processTaskList(userTasks));
    } else {
      TaskDao.getLatestAvailableTasks(offset, limit)
              .then((List<Task> tasks) => processTaskList(tasks));
    }
  }
  
  void processTaskList(List<Task> tasks)
  {
    if (tasks.length > 0) {
      if (tasks.length < limit) {
        moreTasks = false;
      }
      if (tasks.length > 0) {
        tasks.forEach((Task task) {
          addTask(task);
        });
      }
    } else {
      moreTasks = false;
    }
    loaded = true;
  }
  
  void addTask(Task task)
  {
    tasks.add(task);
    DateTime taskTime = DateTime.parse(task.createdTime);
    Duration dur = currentDateTime.difference(taskTime);
    if (dur.inDays > 0) {
      taskAges[task.id] = dur.inDays.toString() + " day(s)";
    } else if (dur.inHours > 0) {
      taskAges[task.id] = dur.inHours.toString() + " hour(s)";
    } else if (dur.inMinutes > 0) {
      taskAges[task.id] = dur.inMinutes.toString() + " minutes(s)";
    } else {
      taskAges[task.id] = dur.inSeconds.toString() + " second(s)";
    }
    taskTags[task.id] = new List<Tag>();
    TaskDao.getTaskTags(task.id).then((List<Tag> tags) {
      taskTags[task.id] = tags;
    });
    taskCount++;
    if (!projectMap.containsKey(task.projectId)) {
      ProjectDao.getProject(task.projectId).then((Project proj) {
        projectMap[proj.id] = proj;
        OrgDao.getOrg(proj.organisationId).then((Organisation org) {
          orgMap[org.id] = org;
          AnchorElement orgPage = this.shadowRoot.querySelector("#org-" + task.id.toString());
          if (orgPage != null) {
            orgPage.href = siteAddress + "org/" + org.id.toString() + "/profile";
          } else {
            print("Org page is null");
          }
        });
      });
    }
  }
  
  void filterStream()
  {
    filter = "";
    if (selectedTaskTypeFilter > 0) {
      filter += "taskType:" + selectedTaskTypeFilter.toString() + ";";                
    }
    if (selectedSourceFilter > 0) {
      filter += "sourceLanguage:" + activeSourceLanguages.elementAt(selectedSourceFilter).code + ";";
    }
    if (selectedTargetFilter > 0) {
      filter += "targetLanguage:" + activeTargetLanguages.elementAt(selectedTargetFilter).code + ";";
    }
    tasks.clear();
    taskCount = 0;
    moreTasks = true;
    TaskDao.getUserTopTasks(userid, taskCount, limit, filter)
            .then((List<Task> userTasks) => processTaskList(userTasks));
  }
}
