import "package:polymer/polymer.dart";
import "dart:async";
import "dart:html";

import '../../lib/SolasMatchDart.dart';

@CustomTag("claimed-tasks-stream")
class ClaimedTasksStream extends PolymerElement
{
  int taskTypeCount = 4;
  DateTime currentDateTime;
  String siteAddress;
  String taskOneColour;
  String taskTwoColour;
  String taskThreeColour;
  String taskFourColour;
  
  @published int userid = 0;
  @published int tasksperpage = 10;
  
  @observable List<Task> tasks;
  @observable Map<int, String> taskAges;
  @observable Map<int, Project> projectMap;
  @observable Map<int, Organisation> orgMap;
  @observable Map<int, List<Tag>> taskTags;
  @observable Map<int, String> taskColours;
  @observable Map<int, String> taskTypes;
  @observable Localisation localisation;
  @observable int currentPage = 0;
  @observable int lastPage = 0;

  ClaimedTasksStream.created() : super.created()
  {
    currentDateTime = new DateTime.now();
    tasks = toObservable(new List<Task>());
    taskAges = toObservable(new Map<int, String>());
    projectMap = toObservable(new Map<int, Project>());
    orgMap = toObservable(new Map<int, Organisation>());
    taskTags = toObservable(new Map<int, List<Tag>>());
    taskColours = toObservable(new Map<int, String>());
    taskTypes = toObservable(new Map<int, String>());
  }
  
  void enteredView()
  {
    localisation = new Localisation();
    Settings settings = new Settings();
    siteAddress = settings.conf.urls.SiteLocation;
    taskColours[1] = settings.conf.task_colours.colour_1;
    taskColours[2] = settings.conf.task_colours.colour_2;
    taskColours[3] = settings.conf.task_colours.colour_3;
    taskColours[4] = settings.conf.task_colours.colour_4;
    taskTypes[1] = localisation.getTranslation("common_segmentation");
    taskTypes[2] = localisation.getTranslation("common_translation");
    taskTypes[3] = localisation.getTranslation("common_proofreading");
    taskTypes[4] = localisation.getTranslation("common_desegmentation");
    List<Future<bool>> successList = new List<Future<bool>>();
    currentPage = 0;
    if (tasksperpage > 0) {
      successList.add(UserDao.getUserClaimedTasksCount(userid)
      .then((int count) {
        bool success = false;
        if (count > 0) {
          num tmp = count / tasksperpage;
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
        AnchorElement button;
        button = querySelector("#firstPage");
        button.onClick.listen((e) => this.goToFirstPage());
        button = querySelector("#previousPage");
        button.onClick.listen((e) => this.goToPreviousPage());
        button = querySelector("#nextPage");
        button.onClick.listen((e) => this.goToNextPage());
        button = querySelector("#lastPage");
        button.onClick.listen((e) => this.goToLastPage());
      });
  }
  
  Future<bool> getClaimedTasks()
  {
    Future<bool> ret;
    if (userid > 0) {
      ret = this.addTasks();
    } else {
      ret = new Future.value(false);
    }
    return ret;
  }
  
  Future<bool> addTasks()
  {
    int offset = currentPage * tasksperpage;
    Future<bool> ret = UserDao.getUserTasks(userid, offset, tasksperpage)
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
    return ret;
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
      button = querySelector("#firstPage");
      button.parent.classes.add("disabled");
      button = querySelector("#previousPage");
      button.parent.classes.add("disabled");
    } else {
      AnchorElement button;
      button = querySelector("#firstPage");
      button.parent.classes.remove("disabled");
      button = querySelector("#previousPage");
      button.parent.classes.remove("disabled");
    }
    
    if (currentPage >= lastPage - 1) {
      AnchorElement button;
      button = querySelector("#nextPage");
      button.parent.classes.add("disabled");
      button = querySelector("#lastPage");
      button.parent.classes.add("disabled");
    } else {
      AnchorElement button;
      button = querySelector("#nextPage");
      button.parent.classes.remove("disabled");
      button = querySelector("#lastPage");
      button.parent.classes.remove("disabled");
    }
  }
  
  void goToFirstPage()
  {
    if (currentPage != 0) {
      currentPage = 0;
      this.addTasks();
    }
  }
  
  void goToPreviousPage()
  {
    if (currentPage > 0) {
      currentPage--;
      this.addTasks();
    }
  }
  
  void goToNextPage()
  {
    if (currentPage < lastPage - 1) {
      currentPage++;
      this.addTasks();
    }
  }
  
  void goToLastPage()
  {
    if (currentPage < lastPage - 1) {
      currentPage = lastPage - 1;
      this.addTasks();
    }
  }
}