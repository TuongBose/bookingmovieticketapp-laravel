import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import '../dtos/UserLoginDTO.dart';
import '../services/UserService.dart';
import 'confirm_otp_screen.dart';

class QuenMatKhauScreen extends StatefulWidget {
  const QuenMatKhauScreen({super.key});

  @override
  State<QuenMatKhauScreen> createState() => _QuenMatKhauScreenState();
}

class _QuenMatKhauScreenState extends State<QuenMatKhauScreen> {
  final TextEditingController phoneController = TextEditingController();
  final UserService _userService = UserService();
  final FirebaseAuth _auth = FirebaseAuth.instance;
  String? phoneError;
  bool isPhoneValid = false;
  String? _verificationId;
  bool _isProcessingOTP = false;

  void validatePhone() {
    final phone = phoneController.text.trim();
    final phoneRegex = RegExp(r'^\d{10}$');

    setState(() {
      isPhoneValid = phoneRegex.hasMatch(phone);
      phoneError =
          isPhoneValid
              ? null
              : 'Số điện thoại không hợp lệ, vui lòng nhập 10 chữ số.';
    });
  }

  @override
  void initState() {
    super.initState();
    phoneController.addListener(validatePhone);
    _cleanupFirebaseSession();
  }

  @override
  void dispose() {
    phoneController.dispose();
    _cleanupFirebaseSession();
    super.dispose();
  }

  Future<void> _cleanupFirebaseSession() async {
    try {
      if (_auth.currentUser != null) {
        await _auth.signOut();
        print('Firebase user signed out during cleanup');
      }
    } catch (e) {
      print('Error during Firebase cleanup: $e');
    }
  }

