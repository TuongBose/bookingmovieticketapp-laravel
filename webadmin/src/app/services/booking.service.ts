import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { Environment } from '../environments/environment';
import { BookingDTO } from '../dtos/booking.dto';

@Injectable({
  providedIn: 'root'
})
export class BookingService {
  constructor(private http: HttpClient) { }

  getBookingsByShowtimeId(showtimeId: number): Observable<BookingDTO[]> {
    return this.http.get<BookingDTO[]>(`${Environment.apiBaseUrl}/bookings/showtimes/${showtimeId}/bookings`)
      .pipe(catchError(this.handleError));
  }

  getBookingsByUserId(userId: number): Observable<BookingDTO[]> {
    return this.http.get<BookingDTO[]>(`${Environment.apiBaseUrl}/bookings/users/${userId}/bookings`)
      .pipe(catchError(this.handleError));
  }

  getAllBookings(): Observable<BookingDTO[]> {
    return this.http.get<BookingDTO[]>(Environment.apiBaseUrl + '/bookings')
      .pipe(catchError(this.handleError));
  }

  private handleError(error: HttpErrorResponse): Observable<never> {
    let errorMessage = 'An error occurred';
    if (error.error instanceof ErrorEvent) {
      errorMessage = error.error.message;
    } else {
      errorMessage = error.error || `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    return throwError(() => new Error(errorMessage));
  }
}