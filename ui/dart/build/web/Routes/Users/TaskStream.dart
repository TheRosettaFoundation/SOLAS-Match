import '../../lib/SolasMatchDart.dart';

import "dart:async";
import "dart:html";
import "package:polymer/polymer.dart";
import "package:sprintf/sprintf.dart";

@CustomTag("task-stream")
class TaskStream extends PolymerElement with ChangeNotifier 
{
  static const int limit = 10;
  
  String siteAddress;
  int taskCount = 0;
  String filter = '';
  DateTime currentDateTime;
  Map<int, String> taskAges;
  Map<int, Project> projectMap;
  Map<int, Organisation> orgMap;
  @reflectable @published int get userid => __$userid; int __$userid = 0; @reflectable set userid(int value) { __$userid = notifyPropertyChange(#userid, __$userid, value); }
  @reflectable @observable bool get loaded => __$loaded; bool __$loaded = false; @reflectable set loaded(bool value) { __$loaded = notifyPropertyChange(#loaded, __$loaded, value); }
  @reflectable @observable Localisation get localisation => __$localisation; Localisation __$localisation; @reflectable set localisation(Localisation value) { __$localisation = notifyPropertyChange(#localisation, __$localisation, value); }
  @reflectable @observable bool get moreTasks => __$moreTasks; bool __$moreTasks = true; @reflectable set moreTasks(bool value) { __$moreTasks = notifyPropertyChange(#moreTasks, __$moreTasks, value); }
  @reflectable @observable List<Task> get tasks => __$tasks; List<Task> __$tasks; @reflectable set tasks(List<Task> value) { __$tasks = notifyPropertyChange(#tasks, __$tasks, value); }
  @reflectable @observable int get selectedTaskTypeFilter => __$selectedTaskTypeFilter; int __$selectedTaskTypeFilter = 0; @reflectable set selectedTaskTypeFilter(int value) { __$selectedTaskTypeFilter = notifyPropertyChange(#selectedTaskTypeFilter, __$selectedTaskTypeFilter, value); }
  @reflectable @observable int get selectedSourceFilter => __$selectedSourceFilter; int __$selectedSourceFilter = 0; @reflectable set selectedSourceFilter(int value) { __$selectedSourceFilter = notifyPropertyChange(#selectedSourceFilter, __$selectedSourceFilter, value); }
  @reflectable @observable int get selectedTargetFilter => __$selectedTargetFilter; int __$selectedTargetFilter = 0; @reflectable set selectedTargetFilter(int value) { __$selectedTargetFilter = notifyPropertyChange(#selectedTargetFilter, __$selectedTargetFilter, value); }
  @reflectable @observable List<Language> get activeSourceLanguages => __$activeSourceLanguages; List<Language> __$activeSourceLanguages; @reflectable set activeSourceLanguages(List<Language> value) { __$activeSourceLanguages = notifyPropertyChange(#activeSourceLanguages, __$activeSourceLanguages, value); }
  @reflectable @observable List<Language> get activeTargetLanguages => __$activeTargetLanguages; List<Language> __$activeTargetLanguages; @reflectable set activeTargetLanguages(List<Language> value) { __$activeTargetLanguages = notifyPropertyChange(#activeTargetLanguages, __$activeTargetLanguages, value); }
  @reflectable @observable Map<int, String> get taskTypes => __$taskTypes; Map<int, String> __$taskTypes; @reflectable set taskTypes(Map<int, String> value) { __$taskTypes = notifyPropertyChange(#taskTypes, __$taskTypes, value); }
  @reflectable @observable Map<int, String> get taskColours => __$taskColours; Map<int, String> __$taskColours; @reflectable set taskColours(Map<int, String> value) { __$taskColours = notifyPropertyChange(#taskColours, __$taskColours, value); }
  @reflectable @observable List<int> get taskTypeIndexes => __$taskTypeIndexes; List<int> __$taskTypeIndexes; @reflectable set taskTypeIndexes(List<int> value) { __$taskTypeIndexes = notifyPropertyChange(#taskTypeIndexes, __$taskTypeIndexes, value); }
  @reflectable @observable Map<int, List<Tag>> get taskTags => __$taskTags; Map<int, List<Tag>> __$taskTags; @reflectable set taskTags(Map<int, List<Tag>> value) { __$taskTags = notifyPropertyChange(#taskTags, __$taskTags, value); }
  
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
  }
  
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
        if (projectMap.containsKey(task.projectId)){
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
