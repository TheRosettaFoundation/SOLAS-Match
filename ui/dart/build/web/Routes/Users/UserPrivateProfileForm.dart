import "package:polymer/polymer.dart";
import "dart:async";
import "dart:html";

import '../../lib/SolasMatchDart.dart';

@CustomTag('user-private-profile-form')
class UserPrivateProfileForm extends PolymerElement with ChangeNotifier 
{
  // attributes
  @reflectable @published int get userid => __$userid; int __$userid; @reflectable set userid(int value) { __$userid = notifyPropertyChange(#userid, __$userid, value); }
  
  // bound variables
  @reflectable @observable bool get translator => __$translator; bool __$translator; @reflectable set translator(bool value) { __$translator = notifyPropertyChange(#translator, __$translator, value); }
  @reflectable @observable bool get proofreader => __$proofreader; bool __$proofreader; @reflectable set proofreader(bool value) { __$proofreader = notifyPropertyChange(#proofreader, __$proofreader, value); }
  @reflectable @observable bool get interpreter => __$interpreter; bool __$interpreter; @reflectable set interpreter(bool value) { __$interpreter = notifyPropertyChange(#interpreter, __$interpreter, value); }
  
  // observables
  @reflectable @observable bool get isLoaded => __$isLoaded; bool __$isLoaded = false; @reflectable set isLoaded(bool value) { __$isLoaded = notifyPropertyChange(#isLoaded, __$isLoaded, value); }
  @reflectable @observable User get user => __$user; User __$user; @reflectable set user(User value) { __$user = notifyPropertyChange(#user, __$user, value); }
  @reflectable @observable UserPersonalInformation get userInfo => __$userInfo; UserPersonalInformation __$userInfo; @reflectable set userInfo(UserPersonalInformation value) { __$userInfo = notifyPropertyChange(#userInfo, __$userInfo, value); }
  @reflectable @observable int get secondaryLanguageCount => __$secondaryLanguageCount; int __$secondaryLanguageCount; @reflectable set secondaryLanguageCount(int value) { __$secondaryLanguageCount = notifyPropertyChange(#secondaryLanguageCount, __$secondaryLanguageCount, value); }
  @reflectable @observable List<int> get secondaryLanguageArray => __$secondaryLanguageArray; List<int> __$secondaryLanguageArray; @reflectable set secondaryLanguageArray(List<int> value) { __$secondaryLanguageArray = notifyPropertyChange(#secondaryLanguageArray, __$secondaryLanguageArray, value); }
  @reflectable @observable List<Locale> get userSecondaryLanguages => __$userSecondaryLanguages; List<Locale> __$userSecondaryLanguages; @reflectable set userSecondaryLanguages(List<Locale> value) { __$userSecondaryLanguages = notifyPropertyChange(#userSecondaryLanguages, __$userSecondaryLanguages, value); }
  @reflectable @observable List<Language> get languages => __$languages; List<Language> __$languages; @reflectable set languages(List<Language> value) { __$languages = notifyPropertyChange(#languages, __$languages, value); }
  @reflectable @observable List<Country> get countries => __$countries; List<Country> __$countries; @reflectable set countries(List<Country> value) { __$countries = notifyPropertyChange(#countries, __$countries, value); }
  @reflectable @observable String get alert => __$alert; String __$alert; @reflectable set alert(String value) { __$alert = notifyPropertyChange(#alert, __$alert, value); }
  @reflectable @observable Localisation get localisation => __$localisation; Localisation __$localisation; @reflectable set localisation(Localisation value) { __$localisation = notifyPropertyChange(#localisation, __$localisation, value); }
  
  // misc
  int secondaryLanguageLimit;
  List<String> randomWords;
  List<Badge> badges;
  SelectElement langSelect;
  SelectElement countrySelect;
  
  UserPrivateProfileForm.created() : super.created()
  {
    secondaryLanguageLimit = 10;
    userSecondaryLanguages = toObservable(new List<Locale>());
    languages = toObservable(new List<Language>());
    countries = toObservable(new List<Country>());
    secondaryLanguageArray = toObservable(new List<int>());
    badges = new List<Badge>();
    secondaryLanguageCount = 0;
    translator = false;
    proofreader = false;
    interpreter = false;
    alert = "";
  }
  
  void enteredView()
  {
    localisation = new Localisation();
    List<Future<bool>> dataLoaded = new List<Future<bool>>();
    UserDao.getUserPersonalInfo(userid).then((UserPersonalInformation info) {
      userInfo = info;
    });
    
    UserDao.getUserBadges(userid).then((List<Badge> userBadges) {
      badges = userBadges;
      badges.forEach((Badge badge) {
        if (badge.id == 6) {
          translator = true;
        } else if(badge.id == 7) {
          proofreader = true;
        } else if (badge.id == 8) {
          interpreter = true;
        }
      });
    });
      
    dataLoaded.add(UserDao.getUser(userid).then((User u) {
      user = u;
      return true;
    }));
    
    dataLoaded.add(UserDao.getSecondaryLanguages(userid).then((List<Locale> locales) {
      userSecondaryLanguages.addAll(locales);
      return true;
    }));
    
    dataLoaded.add(LanguageDao.getAllLanguages().then((List<Language> langs) {
      Language lang = new Language();
      lang.name = "";
      lang.code = "";
      languages.add(lang);
      languages.addAll(langs);
      return true;
    }));
    
    dataLoaded.add(CountryDao.getAllCountries().then((List<Country> regions) {
      Country any = new Country();
      any.name = "";
      any.code = "";
      countries.add(any);
      countries.addAll(regions);
      return true;
    }));
   
    Future.wait(dataLoaded).then((List<bool> successList) {
      setDefaults(successList);
    });
    isLoaded = true;
  }
  
  void setDefaults(List<bool> successList)
  {
    successList.forEach((bool success) {
      if (!success) {
        print("Some data failed to load!");
      }
    });
    
    //  Bind button click events
    ButtonElement mButton;
    mButton = querySelector("#updateBtn");
    mButton.onClick.listen((event) => submitForm());
    mButton = querySelector("#deleteBtn");
    mButton.onClick.listen((event) => deleteUser());
    
    int secLangLength = userSecondaryLanguages.length > 0 ? userSecondaryLanguages.length : 1;
    
    int nativeLanguageIndex = 0;
    int nativeCountryIndex = 0;
    List<int> secondaryLanguageIndex = new List<int>(secLangLength);
    List<int> secondaryCountryIndex = new List<int>(secLangLength);
    
    langSelect = new SelectElement();
    langSelect.style.width = "82%";
    for (int i = 0; i < languages.length; i++) {
      OptionElement option = new OptionElement();
      Language language = languages[i];
      option.value = language.code;
      option.text = language.name;
      
      langSelect.children.add(option);
      
      if (user.nativeLocale != null) {
        if (languages[i].code == user.nativeLocale.languageCode) {
          nativeLanguageIndex = i;
        }
      }
      
      if (userSecondaryLanguages.length > 0) {
        for (int j = 0; j < userSecondaryLanguages.length; j++) {
          if (languages[i].code == userSecondaryLanguages[j].languageCode) {
            secondaryLanguageIndex[j] = i;
          }
        }
      }
    }
    
    if (userSecondaryLanguages.length == 0) {
      secondaryLanguageIndex[0] = 0;
    }
    
    countrySelect = new SelectElement();
    countrySelect.style.width = "82%";
    for (int i = 0; i < countries.length; i++) {
      var option = new OptionElement()
          ..value = countries[i].code
          ..text = countries[i].name;
      countrySelect.children.add(option);
      
      if (user.nativeLocale != null) {
        if (countries[i].code == user.nativeLocale.countryCode) {
          nativeCountryIndex = i;
        }
      }
      
      if (userSecondaryLanguages.length > 0) {
        for (int j = 0; j < userSecondaryLanguages.length; j++) {
          if (countries[i].code == userSecondaryLanguages[j].countryCode) {
            secondaryCountryIndex[j] = i;
          }
        }
      }
    }
    
    if (userSecondaryLanguages.length == 0) {
      secondaryCountryIndex[0] = 0;
    }
    
    var nativeLanguageDiv = new DivElement()
        ..id = "nativeLanguageDiv";
    var label = new LabelElement()
        ..innerHtml = "<strong>" + localisation.getTranslation("common_native_language") + ":</strong>";
    var nativeLanguageSelect = langSelect.clone(true);
    nativeLanguageSelect.id = "nativeLanguageSelect";
    nativeLanguageSelect.selectedIndex = nativeLanguageIndex;
    var nativeCountrySelect = countrySelect.clone(true);
    nativeCountrySelect.id = "nativeCountrySelect";
    nativeCountrySelect.selectedIndex = nativeCountryIndex;
    nativeLanguageDiv.children.add(label);
    nativeLanguageDiv.children.add(nativeLanguageSelect);
    nativeLanguageDiv.children.add(nativeCountrySelect);
    
    var secondaryLanguageDiv = new DivElement()
        ..id = "secondaryLanguageDiv";
    label = new LabelElement()
        ..innerHtml = "<strong>" + localisation.getTranslation("common_secondary_languages") + ":</strong>";
    secondaryLanguageDiv.children.add(label);
    
    ButtonElement button = new ButtonElement()
        ..id = "addLanguageButton"
        ..innerHtml = "<i class='icon-upload icon-white'></i> " +  localisation.getTranslation("user_private_profile_add_secondary_language") 
        ..classes.add("btn")
        ..classes.add("btn-success")
        ..onClick.listen((event) => addSecondaryLanguage());
    if (userSecondaryLanguages.length > 4) {
      button.disabled = true;
    }
    secondaryLanguageDiv.children.add(button);
    
    button = new ButtonElement()
        ..id = "removeLanguageButton"
        ..innerHtml = "<i class='icon-fire icon-white'></i> " + localisation.getTranslation("common_remove")
        ..classes.add("btn")
        ..classes.add("btn-inverse")
        ..onClick.listen((event) => removeSecondaryLanguage());
    if (userSecondaryLanguages.length < 2) {
      button.disabled = true;
    }
    secondaryLanguageDiv.children.add(button);
    
    DivElement div = querySelector("#language_area");
    div.children.add(nativeLanguageDiv);
    div.children.add(secondaryLanguageDiv);
    
    if (userSecondaryLanguages.length > 0) {
      for (int i = 0; i < userSecondaryLanguages.length; i++) {
        this.addSecondaryLanguage(secondaryLanguageIndex[i], secondaryCountryIndex[i]);
      }
    } else {
      this.addSecondaryLanguage(0, 0);
    }
  }
  
  void addSecondaryLanguage([int languageSelected = 0, int countrySelected = 0])
  {
    if (secondaryLanguageCount < secondaryLanguageLimit) {
      DivElement secondaryLanguageDiv = querySelector("#secondaryLanguageDiv");
      DivElement locale = new DivElement()
          ..id = "secondary_locale_$secondaryLanguageCount";
      SelectElement languageBox = langSelect.clone(true);
      languageBox.id = "secondary_language_$secondaryLanguageCount";
      languageBox.selectedIndex = languageSelected;
      locale.children.add(languageBox);
      SelectElement countryBox = countrySelect.clone(true);
      countryBox.id = "secondary_country_$secondaryLanguageCount";
      countryBox.selectedIndex = countrySelected;
      locale.children.add(countryBox);
      HRElement hr = new HRElement();
      hr.style.width = "60%";
      locale.children.add(hr);
      ButtonElement button = querySelector("#addLanguageButton");
      secondaryLanguageDiv.insertBefore(locale, button);
      secondaryLanguageCount++;
      
      if (secondaryLanguageCount >= secondaryLanguageLimit) {
        button = querySelector("#addLanguageButton");
        button.disabled = true;
      }
      
      button = querySelector("#removeLanguageButton");
      if (button.disabled) {
        button.disabled = false;
      }
    }
  }
  
  void removeSecondaryLanguage()
  {
    if (secondaryLanguageCount > 0) {
      secondaryLanguageCount--;
      var element = querySelector("#secondary_locale_$secondaryLanguageCount");
      element.remove();
      
      ButtonElement button = querySelector("#addLanguageButton");
      if (button.disabled) {
        button.disabled = false; 
      }
      
      if (secondaryLanguageCount < 2) {
        button = querySelector("#removeLanguageButton");
        button.disabled = true;
      }
    }
  }
  
  void submitForm()
  {
    this.alert = "";
    
    try {
      if (user.display_name == "") {
        throw new ArgumentError(localisation.getTranslation("user_private_profile_2"));
      }
      SelectElement nativeLanguageSelect = querySelector("#nativeLanguageSelect");
      SelectElement nativeCountrySelect = querySelector("#nativeCountrySelect");
      if (nativeLanguageSelect.selectedIndex > 0 && nativeCountrySelect.selectedIndex > 0) {
        user.nativeLocale.countryCode = countries[nativeCountrySelect.selectedIndex].code;
        user.nativeLocale.languageCode = languages[nativeLanguageSelect.selectedIndex].code;
      } else if((nativeLanguageSelect.selectedIndex > 0 && nativeCountrySelect.selectedIndex == 0) ||
                (nativeLanguageSelect.selectedIndex == 0 && nativeCountrySelect.selectedIndex > 0)) {
        throw new ArgumentError(localisation.getTranslation("user_private_profile_native_language_blanks"));
      }
      
      if (userInfo.receiveCredit) {
        if (userInfo.firstName == "" || userInfo.lastName == "") {
          throw new ArgumentError(localisation.getTranslation("user_private_profile_7"));
        }
      }

      List<Future<bool>> updated = new List<Future<bool>>();
      
      List<Locale> currentSecondaryLocales = new List<Locale>();
      for (int i = 0; i < secondaryLanguageCount; i++) {
        SelectElement secondaryLanguageSelect = querySelector("#secondary_language_$i");
        SelectElement secondaryCountrySelect = querySelector("#secondary_country_$i");
        if (secondaryLanguageSelect.selectedIndex > 0 && secondaryCountrySelect.selectedIndex > 0) {
          Locale found = userSecondaryLanguages.firstWhere((Locale l) {
            return (l.languageCode == languages[secondaryLanguageSelect.selectedIndex].code
                && l.countryCode == countries[secondaryCountrySelect.selectedIndex].code);
          }, orElse: () {
            Locale locale = new Locale();
            locale.countryCode = countries[secondaryCountrySelect.selectedIndex].code;
            locale.languageCode = languages[secondaryLanguageSelect.selectedIndex].code;
            currentSecondaryLocales.add(locale);
            updated.add(UserDao.addSecondaryLanguage(userid, locale));
          });
          if (found != null) {
            currentSecondaryLocales.add(found);
          }
        } else {
          throw new ArgumentError(localisation.getTranslation("user_private_profile_secondary_languages_failed"));
        }
      }
      
      updated.add(UserDao.saveUserDetails(user));
      updated.add(UserDao.saveUserInfo(userInfo));
      
      userSecondaryLanguages.forEach((Locale locale) {
        currentSecondaryLocales.firstWhere((Locale l) {
          return (l.languageCode == locale.languageCode && l.countryCode == locale.countryCode);
        }, orElse: () {
          updated.add(UserDao.removeSecondaryLanguage(userid, locale.languageCode, locale.countryCode));
        });
      });
      
      if (badges != null && badges.length > 0) {
        bool currentlyTranslator = false;
        bool currentlyProofreader = false;
        bool currentlyInterpreter = false;
        badges.forEach((Badge badge) {
          if (badge.id == 6) {
            currentlyTranslator = true;
          } else if (badge.id == 7) {
            currentlyProofreader = true;
          } else if (badge.id == 8) {
            currentlyInterpreter = true;
          }
        });
        if (currentlyTranslator && !translator) {
          updated.add(UserDao.removeUserBadge(userid, 6));
        } else if (!currentlyTranslator && translator) {
          updated.add(UserDao.addUserBadge(userid, 6));
        }
        if (currentlyProofreader && !proofreader) {
          updated.add(UserDao.removeUserBadge(userid, 7));
        } else if (!currentlyProofreader && proofreader) {
          updated.add(UserDao.addUserBadge(userid, 7));
        }
        if (currentlyInterpreter && !interpreter) {
          updated.add(UserDao.removeUserBadge(userid, 8));
        } else if (!currentlyInterpreter && interpreter) {
          updated.add(UserDao.addUserBadge(userid, 8));
        }
      }
      
      Future.wait(updated).then((List<bool> updatesSuccessful) {
        bool failure = false;
        updatesSuccessful.forEach((bool success) {
          if (!success) {
            print("Failed to save some data");
            failure = true;
          }
        });
        
        if (!failure && alert == "") {
          Settings settings = new Settings();
          window.location.assign(settings.conf.urls.SiteLocation + "$userid/profile");
        } else {
          if (alert == "") {
            throw new ArgumentError("Failed to save some data");
          }
        }
      });
    } catch (e) {
      alert = e.message;
    }
  }
  
  void deleteUser()
  {
    if (window.confirm(localisation.getTranslation("user_private_profile_6"))) {
      UserDao.deleteUser(userid).then((bool success) {
        UserDao.destroyUserSession();
        Settings settings = new Settings();
        window.location.assign(settings.conf.urls.SiteLocation);
      });
    }
  }
}