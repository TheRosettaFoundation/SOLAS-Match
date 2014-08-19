import "package:polymer/polymer.dart";
import "package:image/image.dart";
import "dart:async";
import "dart:html";
import "dart:math";

import "package:sprintf/sprintf.dart";
import "../../lib/SolasMatchDart.dart";

/*
 * TODO ADD HANDLING FOR THESE TO TEMPLATE
 * Errors that could arise and need to be handled here and in polymer template:
 * Title, description and/or impact too long.
 * Reference input is not a URL or is too long.
 * One or more tags are too long.
 * Deadline is a date in the past
 * Word count input is not an int.
 * Image file uploaded is too big, empty, not an image, etc
 */
@CustomTag('project-alter-form')
class ProjectAlterForm extends PolymerElement
{
  // Bound Attributes
  @published int projectid;
  @published int userId;
  @published int maxfilesize; //TODO: possibly remove or name change
  @published String css;
  
  SelectElement langSelect;
  SelectElement countrySelect;
  List<int> monthLengths;
  Project oldProject;
  bool userIsAdmin;
  String tagList;
  String imageFilename;
  var projectImageData;
  @observable String wordCountInput;
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
  @observable Localisation localisation;
  @observable Project project;
  @observable List<Language> languages;
  @observable List<Country> countries;
  @observable bool loaded;
  @observable String orgDashboardLink;
  @observable String projectViewLink;
  @observable String imgSource;
  @observable String projectTags; //String to store project tags to be shown in form.
  
  // Error Variables
  @observable String titleError;
  @observable String descriptionError;
  @observable String wordCountError;
  @observable String deadlineError;
  @observable String impactError;
  @observable String createProjectError;
  @observable String tagsError;
  @observable String referenceError;
  @observable String imageError;
  
  //Project image related values that are retrieved from conf.
  int imageMaxFileSize;
  int imageMaxWidth;
  int imageMaxHeight;
  List<String> supportedImageFormats;
  
  /**
   * The constructor for [ProjectAlterForm], handling initialisation of variables.
   */
  ProjectAlterForm.created() : super.created()
  {
    project = new Project();
    projectTags = "";
    project.tag = new List<Tag>();
    //projectFileData = "";
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
    //publish = true;
    //trackProject = true;
    //targetCount = 0;
    wordCountInput = '';
    tagList = "";
  }
  