  Future<void> _sendOTP() async {
    if (!isPhoneValid) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Vui lòng nhập số điện thoại hợp lệ')),
      );
      return;
    }

    setState(() {
      phoneError = null;
      _isProcessingOTP = true;
    });

    String phoneNumberRequest = phoneController.text;
    String phoneNumber = phoneController.text;
    if (!phoneNumber.startsWith('+84')) {
      phoneNumber = '+84${phoneNumber}';
    }

    try {
      bool exists = await _userService.checkDoesNotExistingPhoneNumber(
        phoneNumberRequest,
      );
      if (!exists) {
        setState(() {
          _isProcessingOTP = false;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Số điện thoại này không tồn tại')),
        );
        return;
      }

      await _cleanupFirebaseSession();

      await _auth.verifyPhoneNumber(
        phoneNumber: phoneNumber,
        timeout: const Duration(seconds: 60),
        verificationCompleted: (PhoneAuthCredential credential) async {
          if (_isProcessingOTP) {
            await _auth.signInWithCredential(credential);
            _navigateToConfirmOTPScreen(phoneNumber, _verificationId);
          }
        },
        verificationFailed: (FirebaseAuthException e) {
          setState(() {
            _isProcessingOTP = false;
          });
          ScaffoldMessenger.of(
            context,
          ).showSnackBar(SnackBar(content: Text('Lỗi gửi OTP: ${e.message}')));
        },
        codeSent: (String verificationId, int? resendToken) {
          setState(() {
            _verificationId = verificationId;
          });
          if (_isProcessingOTP) {
            _navigateToConfirmOTPScreen(phoneNumber, verificationId);
          }
        },
        codeAutoRetrievalTimeout: (String verificationId) {
          setState(() {
            _verificationId = verificationId;
          });
        },
      );
    } catch (e) {
      setState(() {
        _isProcessingOTP = false;
      });
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Lỗi: $e')));
    }
  }

  void _navigateToConfirmOTPScreen(
    String phoneNumber, [
    String? verificationId,
  ]) {
    if (!mounted) return;

    Navigator.push(
      context,
      MaterialPageRoute(
        builder:
            (context) => ConfirmOTPScreen(
              phoneNumber: phoneNumber,
              verificationId: verificationId ?? '',
              onOtpVerified: () {
                _showResetPasswordScreen(phoneNumber, verificationId ?? '');
              },
            ),
      ),
    ).then((_) {
      // This runs when we return from the OTP screen
      if (mounted && _isProcessingOTP) {
        setState(() {
          _isProcessingOTP = false; // Reset the flag when we return
        });
      }
    });
  }

  void _showResetPasswordScreen(String phoneNumber, String verificationId) {
    TextEditingController newPasswordController = TextEditingController();
    TextEditingController confirmPasswordController = TextEditingController();
    String passwordError = '';
    String phoneNumberRequest = phoneController.text;
    bool isResetting = false;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder:
          (dialogContext) => StatefulBuilder(
            builder:
                (dialogContext, setDialogState) => AlertDialog(
                  title: const Text('Đặt lại mật khẩu'),
                  content: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      TextField(
                        controller: newPasswordController,
                        obscureText: true,
                        decoration: InputDecoration(
                          labelText: 'Mật khẩu mới',
                          border: const OutlineInputBorder(),
                          errorText:
                              passwordError.isEmpty ? null : passwordError,
                        ),
                      ),
                      const SizedBox(height: 12),
                      TextField(
                        controller: confirmPasswordController,
                        obscureText: true,
                        decoration: InputDecoration(
                          labelText: 'Xác nhận mật khẩu',
                          border: const OutlineInputBorder(),
                          errorText:
                              passwordError.isEmpty ? null : passwordError,
                        ),
                      ),
                      if (isResetting)
                        const Padding(
                          padding: EdgeInsets.only(top: 16.0),
                          child: Center(child: CircularProgressIndicator()),
                        ),
                    ],
                  ),
                  actions: [
                    TextButton(
                      onPressed:
                          isResetting
                              ? null
                              : () {
                                Navigator.pop(dialogContext);
                              },
                      child: const Text('Hủy'),
                    ),
                    ElevatedButton(
                      onPressed:
                          isResetting
                              ? null
                              : () async {
                                setDialogState(() {
                                  passwordError = '';
                                  isResetting = true;
                                });

                                if (newPasswordController.text.isEmpty ||
                                    confirmPasswordController.text.isEmpty) {
                                  setDialogState(() {
                                    passwordError =
                                        'Vui lòng nhập đầy đủ mật khẩu';
                                    isResetting = false;
                                  });
                                  return;
                                }

                                if (newPasswordController.text !=
                                    confirmPasswordController.text) {
                                  setDialogState(() {
                                    passwordError = 'Mật khẩu không khớp';
                                    isResetting = false;
                                  });
                                  return;
                                }

                                try {
                                  // Debug log để kiểm tra
                                  print(
                                    'Calling resetPassword API with phoneNumber: $phoneNumber',
                                  );
                                  print(
                                    'New password: ${newPasswordController.text}',
                                  );

                                  UserLoginDTO userLoginDTO = UserLoginDTO(
                                    phonenumber: phoneNumberRequest,
                                    password: newPasswordController.text,
                                  );
                                  await _userService.resetPassword(
                                    userLoginDTO,
                                  );
                                  print('Reset password API call successful');
                                  await _cleanupFirebaseSession();
                                  setState(() {
                                    _verificationId = null;
                                    _isProcessingOTP = false;
                                  });
                                  Navigator.pop(dialogContext);

                                  if (!mounted) return;

                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                        'Đã thay đổi mật khẩu thành công',
                                      ),
                                    ),
                                  );
                                  Navigator.of(context).pushNamedAndRemoveUntil('/default', (route)=>false, arguments: 3);
                                } catch (e) {
                                  print('Error during resetPassword: $e');
                                  setDialogState(() {
                                    passwordError = 'Lỗi: $e';
                                    isResetting = false;
                                  });
                                }
                              },
                      child: const Text('Xác nhận'),
                    ),
                  ],
                ),
          ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(color: Colors.blue),
        elevation: 0,
        backgroundColor: Colors.white,
      ),
      backgroundColor: Colors.white,
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 24),
            const Text(
              "Quên mật khẩu?",
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 20),
            ),
            const SizedBox(height: 12),
            const Text(
              "Vui lòng nhập số điện thoại. Chúng tôi sẽ gửi mã OTP để đặt lại mật khẩu.",
              style: TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 24),
            TextField(
              controller: phoneController,
              keyboardType: TextInputType.phone,
              decoration: InputDecoration(
                hintText: "Số điện thoại",
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
                errorText: phoneError,
              ),
            ),
            const Spacer(),
            ElevatedButton(
              onPressed: (isPhoneValid && !_isProcessingOTP) ? _sendOTP : null,
              style: ElevatedButton.styleFrom(
                minimumSize: const Size.fromHeight(48),
                backgroundColor:
                    (isPhoneValid && !_isProcessingOTP)
                        ? Colors.blue
                        : Colors.grey[300],
              ),
              child:
                  _isProcessingOTP
                      ? const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              color: Colors.white,
                            ),
                          ),
                          SizedBox(width: 10),
                          Text("Đang xử lý..."),
                        ],
                      )
                      : const Text("Yêu cầu OTP"),
            ),
          ],
        ),
      ),
    );
  }
}
