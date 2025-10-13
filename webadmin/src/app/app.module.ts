import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { NgChartsModule } from 'ng2-charts';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { ListMovieComponent } from './list-movie/list-movie.component';
import { HomeComponent } from './home/home.component';
import { ListCinemaComponent } from './list-cinema/list-cinema.component';
import { ListCustomerComponent } from './list-customer/list-customer.component';
import { ListAdminComponent } from './list-admin/list-admin.component';
import { ListShowtimeComponent } from './list-showtime/list-showtime.component';
import { StatisticsComponent } from './statistics/statistics.component';
import { ListBookingComponent } from './list-booking/list-booking.component';
import { MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { ShowtimeDetailDialogComponent } from './list-showtime/showtime-detail-dialog.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    ListMovieComponent,
    HomeComponent,
    ListCinemaComponent,
    ListCustomerComponent,
    ListAdminComponent,
    ListShowtimeComponent,
    StatisticsComponent,
    ListBookingComponent,
    ShowtimeDetailDialogComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpClientModule,
    AppRoutingModule,
    NgChartsModule,
    MatDialogModule,
    MatButtonModule,
    NgbModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
