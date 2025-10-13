import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ShowtimeDTO } from '../dtos/showtime.dto';

@Component({
    selector: 'app-showtime-detail-dialog',
    standalone: false,
    
    template: `
        <h2 mat-dialog-title>Thông tin</h2>
        <mat-dialog-content>
            <p><strong>Chi tiết suất chiếu #{{ data.id }}</strong></p>
            <p><strong>Movie ID:</strong> {{ data.movieId }}</p>
            <p><strong>Room ID:</strong> {{ data.roomId }}</p>
            <p><strong>Ngày:</strong> {{ data.showdate }}</p>
            <p><strong>Giờ:</strong> {{ data.starttime }}</p>
        </mat-dialog-content>
        <mat-dialog-actions align="end">
            <button mat-button color="primary" (click)="onClose()">OK</button>
        </mat-dialog-actions>
    `,
    styles: [`
        p { margin: 8px 0; }
        mat-dialog-content { padding-bottom: 16px; }
    `]
})
export class ShowtimeDetailDialogComponent {
    constructor(
        public dialogRef: MatDialogRef<ShowtimeDetailDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: ShowtimeDTO
    ) { }

    onClose(): void {
        this.dialogRef.close();
    }
}