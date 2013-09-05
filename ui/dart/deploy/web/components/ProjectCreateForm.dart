library SolasMatchDart;

import "package:web_ui/web_ui.dart";
import "dart:async";
import "dart:html";
import "package:js/js.dart" as js;
import "dart:json" as json;

import '../DataAccessObjects/TaskDao.dart';
import '../DataAccessObjects/ProjectDao.dart';
import '../DataAccessObjects/LanguageDao.dart';
import '../DataAccessObjects/CountryDao.dart';

import '../lib/models/Task.dart';
import '../lib/models/Tag.dart';
import '../lib/models/Project.dart';
import '../lib/models/Org.dart';
import '../lib/models/Language.dart';
import '../lib/models/Locale.dart';
import '../lib/models/Country.dart';
import '../lib/Settings.dart';
import '../lib/Localisation.dart';
import '../lib/TaskTypeEnum.dart';
import '../lib/TaskStatusEnum.dart';

class ProjectCreateForm extends WebComponent
{
  // Bound Attributes
  int userId;
  int orgId;
  int maxFileSize;
  
  // Other
  int maxTargetLanguages;
  String filename;
  String tagList;
  String wordCountInput;
  String orgDashboardLink;
  SelectElement langSelect;
  SelectElement countrySelect;
  @observable bool publish;
  @observable bool trackProject;
  @observable bool loaded;
  @observable Project project;
  @observable List<Language> languages;
  @observable List<Country> countries;
  @observable int targetCount;
  @observable SafeHtml maxTargetsReached;
  
  // Error Variables
  @observable SafeHtml titleError;
  @observable SafeHtml descriptionError;
  @observable SafeHtml wordCountError;
  @observable SafeHtml deadlineError;
  @observable SafeHtml impactError;
  @observable SafeHtml targetLanguageError;
  @observable SafeHtml uniqueLanguageCountryError;
  @observable SafeHtml fileUploadError;
  @observable SafeHtml createProjectError;
  
  ProjectCreateForm()
  {
    project = new Project();
    project.tag = new List<Tag>();
    languages = toObservable(new List<Language>());
    countries = toObservable(new List<Country>());
    maxFileSize = 0;
    loaded = false;
    publish = true;
    trackProject = true;
    targetCount = 0;
    wordCountInput = '';
    tagList = "";
    maxTargetLanguages = 10;
  }
  
  void inserted()
  {
    List<Future<bool>> loadedList = new List<Future<bool>>();
    
    loadedList.add(LanguageDao.getAllLanguages().then((List<Language> langs) {
      languages.addAll(langs);
      return true;
    }));
    
    loadedList.add(CountryDao.getAllCountries().then((List<Country> regions) {
      countries.addAll(regions);
      return true;
    }));
    
    Future.wait(loadedList).then((List<bool> successList) {
      successList.forEach((bool success) {
        if (!success) {
          print("Failed to load some data");
        }
      });
      constructDynamicElements();
    });
  }
  
  void constructDynamicElements()
  {
    langSelect = new SelectElement();
    langSelect.style.width = "400px";
    for (int i = 0; i < languages.length; i++) {
      var option = new OptionElement()
      ..value = languages[i].code
      ..text = languages[i].name;
      langSelect.children.add(option);
    }
    
    countrySelect = new SelectElement();
    countrySelect.style.width = "400px";
    for (int i = 0; i < countries.length; i++) {
      var option = new OptionElement()
      ..value = countries[i].code
      ..text = countries[i].name;
      countrySelect.children.add(option);
    }
    
    DivElement sourceLanguageDiv = query("#sourceLanguageDiv");
    HeadingElement sourceTitle = new HeadingElement.h2()
    ..innerHtml = Localisation.getTranslation("common_source_language") + 
                            ": <span style=\"color: red\">*</span>";
    SelectElement sourceLanguageSelect = langSelect.clone(true);
    sourceLanguageSelect.id = "sourceLanguageSelect";
    SelectElement sourceCountrySelect = countrySelect.clone(true);
    sourceCountrySelect.id = "sourceCountrySelect";
    sourceLanguageDiv.children.add(sourceTitle);
    sourceLanguageDiv.children.add(sourceLanguageSelect);
    sourceLanguageDiv.children.add(sourceCountrySelect);
    
    addMoreTargetLanguages();
    js.context.initDeadlinePicker();
    loaded = true;
  }
  
