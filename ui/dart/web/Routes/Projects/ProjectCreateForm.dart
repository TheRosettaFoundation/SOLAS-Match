import "package:polymer/polymer.dart";
import "dart:async";
import "dart:html";

import "package:sprintf/sprintf.dart";
import "../../lib/SolasMatchDart.dart";

@CustomTag('project-create-form')
class ProjectCreateForm extends PolymerElement
{
  // Bound Attributes
  @published int userid;
  @published int orgid;
  @published int maxfilesize;
  
  // Other
  int maxTargetLanguages;
  String filename;
  String tagList;
  String wordCountInput;
  SelectElement langSelect;
  SelectElement countrySelect;
  String projectFileText;
  List<int> monthLengths;
  @observable List<int> years;
  @observable List<String> months;
  @observable List<int> days;
  @observable List<int> hours;
  @observable List<int> minutes;
  @observable int selectedYear = 0;
  @observable int selectedMonth = 0;
  @observable int selectedDay = 0;
  @observable int selectedHour = 0;
  @observable int selectedMinute = 0;
  @observable String orgDashboardLink;
  @observable bool publish;
  @observable bool trackProject;
  @observable bool loaded;
  @observable Project project;
  @observable List<Language> languages;
  @observable List<Country> countries;
  @observable int targetCount;
  @observable String maxTargetsReached;
  @observable Localisation localisation;
  
  // Error Variables
  @observable String titleError;
  @observable String descriptionError;
  @observable String wordCountError;
  @observable String deadlineError;
  @observable String impactError;
  @observable String createProjectError;
  
  ProjectCreateForm.created() : super.created() 
  {
    project = new Project();
    project.tag = new List<Tag>();
    projectFileText = "";
    languages = toObservable(new List<Language>());
    countries = toObservable(new List<Country>());
    DateTime currentDate = new DateTime.now();
    years = toObservable(new List<int>.generate(10, (int index) => index + currentDate.year, growable: false));
    months = toObservable(["January", "February", "March", "April", "May", "June", "July", "August",
                           "September", "October", "November", "December"]);
    selectedMonth = currentDate.month - 1;
    monthLengths = new List<int>(12);
    monthLengths[0] = 31;
    monthLengths[1] = 28;
    monthLengths[2] = 31;
    monthLengths[3] = 30;
    monthLengths[4] = 31;
    monthLengths[5] = 30;
    monthLengths[6] = 31;
    monthLengths[7] = 31;
    monthLengths[8] = 30;
    monthLengths[9] = 31;
    monthLengths[10] = 30;
    monthLengths[11] = 31;
    days = toObservable(new List<int>.generate(monthLengths[selectedMonth], (int index) => index + 1));
    hours = toObservable(new List<int>.generate(24, (int index) => index , growable: false));
    minutes = toObservable(new List<int>.generate(60, (int index) => index, growable: false));
    
    //account for leap years
    this.selectedYearChanged(0);
    
    loaded = false;
    publish = true;
    trackProject = true;
    targetCount = 0;
    wordCountInput = '';
    tagList = "";
    maxTargetLanguages = 10;
  }
  
  void enteredView()
  {
    Loader.load().then((_) {
      Settings settings = new Settings();
      orgDashboardLink = settings.conf.urls.SiteLocation + "org/dashboard";
      localisation = new Localisation();
      
      ParagraphElement p = this.shadowRoot.querySelector("#source_text_desc");
      p.children.clear();
      p.appendHtml(localisation.getTranslation("project_create_6") + " " +
          sprintf(localisation.getTranslation("common_maximum_file_size_is"), ["${maxfilesize / 1024 / 1024}"]));
      
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
    });
  }
  
