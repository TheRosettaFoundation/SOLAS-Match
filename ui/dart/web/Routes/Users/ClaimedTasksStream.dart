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
  String filter;
  bool isFiltered = false;
  int taskCount = 0;
  
  @published int userid = 0;
  @published int tasksperpage = 10;
  @published String css;
  
  //observable variables to store info about tasks, how to display them, etc
  List<Task> filteredTasks;
  List<Task> filteredTasksBackup;
  @observable List<Task> tasks;
  @observable Map<int, String> taskAges;
  @observable Map<int, Project> projectMap;
  @observable Map<int, Organisation> orgMap;
  @observable Map<int, List<Tag>> taskTags;
  @observable Map<int, String> taskColours;
  @observable Map<int, String> taskTypes;
  @observable Map<int, String> statusFilters;
  @observable Map<int, String> taskStatuses;
  @observable List<int> statusFilterIndexes;
  @observable List<int> taskTypeIndexes;
  @observable Localisation localisation;
  @observable int selectedTaskTypeFilter = 0;
  @observable int selectedStatusFilter = 0;
  @observable int currentPage = 0;
  @observable int lastPage = 0;

  /**
   * The constructor for ClaimedTasksStream, handling initialisation of variables.
   */
  ClaimedTasksStream.created() : super.created()
  {
    currentDateTime = new DateTime.now();
    tasks = toObservable(new List<Task>());
    filteredTasks = new List<Task>();
    filteredTasksBackup = new List<Task>();
    taskAges = toObservable(new Map<int, String>());
    projectMap = toObservable(new Map<int, Project>());
    orgMap = toObservable(new Map<int, Organisation>());
    taskTags = toObservable(new Map<int, List<Tag>>());
    taskColours = toObservable(new Map<int, String>());
    taskTypes = toObservable(new Map<int, String>());
    statusFilters = toObservable(new Map<int, String>());
    taskStatuses = toObservable(new Map<int, String>());
    taskTypeIndexes = toObservable(new List<int>());
    statusFilterIndexes = toObservable(new List<int>());
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
    //import css into polymer element
    if (css != null) {
      css.split(' ').map((path) => new StyleElement()..text = "@import '$siteAddress${path}';").forEach(shadowRoot.append);
    }
    
    taskColours[1] = settings.conf.task_colours.colour_1;
    taskColours[2] = settings.conf.task_colours.colour_2;
    taskColours[3] = settings.conf.task_colours.colour_3;
    taskColours[4] = settings.conf.task_colours.colour_4;
    
    taskTypeIndexes.add(0);
    taskTypes[0] = localisation.getTranslation("index_any_task_type");
    taskTypeIndexes.add(1);
    taskTypes[1] = localisation.getTranslation("common_segmentation");
    taskTypeIndexes.add(2);
    taskTypes[2] = localisation.getTranslation("common_translation");
    taskTypeIndexes.add(3);
    taskTypes[3] = localisation.getTranslation("common_proofreading");
    taskTypeIndexes.add(4);
    taskTypes[4] = localisation.getTranslation("common_desegmentation");
    
    taskStatuses[1] = localisation.getTranslation("common_waiting");
    taskStatuses[2] = localisation.getTranslation("common_unclaimed");
    taskStatuses[3] = localisation.getTranslation("common_in_progress");
    taskStatuses[4] = localisation.getTranslation("common_complete");
    
    statusFilters[0] = localisation.getTranslation("common_any_task_status");
    statusFilterIndexes.add(0);
    statusFilters[1] = localisation.getTranslation("common_in_progress");
    statusFilterIndexes.add(1);
    statusFilters[2] = localisation.getTranslation("common_complete");
    statusFilterIndexes.add(2);
    
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
          AnchorElement a = this.shadowRoot.querySelector("#pagination_pages");
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
        button = this.shadowRoot.querySelector("#firstPage");
        button.onClick.listen((e) => this.goToFirstPage());
        button = this.shadowRoot.querySelector("#previousPage");
        button.onClick.listen((e) => this.goToPreviousPage());
        button = this.shadowRoot.querySelector("#nextPage");
        button.onClick.listen((e) => this.goToNextPage());
        button = this.shadowRoot.querySelector("#lastPage");
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
    //Get the user's claimed tasks for the current page and then clear the old page data from bound variables
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
    
    AnchorElement a = this.shadowRoot.querySelector("#pagination_pages");
    a.children.clear();
    a.appendHtml(sprintf(localisation.getTranslation("pagination_page_of"), ["${currentPage + 1}", "$lastPage"]));
    
    return ret;
  }
  
  void addFilteredTasks(int currentPage, int limit)
  {
    print("Entered addFilteredTasks");
    int offset = currentPage * limit;
    print("OFFSET IS $offset AND LIMIT IS $limit");
    List<Task> subset;
    
    //Check how many claimed tasks (matching the filter options selected) the user has and then set up
    //pagination appropriately.
    UserDao.getUserClaimedTasksCount(userid)
      .then((int count) {
        if (count > 0) {
          if (filteredTasks.length >= limit) {
            num tmp = filteredTasks.length / limit;
            lastPage = tmp.ceil();
          } else {
            //Handle single page result set
            lastPage = currentPage;
          }
          
          //Avoid RangeError when the number of tasks post-filtering is less than tasksPerPage OR not 
          //a multiple of tasksPerPage.
          int modulus = filteredTasks.length % limit; 
          print("CURPAGE IS $currentPage AND LASTPAGE IS $lastPage");
          if (modulus > 0 && currentPage == lastPage) {
            subset = filteredTasks.sublist(offset, modulus);
          } else {
            print("Entered else for subset");
            print("filterTasks LENGTH IS ${filteredTasks.length}");
            print("OFFSET + LIMIT IS ${offset + limit}");
            int end = offset + limit;
            if (end > count) {
              end = count;
            }
            subset = filteredTasks.sublist(offset, end);
          }
          
          tasks.clear();
          projectMap.clear();
          orgMap.clear();
          taskAges.clear();
          taskTags.clear();
          
          subset.forEach((Task task) {
            this.addTask(task);
          });
          this.updatePagination();
          Timer.run(() {
            AnchorElement a = this.shadowRoot.querySelector("#pagination_pages");
            a.children.clear();
            a.appendHtml(
              sprintf(localisation.getTranslation("pagination_page_of"),
              ["${currentPage + 1}", "$lastPage"])
            );
          });
        } else {
          ParagraphElement pElem = this.shadowRoot.querySelector('#noTaskText');
          pElem.text = localisation.getTranslation("claimed_tasks_no_tasks_matching_filters");
        }        
    });
  }
  
  /**
   * This function is called by addTasks() to process the addition of each individual task's data
   * to the task stream.
   */
  void addTask(Task task)
  {
    //add the task to the class-level task list.
    tasks.add(task);
    //Determine how long ago the task was added, and set text appropriately
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
    
    //Begin processing task tag data
    taskTags[task.id] = new List<Tag>();
    //Get all the tags of the task and store the resulting list in the taskTags map.
    TaskDao.getTaskTags(task.id).then((List<Tag> tags) {
      taskTags[task.id] = tags;
    });
    if (!projectMap.containsKey(task.projectId)) {
      //If the project map does not contain a key for the current task's project, retreive the data and set it up.
      ProjectDao.getProject(task.projectId).then((Project proj) {
        projectMap[proj.id] = proj;
        //Get the data of the task's project's org and then add it to the orgMap.
        OrgDao.getOrg(proj.organisationId).then((Organisation org) {
          orgMap[org.id] = org;
          //Set up the text with states the task's project and org
          Timer.run(() {
            ParagraphElement p;
            p = this.shadowRoot.querySelector("#parent_" + task.id.toString());
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
      //The projectMap already has the task's project data, no extra DAO calls needed.
      //Set up the text with states the task's project and org
      Timer.run(() {
        ParagraphElement p;
        p = this.shadowRoot.querySelector("#parent_" + task.id.toString());
        p.children.clear();
        p.appendHtml(sprintf(localisation.getTranslation("common_part_of_for"), 
                             ["${siteAddress}project/${task.projectId}/view",
                              projectMap[task.projectId].title,
                              "${siteAddress}org/${orgMap[projectMap[task.projectId].organisationId].id}/profile",
                              orgMap[projectMap[task.projectId].organisationId].name]));
      });
    }
    //Set up the text stating task age and deadline.
    Timer.run(() {
      ParagraphElement p;
      p = this.shadowRoot.querySelector("#task_age_" + task.id.toString());
      p.children.clear();
      p.appendHtml(taskAges[task.id]);
      p = this.shadowRoot.querySelector("#deadline_" + task.id.toString());
      p.children.clear();
      p.appendHtml(sprintf(localisation.getTranslation("common_due_by"), [task.deadline]));
    });
  }
  
  /**
   * This function is used to disable the page buttons of the claimed task stream whenever they should not
   * be clicked.
   */
  void updatePagination()
  {
    if (currentPage < 1) {
      AnchorElement button;
      button = this.shadowRoot.querySelector("#firstPage");
      button.parent.classes.add("disabled");
      button = this.shadowRoot.querySelector("#previousPage");
      button.parent.classes.add("disabled");
    } else {
      AnchorElement button;
      button = this.shadowRoot.querySelector("#firstPage");
      button.parent.classes.remove("disabled");
      button = this.shadowRoot.querySelector("#previousPage");
      button.parent.classes.remove("disabled");
    }
    
    if (currentPage >= lastPage - 1) {
      AnchorElement button;
      button = this.shadowRoot.querySelector("#nextPage");
      button.parent.classes.add("disabled");
      button = this.shadowRoot.querySelector("#lastPage");
      button.parent.classes.add("disabled");
    } else {
      AnchorElement button;
      button = this.shadowRoot.querySelector("#nextPage");
      button.parent.classes.remove("disabled");
      button = this.shadowRoot.querySelector("#lastPage");
      button.parent.classes.remove("disabled");
    }
  }
  
  /**
   * Page navigation function bound to first page button
   */
  void goToFirstPage()
  {
      if (currentPage != 0) {
        currentPage = 0;
        if (!isFiltered) {
          this.addTasks();
        } else {
          this.addFilteredTasks(currentPage, tasksperpage);
        }
      } else if (isFiltered) {
        this.addFilteredTasks(currentPage, tasksperpage);
      }
  }
  
  /**
   * Page navigation function bound to "previous page" button
   */
  void goToPreviousPage()
  {
    if (currentPage > 0) {
      currentPage--;
      if (!isFiltered) {
        this.addTasks();
      } else {
        this.addFilteredTasks(currentPage, tasksperpage);
      }
    }
  }
  
  /**
   * Page navigation function bound to "next page" button
   */
  void goToNextPage()
  {
    if (currentPage < lastPage - 1) {
      currentPage++;
      if (!isFiltered) {
        this.addTasks();
      } else {
        this.addFilteredTasks(currentPage, tasksperpage);
      }
    }
  }
  
  /**
   * Page navigation function bound to "last page" button
   */
  void goToLastPage()
  {
    if (currentPage < lastPage - 1) {
      currentPage = lastPage - 1;
      if (!isFiltered) {
        this.addTasks();
      } else {
        this.addFilteredTasks(currentPage, tasksperpage);
      }
    }
  }
  
  void filterStream()
  {
    filter = "";
    if (isFiltered) {
      filteredTasks.clear();
    }
    
    if (filteredTasksBackup.length == 0) {
      UserDao.getUserTasks(userid, taskCount)
        .then((List<Task> userTasks) {
          filteredTasks = userTasks;
          filteredTasksBackup = userTasks;
          isFiltered = true;
          
          if (selectedTaskTypeFilter > 0) {
            //Remove all tasks but those of the selected type
            filteredTasks.removeWhere((task) => task.taskType != selectedTaskTypeFilter);
          }
              
          if (selectedStatusFilter > 0) {
            //Remove all tasks but those with the selected status
            //The + 2 is to adjust the values so that they match correctly; task status 3 and 4 are in progress and
            //complete, respectively, but on the UI filter they are the 2nd and 3rd options (hence index 1 and 2).
            filteredTasks.removeWhere((task) => task.taskStatus != selectedStatusFilter + 2);
          }
          goToFirstPage();
      });
    }
  }
}