  void addMoreTargetLanguages()
  {
    if (targetCount < maxTargetLanguages) {
      TableRowElement targetLanguageRow = new TableRowElement()
      ..id = "target_row_$targetCount";
      TableCellElement targetLanguageCell = new TableCellElement();
      SelectElement targetLanguageSelect = langSelect.clone(true);
      targetLanguageSelect.style.width = "400px";
      targetLanguageSelect.id = "target_language_$targetCount";
      SelectElement targetCountrySelect = countrySelect.clone(true);
      targetCountrySelect.style.width = "400px";
      targetCountrySelect.id = "target_country_$targetCount";
      TableCellElement targetTaskTypes = new TableCellElement()
      ..attributes["valign"] = "middle";
      TableElement targetTaskTypesTable = new TableElement();
      TableRowElement taskTypesRow = new TableRowElement()
      ..attributes["align"] = "center";
      TableCellElement segmentationRequired = new TableCellElement()
      ..attributes["valign"] = "middle";
      InputElement segmentationCheckbox = new InputElement(type: "checkbox")
      ..title = Localisation.getTranslation("project_create_10")
      ..id = "segmentation_$targetCount"
      ..onClick.listen((event) => segmentationClicked(event.target));
      TableCellElement translationRequired = new TableCellElement()
      ..attributes["valign"] = "middle";
      InputElement translationCheckbox = new InputElement(type: "checkbox")
      ..title = Localisation.getTranslation("common_create_a_translation_task_for_volunteer_translators_to_pick_up")
      ..id = "translation_$targetCount"
      ..checked = true;
      TableCellElement proofreadingRequired = new TableCellElement()
      ..attributes["valign"] = "middle";
      InputElement proofreadingCheckbox = new InputElement(type: "checkbox")
      ..title = Localisation.getTranslation("common_create_a_proofreading_task_for_evaluating_the_translation_provided_by_a_volunteer")
      ..id = "proofreading_$targetCount"
      ..checked = true;
      
      targetLanguageCell.children.add(targetLanguageSelect);
      targetLanguageCell.children.add(targetCountrySelect);
      targetLanguageRow.children.add(targetLanguageCell);
      segmentationRequired.children.add(segmentationCheckbox);
      translationRequired.children.add(translationCheckbox);
      proofreadingRequired.children.add(proofreadingCheckbox);
      taskTypesRow.children.add(segmentationRequired);
      taskTypesRow.children.add(translationRequired);
      taskTypesRow.children.add(proofreadingRequired);
      targetTaskTypesTable.children.add(taskTypesRow);
      targetTaskTypes.children.add(targetTaskTypesTable);
      targetLanguageRow.children.add(targetTaskTypes);
      
      TableRowElement hrRow = new TableRowElement()
      ..id = "hr_$targetCount"
      ..innerHtml = "<td colspan=\"2\"><hr /></td>";
      
      if (targetCount > 0) {
        TableRowElement lastTarget = query("#target_row_" + (targetCount - 1).toString());
        lastTarget.insertAdjacentElement("afterEnd", targetLanguageRow);
        lastTarget.insertAdjacentElement("afterEnd", hrRow);
      } else {
        TableRowElement targetTitleRow = query("#targetLanguageTitle");
        targetTitleRow.insertAdjacentElement("afterEnd", targetLanguageRow);
        targetTitleRow.insertAdjacentElement("afterEnd", hrRow);
      }
      
      targetCount++;
      if (targetCount >= maxTargetLanguages) {
        maxTargetsReached = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_create_11") + "</span>");
      }
      if (targetCount > 1) {
        ButtonElement removeButton = query("#removeBottomTargetBtn");
        removeButton.disabled = false;
      } else {
        ButtonElement removeButton = query("#removeBottomTargetBtn");
        removeButton.disabled = true;
      }
    }
  }
  
  void removeTargetLanguage()
  {
    if (targetCount > 1) {
      targetCount--;
      TableRowElement targetLanguageRow = query("#target_row_$targetCount");
      TableRowElement hrElement = query("#hr_$targetCount");
      targetLanguageRow.remove();
      hrElement.remove();
      maxTargetsReached = null;
      if (targetCount == 1) {
        ButtonElement removeButton = query("#removeBottomTargetBtn");
        removeButton.disabled = true;
      }
    }
  }
  
