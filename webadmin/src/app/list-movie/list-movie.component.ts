// src/app/list-movie/list-movie.component.ts
import { Component, OnInit } from '@angular/core';
import { MovieService } from '../services/movie.service';
import { MovieDTO } from '../dtos/movie.dto';

@Component({
  selector: 'app-list-movie',
  standalone: false,
  templateUrl: './list-movie.component.html',
  styleUrls: ['./list-movie.component.css'],
})
export class ListMovieComponent implements OnInit {
  nowShowingMovies: MovieDTO[] = [];
  comingSoonMovies: MovieDTO[] = [];
  activeTab: string = 'nowShowing';
  errorMessage: string = '';

  constructor(private movieService: MovieService) {}

  ngOnInit(): void {
    this.fetchMovies();
  }

  fetchMovies(): void {
    // Lấy phim đang chiếu
    this.movieService.getMovieNowPlaying().subscribe({
      next: (movies) => {
        this.nowShowingMovies = movies;
      },
      error: (error) => {
        console.error('Error fetching now playing movies:', error);
        this.errorMessage = 'Không thể tải danh sách phim đang chiếu.';
      },
    });

    // Lấy phim sắp chiếu
    this.movieService.getMovieUpComing().subscribe({
      next: (movies) => {
        this.comingSoonMovies = movies;
      },
      error: (error) => {
        console.error('Error fetching upcoming movies:', error);
        this.errorMessage = 'Không thể tải danh sách phim sắp chiếu.';
      },
    });
  }

  setActiveTab(tab: string): void {
    this.activeTab = tab;
  }
}