  /**
   * Called by the DOM when the ProjectAlterForm element has been inserted into the "live document".
   */
  void enteredView()
  {
    Settings settings = new Settings();
    orgDashboardLink = settings.conf.urls.SiteLocation + "org/dashboard";
    projectViewLink = settings.conf.urls.SiteLocation + "$projectid/view";
    imgSource = settings.conf.urls.SiteLocation + "project/$projectid/image";
    
    String location = settings.conf.urls.SiteLocation;
    //Get project image related data from conf
    imageMaxFileSize = int.parse(settings.conf.project_images.max_size) *1024 * 1024;
    imageMaxWidth = int.parse(settings.conf.project_images.max_width);
    imageMaxHeight = int.parse(settings.conf.project_images.max_height);
    //Image format string is comma separated, split it into a list
    supportedImageFormats = (settings.conf.project_images.supported_formats as String).split(",");
    
    //import css into polymer element
    if (css != null) {
      css.split(' ').map((path) => new StyleElement()..text = "@import '$location${path}';").forEach(shadowRoot.append);
    }
    
    List<Future<bool>> loadedList = new List<Future<bool>>();
        
    loadedList.add(LanguageDao.getAllLanguages().then((List<Language> langs) {
      languages.addAll(langs);
      return true;
    }));
    
    loadedList.add(CountryDao.getAllCountries().then((List<Country> regions) {
      countries.addAll(regions);
      return true;
    }));
    
    loadedList.add(AdminDao.isSiteAdmin(userId).then((bool result) {
      userIsAdmin = result;
      return true;
    }));
   
    loadedList.add(ProjectDao.getProject(projectid).then((Project pro) {
      project = pro;
      ProjectDao.getProjectTags(projectid)
      .then((List<Tag> projTags) {
        if (projTags.length > 0) {
          project.tag = projTags;
          projTags.forEach((Tag tag) {
            projectTags += tag.label + " ";
          });
        }
      });
      oldProject = project;
      return true;
    }));
    
    Future.wait(loadedList).then((List<bool> successList) {
      successList.forEach((bool success) {
        if (!success) {
          print("Failed to load some data");
        }
      });
      constructDynamicElements();
      _setProjectDeadline();
      
      //Insert text stating max image file size
      ParagraphElement imgDesc = this.shadowRoot.querySelector("#help-block");
      imgDesc.children.clear();
      imgDesc.appendHtml(sprintf(
        localisation.getTranslation("common_maximum_file_size_is"),
        [(imageMaxFileSize / 1024 / 1024).toString()])
      );
      
      //Select the project's source locale on the UI
      SelectElement sourceLangSelect = this.shadowRoot.querySelector("#source-lang-select");
      SelectElement sourceCountrySelect = this.shadowRoot.querySelector("#source-country-select");
      sourceLangSelect.selectedIndex = sourceLangSelect.options.indexOf(
          sourceLangSelect.options.firstWhere((OptionElement opt) {
            return opt.value == project.sourceLocale.languageCode;
      }));
      sourceCountrySelect.selectedIndex = sourceCountrySelect.options.indexOf(
          sourceCountrySelect.options.firstWhere((OptionElement opt) {
            return opt.value == project.sourceLocale.countryCode;
      }));
      //Display the project's word count
      InputElement wcInput = this.shadowRoot.querySelector("#word-count");
      wordCountInput = project.wordCount.toString();
      //Unless the user is an admin, disable editing of word count.
      if (!userIsAdmin) {
        wcInput.disabled = true;
      }
    });
  }
  
  void constructDynamicElements()
  {
    langSelect = new SelectElement()
    ..style.width = "400px"
    ..id = "source-lang-select";
    List<OptionElement> options = new List<OptionElement>();
    for (int i = 0; i < languages.length; i++) {
      var option = new OptionElement()
      ..value = languages[i].code
      ..text = languages[i].name;
      options.add(option);
    }
    langSelect.children.addAll(options);
    
    countrySelect = new SelectElement()
    ..style.width = "400px"
    ..id = "source-country-select";
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
    SelectElement sourceCountrySelect = countrySelect.clone(true);
    sourceLanguageDiv.children.add(sourceTitle);
    sourceLanguageDiv.children.add(sourceLanguageSelect);
    sourceLanguageDiv.children.add(sourceCountrySelect);
    loaded = true;
  }
  
  /**
   * This method is used by enteredView() to make the UI display the project's current deadline.
   */
  void _setProjectDeadline()
  {
    DateTime dt = DateTime.parse(project.deadline);
    selectedDay = dt.day - 1;
    selectedMonth = dt.month - 1;
    selectedYear = years.indexOf(dt.year);
    selectedHour = dt.hour;
    selectedMinute = dt.minute;
  }
  
