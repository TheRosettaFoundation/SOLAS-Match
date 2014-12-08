part of SolasMatchDart;

/**
 * Contains various helper methods to be used in Form classes, including some input validation.
 */
class FormHelper
{
  /**
   * This is a simple method to parse the deadline provided for the project.
   */
  static DateTime parseDeadline(int year, int month, int day, int hour, int minute)
  {
    DateTime ret = new DateTime(year, month, day, hour, minute);
    return ret;
  }
  
  /**
   * This is a simple method to check if a year is a leap year or not.
   */
   static bool isLeapYear(int year)
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
   
  /**
   * TODO: Update docs below
   * This is a simple method to parse the project tags from the text input.
   */
  static List<Tag> parseTagsInput(String tags)
  {
    List<String> labels = tags.trim().split(" ");
    List<Tag> ret = new List<Tag>();
    
    labels.forEach((String tagName) {
      Tag t = new Tag();
      t.label = tagName;
      ret.add(t);
    });
    
    return ret;
  }
  
  /**
   * Uses a regular expression to validate the the reference URL for a project (if one is provided) actually is
   * a URL.
   * Credit to http://stackoverflow.com/a/24058129/1799985
   * 
   * Returns true if the provided URL is valid, false otherwise.
   */
  static bool validateReferenceURL(String url)
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
   * Validates user input of text for [Tag]s to catch disallowed characters.
   */
  static bool validateTagList(String tagList)
  {
    if (tagList.indexOf(new RegExp(r'[^a-z0-9\-\s]')) != -1) {
      return false;
    } else {
      return true;
    }
  }
}