import { Component, OnInit, ViewChild, TemplateRef } from '@angular/core';
import { ShowtimeDTO } from '../dtos/showtime.dto';
import { ShowTimeService } from '../services/showtime.service';
import { CinemaDTO } from '../dtos/cinema.dto';
import { CinemaService } from '../services/cinema.service';
import { MovieDTO } from '../dtos/movie.dto';
import { MovieService } from '../services/movie.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { RoomDTO } from '../dtos/room.dto';
import { RoomService } from '../services/room.service';
import { catchError, timeout, throwError } from 'rxjs';

@Component({
    selector: 'app-list-showtime',
    standalone: false,
    templateUrl: './list-showtime.component.html',
    styleUrls: ['./list-showtime.component.css']
})
export class ListShowtimeComponent implements OnInit {
    showtimes: ShowtimeDTO[] = [];
    showtimesByMovie: { movieId: number, movieName: string, showtimes: ShowtimeDTO[], posterURL: string }[] = [];
    cinemas: CinemaDTO[] = [];
    movies: MovieDTO[] = [];
    rooms: RoomDTO[] = [];
    selectedCinemaId: number | null = null;
    selectedDate: string | null = null;
    selectedMovieId: number | null = null;
    showOnlyActive: boolean = false;
    errorMessage: string | null = null;
    successMessage: string | null = null;
    isLoading: boolean = false;
    bookingsCountMap: Map<number, number> = new Map();
    selectedShowtime: ShowtimeDTO | null = null;
    selectedShowtimeMovieName: string = '';

    @ViewChild('addShowtimeModal') addShowtimeModal!: TemplateRef<any>;
    @ViewChild('showtimeDetailModal') showtimeDetailModal!: TemplateRef<any>;

    // Dữ liệu cho form thêm suất chiếu (độc lập với bộ lọc)
    newShowtime = {
        cinemaId: 0,
        movieId: 0,
        roomId: 0,
        showdate: '',
        starttime: '',
        price: 0
    };

    constructor(
        private showTimeService: ShowTimeService,
        private cinemaService: CinemaService,
        private movieService: MovieService,
        private roomService: RoomService,
        private modalService: NgbModal
    ) { }

    ngOnInit(): void {
        this.loadCinemas();
        this.loadMovies();
    }

    loadCinemas(): void {
        this.cinemaService.getAllCinema().subscribe({
            next: (cinemas) => {
                this.cinemas = cinemas;
            },
            error: (error) => {
                this.errorMessage = 'Không thể tải danh sách rạp: ' + error.message;
                console.error('[loadCinemas] Lỗi khi lấy danh sách rạp:', error);
            }
        });
    }

    loadMovies(): void {
        this.movieService.getAllMovie().subscribe({
            next: (nowPlaying) => {
                this.movies = nowPlaying;
                this.movieService.getMovieUpComing().subscribe({
                    next: (upcoming) => {
                        this.movies = [...this.movies, ...upcoming];
                        if (this.showtimes.length > 0) {
                            this.filterAndGroupShowtimes();
                        }
                    },
                    error: (error) => {
                        this.errorMessage = 'Không thể tải danh sách phim sắp chiếu: ' + error.message;
                        console.error('[loadMovies] Lỗi khi lấy danh sách phim sắp chiếu:', error);
                    }
                });
            },
            error: (error) => {
                this.errorMessage = 'Không thể tải danh sách phim đang chiếu: ' + error.message;
                console.error('[loadMovies] Lỗi khi lấy danh sách phim đang chiếu:', error);
            }
        });
    }

    fetchShowtimes(): void {
        if (!this.selectedCinemaId || !this.selectedDate) {
            this.errorMessage = 'Vui lòng chọn rạp và ngày để tìm kiếm.';
            console.warn('[fetchShowtimes] Thiếu rạp hoặc ngày:', {
                selectedCinemaId: this.selectedCinemaId,
                selectedDate: this.selectedDate
            });
            return;
        }

        this.isLoading = true;
        this.errorMessage = null;
        this.successMessage = null;

        this.showTimeService.getShowTimesByCinemaIdAndDate(this.selectedCinemaId, this.selectedDate)
            .subscribe({
                next: (showtimes) => {
                    this.showtimes = showtimes;
                    this.validateShowtimes();
                    this.loadBookingsCount();
                    this.filterAndGroupShowtimes();
                    this.isLoading = false;
                },
                error: (error) => {
                    this.errorMessage = error.message;
                    this.showtimes = [];
                    this.showtimesByMovie = [];
                    this.bookingsCountMap.clear();
                    this.isLoading = false;
                    console.error('[fetchShowtimes] Lỗi khi lấy danh sách suất chiếu:', error);
                }
            });
    }

