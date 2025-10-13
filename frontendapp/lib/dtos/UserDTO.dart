class UserDTO{
  final String name;
  final String email;
  final String password;
  final String retypepassword;
  final String phonenumber;
  final String dateofbirth;
  String? address;

  UserDTO({
    required this.name,
    required this.email,
    required this.password,
    required this.retypepassword,
    required this.phonenumber,
    required this.dateofbirth,
    this.address,
  });

  Map<String,dynamic> toJson() {
    return {
      'name': name,
      'email': email,
      'password': password,
      'retypepassword': retypepassword,
      'phonenumber': phonenumber,
      'dateofbirth': dateofbirth,
      'address': address,
    };
  }
}