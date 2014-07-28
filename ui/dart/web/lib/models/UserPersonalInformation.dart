part of SolasMatchDart;

class UserPersonalInformation
{
  int id;
  int userId;
  String firstName;
  String lastName;
  String mobileNumber;
  String businessNumber;
  String jobTitle;
  String address;
  String city;
  String country;
  bool receiveCredit;
  
  dynamic toJson()
  {
    return {
      "id" : id,
      "userId" : userId,
      "firstName" : firstName,
      "lastName" : lastName,
      "mobileNumber" : mobileNumber,
      "businessNumber" : businessNumber,
      "jobTitle" : jobTitle,
      "address" : address,
      "city" : city,
      "country" : country,
      "receive_credit" : receiveCredit
    };
  }
}