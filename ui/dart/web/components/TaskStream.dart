library SolasMatchDart;

import "package:web_ui/web_ui.dart";
import "dart:async";
import "dart:json";

import '../DataAccessObjects/TaskDao.dart';
import '../DataAccessObjects/ProjectDao.dart';
import '../DataAccessObjects/OrgDao.dart';
import '../DataAccessObjects/LanguageDao.dart';

import '../lib/models/Task.dart';
import '../lib/models/Project.dart';
import '../lib/models/Org.dart';
import '../lib/models/Language.dart';
import '../lib/Settings.dart';
import '../lib/Localisation.dart';

class TaskStream extends WebComponent
{
  static const int limit = 10;
  
  int taskCount = 0;
  String filter = '';
  int userId = 0;
  int selectedTaskTypeFilter = 0;
  int selectedSourceFilter = 0;
  int selectedTargetFilter = 0;
  DateTime currentDateTime;
  @observable String taskOneColour;
  @observable String taskTwoColour;
  @observable String taskThreeColour;
  @observable String taskFourColour;
  @observable bool moreTasks = true;
  @observable List<Task> tasks;
  @observable Map<int, String> taskAges;
  @observable Map<int, Project> projectMap;
  @observable Map<int, Organisation> orgMap;
  @observable List<Language> activeLanguages;
  @observable Map<int, String> taskTypes;
  @observable List<int> taskTypeIndexes;
  
  TaskStream()
  {
    currentDateTime = new DateTime.now();
    tasks = toObservable(new List<Task>());
    taskAges = toObservable(new Map<int, String>());
    projectMap = toObservable(new Map<int, Project>());    
    orgMap = toObservable(new Map<int, Organisation>());
    activeLanguages = toObservable(new List<Language>());
    taskTypes = toObservable(new Map<int, String>());
    taskTypeIndexes = toObservable(new List<int>());
  }
  
  void inserted()
  {
    Settings settings = new Settings();
    settings.loadConf().then((e) {
      loadActiveLanguages();
      addTasks();
      taskTypeIndexes.add(0);
      taskTypes[0] = Localisation.getTranslation("index_any");
      taskTypeIndexes.add(1);
      taskTypes[1] = Localisation.getTranslation("common_segmentation");
      taskOneColour = settings.conf.task_colours.colour_1;
      taskTypeIndexes.add(2);
      taskTypes[2] = Localisation.getTranslation("common_translation");
      taskTwoColour = settings.conf.task_colours.colour_2;
      taskTypeIndexes.add(3);
      taskTypes[3] = Localisation.getTranslation("common_proofreading");
      taskThreeColour = settings.conf.task_colours.colour_3;
      taskTypeIndexes.add(4);
      taskTypes[4] = Localisation.getTranslation("common_desegmentation");
      taskFourColour = settings.conf.task_colours.colour_4;
    });
  }
  
  void loadActiveLanguages()
  {
    Language any = new Language();
    any.name = Localisation.getTranslation("index_any");
    any.code = "";
    activeLanguages.add(any);
    LanguageDao.getActiveLanguages().then((List<Language> langs) {
      langs.forEach((Language lang) {
        activeLanguages.add(lang);
      });
    });
  }
  
  void addTasks()
  {
    int offset = taskCount;
    if (userId > 0) {
      TaskDao.getUserTopTasks(userId, offset, limit, filter)
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
    taskCount++;
    if (!projectMap.containsKey(task.projectId)) {
      ProjectDao.getProject(task.projectId).then((Project proj) {
        projectMap[proj.id] = proj;
        OrgDao.getOrg(proj.organisationId).then((Organisation org) {
          orgMap[org.id] = org;
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
      filter += "sourceLanguage:" + activeLanguages.elementAt(selectedSourceFilter).code + ";";
    }
    if (selectedTargetFilter > 0) {
      filter += "targetLanguage:" + activeLanguages.elementAt(selectedTargetFilter).code + ";";
    }
    tasks.clear();
    taskCount = 0;
    moreTasks = true;
    TaskDao.getUserTopTasks(userId, taskCount, limit, filter)
            .then((List<Task> userTasks) => processTaskList(userTasks));
  }
}