  void submitForm()
  {
    createProjectError = null;
    titleError = null;
    descriptionError = null;
    wordCountError = null;
    deadlineError = null;
    impactError = null;
    targetLanguageError = null;
    uniqueLanguageCountryError = null;
    fileUploadError = null;
    maxTargetsReached = null;
    
    validateInput().then((bool success) {
      if (success) {
        project.organisationId = orgId;
        SelectElement sourceLangSelect = query("#sourceLanguageSelect");
        SelectElement sourceCountrySelect = query("#sourceCountrySelect");
        Language sourceLang = languages[sourceLangSelect.selectedIndex];
        Country sourceCountry = countries[sourceCountrySelect.selectedIndex];
        Locale sourceLocale = new Locale();
        sourceLocale.languageName = sourceLang.name;
        sourceLocale.languageCode = sourceLang.code;
        sourceLocale.countryName = sourceCountry.name;
        sourceLocale.countryCode = sourceCountry.code;
        project.sourceLocale = sourceLocale;
        project.organisationId = orgId;
        
        List<String> projectTags = new List<String>();
        if (tagList.length > 0) {
          projectTags = separateTags(tagList);
        }
        if (projectTags.length > 0) {
          projectTags.forEach((String tagName) {
            Tag tag = new Tag();
            tag.label = tagName;
            project.tag.add(tag);
          });
        }
  
        ProjectDao.createProject(project).then((Project pro) {
          if (pro == null || pro.id == null || pro.id < 1) {
            createProjectError = new SafeHtml.unsafe("<span>Failed to create project</span>");
          } else {
            project = pro;          
            List<Future<bool>> successList = new List<Future<bool>>();
            successList.add(uploadProjectFile().then((bool fileUploaded) {
              Future<bool> ret;
              if (fileUploaded) {
                ret = createProjectTasks();
              } else {
                ret = new Future.value(false);
              }
              return ret;
            }));
            
            if (trackProject) {
              successList.add(ProjectDao.trackProject(project.id, userId));
            }
            
            Future<bool> success = Future.wait(successList).then((List<bool> successes) {
              bool ret = true;
              successes.forEach((bool created) {
                if (!created) {
                  ret = false;
                }
              });
              return ret;
            }).catchError((error) {
              print("An error occurred when evaluating project create success list: " + error.toString());
            });
            
            success.then((bool created) {
              if (!created) {
                print("some data failed, deleting project");
                ProjectDao.deleteProject(project.id);
                project.id = null;
              } else {
                ProjectDao.calculateProjectDeadlines(project.id).then((bool deadlinesCalculated) {
                  Settings settings = new Settings();
                  window.location.assign(settings.conf.urls.SiteLocation + "project/" 
                      + project.id.toString() + "/view");
                });
              }
            }).catchError((error) {
              print("An error occured: " + error.toString());
            });
          }
        });
      } else {
        print("Invalid form input");
      }
    });
  }
  
