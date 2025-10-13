// src/app/services/movie.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError, map, retry, timeout } from 'rxjs/operators';
import { Environment } from '../environments/environment';
import { MovieDTO } from '../dtos/movie.dto';

@Injectable({
  providedIn: 'root',
})
export class MovieService {
  private apiMovieNowPlayingUrl = `${Environment.apiBaseUrl}/movies/nowplaying`;
  private apiMovieUpComingUrl = `${Environment.apiBaseUrl}/movies/upcoming`;

  constructor(private http: HttpClient) {}

  getMovieNowPlaying(): Observable<MovieDTO[]> {
    const headers = new HttpHeaders()
      .set('Content-Type', 'application/json')
      .set('Accept', 'application/json');
    return this.http.get<any>(this.apiMovieNowPlayingUrl, { headers: headers, withCredentials: true }).pipe(
      timeout(50000),
      retry(1),
      map(response => this.mapToMovies(response)), // Backend đã trả về danh sách phim, không cần response.results
      catchError(this.handleError)
    );
  }

  getMovieUpComing(): Observable<MovieDTO[]> {
    const headers = new HttpHeaders()
      .set('Content-Type', 'application/json')
      .set('Accept', 'application/json');
    return this.http.get<any>(this.apiMovieUpComingUrl, { headers: headers, withCredentials: true }).pipe(
      timeout(50000),
      retry(1),
      map(response => this.mapToMovies(response)), // Backend đã trả về danh sách phim, không cần response.results
      catchError(this.handleError)
    );
  }

  getAllMovie(): Observable<MovieDTO[]> {
    const headers = new HttpHeaders()
      .set('Content-Type', 'application/json')
      .set('Accept', 'application/json');
    return this.http.get<any>(`${Environment.apiBaseUrl}/movies`, { headers: headers, withCredentials: true }).pipe(
      timeout(5000),
      retry(1),
      map(response => this.mapToMovies(response)), // Backend đã trả về danh sách phim, không cần response.results
      catchError(this.handleError)
    );
  }

  getMovieById(id: number): Observable<MovieDTO> {
    const headers = new HttpHeaders()
      .set('Content-Type', 'application/json')
      .set('Accept', 'application/json');
    return this.http.get<any>(`${Environment.apiBaseUrl}/movies/${id}`, { headers: headers, withCredentials: true }).pipe(
      timeout(5000),
      retry(1),
      map(response => this.mapToMovies([response])[0]), // Chuyển đổi thành MovieDTO
      catchError(this.handleError)
    );
  }

  private mapToMovies(apiMovies: any[]): MovieDTO[] {
    return apiMovies.map(movie => ({
      id: movie.id,
      name: movie.name,
      posterurl: movie.posterurl,
      releasedate: movie.releasedate,
      voteaverage: movie.voteaverage,
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