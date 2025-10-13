class UserLoginDTO {
  String phonenumber;
  String password;

  UserLoginDTO({
    required this.phonenumber,
    required this.password,
  });

  Map<String, dynamic> toJson() {
    return {
      'phonenumber': phonenumber,
      'password': password,
    };
  }
}