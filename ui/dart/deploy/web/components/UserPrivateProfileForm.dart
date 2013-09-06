library SolasMatchDart;

import "package:web_ui/web_ui.dart";
import "dart:async";
import "dart:json";
import "dart:html";

import '../DataAccessObjects/LanguageDao.dart';
import '../DataAccessObjects/CountryDao.dart';
import '../DataAccessObjects/UserDao.dart';

import '../lib/models/Badge.dart';
import '../lib/models/User.dart';
import '../lib/models/UserPersonalInformation.dart';
import '../lib/models/Locale.dart';
import '../lib/models/Language.dart';
import '../lib/models/Country.dart';
import '../lib/Settings.dart';
import '../lib/Localisation.dart';

class UserPrivateProfileForm extends WebComponent
{
  // xtag attribute
  int userId;
  
  // bound variables
  @observable bool translator;
  @observable bool proofreader;
  @observable bool interpreter;
  
  // observables
  @observable bool isLoaded = false;
  @observable User user;
  @observable UserPersonalInformation userInfo;
  @observable int secondaryLanguageCount;
  @observable List<int> secondaryLanguageArray;
  @observable List<Locale> userSecondaryLanguages;
  @observable List<Language> languages;
  @observable List<Country> countries;
  @observable String alert;
  
  // misc
  List<String> randomWords;
  List<Badge> badges;
  SelectElement langSelect;
  SelectElement countrySelect;
  
