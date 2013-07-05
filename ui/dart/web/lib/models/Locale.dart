library SolasMatchDart;

class Locale
{
  String languageName;
  String languageCode;
  String countryName;
  String countryCode;
  
  String print()
  {
    String ret = "{ Locale : { languageName : " + languageName + 
        ", languageCode : " + languageCode + ", countryName : " +
        countryName + ", countryCode : " + countryCode + "} }";
    return ret;
  }
}