    validateShowtimes(): void {
        const movieIdsInShowtimes = new Set(this.showtimes.map(showtime => showtime.movieId));
        const movieIdsInMovies = new Set(this.movies.map(movie => movie.id));
        const unmatchedMovieIds = [...movieIdsInShowtimes].filter(id => !movieIdsInMovies.has(id));
        if (unmatchedMovieIds.length > 0) {
            console.warn(`[validateShowtimes] Có suất chiếu với movieId không khớp với danh sách phim: ${unmatchedMovieIds.join(', ')}`);
            this.errorMessage = 'Một số suất chiếu không tìm thấy thông tin phim tương ứng.';
        }
    }

    loadBookingsCount(): void {
        this.bookingsCountMap.clear();
        this.showtimes.forEach(showtime => {
            this.showTimeService.getBookingsCountForShowTime(showtime.id).subscribe({
                next: (count) => {
                    this.bookingsCountMap.set(showtime.id, count);
                },
                error: (error) => {
                    console.error(`[loadBookingsCount] Lỗi khi lấy số lượng vé cho suất chiếu ${showtime.id}:`, error);
                }
            });
        });
    }

    filterAndGroupShowtimes(): void {
        if (!this.showtimes || this.showtimes.length === 0) {
            this.showtimesByMovie = [];
            return;
        }

        let filteredShowtimes = [...this.showtimes];

        this.errorMessage = null;

        if (this.showOnlyActive) {
            filteredShowtimes = filteredShowtimes.filter(showtime => showtime.isactive);
        }

        if (this.selectedMovieId !== null && this.selectedMovieId !== undefined) {
            filteredShowtimes = filteredShowtimes.filter(showtime => showtime.movieId === this.selectedMovieId);
        }

        const groupedShowtimes = new Map<number, ShowtimeDTO[]>();
        filteredShowtimes.forEach(showtime => {
            if (!groupedShowtimes.has(showtime.movieId)) {
                groupedShowtimes.set(showtime.movieId, []);
            }
            groupedShowtimes.get(showtime.movieId)!.push(showtime);
        });

        this.showtimesByMovie = Array.from(groupedShowtimes.entries())
            .map(([movieId, showtimes]) => {
                const movie = this.movies.find(m => m.id === movieId);
                if (!movie) {
                    console.warn(`[filterAndGroupShowtimes] Không tìm thấy phim với movieId: ${movieId}`);
                }
                return {
                    movieId,
                    movieName: movie?.name || `Phim ${movieId}`,
                    posterURL: movie?.posterurl || 'https://via.placeholder.com/100x150?text=No+Image',
                    showtimes: showtimes.sort((a, b) => new Date(a.starttime).getTime() - new Date(b.starttime).getTime())
                };
            })
            .sort((a, b) => a.movieName.localeCompare(b.movieName));

        if (this.showtimes.length > 0 && this.showtimesByMovie.length === 0 && (this.selectedMovieId !== null || this.showOnlyActive)) {
            this.errorMessage = 'Không có suất chiếu nào khớp với bộ lọc của bạn.';
        }
    }

    resetFilters(): void {
        this.selectedCinemaId = null;
        this.selectedDate = null;
        this.selectedMovieId = null;
        this.showOnlyActive = false;
        this.showtimes = [];
        this.showtimesByMovie = [];
        this.bookingsCountMap.clear();
        this.errorMessage = null;
        this.successMessage = null;
    }

    viewDetails(showtimeId: number): void {
        this.showTimeService.getShowTimeById(showtimeId).subscribe({
            next: (showtime) => {
                this.selectedShowtime = showtime;
                const movie = this.movies.find(m => m.id === showtime.movieId);
                this.selectedShowtimeMovieName = movie?.name || `Phim ${showtime.movieId}`;
                this.modalService.open(this.showtimeDetailModal, { ariaLabelledBy: 'modal-basic-title' });
            },
            error: (error) => {
                this.errorMessage = 'Không thể lấy chi tiết suất chiếu: ' + error.message;
                console.error(`[viewDetails] Lỗi khi lấy chi tiết suất chiếu ${showtimeId}:`, error);
            }
        });
    }

    toggleShowtimeStatus(showtimeId: number, currentStatus: boolean): void {
        this.errorMessage = null;
        this.successMessage = null;
        this.showTimeService.updateShowTimeStatus(showtimeId, !currentStatus).subscribe({
            next: (response) => {
                this.successMessage = response.message;
                this.fetchShowtimes();
            },
            error: (error) => {
                this.errorMessage = 'Không thể cập nhật trạng thái suất chiếu: ' + error.message;
                console.error(`[toggleShowtimeStatus] Lỗi khi cập nhật trạng thái suất chiếu ${showtimeId}:`, error);
            }
        });
    }

