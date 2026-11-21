<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndonesiaRegionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DI Yogyakarta (34)
        $this->seedYogyakarta();

        // 2. DKI Jakarta (31)
        $this->seedJakarta();

        // 3. Jawa Barat (32)
        $this->seedJawaBarat();

        // 4. Jawa Tengah (33)
        $this->seedJawaTengah();

        // 5. Jawa Timur (35)
        $this->seedJawaTimur();

        // 6. Bali (51)
        $this->seedBali();
    }

    private function seedYogyakarta(): void
    {
        Province::updateOrCreate(['id' => '34'], ['name' => 'DI Yogyakarta']);

        // Kota Yogyakarta (3471)
        Regency::updateOrCreate(['id' => '3471'], [
            'province_id' => '34',
            'name' => 'Kota Yogyakarta',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3471', [
            ['id' => '347101', 'name' => 'Gedongtengen', 'villages' => [
                ['id' => '34710101', 'name' => 'Sosromenduran', 'postal_code' => '55271'],
                ['id' => '34710102', 'name' => 'Pringgokusuman', 'postal_code' => '55272'],
            ]],
            ['id' => '347102', 'name' => 'Danurejan', 'villages' => [
                ['id' => '34710201', 'name' => 'Suryatmajan', 'postal_code' => '55213'],
                ['id' => '34710202', 'name' => 'Tegal Panggung', 'postal_code' => '55212'],
                ['id' => '34710203', 'name' => 'Bausasran', 'postal_code' => '55211'],
            ]],
            ['id' => '347103', 'name' => 'Kraton', 'villages' => [
                ['id' => '34710301', 'name' => 'Patehan', 'postal_code' => '55133'],
                ['id' => '34710302', 'name' => 'Panembahan', 'postal_code' => '55131'],
                ['id' => '34710303', 'name' => 'Kadipaten', 'postal_code' => '55132'],
            ]],
            ['id' => '347104', 'name' => 'Gondomanan', 'villages' => [
                ['id' => '34710401', 'name' => 'Ngupasan', 'postal_code' => '55122'],
                ['id' => '34710402', 'name' => 'Prawirodirjan', 'postal_code' => '55121'],
            ]],
            ['id' => '347105', 'name' => 'Mergangsan', 'villages' => [
                ['id' => '34710501', 'name' => 'Brontokusuman', 'postal_code' => '55153'],
                ['id' => '34710502', 'name' => 'Wirogunan', 'postal_code' => '55151'],
            ]],
        ]);

        // Kab. Sleman (3404)
        Regency::updateOrCreate(['id' => '3404'], [
            'province_id' => '34',
            'name' => 'Kabupaten Sleman',
            'type' => 'Kabupaten',
        ]);
        $this->seedDistrictsAndVillages('3404', [
            ['id' => '340401', 'name' => 'Depok', 'villages' => [
                ['id' => '34040101', 'name' => 'Caturtunggal', 'postal_code' => '55281'],
                ['id' => '34040102', 'name' => 'Maguwoharjo', 'postal_code' => '55282'],
                ['id' => '34040103', 'name' => 'Condongcatur', 'postal_code' => '55283'],
            ]],
            ['id' => '340402', 'name' => 'Mlati', 'villages' => [
                ['id' => '34040201', 'name' => 'Sinduadi', 'postal_code' => '55284'],
                ['id' => '34040202', 'name' => 'Sendangadi', 'postal_code' => '55285'],
            ]],
            ['id' => '340403', 'name' => 'Ngaglik', 'villages' => [
                ['id' => '34040301', 'name' => 'Sariharjo', 'postal_code' => '55581'],
                ['id' => '34040302', 'name' => 'Minomartani', 'postal_code' => '55581'],
            ]],
            ['id' => '340404', 'name' => 'Kalasan', 'villages' => [
                ['id' => '34040401', 'name' => 'Tirtomartani', 'postal_code' => '55571'],
                ['id' => '34040402', 'name' => 'Purwomartani', 'postal_code' => '55571'],
            ]],
        ]);

        // Kab. Bantul (3402)
        Regency::updateOrCreate(['id' => '3402'], [
            'province_id' => '34',
            'name' => 'Kabupaten Bantul',
            'type' => 'Kabupaten',
        ]);
        $this->seedDistrictsAndVillages('3402', [
            ['id' => '340201', 'name' => 'Sewon', 'villages' => [
                ['id' => '34020101', 'name' => 'Bangunharjo', 'postal_code' => '55187'],
                ['id' => '34020102', 'name' => 'Panggungharjo', 'postal_code' => '55188'],
            ]],
            ['id' => '340202', 'name' => 'Kasihan', 'villages' => [
                ['id' => '34020201', 'name' => 'Tirtonirmolo', 'postal_code' => '55181'],
                ['id' => '34020202', 'name' => 'Tamantirto', 'postal_code' => '55183'],
            ]],
            ['id' => '340203', 'name' => 'Kretek', 'villages' => [
                ['id' => '34020301', 'name' => 'Parangtritis', 'postal_code' => '55772'],
                ['id' => '34020302', 'name' => 'Donotirto', 'postal_code' => '55772'],
            ]],
        ]);

        // Kab. Kulon Progo (3401)
        Regency::updateOrCreate(['id' => '3401'], [
            'province_id' => '34',
            'name' => 'Kabupaten Kulon Progo',
            'type' => 'Kabupaten',
        ]);
        $this->seedDistrictsAndVillages('3401', [
            ['id' => '340101', 'name' => 'Temon', 'villages' => [
                ['id' => '34010101', 'name' => 'Glagah (Bandara YIA)', 'postal_code' => '55654'],
                ['id' => '34010102', 'name' => 'Palihan', 'postal_code' => '55654'],
            ]],
            ['id' => '340102', 'name' => 'Wates', 'villages' => [
                ['id' => '34010201', 'name' => 'Wates', 'postal_code' => '55611'],
                ['id' => '34010202', 'name' => 'Bendungan', 'postal_code' => '55612'],
            ]],
        ]);

        // Kab. Gunungkidul (3403)
        Regency::updateOrCreate(['id' => '3403'], [
            'province_id' => '34',
            'name' => 'Kabupaten Gunungkidul',
            'type' => 'Kabupaten',
        ]);
        $this->seedDistrictsAndVillages('3403', [
            ['id' => '340301', 'name' => 'Wonosari', 'villages' => [
                ['id' => '34030101', 'name' => 'Wonosari', 'postal_code' => '55811'],
                ['id' => '34030102', 'name' => 'Kepek', 'postal_code' => '55813'],
            ]],
            ['id' => '340302', 'name' => 'Tanjungsari', 'villages' => [
                ['id' => '34030201', 'name' => 'Kemadang', 'postal_code' => '55881'],
                ['id' => '34030202', 'name' => 'Banjarejo', 'postal_code' => '55881'],
            ]],
        ]);
    }

    private function seedJakarta(): void
    {
        Province::updateOrCreate(['id' => '31'], ['name' => 'DKI Jakarta']);

        // Jakarta Selatan (3174)
        Regency::updateOrCreate(['id' => '3174'], [
            'province_id' => '31',
            'name' => 'Kota Administrasi Jakarta Selatan',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3174', [
            ['id' => '317401', 'name' => 'Kebayoran Baru', 'villages' => [
                ['id' => '31740101', 'name' => 'Senayan', 'postal_code' => '12190'],
                ['id' => '31740102', 'name' => 'Melawai', 'postal_code' => '12160'],
            ]],
            ['id' => '317402', 'name' => 'Cilandak', 'villages' => [
                ['id' => '31740201', 'name' => 'Cilandak Barat', 'postal_code' => '12430'],
                ['id' => '31740202', 'name' => 'Cipete Selatan', 'postal_code' => '12410'],
            ]],
            ['id' => '317403', 'name' => 'Tebet', 'villages' => [
                ['id' => '31740301', 'name' => 'Tebet Barat', 'postal_code' => '12810'],
                ['id' => '31740302', 'name' => 'Menteng Dalam', 'postal_code' => '12870'],
            ]],
            ['id' => '317404', 'name' => 'Setiabudi', 'villages' => [
                ['id' => '31740401', 'name' => 'Kuningan Timur', 'postal_code' => '12950'],
                ['id' => '31740402', 'name' => 'Karet Semanggi', 'postal_code' => '12930'],
            ]],
        ]);

        // Jakarta Pusat (3171)
        Regency::updateOrCreate(['id' => '3171'], [
            'province_id' => '31',
            'name' => 'Kota Administrasi Jakarta Pusat',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3171', [
            ['id' => '317101', 'name' => 'Gambir', 'villages' => [
                ['id' => '31710101', 'name' => 'Gambir', 'postal_code' => '10110'],
                ['id' => '31710102', 'name' => 'Kebon Kelapa', 'postal_code' => '10120'],
            ]],
            ['id' => '317102', 'name' => 'Tanah Abang', 'villages' => [
                ['id' => '31710201', 'name' => 'Gelora', 'postal_code' => '10270'],
                ['id' => '31710202', 'name' => 'Bendungan Hilir', 'postal_code' => '10210'],
            ]],
            ['id' => '317103', 'name' => 'Menteng', 'villages' => [
                ['id' => '31710301', 'name' => 'Menteng', 'postal_code' => '10310'],
                ['id' => '31710302', 'name' => 'Gondangdia', 'postal_code' => '10350'],
            ]],
        ]);

        // Jakarta Timur (3175)
        Regency::updateOrCreate(['id' => '3175'], [
            'province_id' => '31',
            'name' => 'Kota Administrasi Jakarta Timur',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3175', [
            ['id' => '317501', 'name' => 'Makasar', 'villages' => [
                ['id' => '31750101', 'name' => 'Halim Perdana Kusumah', 'postal_code' => '13610'],
                ['id' => '31750102', 'name' => 'Pinang Ranti', 'postal_code' => '13560'],
            ]],
        ]);
    }

    private function seedJawaBarat(): void
    {
        Province::updateOrCreate(['id' => '32'], ['name' => 'Jawa Barat']);

        // Kota Bandung (3273)
        Regency::updateOrCreate(['id' => '3273'], [
            'province_id' => '32',
            'name' => 'Kota Bandung',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3273', [
            ['id' => '327301', 'name' => 'Coblong', 'villages' => [
                ['id' => '32730101', 'name' => 'Dago', 'postal_code' => '40135'],
                ['id' => '32730102', 'name' => 'Lebak Siliwangi', 'postal_code' => '40132'],
            ]],
            ['id' => '327302', 'name' => 'Sumur Bandung', 'villages' => [
                ['id' => '32730201', 'name' => 'Braga', 'postal_code' => '40111'],
                ['id' => '32730202', 'name' => 'Merdeka', 'postal_code' => '40113'],
            ]],
            ['id' => '327303', 'name' => 'Sukajadi', 'villages' => [
                ['id' => '32730301', 'name' => 'Pasteur', 'postal_code' => '40161'],
                ['id' => '32730302', 'name' => 'Sukabungah', 'postal_code' => '40162'],
            ]],
        ]);

        // Kab. Bandung Barat (3217)
        Regency::updateOrCreate(['id' => '3217'], [
            'province_id' => '32',
            'name' => 'Kabupaten Bandung Barat',
            'type' => 'Kabupaten',
        ]);
        $this->seedDistrictsAndVillages('3217', [
            ['id' => '321701', 'name' => 'Lembang', 'villages' => [
                ['id' => '32170101', 'name' => 'Lembang', 'postal_code' => '40391'],
                ['id' => '32170102', 'name' => 'Cikole', 'postal_code' => '40391'],
            ]],
        ]);

        // Kota Bogor (3271)
        Regency::updateOrCreate(['id' => '3271'], [
            'province_id' => '32',
            'name' => 'Kota Bogor',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3271', [
            ['id' => '327101', 'name' => 'Bogor Tengah', 'villages' => [
                ['id' => '32710101', 'name' => 'Pabaton', 'postal_code' => '16121'],
                ['id' => '32710102', 'name' => 'Paledang', 'postal_code' => '16122'],
            ]],
        ]);
    }

    private function seedJawaTengah(): void
    {
        Province::updateOrCreate(['id' => '33'], ['name' => 'Jawa Tengah']);

        // Kota Semarang (3374)
        Regency::updateOrCreate(['id' => '3374'], [
            'province_id' => '33',
            'name' => 'Kota Semarang',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3374', [
            ['id' => '337401', 'name' => 'Semarang Barat', 'villages' => [
                ['id' => '33740101', 'name' => 'Krobokan', 'postal_code' => '50141'],
                ['id' => '33740102', 'name' => 'Bojongsalaman', 'postal_code' => '50141'],
            ]],
            ['id' => '337402', 'name' => 'Semarang Tengah', 'villages' => [
                ['id' => '33740201', 'name' => 'Sekayu', 'postal_code' => '50132'],
                ['id' => '33740202', 'name' => 'Kauman', 'postal_code' => '50137'],
            ]],
        ]);

        // Kota Surakarta / Solo (3372)
        Regency::updateOrCreate(['id' => '3372'], [
            'province_id' => '33',
            'name' => 'Kota Surakarta',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3372', [
            ['id' => '337201', 'name' => 'Banjarsari', 'villages' => [
                ['id' => '33720101', 'name' => 'Manahan', 'postal_code' => '57139'],
                ['id' => '33720102', 'name' => 'Gilingan', 'postal_code' => '57134'],
            ]],
            ['id' => '337202', 'name' => 'Laweyan', 'villages' => [
                ['id' => '33720201', 'name' => 'Purwosari', 'postal_code' => '57142'],
                ['id' => '33720202', 'name' => 'Laweyan', 'postal_code' => '57148'],
            ]],
        ]);

        // Kab. Magelang (3308)
        Regency::updateOrCreate(['id' => '3308'], [
            'province_id' => '33',
            'name' => 'Kabupaten Magelang',
            'type' => 'Kabupaten',
        ]);
        $this->seedDistrictsAndVillages('3308', [
            ['id' => '330801', 'name' => 'Borobudur', 'villages' => [
                ['id' => '33080101', 'name' => 'Borobudur', 'postal_code' => '56553'],
                ['id' => '33080102', 'name' => 'Wanurejo', 'postal_code' => '56553'],
            ]],
        ]);
    }

    private function seedJawaTimur(): void
    {
        Province::updateOrCreate(['id' => '35'], ['name' => 'Jawa Timur']);

        // Kota Surabaya (3578)
        Regency::updateOrCreate(['id' => '3578'], [
            'province_id' => '35',
            'name' => 'Kota Surabaya',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3578', [
            ['id' => '357801', 'name' => 'Gubeng', 'villages' => [
                ['id' => '35780101', 'name' => 'Gubeng', 'postal_code' => '60281'],
                ['id' => '35780102', 'name' => 'Airlangga', 'postal_code' => '60286'],
            ]],
            ['id' => '357802', 'name' => 'Tegalsari', 'villages' => [
                ['id' => '35780201', 'name' => 'Kedungdoro', 'postal_code' => '60261'],
                ['id' => '35780202', 'name' => 'Dr. Soetomo', 'postal_code' => '60264'],
            ]],
        ]);

        // Kota Malang (3573)
        Regency::updateOrCreate(['id' => '3573'], [
            'province_id' => '35',
            'name' => 'Kota Malang',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('3573', [
            ['id' => '357301', 'name' => 'Klojen', 'villages' => [
                ['id' => '35730101', 'name' => 'Klojen', 'postal_code' => '65111'],
                ['id' => '35730102', 'name' => 'Kauman', 'postal_code' => '65119'],
            ]],
            ['id' => '357302', 'name' => 'Lowokwaru', 'villages' => [
                ['id' => '35730201', 'name' => 'Jatimulyo', 'postal_code' => '65141'],
                ['id' => '35730202', 'name' => 'Mojolangu', 'postal_code' => '65142'],
            ]],
        ]);
    }

    private function seedBali(): void
    {
        Province::updateOrCreate(['id' => '51'], ['name' => 'Bali']);

        // Kota Denpasar (5171)
        Regency::updateOrCreate(['id' => '5171'], [
            'province_id' => '51',
            'name' => 'Kota Denpasar',
            'type' => 'Kota',
        ]);
        $this->seedDistrictsAndVillages('5171', [
            ['id' => '517101', 'name' => 'Denpasar Selatan', 'villages' => [
                ['id' => '51710101', 'name' => 'Sanur', 'postal_code' => '80228'],
                ['id' => '51710102', 'name' => 'Panjer', 'postal_code' => '80225'],
            ]],
        ]);

        // Kab. Badung (5103)
        Regency::updateOrCreate(['id' => '5103'], [
            'province_id' => '51',
            'name' => 'Kabupaten Badung',
            'type' => 'Kabupaten',
        ]);
        $this->seedDistrictsAndVillages('5103', [
            ['id' => '510301', 'name' => 'Kuta', 'villages' => [
                ['id' => '51030101', 'name' => 'Kuta', 'postal_code' => '80361'],
                ['id' => '51030102', 'name' => 'Legian', 'postal_code' => '80361'],
                ['id' => '51030103', 'name' => 'Seminyak', 'postal_code' => '80361'],
            ]],
            ['id' => '510302', 'name' => 'Kuta Selatan', 'villages' => [
                ['id' => '51030201', 'name' => 'Jimbaran', 'postal_code' => '80369'],
                ['id' => '51030202', 'name' => 'Benoa', 'postal_code' => '80361'],
                ['id' => '51030203', 'name' => 'Pecatu', 'postal_code' => '80361'],
            ]],
            ['id' => '510303', 'name' => 'Kuta Utara', 'villages' => [
                ['id' => '51030301', 'name' => 'Canggu', 'postal_code' => '80351'],
                ['id' => '51030302', 'name' => 'Tibubeneng', 'postal_code' => '80361'],
                ['id' => '51030303', 'name' => 'Kerobokan', 'postal_code' => '80361'],
            ]],
        ]);

        // Kab. Gianyar (5104)
        Regency::updateOrCreate(['id' => '5104'], [
            'province_id' => '51',
            'name' => 'Kabupaten Gianyar',
            'type' => 'Kabupaten',
        ]);
        $this->seedDistrictsAndVillages('5104', [
            ['id' => '510401', 'name' => 'Ubud', 'villages' => [
                ['id' => '51040101', 'name' => 'Ubud', 'postal_code' => '80571'],
                ['id' => '51040102', 'name' => 'Kedewatan', 'postal_code' => '80571'],
                ['id' => '51040103', 'name' => 'Sayan', 'postal_code' => '80571'],
            ]],
        ]);
    }

    private function seedDistrictsAndVillages(string $regencyId, array $districts): void
    {
        foreach ($districts as $districtData) {
            District::updateOrCreate(
                ['id' => $districtData['id']],
                [
                    'regency_id' => $regencyId,
                    'name' => $districtData['name'],
                ]
            );

            if (!empty($districtData['villages'])) {
                foreach ($districtData['villages'] as $villageData) {
                    Village::updateOrCreate(
                        ['id' => $villageData['id']],
                        [
                            'district_id' => $districtData['id'],
                            'name' => $villageData['name'],
                            'postal_code' => $villageData['postal_code'] ?? null,
                        ]
                    );
                }
            }
        }
    }
}
