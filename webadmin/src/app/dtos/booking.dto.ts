export class BookingDTO {
    id: number;
    userId: number;
    showTimeId: number;
    bookingdate: string;
    totalprice: number;
    paymentmethod: string;
    paymentstatus: string;
    isactive: boolean;
    constructor(data: any) {
        this.id = data.id;
        this.userId = data.userId;
        this.showTimeId = data.showTimeId;
        this.bookingdate = data.bookingdate;
        this.totalprice = data.totalprice;
        this.paymentmethod = data.paymentmethod;
        this.paymentstatus = data.paymentstatus;
        this.isactive = data.isactive;
    };
}