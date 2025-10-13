export class UpdateUserDTO {
    name: string;
    phonenumber: string;
    password: string;
    email: string;
    address: string;
    dateofbirth: Date;

    constructor(data: any) {
        this.name = data.name;
        this.phonenumber = data.phonenumber;
        this.password = data.password;
        this.email = data.email;
        this.address = data.address;
        this.dateofbirth = data.dateofbirth;
    }
}