  void submitForm()
  {
    //reset error variables, clearing any previously displayed errors.
    titleError = null;
    descriptionError = null;
    wordCountError = null;
    deadlineError = null;
    impactError = null;
    tagsError = null;
    referenceError = null;
    imageError = null;
    
    validateInput().then((bool success) {
      if (success) {
        //if (project != oldProject) {
          SelectElement sourceLangSelect = this.shadowRoot.querySelector("#source-lang-select");
          SelectElement sourceCountrySelect = this.shadowRoot.querySelector("#source-country-select");
          Language sourceLang = languages[sourceLangSelect.selectedIndex];
          Country sourceCountry = countries[sourceCountrySelect.selectedIndex];
          Locale sourceLocale = new Locale();
          sourceLocale.languageName = sourceLang.name;
          sourceLocale.languageCode = sourceLang.code;
          sourceLocale.countryName = sourceCountry.name;
          sourceLocale.countryCode = sourceCountry.code;
          project.sourceLocale = sourceLocale;
         
          List<Tag> projectTagsParsed = new List<Tag>();
          if (projectTags.length > 0) {
            projectTagsParsed = FormHelper.parseTagsInput(projectTags);
          }
          if (projectTags.length > 0) {
            project.tag.clear();
            project.tag.addAll(projectTagsParsed);
          }
          //Update the project and then, if a new image has been supplied upload it.
          ProjectDao.updateProject(project)
          .then((_) => uploadProjectImage())
          .then((_) {
            //Once deadlines have been calculated, make the app progress to the "view project" page.
            Settings settings = new Settings();
            window.location.assign(settings.conf.urls.SiteLocation + "project/"
                + project.id.toString() + "/view");
          });
        //}
      } else {
        print("Invalid form input.");
      }
    }).catchError((e, stack) {
      print(e);
      print(stack);
    });
  }
  
  Future<bool> uploadProjectImage()
  {
    return loadProjectImageFile()
      .then((_) { 
      int userid = 1; //TODO remove this and maybe rework the slim route to not use user id
        if (projectImageData != null) {
          ProjectDao.uploadProjectImage(project.id, userid, imageFilename, projectImageData);
        }
      })
      .catchError((e) {
        throw sprintf(localisation.getTranslation("project_create_failed_upload_image"), [e]);
      });
  }
  
  Future<bool> loadProjectImageFile()
    {
      File imageFile = null;
      InputElement fileInput = this.shadowRoot.querySelector("#projectImageFile");
      Image image;
      FileList files = fileInput.files;
      if (!files.isEmpty) {
        imageFile = files[0];
      
        return _validateImageFileInput().then((_) {
          Completer fileIsDone = new Completer();
          FileReader reader = new FileReader();
          var imageFileData = null;
          reader.onLoadEnd.listen((e) {
            imageFileData = e.target.result;
            image = decodeImage(imageFileData);
            image = _resizeProjectImage(image);
            projectImageData = encodeNamedImage(image, imageFile.name);
            
            fileIsDone.complete(true);
          });
          reader.readAsArrayBuffer(imageFile);
          return fileIsDone.future;
        });
      }
      //Just return true if an image was not processed.
      return new Future.value(true);
    }
  