  Future<bool> createProjectTasks()
  {
    Future<bool> success;
    List<Task> createdTasks = new List<Task>();
    List<Future<bool>> successList = new List<Future<bool>>();
    File projectFile = this.getProjectFile();
    FileReader reader = new FileReader();
    String fileText;
    reader.onLoadEnd.listen((e) {
      fileText = reader.result;
    });
    reader.readAsArrayBuffer(projectFile);
    Task templateTask = new Task();
    templateTask.title = project.title;
    templateTask.projectId = project.id;
    templateTask.deadline = project.deadline;
    templateTask.wordCount = project.wordCount;
    templateTask.sourceLocale = project.sourceLocale;
    templateTask.taskStatus = TaskStatusEnum.PENDING_CLAIM.value;
    if (publish) {
      templateTask.published = true;
    } else {
      templateTask.published = false;
    }
    for (int i = 0; i < targetCount; i++) {
      SelectElement targetLanguageSelect = query("#target_language_$i");
      SelectElement targetCountrySelect = query("#target_country_$i");
      Language targetLang = languages[targetLanguageSelect.selectedIndex];
      Country targetCountry = countries[targetCountrySelect.selectedIndex];
      Locale targetLocale = new Locale();
      targetLocale.languageName = targetLang.name;
      targetLocale.languageCode = targetLang.code;
      targetLocale.countryName = targetCountry.name;
      targetLocale.countryCode = targetCountry.code;
      templateTask.targetLocale = targetLocale;
      CheckboxInputElement segmentationCheckbox = query("#segmentation_$i");
      bool segmentationRequired = segmentationCheckbox.checked;
      CheckboxInputElement translationCheckbox = query("#translation_$i");
      bool translationRequired = translationCheckbox.checked;
      CheckboxInputElement proofreadingCheckbox = query("#proofreading_$i");
      bool proofreadingRequired = proofreadingCheckbox.checked;
      if (segmentationRequired) {
        templateTask.taskType = TaskTypeEnum.SEGMENTATION.value;
        successList.add(TaskDao.createTask(templateTask).then((Task segTask) {
          bool ret;
          if (segTask == null || segTask.id == null || segTask.id < 1) {
            createProjectError = Localisation.getTranslationSafe("project_create_13");
            ret = false;
          } else {
            createdTasks.add(segTask);
            TaskDao.saveTaskFile(segTask.id, userId, fileText);
            if (trackProject) {
              TaskDao.trackTask(segTask.id, userId);
            }
            ret = true;
          }
          return ret;
        }));
      } else {
        if (translationRequired) {
          templateTask.taskType = TaskTypeEnum.TRANSLATION.value;
          successList.add(TaskDao.createTask(templateTask).then((Task transTask) {
            Future<bool> ret;
            if (transTask == null || transTask.id == null || transTask.id < 1) {
              createProjectError = Localisation.getTranslationSafe("project_create_14");
              ret = new Future.value(false);
            } else {
              createdTasks.add(transTask);
              TaskDao.saveTaskFile(transTask.id, userId, fileText);
              if (trackProject) {
                TaskDao.trackTask(transTask.id, userId);
              }
              
              if (proofreadingRequired) {
                templateTask.taskType = TaskTypeEnum.PROOFREADING.value;
                templateTask.targetLocale = transTask.targetLocale;
                ret = new Future.sync(() => TaskDao.createTask(templateTask).then((Task proofTask) {
                  Future<bool> ret;
                  if (proofTask == null || proofTask.id == null || proofTask.id < 1) {
                    createProjectError = Localisation.getTranslationSafe("project_create_15"); 
                    ret = new Future.value(false);
                  } else {
                    createdTasks.add(proofTask);
                    TaskDao.saveTaskFile(proofTask.id, userId, fileText);
                    if (trackProject) {
                      TaskDao.trackTask(proofTask.id, userId);
                    }
                    ret = new Future.sync(() => TaskDao.addTaskPreReq(proofTask.id, transTask.id)); 
                  }
                  return ret;
                }));
              } else {
                ret = new Future.value(true);
              }
            }
            return ret;
          }));
        } else if (!translationRequired && proofreadingRequired) {
          templateTask.taskType = TaskTypeEnum.PROOFREADING.value;
          successList.add(TaskDao.createTask(templateTask).then((Task proofTask) {
            bool ret;
            if (proofTask == null || proofTask.id == null || proofTask.id < 1) {
              createProjectError = Localisation.getTranslationSafe("project_create_15");
              ret = false;
            } else {
              createdTasks.add(proofTask);
              TaskDao.saveTaskFile(proofTask.id, userId, fileText);
              if (trackProject) {
                TaskDao.trackTask(proofTask.id, userId);
              }
              ret = true;
            }
            return ret;
          }));
        }
      }
    }
    
    success = Future.wait(successList).then((List<bool> createdList) {
      bool ret = true;
      createdList.forEach((bool created) {
        if (!created) {
          print("Something failed while creating project tasks");
          ret = false;
        }
      });
      return ret;
    });
    return success;
  }
  
  Future<bool> uploadProjectFile()
  {
    Completer<bool> completer = new Completer<bool>();
    File projectFile = this.getProjectFile();
    FileReader reader = new FileReader();
    reader.onLoadEnd.listen((e) {
      ProjectDao.uploadProjectFile(project.id, userId, filename, e.target.result)
        .then((bool success) {
          completer.complete(success);
        });
    });
    reader.readAsArrayBuffer(projectFile);
    return completer.future;
  }
  
