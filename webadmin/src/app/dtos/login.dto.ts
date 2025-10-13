export class LoginDTO{
    phonenumber:string;
    password:string;
    constructor(data:any){
        this.phonenumber=data.phonenumber;
        this.password=data.password;
    }
}