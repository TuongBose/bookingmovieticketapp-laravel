<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cinemas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('city', 100);
            $table->string('coordinates', 50)->nullable();
            $table->string('address', 255);
            $table->string('phonenumber', 20)->nullable();
            $table->integer('maxroom')->nullable();
            $table->string('imagename', 100)->nullable();
            $table->boolean('isactive')->default(true);
        });

        DB::table('cinemas')->insert([
            [
                'name' => 'CineJoy Nguyễn Du',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.773307, 106.693373',
                'address' => '116 Nguyễn Du, Quận 1, TP.HCM',
                'phonenumber' => '19002224',
                'maxroom' => 4,
                'imagename' => 'cinema_1_1746214967186.jpg',
            ],
            [
                'name' => 'CineJoy Tân Bình',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.790432, 106.640716',
                'address' => '246 Nguyễn Hồng Đào, Quận Tân Bình, TP.HCM',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_2_1746215174736.jpg',
            ],
            [
                'name' => 'CineJoy Quang Trung',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.834759, 106.662373',
                'address' => 'Lầu 3, TTTM CoopMart Foodcosa số 304A, Quang Trung, P.11, Q. Gò Vấp, Tp.HCM',
                'phonenumber' => '19002224',
                'maxroom' => 6,
                'imagename' => 'cinema_3_1746215224604.jpg',
            ],
            [
                'name' => 'CineJoy Long Xuyên',
                'city' => 'Tỉnh An Giang',
                'coordinates' => '10.384155, 105.436843',
                'address' => 'Tầng 1, TTTM Nguyễn Kim, số 01 Trần Hưng Đạo, Phường Mỹ Bình, Thành phố Long Xuyên',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_4_1746375169224.jpg',
            ],
            [
                'name' => 'CineJoy Đà Nẵng',
                'city' => 'Thành phố Đà Nẵng',
                'coordinates' => '16.066701, 108.186900',
                'address' => 'Tầng 3, TTTM Coop Mart, 478 Điện Biên Phủ, Quận Thanh Khê, Đà Nẵng',
                'phonenumber' => '19002224',
                'maxroom' => 4,
                'imagename' => 'cinema_5_1746215401993.jpg',
            ],
            [
                'name' => 'CineJoy Co.opXtra Linh Trung',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.867896, 106.776687',
                'address' => 'Tầng trệt, TTTM Co.opXtra Linh Trung, số 934 Quốc Lộ 1A, Phường Linh Trung, Quận Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam',
                'phonenumber' => '19002224',
                'maxroom' => 6,
                'imagename' => 'cinema_6_1746375325231.jpg',
            ],
            [
                'name' => 'CineJoy Huỳnh Tấn Phát',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.712225, 106.736575',
                'address' => 'Lầu 2, TTTM Coopmart, số 1362 Huỳnh Tấn Phát, khu phố 1, Phường Phú Mỹ, Quận 7, Tp.Hồ Chí Minh, Việt Nam.',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_7_1746215524120.jpg',
            ],
            [
                'name' => 'CineJoy Sala',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.771500, 106.721782',
                'address' => 'Tầng 3, Thiso Mall Sala, 10 Mai Chí Thọ, Phường Thủ Thiêm, Thành phố Thủ Đức',
                'phonenumber' => '19002224',
                'maxroom' => 7,
                'imagename' => 'cinema_8_1746215129247.jpg',
            ],
            [
                'name' => 'CineJoy Hải Phòng',
                'city' => 'Thành phố Hải Phòng',
                'coordinates' => '20.856159, 106.686521',
                'address' => 'Lầu 7, TTTM Nguyễn Kim - Sài Gòn Mall, số 104 Lương Khánh Thiện',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_9_1746375448482.jpg',
            ],
            [
                'name' => 'CineJoy Kinh Dương Vương',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.749503, 106.628778',
                'address' => '718 Kinh Dương Vương, Quận 6, TP.HCM',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_10_1746375548086.jpg',
            ],
            [
                'name' => 'CineJoy Bến Tre',
                'city' => 'Tỉnh Bến Tre',
                'coordinates' => '10.241207, 106.376721',
                'address' => 'Lầu 1, TTTM Sense City 26A Trần Quốc Tuấn, Phường An Hội, TP. Bến Tre',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_11_1746375599036.jpg',
            ],
            [
                'name' => 'CineJoy Mipec Long Biên',
                'city' => 'Thành phố Hà Nội',
                'coordinates' => '21.045421, 105.866193',
                'address' => 'Tầng 6, TTTM Mipec Long Biên, Số 2, Phố Long Biên 2, Ngọc Lâm, Long Biên, Hà Nội',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_12_1746375758125.jpg',
            ],
            [
                'name' => 'CineJoy Cà Mau',
                'city' => 'Tỉnh Cà Mau',
                'coordinates' => '9.177908, 105.154540',
                'address' => 'Lầu 2, TTTM Sense City, số 9, Trần Hưng Đạo, P.5, Tp. Cà Mau',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_13_1746375846069.jpg',
            ],
            [
                'name' => 'CineJoy Trung Chánh',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.855339, 106.608343',
                'address' => 'TTVH Quận 12, Số 09 Quốc Lộ 22, P. Trung Mỹ Tây, Quận 12',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_14_1746375944257.jpg',
            ],
            [
                'name' => 'CineJoy Vinh',
                'city' => 'Tỉnh Nghệ An',
                'coordinates' => '18.676724, 105.677608',
                'address' => 'Lầu 5, Trung tâm Giải Trí City HUB – số 1 Lê Hồng Phong, thành phố Vinh',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_15_1746376009746.jpg',
            ],
            [
                'name' => 'CineJoy Nguyễn Văn Quá',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.847156, 106.634100',
                'address' => '119B Nguyễn Văn Quá, Phường Đông Hưng Thuận, Quận 12',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_16_1746376125934.jpg',
            ],
            [
                'name' => 'CineJoy Buôn Ma Thuột',
                'city' => 'Tỉnh Đắk Lắk',
                'coordinates' => '12.692365, 108.062186',
                'address' => 'Tầng trệt, TTTM Coop Mart Buôn Ma Thuột, số 71 Nguyễn Tất Thành, Phường Tân An, Tp. Buôn Ma Thuột, Tỉnh Đắk Lắk, Việt Nam',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_17_1746376188767.jpg',
            ],
            [
                'name' => 'CineJoy Nha Trang Center',
                'city' => 'Tỉnh Khánh Hòa',
                'coordinates' => '12.248043, 109.196326',
                'address' => 'Tầng 3, Trung Tâm Thương Mại Nha Trang Center - 20 Trần Phú, Nha Trang, Khánh Hòa',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_18_1746376268601.jpg',
            ],
            [
                'name' => 'CineJoy Trường Chinh',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.818052, 106.630815',
                'address' => 'Tầng 3 - Co.opMart TTTM Thắng Lợi - Số 2 Trường Chinh, Tây Thạnh, Tân Phú, Thành phố Hồ Chí Minh',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_19_1746376314673.jpg',
            ],
            [
                'name' => 'CineJoy GO! Mall Bà Rịa',
                'city' => 'Tỉnh Bà Rịa - Vũng Tàu',
                'coordinates' => '10.492306, 107.169138',
                'address' => 'Tầng 3 TTTM GO! Bà Rịa, Số 2A đường Nguyễn Đình Chiểu, KP1, P. Phước Hiệp, TP. Bà Rịa, Tỉnh Bà Rịa-Vũng Tàu',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_20_1746376394994.jpg',
            ],
            [
                'name' => 'CineJoy Cine+ Gold Coast Nha Trang',
                'city' => 'Tỉnh Khánh Hòa',
                'coordinates' => '12.247836, 109.194918',
                'address' => 'Tầng 8, TTTM Gold Coast Nha Trang - Số 1 Trần Hưng Đạo, P. Lộc Thọ, TP. Nha Trang',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_21_1746376448605.jpg',
            ],
            [
                'name' => 'CineJoy Cine+ Thiso Phan Huy Ích',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.841520, 106.637373',
                'address' => 'Tầng 4 TTTM Thiso Mall Trường Chinh - Phan Huy Ích - 385 Phan Huy Ích, Phường 14, Quận Gò Vấp, TP. HCM',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_22_1746376485825.jpg',
            ],
            [
                'name' => 'CineJoy Aeon Mall Huế',
                'city' => 'Thành phố Huế',
                'coordinates' => '16.454693, 107.615367',
                'address' => 'Galaxy Aeon Mall Huế - Tầng 4 TTTM Aeon Mall Huế, Cửa số 5 và số 6, Sảnh Đỗ Quyên, 8 Võ Nguyên Giáp, An Đông, Huế, Thừa Thiên - Huế',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_23_1746376523036.jpg',
            ],
            [
                'name' => 'CineJoy Parc Mall Q8',
                'city' => 'Thành phố Hồ Chí Minh',
                'coordinates' => '10.740289, 106.678833',
                'address' => 'Tầng 4 TTTM Parc Mall, 547-549 Tạ Quang Bửu, Phường 4, Quận 8',
                'phonenumber' => '19002224',
                'maxroom' => 5,
                'imagename' => 'cinema_24_1746376581868.jpg',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cinemas');
    }
};
