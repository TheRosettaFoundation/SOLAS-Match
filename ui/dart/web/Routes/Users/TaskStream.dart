import '../../lib/SolasMatchDart.dart';

import "dart:async";
import "dart:html";
import "package:polymer/polymer.dart";
import "package:sprintf/sprintf.dart";

@CustomTag("task-stream")
class TaskStream extends PolymerElement
{
  static const int limit = 10;
  
  String siteAddress;
  int taskCount = 0;
  String filter = '';
  DateTime currentDateTime;
  Map<int, String> taskAges;
  Map<int, Project> projectMap;
  Map<int, Organisation> orgMap;
  @published int userid = 0;
  @published String css;
  @observable bool loaded = false;
  @observable Localisation localisation;
  @observable bool moreTasks = true;
  @observable List<Task> tasks;
  @observable int selectedTaskTypeFilter = 0;
  @observable int selectedSourceFilter = 0;
  @observable int selectedTargetFilter = 0;
  @observable List<Language> activeSourceLanguages;
  @observable List<Language> activeTargetLanguages;
  @observable Map<int, String> taskTypes;
  @observable Map<int, String> taskColours;
  @observable List<int> taskTypeIndexes;
  @observable Map<int, List<Tag>> taskTags;
  
  /**
   * The constructor for TaskStream, handling initialisation of variables.
   */
  TaskStream.created() : super.created()
  {
    currentDateTime = new DateTime.now();
    taskAges = new Map<int, String>();
    projectMap = new Map<int, Project>();    
    orgMap = new Map<int, Organisation>();
    tasks = toObservable(new List<Task>());
    activeSourceLanguages = toObservable(new List<Language>());
    activeTargetLanguages = toObservable(new List<Language>());
    taskTypes = toObservable(new Map<int, String>());
    taskColours = toObservable(new Map<int, String>());
    taskTypeIndexes = toObservable(new List<int>());
    taskTags = toObservable(new Map<int, List<Tag>>());
  }
  
  /**
   * Called by the DOM when the [TaskStream] element has been inserted into "live document".
   */
  void enteredView()
  {
    localisation = new Localisation();
    loadActiveLanguages();
    addTasks();
    Settings settings = new Settings();
    siteAddress = settings.conf.urls.SiteLocation;
    taskTypeIndexes.add(0);
    taskTypes[0] = localisation.getTranslation("index_any_task_type");
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
    
    //import css into polymer element
    String location = settings.conf.urls.SiteLocation;
    if (css != null) {
      css.split(' ').map((path) => new StyleElement()..text = "@import '$location${path}';").forEach(shadowRoot.append);
    }
  }
  
  /**
   * Uses API calls to populate the the source and target language select elements with only "active languages";
   * languages for which there are currently unclaimed tasks.
   */
  void loadActiveLanguages()
  {
    Language anySource = new Language();
    anySource.name = localisation.getTranslation("index_any_source_language");
    anySource.code = "";
    activeSourceLanguages.add(anySource);
    
    Language anyTarget = new Language();
    anyTarget.name = localisation.getTranslation("index_any_target_language");
    anyTarget.code = "";
    activeTargetLanguages.add(anyTarget);
    
    LanguageDao.getActiveSourceLanguages().then((List<Language> langs) {
      activeSourceLanguages.addAll(langs);
    });
    LanguageDao.getActiveTargetLanguages().then((List<Language> langs) {
      activeTargetLanguages.addAll(langs);
    });
  }
  
  /**
   * Calls API to get tasks to add to the list of tasks to be displayed; customised for the logged in user
   * using the system's Task score algorithm if there is a user logged in.
   */
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
  
  /**
   * Processes [newTasks] to add those [Task]s to the list of tasks to display, and sets up other data about
   * those tasks; how long ago it was added, etc.
   */
  void processTaskList(List<Task> newTasks)
  {
    if (newTasks.length > 0) {
      if (newTasks.length < limit) {
        moreTasks = false;
      }
      if (newTasks.length > 0) {
        newTasks.forEach((Task task) {
          addTask(task);
        });
      }
    } else {
      moreTasks = false;
    }
    loaded = true;
    
    Timer.run(() {
      tasks.forEach((Task task) {
        ParagraphElement p;
        p = this.shadowRoot.querySelector("#task_age_" + task.id.toString());
        p.children.clear();
        p.appendHtml(taskAges[task.id]);
        p = this.shadowRoot.querySelector("#deadline_" + task.id.toString());
        p.children.clear();
        p.appendHtml(sprintf(localisation.getTranslation("common_due_by"), [task.deadline]));
        if (projectMap.containsKey(task.projectId)) {
          ParagraphElement p;
          p = this.shadowRoot.querySelector("#parents_" + task.id.toString());
          p.children.clear();
          p.appendHtml(sprintf(localisation.getTranslation("common_part_of_for"), 
                               ["${siteAddress}project/${task.projectId}/view",
                                projectMap[task.projectId].title,
                                "${siteAddress}org/${orgMap[projectMap[task.projectId].organisationId].id}/profile",
                                orgMap[projectMap[task.projectId].organisationId].name]));
        }
      });
    });
  }
  
  /**
   * Adds the [Task] object [task] to the list of tasks to be displayed.
   */
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
    taskCount++;
    if (!projectMap.containsKey(task.projectId)) {
      ProjectDao.getProject(task.projectId).then((Project proj) {
        projectMap[proj.id] = proj;
        OrgDao.getOrg(proj.organisationId).then((Organisation org) {
          orgMap[org.id] = org;
          Timer.run(() {
            ParagraphElement p;
            p = this.shadowRoot.querySelector("#parents_" + task.id.toString());
            p.children.clear();
            p.appendHtml(sprintf(localisation.getTranslation("common_part_of_for"), 
                             ["${siteAddress}project/${task.projectId}/view",
                              projectMap[task.projectId].title,
                              "${siteAddress}org/${orgMap[projectMap[task.projectId].organisationId].id}/profile",
                              orgMap[projectMap[task.projectId].organisationId].name]));
          });
        });
      });
    } 
  }
  
  /**
   * Used to filter the task stream based on the selected filter options. Each selected option adds some text
   * to a filter string which is the passed to the API, where the filtering occurs.
   */
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
