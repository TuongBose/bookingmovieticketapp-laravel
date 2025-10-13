import { Component, OnInit } from '@angular/core';
import { BookingDTO } from '../dtos/booking.dto';
import { BookingService } from '../services/booking.service';

@Component({
  selector: 'app-list-booking',
  standalone: false,
  templateUrl: './list-booking.component.html',
  styleUrls: ['./list-booking.component.css']
})
export class ListBookingComponent implements OnInit {
  allBookings: BookingDTO[] = [];
  activeBookings: BookingDTO[] = [];
  inactiveBookings: BookingDTO[] = [];
  filteredBookings: BookingDTO[] = [];
  bookingsByDate: { date: string, bookings: BookingDTO[] }[] = []; // Nhóm booking theo ngày
  userIdFilter: number | null = null;
  showtimeIdFilter: number | null = null;
  dateFilter: string | null = null;
  errorMessage: string | null = null;
  isLoading: boolean = false;
  activeTab: string = 'all';

  constructor(private bookingService: BookingService) { }

  ngOnInit(): void {
    this.loadAllBookings();
  }

  setActiveTab(tab: string): void {
    this.activeTab = tab;
    this.updateFilteredBookings();
  }

  loadAllBookings(): void {
    this.isLoading = true;
    this.bookingService.getAllBookings().subscribe({
      next: (bookings) => {
        this.allBookings = bookings;
        this.applyDateFilter();
        this.errorMessage = null;
        this.isLoading = false;
      },
      error: (error) => {
        this.errorMessage = error.message;
        this.allBookings = [];
        this.activeBookings = [];
        this.inactiveBookings = [];
        this.bookingsByDate = [];
        this.isLoading = false;
      }
    });
  }

  applyFilters(): void {
    this.isLoading = true;

    if (this.userIdFilter && this.showtimeIdFilter) {
      this.bookingService.getBookingsByUserId(this.userIdFilter).subscribe({
        next: (userBookings) => {
          this.bookingService.getBookingsByShowtimeId(this.showtimeIdFilter!).subscribe({
            next: (showtimeBookings) => {
              this.allBookings = userBookings.filter(userBooking =>
                showtimeBookings.some(showtimeBooking => showtimeBooking.id === userBooking.id)
              );
              this.applyDateFilter();
              this.errorMessage = null;
              this.isLoading = false;
            },
            error: (error) => {
              this.errorMessage = error.message;
              this.allBookings = [];
              this.activeBookings = [];
              this.inactiveBookings = [];
              this.bookingsByDate = [];
              this.isLoading = false;
            }
          });
        },
        error: (error) => {
          this.errorMessage = error.message;
          this.allBookings = [];
          this.activeBookings = [];
          this.inactiveBookings = [];
          this.bookingsByDate = [];
          this.isLoading = false;
        }
      });
    } else if (this.userIdFilter) {
      this.bookingService.getBookingsByUserId(this.userIdFilter).subscribe({
        next: (bookings) => {
          this.allBookings = bookings;
          this.applyDateFilter();
          this.errorMessage = null;
          this.isLoading = false;
        },
        error: (error) => {
          this.errorMessage = error.message;
          this.allBookings = [];
          this.activeBookings = [];
          this.inactiveBookings = [];
          this.bookingsByDate = [];
          this.isLoading = false;
        }
      });
    } else if (this.showtimeIdFilter) {
      this.bookingService.getBookingsByShowtimeId(this.showtimeIdFilter).subscribe({
        next: (bookings) => {
          this.allBookings = bookings;
          this.applyDateFilter();
          this.errorMessage = null;
          this.isLoading = false;
        },
        error: (error) => {
          this.errorMessage = error.message;
          this.allBookings = [];
          this.activeBookings = [];
          this.inactiveBookings = [];
          this.bookingsByDate = [];
          this.isLoading = false;
        }
      });
    } else {
      this.loadAllBookings();
    }
  }

  applyDateFilter(): void {
    let filteredBookings = [...this.allBookings];

    if (this.dateFilter) {
      const selectedDate = new Date(this.dateFilter);
      filteredBookings = filteredBookings.filter(booking => {
        const bookingDate = new Date(booking.bookingdate);
        return bookingDate.toDateString() === selectedDate.toDateString();
      });
    }

    this.allBookings = filteredBookings;
    this.activeBookings = filteredBookings.filter(booking => booking.isactive);
    this.inactiveBookings = filteredBookings.filter(booking => !booking.isactive);
    this.updateFilteredBookings();
  }

  resetFilters(): void {
    this.userIdFilter = null;
    this.showtimeIdFilter = null;
    this.dateFilter = null;
    this.loadAllBookings();
  }

  private updateFilteredBookings(): void {
    if (this.activeTab === 'all') {
      this.filteredBookings = [...this.allBookings];
    } else if (this.activeTab === 'active') {
      this.filteredBookings = [...this.activeBookings];
    } else if (this.activeTab === 'inactive') {
      this.filteredBookings = [...this.inactiveBookings];
    }

    // Nhóm booking theo ngày
    const groupedBookings = new Map<string, BookingDTO[]>();
    this.filteredBookings.forEach(booking => {
      const bookingDate = new Date(booking.bookingdate).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
      if (!groupedBookings.has(bookingDate)) {
        groupedBookings.set(bookingDate, []);
      }
      groupedBookings.get(bookingDate)!.push(booking);
    });

    // Sắp xếp theo ngày giảm dần (mới nhất trước)
    this.bookingsByDate = Array.from(groupedBookings.entries())
      .map(([date, bookings]) => ({ date, bookings }))
      .sort((a, b) => new Date(b.date.split('/').reverse().join('-')).getTime() - new Date(a.date.split('/').reverse().join('-')).getTime());
  }
}