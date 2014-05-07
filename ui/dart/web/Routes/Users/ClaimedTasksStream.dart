import "dart:async";
import "dart:html";

import '../../lib/SolasMatchDart.dart';
import "package:sprintf/sprintf.dart";
import "package:polymer/polymer.dart";

@CustomTag("claimed-tasks-stream")
class ClaimedTasksStream extends PolymerElement
{
  //fields to store current DateTime and site address
  DateTime currentDateTime;
  String siteAddress;
  
  @published int userid = 0;
  @published int tasksperpage = 10;
  
  //observable variables to store info about tasks, how to display them, etc
  @observable List<Task> tasks;
  @observable Map<int, String> taskAges;
  @observable Map<int, Project> projectMap;
  @observable Map<int, Organisation> orgMap;
  @observable Map<int, List<Tag>> taskTags;
  @observable Map<int, String> taskColours;
  @observable Map<int, String> taskTypes;
  @observable Map<int, String> taskStatuses;
  @observable Localisation localisation;
  @observable int currentPage = 0;
  @observable int lastPage = 0;

  /**
   * The constructor for ClaimedTasksStream, handling initialisation of variables.
   */
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
  
  /**
   * Called when by the DOM when the ClaimedTaskStream element has been inserted into the "live document".
   */
  void enteredView()
  {
    //initialise localisation object and load various info from strings file via it, and other info
    //from conf file
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
    //start on the first page
    currentPage = 0;
    if (tasksperpage > 0) {
      //Check how many claimed tasks the user has and then set up pagination appropriately.
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
    //Get the tasks to be displayed
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
  
  /**
   * This function is used to add tasks to the task list.
   */
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
      taskAges[task.id] = sprintf(localisation.getTranslation("common_added_days"), [dur.inDays.toString()]);
    } else if (dur.inHours > 0) {
      taskAges[task.id] = sprintf(localisation.getTranslation("common_added_hours"), [dur.inHours.toString()]);
    } else if (dur.inMinutes > 0) {
      taskAges[task.id] = sprintf(localisation.getTranslation("common_added_minutes"), [dur.inMinutes.toString()]);
    } else {
      taskAges[task.id] = sprintf(localisation.getTranslation("common_added_seconds"), [dur.inSeconds.toString()]);
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