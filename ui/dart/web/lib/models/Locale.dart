part of SolasMatchDart;

class Locale
{
  String languageName;
  String languageCode;
  String countryName;
  String countryCode;
  
  dynamic toJson()
  {
    return {
      "languageName" : languageName,
      "languageCode" : languageCode,
      "countryName" : countryName,
      "countryCode" : countryCode
    };
  }
  
  String print()
  {
    String ret = "{ Locale : { languageName : " + languageName + 
        ", languageCode : " + languageCode + ", countryName : " +
        countryName + ", countryCode : " + countryCode + "} }";
    return ret;
  }
}