  Future<bool> validateInput()
  {
    Future<bool> ret;
    bool success = true;
    //Validate Text Inputs
    if (project.title == '') {
      titleError = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_routehandler_12") + "</span>");
      success = false;
    }
    if (project.description == '') {
      descriptionError = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_routehandler_14") + "</span>");
      success = false;
    }
    if (project.impact == '') {
      impactError = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_routehandler_15") + "</span>");
      success = false;
    }
    if (wordCountInput != null && wordCountInput != '') {
      project.wordCount = int.parse(wordCountInput, onError: (String wordCountString) {
        wordCountError = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_routehandler_16") + "</span>");
        success = false;
        return 0;
      });
      if (project.wordCount > 5000) {
        int i = 0;
        bool segmentationMissing = false;
        CheckboxInputElement segmentationCheckbox; 
        while (i < targetCount && !segmentationMissing) {
          segmentationCheckbox = query("#segmentation_$i");
          if (!segmentationCheckbox.checked) {
            segmentationMissing = true;
          }
          i++;
        }
        if (segmentationMissing && !window.confirm(Localisation.getTranslation("project_create_22"))) {
          success = false;
        }
      }
    } else {
      wordCountError = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_routehandler_16") + "</span>");
      success = false;
    }
    InputElement deadlineInput = query("#deadline");
    if (deadlineInput.value != '') {
      DateTime projectDeadline = parseDeadline(deadlineInput.value);
      if (projectDeadline != null) {
        String monthAsString = projectDeadline.month.toString();
        monthAsString = monthAsString.length == 1 ? "0$monthAsString" : monthAsString;
        String dayAsString = projectDeadline.day.toString();
        dayAsString = dayAsString.length == 1 ? "0$dayAsString" : dayAsString;
        String hourAsString = projectDeadline.hour.toString();
        hourAsString = hourAsString.length > 2 ? "0$hourAsString" : hourAsString;
        String minuteAsString = projectDeadline.minute.toString();
        minuteAsString = minuteAsString.length < 2 ? "0$minuteAsString" : minuteAsString;
        project.deadline = projectDeadline.year.toString() + "-" + monthAsString + "-" + dayAsString
            + " " + hourAsString + ":" + minuteAsString + ":00";
      } else {
        deadlineError = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_routehandler_13") + "</span>");
        success = false;
      }
    } else {
      deadlineError = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_routehandler_13") + "<span>");
      success = false;
    }
    //Validate targets
    List<Language> targetLanguages = new List<Language>();
    List<Country> targetCountries = new List<Country>();
    for (int i = 0; i < targetCount; i++) {
      CheckboxInputElement segmentationCheckbox = query("#segmentation_$i");
      bool segmentationRequired = segmentationCheckbox.checked;
      CheckboxInputElement translationCheckbox = query("#translation_$i");
      bool translationRequired = translationCheckbox.checked;
      CheckboxInputElement proofreadingCheckbox = query("#proofreading_$i");
      bool proofreadingRequired = proofreadingCheckbox.checked;
      if (!segmentationRequired && !translationRequired && !proofreadingRequired) {
        createProjectError = Localisation.getTranslationSafe("project_routehandler_17");
        success = false;
      }
      
      SelectElement targetLanguageSelect = query("#target_language_$i");
      SelectElement targetCountrySelect = query("#target_country_$i");
      Language targetLang = languages[targetLanguageSelect.selectedIndex];
      Country targetCountry = countries[targetCountrySelect.selectedIndex];
      if (targetLanguages.contains(targetLang) && targetCountries.contains(targetCountry)) {
        createProjectError = Localisation.getTranslationSafe("project_routehandler_17");
        success = false;
      } else {
        targetLanguages.add(targetLang);
        targetCountries.add(targetCountry);
      }
    }
    //Validate file input
    ret = new Future.sync(validateFileInput).then((bool valid) {
      return success && valid;
    });
    
    return ret;
  }
  