  UserPrivateProfileForm()
  {
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
  
  void inserted()
  {
    List<Future<bool>> dataLoaded = new List<Future<bool>>();
    UserDao.getUserPersonalInfo(userId).then((UserPersonalInformation info) {
      userInfo = info;
    });
    
    UserDao.getUserBadges(userId).then((List<Badge> userBadges) {
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
      
    dataLoaded.add(UserDao.getUser(userId).then((User u) {
      user = u;
      return true;
    }));
    
    dataLoaded.add(UserDao.getSecondaryLanguages(userId).then((List<Locale> locales) {
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
    
    Future.wait(dataLoaded).then((List<bool> successList) => setDefaults(successList)); 
  }
  
  void setDefaults(List<bool> successList)
  {
    successList.forEach((bool success) {
      if (!success) {
        print("Some data failed to load!");
      }
    });
    
    int secLangLength = userSecondaryLanguages.length > 0 ? userSecondaryLanguages.length : 1;
    
    int nativeLanguageIndex = 0;
    int nativeCountryIndex = 0;
    List<int> secondaryLanguageIndex = new List<int>(secLangLength);
    List<int> secondaryCountryIndex = new List<int>(secLangLength);
    
    langSelect = new SelectElement();
    langSelect.style.width = "82%";
    for (int i = 0; i < languages.length; i++) {
      var option = new OptionElement()
          ..value = languages[i].code
          ..text = languages[i].name;
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
        ..innerHtml = "<strong>" + Localisation.getTranslation("common_native_language") + ":</strong>";
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
        ..innerHtml = "<strong>" + Localisation.getTranslation("common_secondary_languages") + ":</strong>";
    secondaryLanguageDiv.children.add(label);
    
    ButtonElement button = new ButtonElement()
        ..id = "addLanguageButton"
        ..innerHtml = "<i class='icon-upload icon-white'></i> " +  Localisation.getTranslation("user_private_profile_add_secondary_language") 
        ..classes.add("btn")
        ..classes.add("btn-success")
        ..onClick.listen((event) => addSecondaryLanguage());
    if (userSecondaryLanguages.length > 4) {
      button.disabled = true;
    }
    secondaryLanguageDiv.children.add(button);
    
    button = new ButtonElement()
        ..id = "removeLanguageButton"
        ..innerHtml = "<i class='icon-fire icon-white'></i> " + Localisation.getTranslation("common_remove")
        ..classes.add("btn")
        ..classes.add("btn-inverse")
        ..onClick.listen((event) => removeSecondaryLanguage());
    if (userSecondaryLanguages.length < 2) {
      button.disabled = true;
    }
    secondaryLanguageDiv.children.add(button);
    
    DivElement div = query("#language_area");
    div.children.add(nativeLanguageDiv);
    div.children.add(secondaryLanguageDiv);
    
    if (userSecondaryLanguages.length > 0) {
      for (int i = 0; i < userSecondaryLanguages.length; i++) {
        this.addSecondaryLanguage(secondaryLanguageIndex[i], secondaryCountryIndex[i]);
      }
    } else {
      this.addSecondaryLanguage(0, 0);
    }
    isLoaded = true;
  }
  
  void addSecondaryLanguage([int languageSelected = 0, int countrySelected = 0])
  {
    if (secondaryLanguageCount < 5) {
      DivElement secondaryLanguageDiv = query("#secondaryLanguageDiv");
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
      ButtonElement button = query("#addLanguageButton");
      secondaryLanguageDiv.insertBefore(locale, button);
      secondaryLanguageCount++;
      
      if (secondaryLanguageCount > 4) {
        button = query("#addLanguageButton");
        button.disabled = true;
      }
      
      button = query("#removeLanguageButton");
      if (button.disabled) {
        button.disabled = false;
      }
    }
  }
  
  void removeSecondaryLanguage()
  {
    if (secondaryLanguageCount > 0) {
      secondaryLanguageCount--;
      var element = query("#secondary_locale_$secondaryLanguageCount");
      element.remove();
      
      ButtonElement button = query("#addLanguageButton");
      if (button.disabled) {
        button.disabled = false; 
      }
      
      if (secondaryLanguageCount < 2) {
        button = query("#removeLanguageButton");
        button.disabled = true;
      }
    }
  }
  
  void submitForm()
  {
    this.alert = "";
    if (user.display_name == "") {
      alert = Localisation.getTranslation("user_private_profile_2");
    } else {
      List<Future<bool>> updated = new List<Future<bool>>();
      SelectElement nativeLanguageSelect = query("#nativeLanguageSelect");
      SelectElement nativeCountrySelect = query("#nativeCountrySelect");
      if (nativeLanguageSelect.selectedIndex > 0 && nativeCountrySelect.selectedIndex > 0) {
        user.nativeLocale.countryCode = countries[nativeCountrySelect.selectedIndex].code;
        user.nativeLocale.languageCode = languages[nativeLanguageSelect.selectedIndex].code;
      }
      updated.add(UserDao.saveUserDetails(user));
      updated.add(UserDao.saveUserInfo(userInfo));
      
      List<Locale> currentSecondaryLocales = new List<Locale>();
      for (int i = 0; i < secondaryLanguageCount; i++) {
        SelectElement secondaryLanguageSelect = query("#secondary_language_$i");
        SelectElement secondaryCountrySelect = query("#secondary_country_$i");
        if (secondaryLanguageSelect.selectedIndex > 0 && secondaryCountrySelect.selectedIndex > 0) {
          Locale found = userSecondaryLanguages.firstWhere((Locale l) {
            return (l.languageCode == languages[secondaryLanguageSelect.selectedIndex].code
                && l.countryCode == countries[secondaryCountrySelect.selectedIndex].code);
          }, orElse: () {
            Locale locale = new Locale();
            locale.countryCode = countries[secondaryCountrySelect.selectedIndex].code;
            locale.languageCode = languages[secondaryLanguageSelect.selectedIndex].code;
            currentSecondaryLocales.add(locale);
            updated.add(UserDao.addSecondaryLanguage(userId, locale));
          });
          if (found != null) {
            currentSecondaryLocales.add(found);
          }
        }
      }
      
      userSecondaryLanguages.forEach((Locale locale) {
        currentSecondaryLocales.firstWhere((Locale l) {
          return (l.languageCode == locale.languageCode && l.countryCode == locale.countryCode);
        }, orElse: () {
          updated.add(UserDao.removeSecondaryLanguage(userId, locale.languageCode, locale.countryCode));
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
          updated.add(UserDao.removeUserBadge(userId, 6));
        } else if (!currentlyTranslator && translator) {
          updated.add(UserDao.addUserBadge(userId, 6));
        }
        if (currentlyProofreader && !proofreader) {
          updated.add(UserDao.removeUserBadge(userId, 7));
        } else if (!currentlyProofreader && proofreader) {
          updated.add(UserDao.addUserBadge(userId, 7));
        }
        if (currentlyInterpreter && !interpreter) {
          updated.add(UserDao.removeUserBadge(userId, 8));
        } else if (!currentlyInterpreter && interpreter) {
          updated.add(UserDao.addUserBadge(userId, 8));
        }
      }
      
      Future.wait(updated).then((List<bool> updatesSuccessful) {
        updatesSuccessful.forEach((bool success) {
          if (!success) {
            print("Failed to save some data");
          } else {
            Settings settings = new Settings();
            window.location.assign(settings.conf.urls.SiteLocation + "$userId/profile");
          }
        });
      });
    }
  }
  
  void deleteUser()
  {
    if (window.confirm(Localisation.getTranslation("user_private_profile_6"))) {
      UserDao.deleteUser(userId).then((bool success) {
        UserDao.destroyUserSession();
        Settings settings = new Settings();
        window.location.assign(settings.conf.urls.SiteLocation);
      });
    }
  }
}