import { Component, OnInit, TemplateRef, ViewChild } from '@angular/core';
import { UserService } from '../services/user.service';
import { UserDTO } from '../dtos/user.dto';
import { Environment } from '../environments/environment';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-list-customer',
  standalone: false,
  templateUrl: './list-customer.component.html',
  styleUrls: ['./list-customer.component.css']
})
export class ListCustomerComponent implements OnInit {
  customers: UserDTO[] = [];
  isLoading: boolean = false;
  errorMessage: string | null = null;
  successMessage: string | null = null;
  selectedCustomer: UserDTO | null = null;
  dateOfBirthString: string = ''; // For handling the date input value

  @ViewChild('editCustomerModal') editCustomerModal!: TemplateRef<any>;

  constructor(private userService: UserService, private modalService: NgbModal) { }

  ngOnInit(): void {
    this.loadCustomers();
  }

  loadCustomers(): void {
    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    this.userService.getAllUserCustomer().subscribe({
      next: (customers) => {
        this.customers = customers.map(customer => new UserDTO({
          ...customer,
          dateofbirth: customer.dateofbirth ? new Date(customer.dateofbirth) : new Date(),
          createdat: customer.createdat ? new Date(customer.createdat) : new Date()
        }));
        this.isLoading = false;
        console.log('[ListCustomerComponent] Đã nhận danh sách customer:', this.customers);
      },
      error: (error: any) => {
        this.errorMessage = error.message || 'Không thể tải danh sách customer';
        this.isLoading = false;
        console.error('[ListCustomerComponent] Lỗi khi lấy danh sách customer:', error);
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

  toggleActiveStatus(user: UserDTO): void {
    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    this.userService.updateUserStatus(user.id, !user.isactive).subscribe({
      next: (response) => {
        user.isactive = !user.isactive;
        this.successMessage = response.message || 'Cập nhật trạng thái thành công';
        this.isLoading = false;
        console.log(`[ListCustomerComponent] Đã cập nhật trạng thái user ${user.id}:`, response);
      },
      error: (error: any) => {
        this.errorMessage = error.error?.error || 'Không thể cập nhật trạng thái user';
        this.isLoading = false;
        console.error(`[toggleUserCustomerIsActive] Lỗi khi cập nhật trạng thái UserCustomer:`, error);
      }
    });
  }

  openEditModal(customer: UserDTO): void {
    this.selectedCustomer = { ...customer }; 
    
    // Set the date string for the input field
    if (this.selectedCustomer.dateofbirth) {
      this.dateOfBirthString = this.formatDateForInput(this.selectedCustomer.dateofbirth);
    } else {
      this.dateOfBirthString = '';
    }
    
    this.errorMessage = null;
    this.successMessage = null;
    this.modalService.open(this.editCustomerModal, { ariaLabelledBy: 'modal-basic-title' });
    
    console.log('Opening modal with dateOfBirthString:', this.dateOfBirthString);
    console.log('Selected customer dateofbirth:', this.selectedCustomer.dateofbirth);
  }

  // Handle date input change
  onDateOfBirthChange(dateString: string): void {
    console.log('Date input changed to:', dateString);
    this.dateOfBirthString = dateString;
    if (this.selectedCustomer) {
      // Handle null case but ensure we always set a Date object
      this.selectedCustomer.dateofbirth = dateString ? new Date(dateString) : new Date();
      console.log('Updated selectedCustomer.dateofbirth:', this.selectedCustomer.dateofbirth);
    }
  }

  updateCustomer(): void {
    console.log('updateCustomer called');
    if (!this.selectedCustomer) {
      console.log('selectedCustomer is null');
      return;
    }

    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    // Kiểm tra các trường bắt buộc
    if (!this.selectedCustomer.name || !this.selectedCustomer.email || !this.selectedCustomer.phonenumber ||
      !this.selectedCustomer.password || !this.selectedCustomer.dateofbirth) {
      this.errorMessage = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
      this.isLoading = false;
      console.log('Validation failed:', this.errorMessage);
      return;
    }

    // Chuẩn bị dữ liệu theo cấu trúc UpdateUserDTO
    const userData = {
      name: this.selectedCustomer.name,
      email: this.selectedCustomer.email,
      phonenumber: this.selectedCustomer.phonenumber,
      password: this.selectedCustomer.password,
      address: this.selectedCustomer.address || '',
      dateofbirth: this.formatDateForAPI(this.selectedCustomer.dateofbirth) // Format date as yyyy-MM-dd
    };
    
    console.log('Sending userData to backend:', userData);

    this.userService.updateUser(this.selectedCustomer.id, userData).subscribe({
      next: (response) => {
        console.log('API response:', response);
        this.isLoading = false;
        this.successMessage = 'Cập nhật thông tin customer thành công!';
        setTimeout(() => this.successMessage = null, 3000);

        // Đóng modal trước khi làm mới danh sách
        this.modalService.dismissAll();

        // Làm mới danh sách customer
        this.loadCustomers();

        // Đặt lại selectedCustomer
        this.selectedCustomer = null;
      },
      error: (error: any) => {
        console.error('API error:', error);
        this.errorMessage = error.error?.error || 'Không thể cập nhật thông tin customer';
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
}