
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
  @published String css;
  
  // Other
  int maxTargetLanguages;
  String filename;
  String tagList;
  String wordCountInput;
  SelectElement langSelect;
  SelectElement countrySelect;
  var projectFileData;
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
  @observable String tagsError;
  @observable String referenceError;
  
  /**
   * The constructor for ProjectCreateForm, handling intialisation and setup of various things.
   */
  ProjectCreateForm.created() : super.created() 
  {
    project = new Project();
    project.tag = new List<Tag>();
    projectFileData = "";
    localisation = new Localisation();
    languages = toObservable(new List<Language>());
    countries = toObservable(new List<Country>());
    DateTime currentDate = new DateTime.now();
    //Setup information about dates
    years = toObservable(new List<int>.generate(10, (int index) => index + currentDate.year, growable: false));
    months = toObservable([localisation.getTranslation("common_january"), 
                           localisation.getTranslation("common_february"), 
                           localisation.getTranslation("common_march"), 
                           localisation.getTranslation("common_april"), 
                           localisation.getTranslation("common_may"), 
                           localisation.getTranslation("common_june"), 
                           localisation.getTranslation("common_july"), 
                           localisation.getTranslation("common_august"),
                           localisation.getTranslation("common_september"), 
                           localisation.getTranslation("common_october"), 
                           localisation.getTranslation("common_november"), 
                           localisation.getTranslation("common_december")]);
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
  
  /**
   * Called when by the DOM when the ProjectCreateForm element has been inserted into the "live document".
   */
  void enteredView()
  {
    Settings settings = new Settings();
    orgDashboardLink = settings.conf.urls.SiteLocation + "org/dashboard";
    String location = settings.conf.urls.SiteLocation;
    //import css into polymer element
    if (css != null) {
      css.split(' ').map((path) => new StyleElement()..text = "@import '$location${path}';").forEach(shadowRoot.append);
    }
    
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
  }
  
  /**
   * This method is used by enteredView() to add the elements of the source and target locales to the page.
   */
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
  
  /**
   * This method is used to add target languages to the form.
   */
  void addMoreTargetLanguages()
  {
    //Unless the targetCount is less than the maxTargetLanguages, don't do anything.
    //On the UI this shouldn't be an issue anyway because this method will disable the add button when
    //adding a language pushes the targetCount to the max, so this is an extra safeguard.
    if (targetCount < maxTargetLanguages) {
      //Prepare the div elements that will make up the new target language section.
      DivElement targetLanguageRow = new DivElement() //The main div, subdivided into the language/country 
     ..id = "target_row_$targetCount"                 //selects and task type checkboxes
      ..classes.addAll(["target-row", "bottom-line-border"]);
      DivElement targetLanguageCell = new DivElement() //Sub-div for select elements
      ..classes.addAll(["pull-left", "width-50"]);
      //Create the select elements by cloning the preexisting langSelect and countrySelect
      SelectElement targetLanguageSelect = langSelect.clone(true);
      targetLanguageSelect.style.width = "400px";
      targetLanguageSelect.id = "target_language_$targetCount";
      SelectElement targetCountrySelect = countrySelect.clone(true);
      targetCountrySelect.style.width = "400px";
      targetCountrySelect.id = "target_country_$targetCount";

      DivElement taskTypesRow = new DivElement() //Sub-div for task type checkboxes, holds individual divs for
      ..id = "task-type-checkboxes"              //each checkox
      ..classes.addAll(["pull-left", "width-50"]);
      DivElement segmentationRequired = new DivElement()
      ..classes.addAll(["pull-left", "proj-task-type-checkbox"]);
      InputElement segmentationCheckbox = new InputElement(type: "checkbox")
      ..title = localisation.getTranslation("project_create_10")
      ..id = "segmentation_$targetCount"
      ..onClick.listen((event) => segmentationClicked(event.target));
      DivElement translationRequired = new DivElement()
      ..classes.addAll(["pull-left", "proj-task-type-checkbox"]);
      InputElement translationCheckbox = new InputElement(type: "checkbox")
      ..title = localisation.getTranslation("common_create_a_translation_task_for_volunteer_translators_to_pick_up")
      ..id = "translation_$targetCount"
      ..checked = true;
      DivElement proofreadingRequired = new DivElement()
      ..classes.addAll(["pull-left", "proj-task-type-checkbox"]);
      InputElement proofreadingCheckbox = new InputElement(type: "checkbox")
      ..title = localisation.getTranslation("common_create_a_proofreading_task_for_evaluating_the_translation_provided_by_a_volunteer")
      ..id = "proofreading_$targetCount"
      ..checked = true;
      
      //Put the SelectElements into their div
      targetLanguageCell.children.add(targetLanguageSelect);
      targetLanguageCell.children.add(targetCountrySelect);
      //Put the SelectElements' div into the main div
      targetLanguageRow.children.add(targetLanguageCell);
      //Put the checkbox InputElements into their own divs
      segmentationRequired.children.add(segmentationCheckbox);
      translationRequired.children.add(translationCheckbox);
      proofreadingRequired.children.add(proofreadingCheckbox);
      //Put each checkbox div into the div that is to contain them all
      taskTypesRow.children.add(segmentationRequired);
      taskTypesRow.children.add(translationRequired);
      taskTypesRow.children.add(proofreadingRequired);
      //Put the div encompassing the three checkboxes into the main div
      targetLanguageRow.children.add(taskTypesRow);

      //Add the completed target language "row" to the div containing all previously added target 
      //language "rows."
      DivElement targetLangDiv = this.shadowRoot.querySelector("#targetLangSelectDiv")
      ..children.add(targetLanguageRow);
      
      targetCount++;
      if (targetCount == 5) {
        window.alert(localisation.getTranslation("project_create_target_language_increase"));
      }

      //If maximum amount of target languages has been reached, display message to notify user.
      if (targetCount >= maxTargetLanguages) {
        maxTargetsReached = localisation.getTranslation("project_create_11");
        ButtonElement addBtn = this.shadowRoot.querySelector("#addTargetLanguageBtn");
        addBtn.disabled = true;
      }
      
      ButtonElement removeButton = this.shadowRoot.querySelector("#removeBottomTargetBtn");
      if (removeButton != null) {
        //Disable the remove button if there is only 1 target language on the form.
        if (targetCount > 1) {
          removeButton.disabled = false;
        } else {
          removeButton.disabled = true;
        }
      }
    }
  }
  
  /**
   * This method is used to remove target languages
   */
  void removeTargetLanguage()
  {
    if (targetCount > 1) {
      targetCount--;
      DivElement targetLanguageRow = this.shadowRoot.querySelector("#target_row_$targetCount");
      targetLanguageRow.remove();
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
  
  /**
   * This is the core method of this class, called when "Submit form" button is clicked, triggering validation
   * of all the input and, if there is no invalid input or errors in the process, creation of the new project.
   * The work is divided into various submethods:
   * createProjectTasks(),
   * validateInput(),
   * uploadProjectFile()
   * 
   * These each do some work and throw back errors, should they arise.
   */
  void submitForm()
    {
      //reset error variables, clearing any previously displayed errors.
      createProjectError = null;
      titleError = null;
      descriptionError = null;
      wordCountError = null;
      deadlineError = null;
      impactError = null;
      tagsError = null;
      referenceError = null;
      maxTargetsReached = null;
     
      //Validate form input and then check for success
      validateInput().then((bool success) {
        if (success) {
          //If the validation has succeeded, then begin setting up Project object of new project.
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
            projectTags = _separateTags(tagList);
          }
          if (projectTags.length > 0) {
            projectTags.forEach((String tagName) {
              Tag tag = new Tag();
              tag.label = tagName;
              project.tag.add(tag);
            });
          }
         
          //Using DAO method request Project creation
          ProjectDao.createProject(project)
            .then((Project pro) {
            //If no errors have occurred, then we can get the ID assigned to the newly created project
            project.id = pro.id;
           
            //Success list used to wait on result of uploading project file and tracking project
            List<Future<bool>> successList = new List<Future<bool>>();
            //upload the file and then create tasks
            successList.add(uploadProjectFile()
              .then((_) => createProjectTasks()));
           
            if (trackProject) {
              //track project via DAO if user selected that option
              successList.add(ProjectDao.trackProject(project.id, userid)
                .catchError((e) {
                  return new Future.error(
                    sprintf(localisation.getTranslation("project_create_failed_project_track"), [e.toString()]));
              }));
            }
           
            return Future.wait(successList).then((_) {
              //wait for project file to have been uploaded (and by extension tasks created) and project tracked
              //and then calculate project deadlines
              return ProjectDao.calculateProjectDeadlines(project.id).then((bool deadlinesCalculated) {
                //Once deadlines have been calculated, make the app progress to the "view project" page.
                Settings settings = new Settings();
                window.location.assign(settings.conf.urls.SiteLocation + "project/"
                    + project.id.toString() + "/view");
              }).catchError((error, stack) {
                return new Future.error(sprintf(
                    localisation.getTranslation("project_create_failed_project_deadlines"), [error.toString()]),
                    stack);
              });
            });
            //catch any as yet uncaught error that occurred while creating the project or in the subsequent
            //.then() and delete the project.
          }).catchError((e){
            print("Something went wrong, deleting project");
            return ProjectDao.deleteProject(project.id)
            .then((_) {
              project.id = null;
              throw e;
            });
          });
        //If validation failed, print message to console.
        } else {
          print("Invalid form input");
        }
        //catch any as yet uncaught errors from validateInput or the subsequent .then() and set
        //createProjectError to that error.
      }).catchError((e, stack) {
        createProjectError = e;
        print(stack);
      });
    }
  
  /**
   * This method is called from submitForm() to create all the project's tasks.
   */
  Future<bool> createProjectTasks()
    {
      //Initialise some variables, and gather task info from form
      List<Task> createdTasks = new List<Task>();
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
      //Loop up to the number of targets, create task(s) for each
      return Future.forEach(new List.generate(targetCount, (int i) => i), (int i) {
        //Get task info from the form
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
        //Determine from the form which task types are selected
        CheckboxInputElement segmentationCheckbox = this.shadowRoot.querySelector("#segmentation_$i");
        bool segmentationRequired = segmentationCheckbox.checked;
        CheckboxInputElement translationCheckbox = this.shadowRoot.querySelector("#translation_$i");
        bool translationRequired = translationCheckbox.checked;
        CheckboxInputElement proofreadingCheckbox = this.shadowRoot.querySelector("#proofreading_$i");
        bool proofreadingRequired = proofreadingCheckbox.checked;
        //Create segmentation task if necessary
        if (segmentationRequired) {
          templateTask.taskType = TaskTypeEnum.SEGMENTATION.value;
          //Add result of entire segmentation task creation process to successList
          //Create task and then add to the created tasks list
          return TaskDao.createTask(templateTask)
          .then((Task segTask) {
            createdTasks.add(segTask);
            //Save the file
            List<Future<bool>> segSuccess = new List<Future<bool>>();
            segSuccess.add(TaskDao.saveTaskFile(segTask.id, userid, projectFileData)
            .then((_) => true)
            .catchError((e) {
              //Catch error in saving file
              throw sprintf(
                     localisation.getTranslation("project_create_failed_upload_file"), 
                     [localisation.getTranslation("common_segmentation"), e.toString()]);
            }));
            
            if (trackProject) {
              //Track seg task
              segSuccess.add(TaskDao.trackTask(segTask.id, userid)
              .then((_) => true)
              .catchError((e) {
                throw sprintf(
                    localisation.getTranslation("project_create_failed_track_task"),
                    [localisation.getTranslation("common_segmentation"), e.toString()]);
              }));
            }
            
            return Future.wait(segSuccess)
            .then((_) => true)
            .catchError((e) {
              throw e.toString();
            });
          //Catch as yet uncaught errors from process of creating seg task  
          }).catchError((e) {
            throw localisation.getTranslation("project_create_13") + e.toString();
          });
        } else {//Not a seg task, so translation and/or proofreading will be created.
          //Create translation task if necessary
          if (translationRequired) {
                    templateTask.taskType = TaskTypeEnum.TRANSLATION.value;
                    //Add result of entire translation task creation process to successList
                    //Create task and then add to the created tasks list
                    return TaskDao.createTask(templateTask)
                    .then((Task transTask) {
                      createdTasks.add(transTask);
                      
                      List<Future<bool>> transSuccess = new List<Future<bool>>();
                      //Save the file
                      transSuccess.add(TaskDao.saveTaskFile(transTask.id, userid, projectFileData)
                      .catchError((e) {
                        //Catch error from saving file
                        throw sprintf(
                                localisation.getTranslation("project_create_failed_upload_file"), 
                                [localisation.getTranslation("common_translation"), e.toString()]);
                      }));
                      
                      if (trackProject) {
                        transSuccess.add(TaskDao.trackTask(transTask.id, userid)
                        .catchError((e) {
                          throw sprintf(
                              localisation.getTranslation("project_create_failed_track_task"),
                              [localisation.getTranslation("common_translation"), e.toString()]);
                        }));
                      }
                      //Create proofreading task with translation task if necessary
                      if (proofreadingRequired) {
                        templateTask.taskType = TaskTypeEnum.PROOFREADING.value;
                        templateTask.targetLocale = transTask.targetLocale;
                        //Create task and then add to the created tasks list
                        transSuccess.add(TaskDao.createTask(templateTask).then((Task proofTask) {
                          createdTasks.add(proofTask);
                          
                          List<Future<bool>> proofSuccess = new List<Future<bool>>();
                          //Save file
                          proofSuccess.add(TaskDao.saveTaskFile(proofTask.id, userid, projectFileData)
                          .catchError((e) {
                            //Catch error from saving file
                            throw sprintf(
                                localisation.getTranslation("project_create_failed_upload_file"), 
                                [localisation.getTranslation("common_proofreading"), e.toString()]);
                          }));
                          
                          if (trackProject) {
                            proofSuccess.add(TaskDao.trackTask(proofTask.id, userid)
                            .catchError((e) {
                              throw sprintf(
                                  localisation.getTranslation("project_create_failed_track_task"),
                                  [localisation.getTranslation("common_proofreading"), e.toString()]);
                            }));
                          }
                          
                          //Add the translation task as a prerequisite to the proofreading task
                          proofSuccess.add(TaskDao.addTaskPreReq(proofTask.id, transTask.id)
                          .catchError((e) {
                            throw sprintf(
                                localisation.getTranslation("project_create_failed_add_prereq"), [e.toString()]);
                          }));
                          
                          return Future.wait(proofSuccess).then(( _ ) => true);
                        }).catchError((e) {
                          throw localisation.getTranslation("project_create_15") + e.toString();
                        }));
                      }
                      
                      return Future.wait(transSuccess)
                        .then(( _ ) => true);
                    }).catchError((e) {
                      throw localisation.getTranslation("project_create_14") + e.toString();
                    });
          } else if (!translationRequired && proofreadingRequired) {//Only a proofreading task to be created
            templateTask.taskType = TaskTypeEnum.PROOFREADING.value;
            
            //Add result of entire proofreading task creation process to successList
            //Create task and then add to the created tasks list
            return TaskDao.createTask(templateTask)
            .then((Task proofTask) {
              createdTasks.add(proofTask);
              
              List<Future<bool>> proofSuccess = new List<Future<bool>>();

              //Upload proofreading task
              proofSuccess.add(TaskDao.saveTaskFile(proofTask.id, userid, projectFileData)
              .then((_) => true)
              .catchError((e) {
                throw sprintf(
                  localisation.getTranslation("project_create_failed_upload_file"), 
                  [localisation.getTranslation("common_proofreading"), e.toString()]);
              }));
              
              if (trackProject) {
                //Track proofreading task
                proofSuccess.add(TaskDao.trackTask(proofTask.id, userid)
                .then((_) => true)
                .catchError((e) {
                  throw sprintf(
                      localisation.getTranslation("project_create_failed_track_task"),
                      [localisation.getTranslation("common_proofreading"), e.toString()]);
                }));
              }
              
              return Future.wait(proofSuccess)
              .then((_) => true);
            }).catchError((e) {
              throw localisation.getTranslation("project_create_15") + e.toString();
            });
          }
        }
      });
    }
  
  /**
   * This method is called to upload the project file.
   */
  Future<bool> uploadProjectFile()
    {
      //Load the file and then call the API to upload it
      return loadProjectFile().then((_) {
        return ProjectDao.uploadProjectFile(project.id, userid, filename, projectFileData);
      }).catchError((e) {
            throw sprintf(
                localisation.getTranslation("project_create_failed_upload_file"),
                [localisation.getTranslation("common_project"), e]);
          });
    }
  
  /**
   * This method loads the content of the file and validates the file details.
   */
  Future<bool> loadProjectFile()
    {
      File projectFile = null;
      InputElement fileInput = this.shadowRoot.querySelector("#projectFile");
      FileList files = fileInput.files;
      if (!files.isEmpty) {
        projectFile = files[0];
      }
      //Validate the file details and then load its content.
      return _validateFileInput().then((bool success) {
        Completer fileIsDone = new Completer();
        FileReader reader = new FileReader();
        reader.onLoadEnd.listen((e) {
          projectFileData = e.target.result;
          fileIsDone.complete(true);
        });
        reader.readAsArrayBuffer(projectFile);
        return fileIsDone.future;
      });
    }
  
  /**
   * This method validates the form input and sets various error messages if needed fields are not set or
   * invalid data is given.
   */
  Future<bool> validateInput()
    {
      //Validate textual form input and deadline info
      return new Future((){
        //title is empty
        if (project.title == '') {
            titleError = localisation.getTranslation("project_create_error_title_not_set");
            return false;
          //title too long
          } else if (project.title.length > 110) {
            titleError = localisation.getTranslation("project_create_error_title_too_long");
            return false;
          //Is the project title simply a number? Don't allow this, thus avoiding Slim route mismatch,
          //calling route for getProject when it should be getProjectByName
          } else if (project.title.indexOf(new RegExp(r'^\d+$')) != -1) {
            titleError = localisation.getTranslation("project_create_title_cannot_be_number");
          } else {
            //has the title already been used?
            return ProjectDao.getProjectByName(project.title).then((Project checkExist) {
              if (checkExist != null) {
                print("CHECKING IF TITLE IS IN USE");
                titleError = localisation.getTranslation("project_create_title_conflict");
                return false;
              }else{
                return true;
              }
            });
          }
      }).then((bool success){
        //Project description not set
        if (project.description == '') {
          descriptionError = localisation.getTranslation("project_create_33");
          success = false;
        } else if (project.description.length > 4096) {
          //Project description is too long
          descriptionError = localisation.getTranslation("project_create_error_description_too_long");
          success = false;
        }
        
        //Project impact not set
        if (project.impact == '') {
          impactError = localisation.getTranslation("project_create_26");
          success = false;
        } else if (project.impact.length > 4096) {
          //Project impact is too long
          impactError = localisation.getTranslation("project_create_error_impact_too_long");
          success = false;
        }
        
        if(project.reference != null && project.reference != '') {
          if(project.reference.length > 128) {
            //Project reference is too long
            referenceError = localisation.getTranslation("project_create_error_reference_too_long");
            success = false;
            Timer.run(() {
              LIElement referenceErrTop = this.shadowRoot.querySelector("#reference_error_top");
              referenceErrTop.setInnerHtml(
                referenceError,
                validator : new NodeValidatorBuilder()
                  ..allowHtml5()
                  ..allowElement('a', attributes: ['href'])
              );
              LIElement referenceErrBtm = this.shadowRoot.querySelector("#reference_error_btm");
              referenceErrBtm.setInnerHtml(
                referenceError,
                validator : new NodeValidatorBuilder()
                  ..allowHtml5()
                  ..allowElement('a', attributes: ['href'])
              );
            });
          } else if (_validateReferenceURL(project.reference) == false) {
            referenceError = localisation.getTranslation("project_create_error_reference_invalid");
            success = false;
          }
        }
        
        if (wordCountInput != null && wordCountInput != '') {
          //If word count is set, ensure it is a valid natural number
          project.wordCount = int.parse(wordCountInput, onError: (String wordCountString) {
            wordCountError = localisation.getTranslation("project_create_27");
            success = false;
            return 0;
          });
          //If word count is greater than 5000, and segmentation is not selected, display warning message
          //to user.
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
          //Word count is not set, set error message
          wordCountError = localisation.getTranslation("project_create_27");
          success = false;
        }
       
        if (_validateTagList(tagList) == false) {
          //Invalid tags detected, set error message
          tagsError = localisation.getTranslation('project_create_invalid_tags');
          success = false;
        } else {
          List<String> list = tagList.split(" ");
          int listLen = list.length;
          for (int i = 0; i < listLen; i++) {
            if (list.elementAt(i).length > 50) {
              //One of the tags is too long, set error message
              tagsError = localisation.getTranslation("project_create_error_tags_too_long");
              success = false;
              break;
            }
          }
        }
       
        //Parse project deadline info
        DateTime projectDeadline = _parseDeadline();
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
            //Deadline is not a date in the future, set error message
            deadlineError = localisation.getTranslation("project_create_25");
            success = false;
          }
        } else {
          //Deadline is not set (can this even happen in current code?)
          deadlineError = localisation.getTranslation("project_create_32");
          success = false;
        }
        return success;
        //Textual input and deadline info have been validated, validate target languages
      }).then((bool success){
        
        List<Language> targetLanguages = new List<Language>();
        List<Country> targetCountries = new List<Country>();
        for (int i = 0; i < targetCount; i++) {
          CheckboxInputElement segmentationCheckbox = this.shadowRoot.querySelector("#segmentation_$i");
          bool segmentationRequired = segmentationCheckbox.checked;
          CheckboxInputElement translationCheckbox = this.shadowRoot.querySelector("#translation_$i");
          bool translationRequired = translationCheckbox.checked;
          CheckboxInputElement proofreadingCheckbox = this.shadowRoot.querySelector("#proofreading_$i");
          bool proofreadingRequired = proofreadingCheckbox.checked;
          //If no task type is set, display error message
          if (!segmentationRequired && !translationRequired && !proofreadingRequired) {
            success = false;
            throw localisation.getTranslation("project_create_29");
          }
         
          SelectElement targetLanguageSelect = this.shadowRoot.querySelector("#target_language_$i");
          SelectElement targetCountrySelect = this.shadowRoot.querySelector("#target_country_$i");
          Language targetLang = languages[targetLanguageSelect.selectedIndex];
          Country targetCountry = countries[targetCountrySelect.selectedIndex];
          //If a duplicate locale is encountered, throw error message
          if (targetLanguages.contains(targetLang) && targetCountries.contains(targetCountry)) {
            success = false;
            throw localisation.getTranslation("project_create_28");
          } else {
            targetLanguages.add(targetLang);
            targetCountries.add(targetCountry);
          }
        }
       
        //Validate file input
        return _validateFileInput().then((bool valid) {
          if (success && valid) {
            return true;
          } else {
            return false;
          }
        });
      });
    }
  
  /**
   * This method is used to validate the details of the project file provided.
   */
  Future<bool> _validateFileInput()
  {
    return new Future(() {
      File projectFile = null;
      InputElement fileInput = this.shadowRoot.querySelector("#projectFile");
      FileList files = fileInput.files;
      if (!files.isEmpty) {
        projectFile = files[0];
      }
    
      //Ensure projectFile is not null
      if (projectFile != null) {
        //Check if file is empty
        if (projectFile.size > 0) {
          //Check that file does not exceed the maximum allowed file size
          if (projectFile.size < maxfilesize) {
            int extensionStartIndex = projectFile.name.lastIndexOf(".");
            //Check that file has an extension
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
                //If file is a pdf, warn user that PDFs are difficult to work with
                if (!window.confirm(localisation.getTranslation("project_create_19"))) {
                  finished = true;
                  return false;
                }
              }
            
              if (!finished) {
                return true;
              }
            } else {
              //File has no extension, throw error
              throw localisation.getTranslation("project_create_20");
            }
          } else {
            //File is too big, throw error
            throw localisation.getTranslation("project_create_21");
          }
        } else {
          //File is empty, throw error
          throw localisation.getTranslation("project_create_17");
        }
      } else {
        //No file provided, throw error
        throw localisation.getTranslation("project_create_16");
      }
    });
  }
  
  /**
   * This method is called by validateInput to validate the project tags provided.
   */
  bool _validateTagList(String tagList)
  {
    if (tagList.indexOf(new RegExp(r'[^a-z0-9\-\s]')) != -1) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
   * Uses a regular expression to validate the the reference URL for a project (if one is provided) actually is
   * a URL.
   * Credit to http://stackoverflow.com/a/24058129/1799985
   * 
   * Returns true if the provided URL is valid, false otherwise.
   */
  bool _validateReferenceURL(String url)
  {
    //Spread out the regular expression string on 3 lines and concatenate it together for better presentation
    //of code
    String regExp = 
        r'(([\w]+:)?//)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,' +
        r'253}[\d\w]\.)+[\w]{2,13}(:[\d]+)?(/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-' +
        r'fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?';
    if (url.indexOf(new RegExp(regExp)) == -1) {
      //String did not match pattern, it is not a URL
      return false;
    } else {
      //String matched, it is a URL
      return true;
    }
  }
  
  /**
   * This is a simple method to parse the deadline provided for the project.
   */
  DateTime _parseDeadline()
  {
    DateTime ret = new DateTime(years[selectedYear], selectedMonth + 1, selectedDay + 1, selectedHour, selectedMinute);
    return ret;
  }
  
  /**
   * This is a simple method to parse the project tags from the text input.
   */
  List<String> _separateTags(String tags)
  {
    return tags.split(" ");
  }
  
  /**
   * This method is called when the segmentation checkbox is clicked for a target language, disabling the
   * translation and proofreading checkboxes.
   */
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
  
  /**
   * Automatically bound to changes on selectedYear
   */
  void selectedYearChanged(int oldValue)
  {
    if (this._isLeapYear(years[selectedYear])) {
      monthLengths[1] = 29;
    } else {
      monthLengths[1] = 28;
    }
    
    if (selectedMonth == 1) {
      // in case leap year status changed
      this.selectedMonthChanged(selectedMonth);
    }
  }
  
  /**
   * Automatically bound to changes on selectedMonth
   */
  void selectedMonthChanged(int oldValue)
  {
    days = new List<int>.generate(monthLengths[selectedMonth], (int index) => index + 1);
  }
  
  /**
   * This is a simple method to check if a year is a leap year or not.
   */
  bool _isLeapYear(int year)
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
