export class RoomDTO {
    id: number;
    cinemaId: number;
    name: string;
    seatcolumnmax: number;
    seatrowmax: number;

    constructor(data: any) {
        this.id = data.id;
        this.cinemaId = data.cinemaId;
        this.name = data.name;
        this.seatcolumnmax = data.seatcolumnmax;
        this.seatrowmax = data.seatrowmax;
    }
}