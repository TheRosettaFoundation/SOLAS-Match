import "package:polymer/polymer.dart";
import "dart:async";
import "dart:html";

import "package:sprintf/sprintf.dart";
import "../../lib/SolasMatchDart.dart";

/*
 * TODO
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
  @published int maxfilesize; //TODO: possibly remove or name change
  @published String css;
  
  SelectElement langSelect;
  SelectElement countrySelect;
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
  @observable Localisation localisation;
  @observable Project project;
  @observable List<Language> languages;
  @observable List<Country> countries;
  @observable bool loaded;
  @observable String orgDashboardLink;
  @observable String projectViewLink;
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
  
  /**
   * The constructor for [ProjectAlterForm], handling initialisation of variables.
   */
  ProjectAlterForm.created() : super.created()
  {
    project = new Project();
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
  }
  
  /**
   * Called by the DOM when the ProjectAlterForm element has been inserted into the "live document".
   */
  void enteredView()
  {
    Settings settings = new Settings();
    orgDashboardLink = settings.conf.urls.SiteLocation + "org/dashboard";
    projectViewLink = settings.conf.urls.SiteLocation + "$projectid/view";
    
    String location = settings.conf.urls.SiteLocation;
    
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
   
    loadedList.add(ProjectDao.getProject(projectid).then((Project pro) {
      project = pro;
      ProjectDao.getProjectTags(projectid)
      .then((List<Tag> projTags) {
        if (projTags != null) {
          projTags.forEach((Tag tag) {
            projectTags += tag.label + " ";
          });
        }
      });
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
    
  }
  
  /**
   * Automatically bound to changes on selectedYear
   */
  void selectedYearChanged(int oldValue)
  {
    if (_isLeapYear(years[selectedYear])) {
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