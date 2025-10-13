// src/app/login/login.component.ts
import { Component, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../services/user.service';
import { LoginDTO } from '../dtos/login.dto';
import { NgForm } from '@angular/forms';

@Component({
  selector: 'app-login',
  standalone: false,
  templateUrl: './login.component.html',
  styleUrl: './login.component.css',
})
export class LoginComponent {
  @ViewChild('loginForm') loginForm!: NgForm;
  phonenumber: string = '';
  password: string = '';
  errorMessage: string = ''; // Thêm biến để hiển thị lỗi

  constructor(private router: Router, private userService: UserService) {}

  onPhoneNumberChange() {
    console.log(`Phone typed: ${this.phonenumber}`);
  }

  login() {
    const loginDTO: LoginDTO = {
      phonenumber: this.phonenumber,
      password: this.password,
    };

    this.userService.login(loginDTO).subscribe({
      next: (response: any) => {
        console.log('Login successful, response:', response);
        // Lưu thông tin đăng nhập (nếu cần)
        this.userService.setUser(response); 
        this.router.navigate(['/home']);
      },
      error: (error: any) => {
        console.error('Login failed:', error);
        if (error.status === 403) {
          this.errorMessage = 'Truy cập bị từ chối. Vui lòng kiểm tra thông tin đăng nhập.';
        } else {
          this.errorMessage = error.error || 'Đăng nhập thất bại. Vui lòng thử lại.';
        }
      },
    });
  }
}