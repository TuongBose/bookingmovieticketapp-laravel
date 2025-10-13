export class Showtime {
    id: number;
    movieId: number;
    roomId: number;
    showdate: string;
    starttime: string;
    price: number;
    isactive: boolean;

    constructor(data: any) {
        this.id = data.id;
        this.movieId = data.movieId;
        this.roomId = data.roomId;
        this.showdate = data.showdate;
        this.starttime = data.starttime;
        this.price = data.price;
        this.isactive = data.isactive;
    }
}