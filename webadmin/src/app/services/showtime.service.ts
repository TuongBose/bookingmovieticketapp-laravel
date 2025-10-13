// src/app/services/movie.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError, map, retry, timeout } from 'rxjs/operators';
import { Environment } from '../environments/environment';
import { ShowtimeDTO } from '../dtos/showtime.dto';
import { HttpParams } from '@angular/common/http';

@Injectable({
    providedIn: 'root',
})
export class ShowTimeService {
    constructor(private http: HttpClient) { }

    getShowTimesByMovieIdAndCinemaIdAndDate(movieId: number, cinemaId: number, date: string): Observable<ShowtimeDTO[]> {
        const params = new HttpParams()
            .set('movieId', movieId.toString())
            .set('cinemaId', cinemaId.toString())
            .set('date', date);

        return this.http.get<ShowtimeDTO[]>(`${Environment.apiBaseUrl}/showtimes`, { params })
            .pipe(
                catchError(this.handleError),
                map((data: any[]) => data.map(item => new ShowtimeDTO(item)))
            );
    }

    getShowTimesByCinemaIdAndDate(cinemaId: number, date: string): Observable<ShowtimeDTO[]> {
        const params = new HttpParams()
            .set('cinemaId', cinemaId.toString())
            .set('date', date);

        return this.http.get<ShowtimeDTO[]>(`${Environment.apiBaseUrl}/showtimes/cinemaanddate`, { params })
            .pipe(
                catchError(this.handleError),
                map((data: any[]) => data.map(item => new ShowtimeDTO(item)))
            );
    }

    getShowTimeById(id: number): Observable<ShowtimeDTO> {
        return this.http.get<ShowtimeDTO>(`${Environment.apiBaseUrl}/showtimes/${id}`)
            .pipe(
                catchError(this.handleError),
                map((data: any) => new ShowtimeDTO(data))
            );
    }

    getBookingsCountForShowTime(showtimeId: number): Observable<number> {
        return this.http.get<number>(`${Environment.apiBaseUrl}/showtimes/${showtimeId}/bookings-count`)
            .pipe(catchError(this.handleError));
    }

    updateShowTimeStatus(showtimeId: number, isActive: boolean): Observable<{message:string}> {
        return this.http.put<{message:string}>(`${Environment.apiBaseUrl}/showtimes/${showtimeId}/status`, { isActive })
            .pipe(catchError(this.handleError));
    }

    createShowTime(formData: FormData): Observable<any> {
        const headers = new HttpHeaders()
            .set('Accept', 'application/json')
            .set('Authorization', `Bearer ${localStorage.getItem('access_token')}`); // Thêm token vào header nếu cần
        return this.http.post(`${Environment.apiBaseUrl}/showtimes`, formData, {
            headers: headers,
            withCredentials: true,
        }).pipe(
            timeout(5000),
            retry(1),
            catchError(this.handleError)
        );
    }

    private handleError(error: HttpErrorResponse): Observable<never> {
        let errorMessage = 'An error occurred';
        if (error.error instanceof ErrorEvent) {
            // Lỗi phía client (mạng, CORS, v.v.)
            errorMessage = `Client-side error: ${error.error.message}`;
        } else if (typeof error.error === 'string') {
            // Backend trả về lỗi dạng chuỗi
            errorMessage = error.error;
        } else {
            // Backend trả về lỗi dạng JSON hoặc không xác định
            errorMessage = error.error?.message || error.error?.error || `Server error: ${error.status} - ${error.statusText || error.message}`;
        }
        console.error('Error details:', error); // Ghi log chi tiết để debug
        return throwError(() => new Error(errorMessage));
    }
}