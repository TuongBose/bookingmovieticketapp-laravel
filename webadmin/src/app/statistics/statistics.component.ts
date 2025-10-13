// src/app/statistics/statistics.component.ts
import { Component, OnInit, AfterViewInit, ElementRef, ViewChild } from '@angular/core';
import { StatisticService } from '..//services/statistic.service';
import { Chart } from 'chart.js/auto';

@Component({
  selector: 'app-statistics',
  standalone: false,
  templateUrl: './statistics.component.html',
  styleUrls: ['./statistics.component.css'],
})
export class StatisticsComponent implements OnInit, AfterViewInit {
  @ViewChild('barChartCanvas') barChartCanvas!: ElementRef<HTMLCanvasElement>;
  @ViewChild('pieChartCanvas') pieChartCanvas!: ElementRef<HTMLCanvasElement>;

  currentMonth: string = '';
  totalRevenue: number = 0;
  mostBookedMovie: any = null;
  secondMostBookedMovie: any = null;
  thirdMostBookedMovie: any = null;
  mostBookedCinema: any = null;
  errorMessage: string = '';

  private barChart: Chart<'bar', number[], string> | undefined;
  private pieChart: Chart<'pie', number[], string> | undefined;

  constructor(private statisticService: StatisticService) {}

  ngOnInit(): void {
    const today = new Date();
    const year = today.getFullYear();
    const month = (today.getMonth() + 1).toString().padStart(2, '0');
    this.currentMonth = `${month}-${year}`;

    this.fetchStatistics();
  }

  ngAfterViewInit(): void {
    this.createCharts();
  }

  fetchStatistics() {
    this.statisticService.getMonthlyStatistics().subscribe({
      next: (data) => {
        console.log('Statistics data:', data);
        this.totalRevenue = data.totalRevenue || 0;
        this.mostBookedMovie = data.mostBookedMovie || null;
        this.secondMostBookedMovie = data.secondMostBookedMovie || null;
        this.thirdMostBookedMovie = data.thirdMostBookedMovie || null;
        this.mostBookedCinema = data.mostBookedCinema || null;

        this.updateCharts();
      },
      error: (error) => {
        console.error('Error fetching statistics:', error);
        this.errorMessage = error.error || 'Không thể tải dữ liệu thống kê.';
        this.totalRevenue = 0;
        this.mostBookedMovie = null;
        this.secondMostBookedMovie = null;
        this.thirdMostBookedMovie = null;
        this.mostBookedCinema = null;

        this.updateCharts();
      },
    });
  }

  createCharts(): void {
    this.barChart = new Chart(this.barChartCanvas.nativeElement, {
      type: 'bar',
      data: {
        labels: [],
        datasets: [
          {
            label: 'Số lượng vé',
            data: [],
            backgroundColor: ['#007bff', '#28a745', '#dc3545'],
            borderColor: ['#0056b3', '#1e7e34', '#a71d2a'],
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Số lượng vé',
            },
          },
        },
        plugins: {
          legend: {
            display: false,
          },
        },
      },
    });

    this.pieChart = new Chart(this.pieChartCanvas.nativeElement, {
      type: 'pie',
      data: {
        labels: [],
        datasets: [
          {
            data: [],
            backgroundColor: ['#007bff', '#28a745', '#dc3545'],
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top',
          },
        },
      },
    });
  }

  updateCharts(): void {
    const labels = [
      this.mostBookedMovie?.movieName || 'Không có dữ liệu',
      this.secondMostBookedMovie?.movieName || 'Không có dữ liệu',
      this.thirdMostBookedMovie?.movieName || 'Không có dữ liệu',
    ];
    const data = [
      this.mostBookedMovie?.totalBookings ?? 0,
      this.secondMostBookedMovie?.totalBookings ?? 0,
      this.thirdMostBookedMovie?.totalBookings ?? 0,
    ].map(value => Number(value));

    if (this.barChart) {
      this.barChart.data.labels = labels;
      this.barChart.data.datasets[0].data = data;
      this.barChart.update();
    }

    if (this.pieChart) {
      this.pieChart.data.labels = labels;
      this.pieChart.data.datasets[0].data = data;
      this.pieChart.update();
    }
  }
}