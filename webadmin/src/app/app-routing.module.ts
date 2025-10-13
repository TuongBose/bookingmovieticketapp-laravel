import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { ListMovieComponent } from './list-movie/list-movie.component';
import { AuthGuard } from './guard/auth.guard';
import { ListCinemaComponent } from './list-cinema/list-cinema.component';
import { ListCustomerComponent } from './list-customer/list-customer.component';
import { ListAdminComponent } from './list-admin/list-admin.component';
import { ListShowtimeComponent } from './list-showtime/list-showtime.component';
import { StatisticsComponent } from './statistics/statistics.component';
import { ListBookingComponent } from './list-booking/list-booking.component';

const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  {
    path: 'home',
    component: HomeComponent,
    canActivate: [AuthGuard],
    children: [
      {path: 'statistics', component: StatisticsComponent},
      {path: 'bookings', component: ListBookingComponent},
      { path: 'movies', component: ListMovieComponent },
      {path: 'cinemas', component: ListCinemaComponent},
      {path: 'customers', component: ListCustomerComponent},
      {path: 'admins', component: ListAdminComponent},  
      {path: 'showtimes', component: ListShowtimeComponent},
      { path: '', redirectTo: 'statistics', pathMatch: 'full' },
    ],
  },
  { path: '**', redirectTo: 'login' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
