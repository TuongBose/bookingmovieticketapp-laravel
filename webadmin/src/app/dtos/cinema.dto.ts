export class CinemaDTO{
    id:number;
    name:string;
    city:string;
    coordinates:string;
    address:string;
    phonenumber:string;
    maxroom:number;
    imagename:string;
    isactive:boolean;

    constructor(data:any){
        this.id=data.id;
        this.name=data.name;
        this.city=data.city;
        this.coordinates=data.coordinates;
        this.address=data.address;
        this.phonenumber=data.phonenumber;
        this.maxroom=data.maxroom;
        this.imagename=data.imagename;
        this.isactive=data.isactive;
    }
}