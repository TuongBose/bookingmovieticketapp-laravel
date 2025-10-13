import { Component, OnInit, ViewChild, TemplateRef } from '@angular/core';
import { CinemaDTO } from '../dtos/cinema.dto';
import { CinemaService } from '../services/cinema.service';
import { Environment } from '../environments/environment';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { HttpClient } from '@angular/common/http';
import { catchError, map, retry, timeout } from 'rxjs/operators';
import { Observable, throwError } from 'rxjs';

@Component({
  selector: 'app-list-cinema',
  standalone: false,
  templateUrl: './list-cinema.component.html',
  styleUrls: ['./list-cinema.component.css']
})
export class ListCinemaComponent implements OnInit {
  cinemas: CinemaDTO[] = [];
  filteredCinemas: CinemaDTO[] = [];
  cities: string[] = [];
  selectedCity: string = 'Toàn quốc';
  errorMessage: string = '';
  successMessage: string = '';
  isLoading: boolean = true;
  provinces: any[] = [];
  selectedImage: File | null = null;

  @ViewChild('addCinemaModal') addCinemaModal!: TemplateRef<any>;
  @ViewChild('editCinemaModal') editCinemaModal!: TemplateRef<any>;

  name: string = '';
  city: string = '';
  coordinates: string = '';
  address: string = '';
  phonenumber: string = '';
  maxroom: number = 0;
  imagename: string = '';
  isactive: boolean = false;

  editCinema: CinemaDTO = {
    id: 0,
    name: '',
    city: '',
    coordinates: '',
    address: '',
    phonenumber: '',
    maxroom: 0,
    imagename: '',
    isactive: false
  };

  constructor(
    private cinemaService: CinemaService,
    private http: HttpClient,
    private modalService: NgbModal
  ) { }

  ngOnInit(): void {
    this.fetchCinemas();
    this.fetchProvinces();
  }

  fetchCinemas(): void {
    this.isLoading = true;
    this.cinemaService.getAllCinema().pipe(
      timeout(10000), // Timeout sau 10 giây
      retry(2), // Thử lại 2 lần nếu lỗi
      catchError(error => {
        console.error('Error fetching cinemas:', error);
        this.errorMessage = 'Không thể tải danh sách rạp chiếu phim. Vui lòng thử lại sau.';
        this.isLoading = false;
        return throwError(() => new Error(error.message));
      })
    ).subscribe({
      next: (cinemas) => {
        this.cinemas = cinemas;
        this.getUniqueCities();
        this.filterCinemas();
        this.isLoading = false;
      }
    });
  }

  fetchProvinces(): void {
    this.http.get<any[]>('https://provinces.open-api.vn/api/?depth=2').pipe(
      timeout(5000), // Timeout sau 5 giây
      catchError(error => {
        console.error('Error fetching provinces:', error);
        this.errorMessage = 'Không thể tải danh sách thành phố.';
        return throwError(() => new Error(error.message));
      })
    ).subscribe({
      next: (data) => {
        this.provinces = data.map(province => ({
          code: province.code,
          name: province.name,
          districts: province.districts
        }));
      }
    });
  }

  getUniqueCities(): void {
    this.cities = [...new Set(this.cinemas.map(cinema => cinema.city))];
    this.cities.unshift('Toàn quốc');
  }

  filterCinemas(): void {
    if (this.selectedCity === 'Toàn quốc') {
      this.filteredCinemas = [...this.cinemas];
    } else {
      this.filteredCinemas = this.cinemas.filter(cinema => cinema.city === this.selectedCity);
    }
  }

  getCinemaImageUrl(cinemaId: number, imagename: string): string {
    return imagename && imagename !== 'no_image' ? `${Environment.apiBaseUrl}/cinemas/${cinemaId}/image` : 'assets/images/no_image.jpg';
  }

  onImageError(event: Event, cinemaId: number): void {
    const imgElement = event.target as HTMLImageElement;
    imgElement.src = 'https://yt3.googleusercontent.com/ytc/AIdro_nml8pToD7yNeAVIPMck_emdM0lt4pFCI_i-y_k0EFUzyg=s900-c-k-c0x00ffffff-no-rj';
    console.warn(`Failed to load image for cinema ID ${cinemaId}, using default image.`);
  }

  openAddCinemaModal(): void {
    // Reset các trường form
    this.name = '';
    this.city = '';
    this.coordinates = '';
    this.address = '';
    this.phonenumber = '';
    this.maxroom = 0;
    this.imagename = '';
    this.isactive = false;
    this.selectedImage = null;
    this.errorMessage = '';
    this.successMessage = '';
    this.modalService.open(this.addCinemaModal, { ariaLabelledBy: 'modal-basic-title' });
  }

  onImageSelected(event: any): void {
    this.selectedImage = event.target.files[0] as File;
    this.imagename = this.selectedImage ? this.selectedImage.name : '';
  }