  void constructDynamicElements()
  {
    langSelect = new SelectElement();
    langSelect.style.width = "400px";
    List<OptionElement> options = new List<OptionElement>();
    for (int i = 0; i < languages.length; i++) {
      var option = new OptionElement()
      ..value = languages[i].code
      ..text = languages[i].name;
      options.add(option);
    }
    langSelect.children.addAll(options);
    
    countrySelect = new SelectElement();
    countrySelect.style.width = "400px";
    options = new List<OptionElement>();
    for (int i = 0; i < countries.length; i++) {
      var option = new OptionElement()
      ..value = countries[i].code
      ..text = countries[i].name;
      options.add(option);
    }
    countrySelect.children.addAll(options);    
    
    DivElement sourceLanguageDiv = this.shadowRoot.querySelector("#sourceLanguageDiv");
    
    HeadingElement sourceTitle = new HeadingElement.h2()
      ..text = localisation.getTranslation("common_source_language") + ": ";
    SpanElement redSpan = new SpanElement()
      ..style.color = "red"
      ..text = "*";
    sourceTitle.children.add(redSpan);
    
    SelectElement sourceLanguageSelect = langSelect.clone(true);
    sourceLanguageSelect.id = "sourceLanguageSelect";
    SelectElement sourceCountrySelect = countrySelect.clone(true);
    sourceCountrySelect.id = "sourceCountrySelect";
    sourceLanguageDiv.children.add(sourceTitle);
    sourceLanguageDiv.children.add(sourceLanguageSelect);
    sourceLanguageDiv.children.add(sourceCountrySelect);
    
    addMoreTargetLanguages();
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
      ..title = localisation.getTranslation("project_create_10")
      ..id = "segmentation_$targetCount"
      ..onClick.listen((event) => segmentationClicked(event.target));
      TableCellElement translationRequired = new TableCellElement()
      ..attributes["valign"] = "middle";
      InputElement translationCheckbox = new InputElement(type: "checkbox")
      ..title = localisation.getTranslation("common_create_a_translation_task_for_volunteer_translators_to_pick_up")
      ..id = "translation_$targetCount"
      ..checked = true;
      translationCheckbox.id ="translation_$targetCount"; 
      TableCellElement proofreadingRequired = new TableCellElement()
      ..attributes["valign"] = "middle";
      InputElement proofreadingCheckbox = new InputElement(type: "checkbox")
      ..title = localisation.getTranslation("common_create_a_proofreading_task_for_evaluating_the_translation_provided_by_a_volunteer")
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
      ..id = "hr_$targetCount";
      TableCellElement td = new TableCellElement()
        ..colSpan = 2;
      HRElement hr = new HRElement();
      td.children.add(hr);
      hrRow.children.add(td);
      
      if (targetCount > 0) {
        TableRowElement lastTarget = this.shadowRoot.querySelector("#target_row_" + (targetCount - 1).toString());
        lastTarget.parent.insertBefore(targetLanguageRow, lastTarget.nextElementSibling);
        lastTarget.parent.insertBefore(hrRow, lastTarget.nextElementSibling);
      } else {
        TableRowElement targetTitleRow = this.shadowRoot.querySelector("#targetLanguageTitle");
        targetTitleRow.parent.insertBefore(targetLanguageRow, targetTitleRow.nextElementSibling);
        targetTitleRow.parent.insertBefore(hrRow, targetTitleRow.nextElementSibling);
      }
      
      targetCount++;
      if (targetCount >= maxTargetLanguages) {
        maxTargetsReached = localisation.getTranslation("project_create_11");
        ButtonElement addBtn = this.shadowRoot.querySelector("#addTargetLanguageBtn");
        addBtn.disabled = true;
      }
      
      ButtonElement removeButton = this.shadowRoot.querySelector("#removeBottomTargetBtn");
      if (removeButton != null) {
        if (targetCount > 1) {
          removeButton.disabled = false;
        } else {
          removeButton.disabled = true;
        }
      }
    }
  }
  
  void removeTargetLanguage()
  {
    if (targetCount > 1) {
      targetCount--;
      TableRowElement targetLanguageRow = this.shadowRoot.querySelector("#target_row_$targetCount");
      TableRowElement hrElement = this.shadowRoot.querySelector("#hr_$targetCount");
      targetLanguageRow.remove();
      hrElement.remove();
      maxTargetsReached = null;
      if (targetCount == 1) {
        ButtonElement removeButton = this.shadowRoot.querySelector("#removeBottomTargetBtn");
        removeButton.disabled = true;
      }
      ButtonElement addBtn = this.shadowRoot.querySelector("#addTargetLanguageBtn");
      if (addBtn.disabled == true) {
        addBtn.disabled = false;
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
    maxTargetsReached = null;
    
    validateInput().then((bool success) {
      if (success) {
        project.organisationId = orgid;
        SelectElement sourceLangSelect = this.shadowRoot.querySelector("#sourceLanguageSelect");
        SelectElement sourceCountrySelect = this.shadowRoot.querySelector("#sourceCountrySelect");
        Language sourceLang = languages[sourceLangSelect.selectedIndex];
        Country sourceCountry = countries[sourceCountrySelect.selectedIndex];
        Locale sourceLocale = new Locale();
        sourceLocale.languageName = sourceLang.name;
        sourceLocale.languageCode = sourceLang.code;
        sourceLocale.countryName = sourceCountry.name;
        sourceLocale.countryCode = sourceCountry.code;
        project.sourceLocale = sourceLocale;
        project.organisationId = orgid;
        
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
            createProjectError = "Failed to create project";
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
              successList.add(ProjectDao.trackProject(project.id, userid));
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
                
                if (createProjectError != null) {
                  Timer.run(() {
                    SpanElement span = this.shadowRoot.querySelector("#project_create_error");
                    span.children.clear();
                    span.appendHtml(createProjectError);
                  });
                }
                
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
        if (createProjectError != null) {
          Timer.run(() {
            SpanElement span = this.shadowRoot.querySelector("#project_create_error");
            span.children.clear();
            span.appendHtml(createProjectError);
          });
        }
      }
    });
  }
  
  Future<bool> createProjectTasks()
  {
    Future<bool> success;
    List<Task> createdTasks = new List<Task>();
    List<Future<bool>> successList = new List<Future<bool>>();
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
      SelectElement targetLanguageSelect = this.shadowRoot.querySelector("#target_language_$i");
      SelectElement targetCountrySelect = this.shadowRoot.querySelector("#target_country_$i");
      Language targetLang = languages[targetLanguageSelect.selectedIndex];
      Country targetCountry = countries[targetCountrySelect.selectedIndex];
      Locale targetLocale = new Locale();
      targetLocale.languageName = targetLang.name;
      targetLocale.languageCode = targetLang.code;
      targetLocale.countryName = targetCountry.name;
      targetLocale.countryCode = targetCountry.code;
      templateTask.targetLocale = targetLocale;
      CheckboxInputElement segmentationCheckbox = this.shadowRoot.querySelector("#segmentation_$i");
      bool segmentationRequired = segmentationCheckbox.checked;
      CheckboxInputElement translationCheckbox = this.shadowRoot.querySelector("#translation_$i");
      bool translationRequired = translationCheckbox.checked;
      CheckboxInputElement proofreadingCheckbox = this.shadowRoot.querySelector("#proofreading_$i");
      bool proofreadingRequired = proofreadingCheckbox.checked;
      if (segmentationRequired) {
        templateTask.taskType = TaskTypeEnum.SEGMENTATION.value;
        successList.add(TaskDao.createTask(templateTask).then((Task segTask) {
          bool ret;
          if (segTask == null || segTask.id == null || segTask.id < 1) {
            createProjectError = localisation.getTranslation("project_create_13");
            ret = false;
          } else {
            createdTasks.add(segTask);
            TaskDao.saveTaskFile(segTask.id, userid, projectFileText);
            if (trackProject) {
              TaskDao.trackTask(segTask.id, userid);
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
              createProjectError = localisation.getTranslation("project_create_14");
              ret = new Future.value(false);
            } else {
              createdTasks.add(transTask);
              TaskDao.saveTaskFile(transTask.id, userid, projectFileText);
              if (trackProject) {
                TaskDao.trackTask(transTask.id, userid);
              }
              
              if (proofreadingRequired) {
                templateTask.taskType = TaskTypeEnum.PROOFREADING.value;
                templateTask.targetLocale = transTask.targetLocale;
                ret = new Future.sync(() => TaskDao.createTask(templateTask).then((Task proofTask) {
                  Future<bool> ret;
                  if (proofTask == null || proofTask.id == null || proofTask.id < 1) {
                    createProjectError = localisation.getTranslation("project_create_15"); 
                    ret = new Future.value(false);
                  } else {
                    createdTasks.add(proofTask);
                    TaskDao.saveTaskFile(proofTask.id, userid, projectFileText);
                    if (trackProject) {
                      TaskDao.trackTask(proofTask.id, userid);
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
              createProjectError = localisation.getTranslation("project_create_15");
              ret = false;
            } else {
              createdTasks.add(proofTask);
              TaskDao.saveTaskFile(proofTask.id, userid, projectFileText);
              if (trackProject) {
                TaskDao.trackTask(proofTask.id, userid);
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
    loadProjectFile().then((bool fileLoaded) {
      ProjectDao.uploadProjectFile(project.id, userid, filename, projectFileText)
        .then((bool success) {
          completer.complete(success);
        });
    });
    return completer.future;
  }
  
  Future<bool> loadProjectFile()
  {
    Completer<bool> completer = new Completer<bool>();
    File projectFile = null;
    InputElement fileInput = this.shadowRoot.querySelector("#projectFile");
    FileList files = fileInput.files;
    if (!files.isEmpty) {
      projectFile = files[0];
    }
    validateFileInput().then((bool success) {
      FileReader reader = new FileReader();
      reader.onLoadEnd.listen((e) {
        projectFileText = e.target.result;
        completer.complete(true);
      });
      reader.readAsArrayBuffer(projectFile);
    });
    return completer.future;
  }
  
  Future<bool> validateInput()
  {
    Future<bool> ret;
    bool success = true;
    //Validate Text Inputs
    if (project.title == '') {
      titleError = localisation.getTranslation("project_create_31");
      
      Timer.run(() {
        LIElement li;
        li = this.shadowRoot.querySelector("#title_error_top");
        li.children.clear();
        li.appendHtml(titleError);
        li = this.shadowRoot.querySelector("#title_error_btm");
        li.children.clear();
        li.appendHtml(titleError);
      });
      
      success = false;
    }
    if (project.title.length > 110) {
      titleError = localisation.getTranslation("project_create_23");
      
      Timer.run(() {
        LIElement li;
        li = this.shadowRoot.querySelector("#title_error_top");
        li.children.clear();
        li.appendHtml(titleError);
        li = this.shadowRoot.querySelector("#title_error_btm");
        li.children.clear();
        li.appendHtml(titleError);
      });
      
      success = false;
    }
    if (project.description == '') {
      descriptionError = localisation.getTranslation("project_create_33");

      Timer.run(() {
        LIElement li;
        li = this.shadowRoot.querySelector("#description_error_top");
        li.children.clear();
        li.appendHtml(descriptionError);
        li = this.shadowRoot.querySelector("#description_error_btm");
        li.children.clear();
        li.appendHtml(descriptionError);
      });
      
      success = false;
    }
    if (project.impact == '') {
      impactError = localisation.getTranslation("project_create_26");

      Timer.run(() {
        LIElement li;
        li = this.shadowRoot.querySelector("#impact_error_top");
        li.children.clear();
        li.appendHtml(impactError);
        li = this.shadowRoot.querySelector("#impact_error_btm");
        li.children.clear();
        li.appendHtml(impactError);
      });
      
      success = false;
    }
    if (wordCountInput != null && wordCountInput != '') {
      project.wordCount = int.parse(wordCountInput, onError: (String wordCountString) {
        wordCountError = localisation.getTranslation("project_create_27");

        Timer.run(() {
          LIElement li;
          li = this.shadowRoot.querySelector("#word_count_error_top");
          li.children.clear();
          li.appendHtml(wordCountError);
          li = this.shadowRoot.querySelector("#word_count_error_btm");
          li.children.clear();
          li.appendHtml(wordCountError);
        });
        
        success = false;
        return 0;
      });
      if (project.wordCount > 5000) {
        int i = 0;
        bool segmentationMissing = false;
        CheckboxInputElement segmentationCheckbox; 
        while (i < targetCount && !segmentationMissing) {
          segmentationCheckbox = this.shadowRoot.querySelector("#segmentation_$i");
          if (!segmentationCheckbox.checked) {
            segmentationMissing = true;
          }
          i++;
        }
        if (segmentationMissing && !window.confirm(localisation.getTranslation("project_create_22"))) {
          success = false;
        }
      }
    } else {
      wordCountError = localisation.getTranslation("project_create_27");

      Timer.run(() {
        LIElement li;
        li = this.shadowRoot.querySelector("#word_count_error_top");
        li.children.clear();
        li.appendHtml(wordCountError);
        li = this.shadowRoot.querySelector("#word_count_error_btm");
        li.children.clear();
        li.appendHtml(wordCountError);
      });
      
      success = false;
    }
    
    DateTime projectDeadline = parseDeadline();
    if (projectDeadline != null) {
      if (projectDeadline.isAfter(new DateTime.now())) {
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
        deadlineError = localisation.getTranslation("project_create_25");

        Timer.run(() {
          LIElement li;
          li = this.shadowRoot.querySelector("#deadline_error_top");
          li.children.clear();
          li.appendHtml(deadlineError);
          li = this.shadowRoot.querySelector("#deadline_error_btm");
          li.children.clear();
          li.appendHtml(deadlineError);
        });
        
        success = false;
      }
    } else {
      deadlineError = localisation.getTranslation("project_create_32");

      Timer.run(() {
        LIElement li;
        li = this.shadowRoot.querySelector("#deadline_error_top");
        li.children.clear();
        li.appendHtml(deadlineError);
        li = this.shadowRoot.querySelector("#deadline_error_btm");
        li.children.clear();
        li.appendHtml(deadlineError);
      });
      
      success = false;
    }
    
    //Validate targets
    List<Language> targetLanguages = new List<Language>();
    List<Country> targetCountries = new List<Country>();
    for (int i = 0; i < targetCount; i++) {
      CheckboxInputElement segmentationCheckbox = this.shadowRoot.querySelector("#segmentation_$i");
      bool segmentationRequired = segmentationCheckbox.checked;
      CheckboxInputElement translationCheckbox = this.shadowRoot.querySelector("#translation_$i");
      bool translationRequired = translationCheckbox.checked;
      CheckboxInputElement proofreadingCheckbox = this.shadowRoot.querySelector("#proofreading_$i");
      bool proofreadingRequired = proofreadingCheckbox.checked;
      if (!segmentationRequired && !translationRequired && !proofreadingRequired) {
        createProjectError = localisation.getTranslation("project_create_29");
        success = false;
      }
      
      SelectElement targetLanguageSelect = this.shadowRoot.querySelector("#target_language_$i");
      SelectElement targetCountrySelect = this.shadowRoot.querySelector("#target_country_$i");
      Language targetLang = languages[targetLanguageSelect.selectedIndex];
      Country targetCountry = countries[targetCountrySelect.selectedIndex];
      if (targetLanguages.contains(targetLang) && targetCountries.contains(targetCountry)) {
        createProjectError = localisation.getTranslation("project_create_28");
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
    File projectFile = null;
    InputElement fileInput = this.shadowRoot.querySelector("#projectFile");
    FileList files = fileInput.files;
    if (!files.isEmpty) {
      projectFile = files[0];
    }
    if (projectFile == null) {
      createProjectError = localisation.getTranslation("project_create_16");
    }
    
    if (projectFile != null) {
      if (projectFile.size > 0) {
        if (projectFile.size < maxfilesize) {
          int extensionStartIndex = projectFile.name.lastIndexOf(".");
          if (extensionStartIndex >= 0) {
            filename = projectFile.name;
            String extension = filename.substring(extensionStartIndex + 1);
            if (extension != extension.toLowerCase()) {
              extension = extension.toLowerCase();
              filename = filename.substring(0, extensionStartIndex + 1) + extension;
              window.alert(localisation.getTranslation("project_create_18"));
            }
            bool finished = false;
            if (extension == "pdf") {
              if (!window.confirm(localisation.getTranslation("project_create_19"))) {
                finished = true;
                completer.complete(false);
              }
            }
            
            if (!finished) {
              completer.complete(true);
            }
          } else {
            createProjectError = localisation.getTranslation("project_create_20");
            completer.complete(false);
          }
        } else {
          createProjectError = localisation.getTranslation("project_create_21");
          completer.complete(false);
        }
      } else {
        createProjectError = localisation.getTranslation("project_create_17");
        completer.complete(false);
      }
    } else {
      createProjectError = "No file uploaded";
      completer.complete(false);
    }
    
    return completer.future;
  }
  
  DateTime parseDeadline()
  {
    DateTime ret = new DateTime(years[selectedYear], selectedMonth + 1, selectedDay + 1, selectedHour, selectedMinute);
    return ret;
  }
  
  List<String> separateTags(String tags)
  {
    return tags.split(" ");
  }
  
  void segmentationClicked(InputElement target)
  {
    int index = int.parse(target.id.substring(target.id.indexOf("_") + 1));
    InputElement transCheckbox = this.shadowRoot.querySelector("#translation_$index");
    InputElement proofCheckbox = this.shadowRoot.querySelector("#proofreading_$index");
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
  
  /*
   * Automatically bound to changes on selectedYear
   */
  void selectedYearChanged(int oldValue)
  {
    if (this.isLeapYear(years[selectedYear])) {
      monthLengths[1] = 29;
    } else {
      monthLengths[1] = 28;
    }
    
    if (selectedMonth == 1) {
      // in case leap year status changed
      this.selectedMonthChanged(selectedMonth);
    }
  }
  
  /*
   * Automatically bound to changes on selectedMonth
   */
  void selectedMonthChanged(int oldValue)
  {
    days = new List<int>.generate(monthLengths[selectedMonth], (int index) => index + 1);
  }
  
  bool isLeapYear(int year)
  {
    bool ret = true;
    if (year % 4 != 0) {
      ret = false;
    } else {
      if (year % 100 == 0 && year % 400 != 0) {
        ret = false;
      }
    }
    return ret;
  }
}