    openAddShowtimeModal(): void {
        // Không phụ thuộc vào selectedCinemaId và selectedDate từ bộ lọc
        this.newShowtime = {
            cinemaId: 0,
            movieId: 0,
            roomId: 0,
            showdate: '',
            starttime: '',
            price: 0
        };
        this.errorMessage = null;
        this.successMessage = null;

        // Tải danh sách rạp, phim, và phòng để hiển thị trong modal
        this.loadCinemasForModal();
        this.loadMoviesForModal();
        this.loadRoomsForModal(0); // Gọi với cinemaId mặc định, sẽ cập nhật khi chọn rạp
        this.modalService.open(this.addShowtimeModal, { ariaLabelledBy: 'modal-basic-title' });
    }

    loadCinemasForModal(): void {
        this.cinemaService.getAllCinema().subscribe({
            next: (cinemas) => {
                this.cinemas = cinemas;
            },
            error: (error) => {
                this.errorMessage = 'Không thể tải danh sách rạp: ' + error.message;
                console.error('[loadCinemasForModal] Lỗi khi lấy danh sách rạp:', error);
            }
        });
    }

    loadMoviesForModal(): void {
        this.movieService.getAllMovie().subscribe({
            next: (nowPlaying) => {
                this.movies = nowPlaying;
                this.movieService.getMovieUpComing().subscribe({
                    next: (upcoming) => {
                        this.movies = [...this.movies, ...upcoming];
                    },
                    error: (error) => {
                        this.errorMessage = 'Không thể tải danh sách phim: ' + error.message;
                        console.error('[loadMoviesForModal] Lỗi khi lấy danh sách phim:', error);
                    }
                });
            },
            error: (error) => {
                this.errorMessage = 'Không thể tải danh sách phim: ' + error.message;
                console.error('[loadMoviesForModal] Lỗi khi lấy danh sách phim:', error);
            }
        });
    }

    loadRoomsForModal(cinemaId: number): void {
        if (cinemaId) {
            this.roomService.getRoomsByCinemaId(cinemaId).subscribe({
                next: (rooms) => {
                    this.rooms = rooms;
                },
                error: (error) => {
                    this.errorMessage = 'Không thể tải danh sách phòng: ' + error.message;
                    console.error('[loadRoomsForModal] Lỗi khi lấy danh sách phòng:', error);
                }
            });
        } else {
            this.rooms = []; // Reset rooms nếu chưa chọn rạp
        }
    }

    addShowtime(): void {
    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    // Kiểm tra các trường bắt buộc
    if (!this.newShowtime.cinemaId || !this.newShowtime.movieId || !this.newShowtime.roomId || 
        !this.newShowtime.showdate || !this.newShowtime.starttime || !this.newShowtime.price) {
        this.errorMessage = 'Vui lòng điền đầy đủ thông tin.';
        this.isLoading = false;
        return;
    }

    if (this.newShowtime.price < 80000) {
        this.errorMessage = 'Giá vé phải lớn hơn hoặc bằng 80,000.';
        this.isLoading = false;
        return;
    }

    const fullStartTime = `${this.newShowtime.showdate}T${this.newShowtime.starttime}:00`;

    const formData = new FormData();
    formData.append('movieid', this.newShowtime.movieId.toString());
    formData.append('roomid', this.newShowtime.roomId.toString());
    formData.append('showdate', this.newShowtime.showdate);
    formData.append('starttime', fullStartTime);
    formData.append('price', this.newShowtime.price.toString());

    this.showTimeService.createShowTime(formData).pipe(
        timeout(10000),
        catchError(error => {
            console.error('Error adding showtime:', error);
            this.isLoading = false;
            this.errorMessage = 'Lỗi khi thêm suất chiếu: ' + (error.error?.error || error.message || 'Không xác định');
            return throwError(() => new Error(error.message || 'Không xác định'));
        })
    ).subscribe({
        next: (response: ShowtimeDTO) => {
            this.isLoading = false;
            this.modalService.dismissAll();
            this.successMessage = 'Thêm suất chiếu thành công!';
            setTimeout(() => this.successMessage = null, 3000);
            this.newShowtime = { cinemaId: 0, movieId: 0, roomId: 0, showdate: '', starttime: '', price: 0 }; // Reset form
            // Không thêm vào this.showtimes hoặc gọi filterAndGroupShowtimes
        }
    });
}

    // Cập nhật danh sách phòng khi chọn rạp trong modal
    onCinemaChange(cinemaId: number): void {
        this.newShowtime.cinemaId = cinemaId;
        this.loadRoomsForModal(cinemaId);
        this.newShowtime.roomId = 0; // Reset roomId khi thay đổi rạp
    }
}