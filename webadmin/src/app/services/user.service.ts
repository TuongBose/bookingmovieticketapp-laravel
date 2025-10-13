// src/app/services/user.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { LoginDTO } from '../dtos/login.dto';
import { Environment } from '../environments/environment';
import { UserDTO } from '../dtos/user.dto';
import { HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { catchError, map, retry, timeout } from 'rxjs/operators';
import { throwError } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class UserService {
  constructor(private http: HttpClient) { }

  login(loginDTO: LoginDTO): Observable<any> {
    return this.http.post<any>(`${Environment.apiBaseUrl}/users/login/admin`, loginDTO);
  }

  setUser(user: any): void {
    localStorage.setItem('user', JSON.stringify(user));
    localStorage.setItem('isLoggedIn', 'true');
  }

  isLoggedIn(): boolean {
    return localStorage.getItem('isLoggedIn') === 'true';
  }

  getUser(): any {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }

  logout(): void {
    localStorage.removeItem('user');
    localStorage.removeItem('isLoggedIn');
  }

  getAllUserAdmin(): Observable<UserDTO[]> {
    const headers = new HttpHeaders()
      .set('Content-Type', 'application/json')
      .set('Accept', 'application/json');
    return this.http.get<any>(`${Environment.apiBaseUrl}/users/admin`, { headers: headers, withCredentials: true }).pipe(
      timeout(5000),
      retry(1),
      map(response => this.mapToUsers(response)), // Backend đã trả về danh sách người dùng, không cần response.results
      catchError(this.handleError)
    );
  }

  getAllUserCustomer(): Observable<UserDTO[]> {
    const headers = new HttpHeaders()
      .set('Content-Type', 'application/json')
      .set('Accept', 'application/json');
    return this.http.get<any>(`${Environment.apiBaseUrl}/users/customer`, { headers: headers, withCredentials: true }).pipe(
      timeout(5000),
      retry(1),
      map(response => this.mapToUsers(response)), // Backend đã trả về danh sách người dùng, không cần response.results
      catchError(this.handleError)
    );
  }

  updateUserStatus(userId: number, isActive: boolean): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(`${Environment.apiBaseUrl}/users/${userId}/status`, { isActive })
      .pipe(catchError(this.handleError));
  }

  updateUser(userId: number, userData: any): Observable<any> {
    const headers = new HttpHeaders()
    .set('Content-Type', 'application/json')
      .set('Accept', 'application/json')
      .set('Authorization', `Bearer ${localStorage.getItem('access_token')}`); // Thêm token vào header nếu cần
    return this.http.put(`${Environment.apiBaseUrl}/users/${userId}`, userData, {
      headers: headers,
      withCredentials: true,
    }).pipe(
      timeout(5000),
      retry(1),
      catchError(this.handleError)
    );
  }

  private mapToUsers(apiUsers: any[]): UserDTO[] {
    return apiUsers.map(user => ({
      id: user.id,
      name: user.name,
      email: user.email,
      password: user.password,
      phonenumber: user.phonenumber,
      address: user.address,
      dateofbirth: user.dateofbirth,
      createdat: user.createdat,
      isactive: user.isactive,
      rolename: user.rolename,
      imagename: user.imagename,
    }));
  }

  private handleError(error: HttpErrorResponse) {
    console.error('An error occurred:', error);

    if (error.status === 0) {
      return throwError(() => 'Connection to server failed. Please check if the backend is running.');
    }

    const errorMessage = error.error?.message || error.message || 'Unknown error occurred';
    return throwError(() => errorMessage);
  }
}