  void deleteImage()
  {
    if (window.confirm(localisation.getTranslation("project_alter_confirm_delete_image"))) {
      ProjectDao.deleteProjectImage(project.id, project.organisationId)
      .then((bool deletionSuccess) {
        if (deletionSuccess) {
          window.alert(localisation.getTranslation("project_alter_image_successfully_deleted"));
          DivElement imgDisplay = this.shadowRoot.querySelector("#proj-image-display");
          imgDisplay.style.display = 'none';
        }
      });
    }
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
          return false;
        } else {
          //has the title already been used?
          return ProjectDao.getProjectByName(project.title).then((Project checkExist) {
            if (checkExist != null) {
              titleError = localisation.getTranslation("project_create_title_conflict");
              return false;
            }else{
              return true;
            }
          });
        }
    }).then((bool success) {
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
        } else if (FormHelper.validateReferenceURL(project.reference) == false) {
          referenceError = localisation.getTranslation("project_create_error_reference_invalid");
          success = false;
        }
      }
      
      if (wordCountInput == '') {
        //Word count is not set, set error message
        wordCountError = localisation.getTranslation("project_create_27");
        success = false; 
        return success;
      } else {
        //If word count is set, ensure it is a valid natural number
        int newWordCount = int.parse(wordCountInput, onError: (String wordCountString) {
          wordCountError = localisation.getTranslation("project_create_27");
          success = false;
          return 0;
        });
        //only call API for word count iff parse error didn't occur
        if (newWordCount != 0 && userIsAdmin) {
          return ProjectDao.updateProjectWordCount(project.id, newWordCount)
          .then((int result) {
            if (result == 1) {
              project.wordCount = newWordCount;
            } else if (result == 2) {
              window.alert(localisation.getTranslation("project_alter_word_count_error_1"));
            } else {
              window.alert(localisation.getTranslation("project_alter_word_count_error_2"));
            }
            return true;
          });
        } else {
          return true;
        }
      }
      
      //After word count has been validated (possibly meaning an API call), continue with rest of validation.
    }).then((bool success) {
      if (projectTags != null && projectTags != "") {
        if (FormHelper.validateTagList(projectTags) == false) {
          //Invalid tags detected, set error message
          tagsError = localisation.getTranslation('project_create_invalid_tags');
          success = false;
        } else {
          List<String> list = projectTags.split(" ");
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
      }

      //Parse project deadline info
      DateTime projectDeadline = FormHelper.parseDeadline(years[selectedYear], selectedMonth + 1, selectedDay + 1, selectedHour, selectedMinute);
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
      //Textual input and deadline info have been validated, validate image file
    }).then((bool success) {
     
      //Validate file input
      return _validateImageFileInput().then((bool imageIsValid) {
        if (success && imageIsValid) {
          return true;
        } else {
          return false;
        }
      });
    });
  }
  
  Future<bool> _validateImageFileInput()
  {
    bool success = true;
    return new Future(() {
      File imageFile = null;
      InputElement fileInput = this.shadowRoot.querySelector("#projectImageFile");
      FileList files = fileInput.files;
      if (!files.isEmpty) {
        imageFile = files[0];
      }
      if (imageFile != null) {
        if (imageFile.size > 0) {
          //Check that file does not exceed the maximum allowed file size
          if (imageFile.size < (imageMaxFileSize)) {
            int extensionStartIndex = imageFile.name.lastIndexOf(".");
            //Check that file has an extension
            if (extensionStartIndex >= 0) {
              imageFilename = imageFile.name;
              String extension = imageFilename.substring(extensionStartIndex + 1);
              if (extension != extension.toLowerCase()) {
                extension = extension.toLowerCase();
                imageFilename = imageFilename.substring(0, extensionStartIndex + 1) + extension;
                window.alert(localisation.getTranslation("project_create_18"));
              }
              //Check that the file extension is valid for an image
              if (supportedImageFormats.contains(extension) == false) {
                imageError = sprintf(
                  localisation.getTranslation("project_create_please_upload_valid_image_file"),
                  [".$extension"]
                );
                success = false;
              }
            } else {
              //File has no extension, set error
              imageError = localisation.getTranslation("project_create_image_has_no_extension");
              success = false;
            }
          } else {
            //File is too big, set error
            imageError = localisation.getTranslation("project_create_image_is_too_big");
            success = false;
          } 
        } else {
          //File is empty, set error
          imageError = localisation.getTranslation("project_create_image_file_empty");
          success = false;
        }
      }
      return success;
    });
  }
  
  /**
   * Resizes the [Image] [original] while preserving the aspect ratio. This method is used to resize big 
   * images uploaded with projects so they fit our desired dimensions. Credit (for the logic) to
   * http://opensourcehacker.com/2011/12/01/calculate-aspect-ratio-conserving-resize-for-images-in-javascript/ 
   * via http://stackoverflow.com/a/14731922 
   */
  Image _resizeProjectImage(Image original) 
  {
    int width = original.width;
    int height = original.height;
    double ratio;
    
    if ((width <= imageMaxWidth) && (height <= imageMaxHeight)) {
      return original;
    } else {
      ratio = min(imageMaxWidth / width, imageMaxHeight / height);
      int newWidth = (width * ratio).floor();
      int newHeight = (height * ratio).floor();
      Image resized = copyResize(original, newWidth, newHeight);
      
      return resized;
   
    }
  }
  
  /**
   * Automatically bound to changes on selectedYear
   */
  void selectedYearChanged(int oldValue)
  {
    if (FormHelper.isLeapYear(years[selectedYear])) {
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
}