  Future<bool> validateFileInput()
  {
    Completer<bool> completer = new Completer<bool>();
    File projectFile = this.getProjectFile();
    if (projectFile == null) {
      createProjectError = Localisation.getTranslationSafe("project_create_16");
    }
    
    if (projectFile != null) {
      if (projectFile.size > 0) {
        if (projectFile.size < maxFileSize) {
          int extensionStartIndex = projectFile.name.lastIndexOf(".");
          if (extensionStartIndex >= 0) {
            filename = projectFile.name;
            String extension = filename.substring(extensionStartIndex + 1);
            if (extension != extension.toLowerCase()) {
              extension = extension.toLowerCase();
              filename = filename.substring(0, extensionStartIndex + 1) + extension;
              window.alert(Localisation.getTranslation("project_create_18"));
            }
            bool finished = false;
            if (extension == "pdf") {
              if (!window.confirm(Localisation.getTranslation("project_create_19"))) {
                finished = true;
                completer.complete(false);
              }
            }
            
            if (!finished) {
              completer.complete(true);
            }
          } else {
            createProjectError = Localisation.getTranslationSafe("project_create_20");
            completer.complete(false);
          }
        } else {
          createProjectError = Localisation.getTranslationSafe("project_create_21");
          completer.complete(false);
        }
      } else {
        createProjectError = Localisation.getTranslationSafe("project_create_17");
        completer.complete(false);
      }
    } else {
      createProjectError = new SafeHtml.unsafe("<span>No file uploaded</span>");
      completer.complete(false);
    }
    
    return completer.future;
  }
  
  DateTime parseDeadline(String deadlineText)
  {
    //Assumes deadline is in a format like "31 July 2013 10:50 UTC"
    DateTime ret;
    try {
      int startIndex = 0;
      int endIndex = deadlineText.indexOf(" ");
      String day = deadlineText.substring(startIndex, endIndex).toString();
      startIndex = endIndex + 1;
      endIndex = deadlineText.indexOf(" ", startIndex);
      String month = deadlineText.substring(startIndex, endIndex);
      startIndex = endIndex + 1;
      endIndex = deadlineText.indexOf(" ", startIndex);
      String year = deadlineText.substring(startIndex, endIndex);
      startIndex = endIndex + 1;
      endIndex = deadlineText.indexOf(":", startIndex);
      String hour = deadlineText.substring(startIndex, endIndex);
      startIndex = endIndex + 1;
      endIndex = deadlineText.indexOf(" ", startIndex);
      String minute = deadlineText.substring(startIndex, endIndex);
      
      int monthNum = 0;
      month = month.toLowerCase();
      switch (month) {
        case "january":
          monthNum = 1;
          break;
        case "february":
          monthNum = 2;
          break;
        case "march":
          monthNum = 3;
          break;
        case "april": 
          monthNum = 4;
          break;
        case "may":
          monthNum = 5;
          break;
        case "june":
          monthNum = 6;
          break;
        case "july":
          monthNum = 7;
          break;
        case "august":
          monthNum = 8;
          break;
        case "september":
          monthNum = 9;
          break;
        case "october":
          monthNum = 10;
          break;
        case "november":
          monthNum = 11;
          break;
        case "december":
          monthNum = 12;
          break;
        default:
          monthNum = 0;
      }
      ret = new DateTime(int.parse(year), monthNum, int.parse(day), 
          int.parse(hour), int.parse(minute));
    } catch(e) {
      deadlineError = new SafeHtml.unsafe("<span>" + Localisation.getTranslation("project_routehandler_13") + "</span>");
    }
    return ret;
  }
  
  bool isError()
  {
    bool ret;
    if (titleError == null && descriptionError == null && wordCountError == null && deadlineError == null &&
        impactError == null && targetLanguageError == null && uniqueLanguageCountryError == null && 
        fileUploadError == null) {
      ret = false;
    } else {
      ret = true;
    }
    return ret;
  }
  
  List<String> separateTags(String tags)
  {
    return tags.split(" ");
  }
  
  void segmentationClicked(InputElement target)
  {
    int index = int.parse(target.id.substring(target.id.indexOf("_") + 1));
    InputElement transCheckbox = query("#translation_$index");
    InputElement proofCheckbox = query("#proofreading_$index");
    if (target.checked) {
      transCheckbox.checked = false;
      transCheckbox.disabled = true;
      proofCheckbox.checked = false;
      proofCheckbox.disabled = true;
    } else {
      transCheckbox.disabled = false;
      proofCheckbox.disabled = false;
    }
  }
  
  File getProjectFile()
  {
    File projectFile = null;
    InputElement fileInput = query("#projectFile");
    FileList files = fileInput.files;
    if (!files.isEmpty) {
      projectFile = files[0];
    }
    return projectFile;
  }
}
