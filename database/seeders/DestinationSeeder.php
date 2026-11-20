<?php

namespace Database\Seeders;

use App\Enums\RoadCondition;
use App\Enums\TerrainType;
use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Taman Nasional Bromo Tengger Semeru',
                'slug' => 'bromo-tengger-semeru',
                'description' => 'Kawasan lautan pasir vulkanik, kaldera luas, dan bukit teletubbies dengan panorama sunrise spektakuler.',
                'address' => 'Area Gunung Bromo, Podokoyo, Tosari, Pasuruan',
                'city' => 'Pasuruan / Probolinggo',
                'province' => 'Jawa Timur',
                'latitude' => -7.942493,
                'longitude' => 112.953011,
                'terrain_type' => TerrainType::Offroad,
                'road_condition' => RoadCondition::Offroad,
                'accessibility_notes' => 'Lautan pasir berdebu tebal dan tanjakan terjal. Sangat disarankan unit 4x4 (Jeep/SUV tinggi).',
                'is_active' => true,
            ],
            [
                'name' => 'Dataran Tinggi Dieng',
                'slug' => 'dataran-tinggi-dieng',
                'description' => 'Dataran tinggi vulkanik dengan telaga warna, kawah belerang, dan candi bersejarah di ketinggian 2.000 mdpl.',
                'address' => 'Dieng Kulon, Batur, Banjarnegara',
                'city' => 'Wonosobo / Banjarnegara',
                'province' => 'Jawa Tengah',
                'latitude' => -7.204554,
                'longitude' => 109.907993,
                'terrain_type' => TerrainType::Mountain,
                'road_condition' => RoadCondition::Moderate,
                'accessibility_notes' => 'Jalan berkelok tajam dan tanjakan curam (Gardu Pandang Tieng). Membutuhkan torsi mesin kuat dan rem prima.',
                'is_active' => true,
            ],
            [
                'name' => 'Pantai Baron & Krakal',
                'slug' => 'pantai-baron-krakal',
                'description' => 'Garis pantai selatan berpasir putih dengan deburan ombak khas samudera dan deretan tebing karang.',
                'address' => 'Rejosari, Kemadang, Tanjungsari, Gunungkidul',
                'city' => 'Gunungkidul',
                'province' => 'DI Yogyakarta',
                'latitude' => -8.128790,
                'longitude' => 110.548980,
                'terrain_type' => TerrainType::Beach,
                'road_condition' => RoadCondition::Good,
                'accessibility_notes' => 'Jalan aspal mulus namun naik-turun perbukitan karst khas Gunungkidul.',
                'is_active' => true,
            ],
            [
                'name' => 'Candi Borobudur',
                'slug' => 'candi-borobudur',
                'description' => 'Candi Buddha terbesar di dunia yang merupakan warisan budaya dunia UNESCO.',
                'address' => 'Jl. Badrawati, Kw. Candi Borobudur, Magelang',
                'city' => 'Magelang',
                'province' => 'Jawa Tengah',
                'latitude' => -7.607874,
                'longitude' => 110.203751,
                'terrain_type' => TerrainType::Highway,
                'road_condition' => RoadCondition::Good,
                'accessibility_notes' => 'Akses jalan protokol lebar dan sangat mulus, cocok untuk semua tipe mobil termasuk sedan dan city car.',
                'is_active' => true,
            ],
            [
                'name' => 'Kawasan Malioboro & Keraton',
                'slug' => 'malioboro-keraton-jogja',
                'description' => 'Pusat wisata belanja budaya, kuliner malam, dan cagar sejarah di jantung kota Yogyakarta.',
                'address' => 'Jl. Malioboro, Sosromenduran, Gedong Tengen, Kota Yogyakarta',
                'city' => 'Kota Yogyakarta',
                'province' => 'DI Yogyakarta',
                'latitude' => -7.792618,
                'longitude' => 110.365845,
                'terrain_type' => TerrainType::City,
                'road_condition' => RoadCondition::Good,
                'accessibility_notes' => 'Lalu lintas kota padat dengan ruang parkir terbatas. City car dan MPV lincah sangat nyaman di area ini.',
                'is_active' => true,
            ],
            [
                'name' => 'Kawah Ijen',
                'slug' => 'kawah-ijen-banyuwangi',
                'description' => 'Danau kawah asam toska terindah dan fenomena api biru (blue fire) langka dunia.',
                'address' => 'Tamansari, Licin, Banyuwangi',
                'city' => 'Banyuwangi',
                'province' => 'Jawa Timur',
                'latitude' => -8.058372,
                'longitude' => 114.242337,
                'terrain_type' => TerrainType::Mountain,
                'road_condition' => RoadCondition::Rough,
                'accessibility_notes' => 'Rute Paltuding memiliki tanjakan sangat tajam dan beberapa ruas berlubang. Kendaraan wajib berpenggerak kuat.',
                'is_active' => true,
            ],
        ];

        foreach ($destinations as $dest) {
            Destination::updateOrCreate(['slug' => $dest['slug']], $dest);
        }
    }
}
