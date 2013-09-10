library SolasMatchDart;

import "package:web_ui/web_ui.dart";
import "dart:async";
import "dart:html";
import "dart:json";

import '../DataAccessObjects/UserDao.dart';
import '../DataAccessObjects/TaskDao.dart';
import '../DataAccessObjects/ProjectDao.dart';
import '../DataAccessObjects/OrgDao.dart';
import '../DataAccessObjects/LanguageDao.dart';

import '../lib/models/Task.dart';
import '../lib/models/Tag.dart';
import '../lib/models/Project.dart';
import '../lib/models/Org.dart';
import '../lib/models/Language.dart';
import '../lib/Settings.dart';
import '../lib/Localisation.dart';

class ClaimedTasksStream extends WebComponent
{
  int userId = 0;
  int tasksPerPage = 10;
  int taskTypeCount = 4;
  DateTime currentDateTime;
  String siteAddress;
  String taskOneColour;
  String taskTwoColour;
  String taskThreeColour;
  String taskFourColour;
  List<Task> allTasks;
  @observable List<Task> tasks;
  @observable Map<int, String> taskAges;
  @observable Map<int, Project> projectMap;
  @observable Map<int, Organisation> orgMap;
  @observable Map<int, List<Tag>> taskTags;
  @observable int currentPage = 0;
  @observable int lastPage = 0;

  ClaimedTasksStream()
  {
    Settings settings = new Settings();
    currentDateTime = new DateTime.now();
    siteAddress = settings.conf.urls.SiteLocation;
    taskOneColour = settings.conf.task_colours.colour_1;
    taskTwoColour = settings.conf.task_colours.colour_2;
    taskThreeColour = settings.conf.task_colours.colour_3;
    taskFourColour = settings.conf.task_colours.colour_4;
    tasks = toObservable(new List<Task>());
    taskAges = toObservable(new Map<int, String>());
    projectMap = toObservable(new Map<int, Project>());
    orgMap = toObservable(new Map<int, Organisation>());
    taskTags = toObservable(new Map<int, List<Tag>>());
  }
  
  void inserted()
  {
    List<Future<bool>> successList = new List<Future<bool>>();
    currentPage = 0;
    if (tasksPerPage > 0) {
      successList.add(UserDao.getUserClaimedTasksCount(userId)
      .then((int count) {
        bool success = false;
        if (count > 0) {
          num tmp = count / tasksPerPage;
          lastPage = tmp.ceil();
          success = true;
        }
        return success;
      }));
    }
    successList.add(this.getClaimedTasks());
    Future.wait(successList)
      .then((List<bool> successes) {
        bool finished = true;
        successes.forEach((bool tmp) {
          if (!tmp) {
            finished = false;
          }
        });
        if (!finished) {
          print("Something failed");
        }
      });
    AnchorElement button;
    button = query("#firstPage");
    button.onClick.listen((e) => this.goToFirstPage());
    button = query("#previousPage");
    button.onClick.listen((e) => this.goToPreviousPage());
    button = query("#nextPage");
    button.onClick.listen((e) => this.goToNextPage());
    button = query("#lastPage");
    button.onClick.listen((e) => this.goToLastPage());
  }
  
  Future<bool> getClaimedTasks()
  {
    Future<bool> ret;
    if (userId > 0) {
      ret = this.addTasks();
    } else {
      ret = new Future.value(false);
    }
    return ret;
  }
  
  Future<bool> addTasks()
  {
    int offset = currentPage * tasksPerPage;
    return UserDao.getUserTasks(userId, offset, tasksPerPage)
      .then((List<Task> userTasks) {
        tasks.clear();
        projectMap.clear();
        orgMap.clear();
        taskAges.clear();
        taskTags.clear();
        if (userTasks.length > 0) {
          userTasks.forEach((Task task) {
            this.addTask(task);
          });
        }
        this.updatePagination();
        return true;
      });
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
    if (!projectMap.containsKey(task.projectId)) {
      ProjectDao.getProject(task.projectId).then((Project proj) {
        projectMap[proj.id] = proj;
        OrgDao.getOrg(proj.organisationId).then((Organisation org) {
          orgMap[org.id] = org;
        });
      });
    }
  }
  
  void updatePagination()
  {
    if (currentPage < 1) {
      AnchorElement button;
      button = query("#firstPage");
      button.parent.classes.add("disabled");
      button = query("#previousPage");
      button.parent.classes.add("disabled");
    } else {
      AnchorElement button;
      button = query("#firstPage");
      button.parent.classes.remove("disabled");
      button = query("#previousPage");
      button.parent.classes.remove("disabled");
    }
    
    if (currentPage >= lastPage - 1) {
      AnchorElement button;
      button = query("#nextPage");
      button.parent.classes.add("disabled");
      button = query("#lastPage");
      button.parent.classes.add("disabled");
    } else {
      AnchorElement button;
      button = query("#nextPage");
      button.parent.classes.remove("disabled");
      button = query("#lastPage");
      button.parent.classes.remove("disabled");
    }
  }
  
  void goToFirstPage()
  {
    print("Going to first page");
    if (currentPage != 0) {
      currentPage = 0;
      this.addTasks();
    }
  }
  
  void goToPreviousPage()
  {
    print("Going to previous page");
    if (currentPage > 0) {
      currentPage--;
      this.addTasks();
    }
  }
  
  void goToNextPage()
  {
    print("Going to next page");
    if (currentPage < lastPage - 1) {
      currentPage++;
      this.addTasks();
    }
  }
  
  void goToLastPage()
  {
    print("Going to last page");
    if (currentPage < lastPage - 1) {
      currentPage = lastPage - 1;
      this.addTasks();
    }
  }
}