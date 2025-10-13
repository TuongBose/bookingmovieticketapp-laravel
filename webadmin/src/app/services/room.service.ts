// src/app/services/room.service.ts

import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { Environment } from '../environments/environment';
import { RoomDTO } from '../dtos/room.dto';

@Injectable({
    providedIn: 'root',
})
export class RoomService {
    constructor(private http: HttpClient) { }

    getRoomsByCinemaId(cinemaId: number): Observable<RoomDTO[]> {
        return this.http.get<RoomDTO[]>(`${Environment.apiBaseUrl}/rooms/cinema/${cinemaId}`)
            .pipe(
                catchError(this.handleError),
                map((data: any[]) => data.map(item => new RoomDTO(item)))
            );
    }

    private handleError(error: HttpErrorResponse): Observable<never> {
        let errorMessage = 'An error occurred';
        if (error.error instanceof ErrorEvent) {
            errorMessage = `Client-side error: ${error.error.message}`;
        } else if (typeof error.error === 'string') {
            errorMessage = error.error;
        } else {
            errorMessage = error.error?.message || error.error?.error || `Server error: ${error.status} - ${error.statusText || error.message}`;
        }
        console.error('Error details:', error);
        return throwError(() => new Error(errorMessage));
    }
}