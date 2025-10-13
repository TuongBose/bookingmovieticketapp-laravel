import { Component, OnInit, TemplateRef, ViewChild } from '@angular/core';
import { UserService } from '../services/user.service';
import { UserDTO } from '../dtos/user.dto';
import { Environment } from '../environments/environment';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-list-admin',
  standalone: false,
  templateUrl: './list-admin.component.html',
  styleUrls: ['./list-admin.component.css']
})
export class ListAdminComponent implements OnInit {
  admins: UserDTO[] = [];
  isLoading: boolean = false;
  errorMessage: string | null = null;
  successMessage: string | null = null;
  selectedAdmin: UserDTO | null = null;
  dateOfBirthString: string = ''; // For handling the date input value

  @ViewChild('editAdminModal') editAdminModal!: TemplateRef<any>;

  constructor(private userService: UserService, private modalService: NgbModal) {}

  ngOnInit(): void {
    this.loadAdmins();
  }

  loadAdmins(): void {
    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    this.userService.getAllUserAdmin().subscribe({
      next: (admins) => {
        this.admins = admins.map(admin => new UserDTO({
          ...admin,
          dateofbirth: admin.dateofbirth ? new Date(admin.dateofbirth) : new Date(),
          createdat: admin.createdat ? new Date(admin.createdat) : new Date()
        }));
        this.isLoading = false;
        console.log('[ListAdminComponent] Đã nhận danh sách admin:', this.admins);
      },
      error: (error: any) => {
        this.errorMessage = error.message || 'Không thể tải danh sách admin';
        this.isLoading = false;
        console.error('[ListAdminComponent] Lỗi khi lấy danh sách admin:', error);
      }
    });
  }

  // Hàm parse chuỗi ISO thành Date - improved handling with null checks
  parseDate(value: any): Date {
    if (!value) return new Date(); // Return current date instead of null
    if (value instanceof Date) return value;
    if (typeof value === 'string') {
      const parsedDate = new Date(value);
      // Check if parsed date is valid
      return isNaN(parsedDate.getTime()) ? new Date() : parsedDate;
    }
    return new Date(); // Default to current date
  }

  // Format date to yyyy-MM-dd string for API
  formatDateForAPI(date: Date): string {
    if (isNaN(date.getTime())) return '';
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  // Format date to yyyy-MM-dd string for HTML date input
  formatDateForInput(date: Date): string {
    if (isNaN(date.getTime())) return '';
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  toggleActiveStatus(admin: UserDTO): void {
    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    this.userService.updateUserStatus(admin.id, !admin.isactive).subscribe({
      next: (response) => {
        admin.isactive = !admin.isactive;
        this.successMessage = response.message || 'Cập nhật trạng thái thành công';
        this.isLoading = false;
        console.log(`[ListAdminComponent] Đã cập nhật trạng thái user ${admin.id}:`, response);
      },
      error: (error: any) => {
        this.errorMessage = error.error?.error || 'Không thể cập nhật trạng thái user';
        this.isLoading = false;
        console.error(`[toggleUserCustomerIsActive] Lỗi khi cập nhật trạng thái UserAdmin:`, error);
      }
    });
  }

  openEditModal(admin: UserDTO): void {
    this.selectedAdmin = { ...admin };
    
    // Set the date string for the input field
    if (this.selectedAdmin.dateofbirth) {
      this.dateOfBirthString = this.formatDateForInput(this.selectedAdmin.dateofbirth);
    } else {
      this.dateOfBirthString = '';
    }
    
    this.errorMessage = null;
    this.successMessage = null;
    this.modalService.open(this.editAdminModal, { ariaLabelledBy: 'modal-basic-title' });
    
    console.log('Opening modal with dateOfBirthString:', this.dateOfBirthString);
    console.log('Selected admin dateofbirth:', this.selectedAdmin.dateofbirth);
  }

  // Handle date input change
  onDateOfBirthChange(dateString: string): void {
    console.log('Date input changed to:', dateString);
    this.dateOfBirthString = dateString;
    if (this.selectedAdmin) {
      // Handle null case but ensure we always set a Date object
      this.selectedAdmin.dateofbirth = dateString ? new Date(dateString) : new Date();
      console.log('Updated selectedAdmin.dateofbirth:', this.selectedAdmin.dateofbirth);
    }
  }

  updateAdmin(): void {
    console.log('updateAdmin called');
    if (!this.selectedAdmin) {
      console.log('selectedAdmin is null');
      return;
    }

    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    // Kiểm tra các trường bắt buộc
    if (!this.selectedAdmin.name || !this.selectedAdmin.email || !this.selectedAdmin.phonenumber ||
      !this.selectedAdmin.password || !this.selectedAdmin.dateofbirth) {
      this.errorMessage = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
      this.isLoading = false;
      console.log('Validation failed:', this.errorMessage);
      return;
    }

    // Chuẩn bị dữ liệu theo cấu trúc UpdateUserDTO
    const userData = {
      name: this.selectedAdmin.name,
      email: this.selectedAdmin.email,
      phonenumber: this.selectedAdmin.phonenumber,
      password: this.selectedAdmin.password,
      address: this.selectedAdmin.address || '',
      dateofbirth: this.formatDateForAPI(this.selectedAdmin.dateofbirth) // Format date as yyyy-MM-dd
    };
    
    console.log('Sending userData to backend:', userData);

    this.userService.updateUser(this.selectedAdmin.id, userData).subscribe({
      next: (response) => {
        console.log('API response:', response);
        this.isLoading = false;
        this.successMessage = 'Cập nhật thông tin admin thành công!';
        setTimeout(() => this.successMessage = null, 3000);

        // Đóng modal trước khi làm mới danh sách
        this.modalService.dismissAll();

        // Làm mới danh sách admin
        this.loadAdmins();

        // Đặt lại selectedAdmin
        this.selectedAdmin = null;
      },
      error: (error: any) => {
        console.error('API error:', error);
        this.errorMessage = error.error?.error || 'Không thể cập nhật thông tin admin';
        this.isLoading = false;
      }
    });
  }

  getUserImageUrl(userId: number, imagename: string): string {
    return imagename && imagename !== 'no_image' ? `${Environment.apiBaseUrl}/users/${userId}/image` : 'assets/images/no_image.jpg';
  }

  onImageError(event: Event, userId: number): void {
    const imgElement = event.target as HTMLImageElement;
    imgElement.src = 'https://yt3.googleusercontent.com/ytc/AIdro_nml8pToD7yNeAVIPMck_emdM0lt4pFCI_i-y_k0EFUzyg=s900-c-k-c0x00ffffff-no-rj';
    console.warn(`Failed to load image for user ID ${userId}, using default image.`);
  }

  isCurrentUser(id: number): boolean {
    if (this.userService.getUser() && this.userService.getUser()?.id === id) {
      return true;
    } else {
      return false;
    }
  }
}