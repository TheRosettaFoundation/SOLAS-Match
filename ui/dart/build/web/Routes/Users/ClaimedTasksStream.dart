import "dart:async";
import "dart:html";

import '../../lib/SolasMatchDart.dart';
import "package:sprintf/sprintf.dart";
import "package:polymer/polymer.dart";

@CustomTag("claimed-tasks-stream")
class ClaimedTasksStream extends PolymerElement with ChangeNotifier 
{
  int taskTypeCount = 4;
  DateTime currentDateTime;
  String siteAddress;
  String taskOneColour;
  String taskTwoColour;
  String taskThreeColour;
  String taskFourColour;
  
  @reflectable @published int get userid => __$userid; int __$userid = 0; @reflectable set userid(int value) { __$userid = notifyPropertyChange(#userid, __$userid, value); }
  @reflectable @published int get tasksperpage => __$tasksperpage; int __$tasksperpage = 10; @reflectable set tasksperpage(int value) { __$tasksperpage = notifyPropertyChange(#tasksperpage, __$tasksperpage, value); }
  
  @reflectable @observable List<Task> get tasks => __$tasks; List<Task> __$tasks; @reflectable set tasks(List<Task> value) { __$tasks = notifyPropertyChange(#tasks, __$tasks, value); }
  @reflectable @observable Map<int, String> get taskAges => __$taskAges; Map<int, String> __$taskAges; @reflectable set taskAges(Map<int, String> value) { __$taskAges = notifyPropertyChange(#taskAges, __$taskAges, value); }
  @reflectable @observable Map<int, Project> get projectMap => __$projectMap; Map<int, Project> __$projectMap; @reflectable set projectMap(Map<int, Project> value) { __$projectMap = notifyPropertyChange(#projectMap, __$projectMap, value); }
  @reflectable @observable Map<int, Organisation> get orgMap => __$orgMap; Map<int, Organisation> __$orgMap; @reflectable set orgMap(Map<int, Organisation> value) { __$orgMap = notifyPropertyChange(#orgMap, __$orgMap, value); }
  @reflectable @observable Map<int, List<Tag>> get taskTags => __$taskTags; Map<int, List<Tag>> __$taskTags; @reflectable set taskTags(Map<int, List<Tag>> value) { __$taskTags = notifyPropertyChange(#taskTags, __$taskTags, value); }
  @reflectable @observable Map<int, String> get taskColours => __$taskColours; Map<int, String> __$taskColours; @reflectable set taskColours(Map<int, String> value) { __$taskColours = notifyPropertyChange(#taskColours, __$taskColours, value); }
  @reflectable @observable Map<int, String> get taskTypes => __$taskTypes; Map<int, String> __$taskTypes; @reflectable set taskTypes(Map<int, String> value) { __$taskTypes = notifyPropertyChange(#taskTypes, __$taskTypes, value); }
  @reflectable @observable Map<int, String> get taskStatuses => __$taskStatuses; Map<int, String> __$taskStatuses; @reflectable set taskStatuses(Map<int, String> value) { __$taskStatuses = notifyPropertyChange(#taskStatuses, __$taskStatuses, value); }
  @reflectable @observable Localisation get localisation => __$localisation; Localisation __$localisation; @reflectable set localisation(Localisation value) { __$localisation = notifyPropertyChange(#localisation, __$localisation, value); }
  @reflectable @observable int get currentPage => __$currentPage; int __$currentPage = 0; @reflectable set currentPage(int value) { __$currentPage = notifyPropertyChange(#currentPage, __$currentPage, value); }
  @reflectable @observable int get lastPage => __$lastPage; int __$lastPage = 0; @reflectable set lastPage(int value) { __$lastPage = notifyPropertyChange(#lastPage, __$lastPage, value); }

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
    taskStatuses = toObservable(new Map<int, String>());
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
    taskStatuses[1] = localisation.getTranslation("common_waiting");
    taskStatuses[2] = localisation.getTranslation("common_unclaimed");
    taskStatuses[3] = localisation.getTranslation("common_in_progress");
    taskStatuses[4] = localisation.getTranslation("common_complete");
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
        
        Timer.run(() {
          AnchorElement a = querySelector("#pagination_pages");
          a.children.clear();
          a.appendHtml(sprintf(localisation.getTranslation("pagination_page_of"), ["${currentPage + 1}", "$lastPage"]));
        });
        
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
    
    AnchorElement a = querySelector("#pagination_pages");
    a.children.clear();
    a.appendHtml(sprintf(localisation.getTranslation("pagination_page_of"), ["${currentPage + 1}", "$lastPage"]));
    
    return ret;
  }
  
  void addTask(Task task)
  {
    tasks.add(task);
    DateTime taskTime = DateTime.parse(task.createdTime);
    Duration dur = currentDateTime.difference(taskTime);
    if (dur.inDays > 0) {
      taskAges[task.id] = sprintf(localisation.getTranslation("common_added"), [dur.inDays.toString() + " day(s)"]);
    } else if (dur.inHours > 0) {
      taskAges[task.id] = sprintf(localisation.getTranslation("common_added"), [dur.inHours.toString() + " hour(s)"]);
    } else if (dur.inMinutes > 0) {
      taskAges[task.id] = sprintf(localisation.getTranslation("common_added"), [dur.inMinutes.toString() + " minutes(s)"]);
    } else {
      taskAges[task.id] = sprintf(localisation.getTranslation("common_added"), [dur.inSeconds.toString() + " second(s)"]);
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
          Timer.run(() {
            ParagraphElement p;
            p = querySelector("#parent_" + task.id.toString());
            p.children.clear();
            p.appendHtml(sprintf(localisation.getTranslation("common_part_of_for"), 
                                 ["${siteAddress}project/${task.projectId}/view",
                                  projectMap[task.projectId].title,
                                  "${siteAddress}org/${orgMap[projectMap[task.projectId].organisationId].id}/profile",
                                  orgMap[projectMap[task.projectId].organisationId].name]));
          });
        });
      });
    } else {
      Timer.run(() {
        ParagraphElement p;
        p = querySelector("#parent_" + task.id.toString());
        p.children.clear();
        p.appendHtml(sprintf(localisation.getTranslation("common_part_of_for"), 
                             ["${siteAddress}project/${task.projectId}/view",
                              projectMap[task.projectId].title,
                              "${siteAddress}org/${orgMap[projectMap[task.projectId].organisationId].id}/profile",
                              orgMap[projectMap[task.projectId].organisationId].name]));
      });
    }
    
    Timer.run(() {
      ParagraphElement p;
      p = querySelector("#task_age_" + task.id.toString());
      p.children.clear();
      p.appendHtml(taskAges[task.id]);
      p = querySelector("#deadline_" + task.id.toString());
      p.children.clear();
      p.appendHtml(sprintf(localisation.getTranslation("common_due_by"), [task.deadline]));
    });
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