  addCinema(): void {
    this.isLoading = true;
    this.errorMessage = '';
    this.successMessage = '';

    if (!this.name || !this.city || !this.coordinates ||
      !this.address || !this.phonenumber || !this.maxroom) {
      this.errorMessage = 'Vui lòng điền đầy đủ thông tin.';
      this.isLoading = false;
      return;
    }

    const coordRegex = /^-?\d+\.\d+\s*,?\s*-?\d+\.\d+$/;
    if (!coordRegex.test(this.coordinates)) {
      this.errorMessage = 'Tọa độ phải có định dạng latitude,longitude (ví dụ: 10.7790,106.6918).';
      this.isLoading = false;
      return;
    }
    const phoneRegex = /^0\d{9,10}$/;
    if (!phoneRegex.test(this.phonenumber)) {
      this.errorMessage = 'Số điện thoại phải bắt đầu bằng 0 và có 9-10 chữ số.';
      this.isLoading = false;
      return;
    }
    if (this.maxroom <= 0) {
      this.errorMessage = 'Số phòng chiếu tối đa phải lớn hơn 0.';
      this.isLoading = false;
      return;
    }

    const formData = new FormData();
    formData.append('name', this.name);
    formData.append('city', this.city);
    formData.append('coordinates', this.coordinates);
    formData.append('address', this.address);
    formData.append('phonenumber', this.phonenumber);
    formData.append('maxroom', this.maxroom.toString());
    formData.append('isactive', this.isactive.toString());
    if (this.selectedImage) {
      formData.append('image', this.selectedImage);
    }

    this.cinemaService.createCinema(formData).pipe(
      timeout(10000),
      catchError(error => {
        console.error('Error adding cinema:', error);
        this.isLoading = false;
        this.errorMessage = 'Lỗi khi thêm rạp: ' + (error.error?.error || error.message || 'Không xác định');
        return throwError(() => new Error(error.message || 'Không xác định'));
      })
    ).subscribe({
      next: (response: CinemaDTO) => {
        this.isLoading = false;
        this.cinemas.push(response);
        this.getUniqueCities();
        this.filterCinemas();
        this.modalService.dismissAll();
        this.successMessage = 'Thêm rạp thành công!';
        setTimeout(() => this.errorMessage = '', 3000);
      }
    });
  }

  openGoogleMaps(coordinates: string): void {
    const [lat, lon] = coordinates.split(',').map(coord => coord.trim());
    const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lon}`;
    window.open(googleMapsUrl, '_blank');
  }

  openEditCinemaModal(cinema: CinemaDTO): void {
    this.editCinema = { ...cinema }; // Sao chép dữ liệu để chỉnh sửa
    this.errorMessage = '';
    this.successMessage = '';
    this.modalService.open(this.editCinemaModal, { ariaLabelledBy: 'modal-basic-title' });
  }

  updateCinema(): void {
    this.isLoading = true;
    this.errorMessage = '';
    this.successMessage = '';

    if (!this.editCinema.name || !this.editCinema.city || !this.editCinema.coordinates || !this.editCinema.address || !this.editCinema.phonenumber || !this.editCinema.maxroom) {
      this.errorMessage = 'Vui lòng điền đầy đủ thông tin.';
      this.isLoading = false;
      return;
    }

    const coordRegex = /^-?\d+\.\d+\s*,?\s*-?\d+\.\d+$/;
    if (!coordRegex.test(this.editCinema.coordinates)) {
      this.errorMessage = 'Tọa độ phải có định dạng latitude,longitude (ví dụ: 10.7790,106.6918).';
      this.isLoading = false;
      return;
    }
    const phoneRegex = /^0\d{9,10}$/;
    if (!phoneRegex.test(this.editCinema.phonenumber)) {
      this.errorMessage = 'Số điện thoại phải bắt đầu bằng 0 và có 9-10 chữ số.';
      this.isLoading = false;
      return;
    }
    if (this.editCinema.maxroom <= 0) {
      this.errorMessage = 'Số phòng chiếu tối đa phải lớn hơn 0.';
      this.isLoading = false;
      return;
    }

    const formData = new FormData();
    formData.append('id', this.editCinema.id.toString());
    formData.append('name', this.editCinema.name);
    formData.append('city', this.editCinema.city);
    formData.append('coordinates', this.editCinema.coordinates);
    formData.append('address', this.editCinema.address);
    formData.append('phonenumber', this.editCinema.phonenumber);
    formData.append('maxroom', this.editCinema.maxroom.toString());
    formData.append('isactive', this.editCinema.isactive.toString());
    if (this.selectedImage) {
      formData.append('image', this.selectedImage);
    }

    this.cinemaService.updateCinema(this.editCinema.id, formData).pipe(
      timeout(10000),
      catchError(error => {
        console.error('Error updating cinema:', error);
        this.isLoading = false;
        this.errorMessage = 'Lỗi khi cập nhật rạp: ' + (error.error?.error || error.message || 'Không xác định');
        return throwError(() => new Error(error.message || 'Không xác định'));
      })
    ).subscribe({
      next: (response: CinemaDTO) => {
        this.isLoading = false;
        const index = this.cinemas.findIndex(c => c.id === response.id);
        if (index !== -1) this.cinemas[index] = response;
        this.filterCinemas();
        this.modalService.dismissAll();
        this.successMessage = 'Cập nhật rạp thành công!';
        setTimeout(() => this.errorMessage = '', 3000);
      }
    });
  }

  toggleActive(cinemaId: number, newStatus: boolean): void {
    this.isLoading = true;
    this.errorMessage = '';
    this.successMessage = ''; // Reset successMessage

    this.cinemaService.updateCinemaStatus(cinemaId, newStatus).pipe(
      timeout(10000),
      catchError(error => {
        console.error('Error toggling active:', error);
        this.isLoading = false;
        this.errorMessage = 'Lỗi khi thay đổi trạng thái: ' + (error.error?.error || error.message || 'Không xác định');
        return throwError(() => new Error(error.message || 'Không xác định'));
      })
    ).subscribe({
      next: (response) => {
        this.isLoading = false;
        this.successMessage = response.message || `Đã ${newStatus ? 'kích hoạt' : 'ngưng'} rạp thành công!`;
        this.fetchCinemas();
        setTimeout(() => this.successMessage = '', 3000);
      }
    });
  }
}