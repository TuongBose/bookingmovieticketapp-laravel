// src/app/services/statistic.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError, retry, timeout } from 'rxjs/operators';
import { Environment } from '../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class StatisticService {
  private apiUrl = `${Environment.apiBaseUrl}/statistics`; 

  constructor(private http: HttpClient) {}

  getMonthlyStatistics(): Observable<any> {
    const headers = new HttpHeaders()
    .set('Content-Type', 'application/json')
    .set('Accept', 'application/json');
    return this.http.get<any>(this.apiUrl, {headers: headers, withCredentials:true}).pipe(
      timeout(5000),
      retry(1),
      catchError(this.handleError)
    );
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