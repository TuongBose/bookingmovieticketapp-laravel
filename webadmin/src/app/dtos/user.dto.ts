export class UserDTO {
    id: number;
    name: string;
    email: string;
    password: string;
    phonenumber: string;
    address: string;
    dateofbirth: Date;
    createdat: Date;
    isactive: boolean;
    rolename: boolean;
    imagename: string;

    constructor(data: any) {
        this.id = data.id;
        this.name = data.name;
        this.email = data.email;
        this.password = data.password;
        this.phonenumber = data.phonenumber;
        this.address = data.address;
        this.dateofbirth = data.dateofbirth;
        this.createdat = data.createdat;
        this.isactive = data.isactive;
        this.rolename = data.rolename;
        this.imagename = data.imagename
    }
}