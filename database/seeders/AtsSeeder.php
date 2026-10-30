<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobVacancy;
use App\Models\JobApplication;

class AtsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Job Vacancies
        $v1 = JobVacancy::create([
            'title' => 'Barista Full-Time',
            'description' => 'Kami mencari Barista berenergi tinggi untuk meracik kopi berkualitas premium di Ngafe Lounge. Anda akan bertanggung jawab untuk melayani pelanggan, mengoperasikan mesin espresso, menjaga kebersihan bar, dan meracik minuman sesuai standar resep.',
            'requirements' => "- Pria/Wanita, usia maksimal 28 tahun\n- Pengalaman minimal 1 tahun sebagai barista di coffee shop\n- Menguasai teknik latte art dasar & kalibrasi mesin espresso\n- Bersedia bekerja dalam sistem shift, akhir pekan, dan hari libur nasional\n- Berpenampilan menarik & komunikatif",
            'status' => 'published',
        ]);

        $v2 = JobVacancy::create([
            'title' => 'Kitchen Chef / Cook Helper',
            'description' => 'Kitchen Chef bertugas menyiapkan hidangan makanan berat & snack, mengelola stok bahan baku dapur, serta menjaga kebersihan & sanitasi area dapur sesuai standar keamanan pangan.',
            'requirements' => "- Pengalaman minimal 2 tahun di bidang F&B (dapur)\n- Memiliki pengetahuan tentang teknik memasak Western & Nusantara\n- Cepat, disiplin, dan mampu bekerja di bawah tekanan tinggi\n- Memiliki sertifikat sanitasi pangan menjadi nilai tambah\n- Bersedia ditempatkan di Jakarta Selatan",
            'status' => 'published',
        ]);

        $v3 = JobVacancy::create([
            'title' => 'Waiter / Waitress',
            'description' => 'Menjadi garda terdepan pelayanan kafe. Bertugas menyambut pelanggan, mencatat pesanan (bila diperlukan), mengantarkan hidangan dengan ramah, dan memastikan kebersihan area meja makan.',
            'requirements' => "- Pria/Wanita, tinggi minimal 160cm (Pria) / 155cm (Wanita)\n- Usia maksimal 25 tahun\n- Jujur, murah senyum, sopan, dan berpenampilan bersih\n- Pendidikan minimal SMA/SMK sederajat\n- Belum menikah",
            'status' => 'published',
        ]);

        $v4 = JobVacancy::create([
            'title' => 'Digital Marketing & Content Creator',
            'description' => 'Mengelola media sosial kafe (Instagram, TikTok), membuat konten kreatif harian (video, foto), merancang promosi bulanan, dan berinteraksi dengan pengikut sosial media.',
            'requirements' => "- Pendidikan minimal Diploma/S1 Ilmu Komunikasi/DKV/Marketing\n- Menguasai aplikasi editing video/foto (CapCut, Canva, Premiere, Photoshop)\n- Update dengan tren media sosial terkini\n- Memiliki portofolio konten kreatif",
            'status' => 'draft', // Draft vacancy for testing
        ]);

        // 2. Job Applications
        $applications = [
            // Barista Applications
            [
                'job_vacancy_id' => $v1->id,
                'applicant_name' => 'Ferry Setiawan',
                'email' => 'ferry.setiawan@gmail.com',
                'phone_number' => '081299887766',
                'cv_path' => 'cv_ferry_setiawan.pdf',
                'status' => 'interviewing',
            ],
            [
                'job_vacancy_id' => $v1->id,
                'applicant_name' => 'Lestari Wulandari',
                'email' => 'lestari.wulan@yahoo.com',
                'phone_number' => '082155667788',
                'cv_path' => 'cv_lestari_wulandari.pdf',
                'status' => 'submitted',
            ],
            [
                'job_vacancy_id' => $v1->id,
                'applicant_name' => 'Riko Prasetyo',
                'email' => 'riko.prast@gmail.com',
                'phone_number' => '087844332211',
                'cv_path' => 'cv_riko_prasetyo.pdf',
                'status' => 'rejected',
            ],
            // Chef Applications
            [
                'job_vacancy_id' => $v2->id,
                'applicant_name' => 'Chef Bambang Suryono',
                'email' => 'chef.bambang@outlook.com',
                'phone_number' => '089900112233',
                'cv_path' => 'cv_bambang_chef.pdf',
                'status' => 'hired',
            ],
            [
                'job_vacancy_id' => $v2->id,
                'applicant_name' => 'Citra Kirana',
                'email' => 'citra.kirana@gmail.com',
                'phone_number' => '081344556677',
                'cv_path' => 'cv_citra_kirana.pdf',
                'status' => 'reviewing',
            ],
            // Waiter Applications
            [
                'job_vacancy_id' => $v3->id,
                'applicant_name' => 'Andi Wijaya',
                'email' => 'andi.wijaya99@gmail.com',
                'phone_number' => '085288990011',
                'cv_path' => 'cv_andi_wijaya.pdf',
                'status' => 'submitted',
            ]
        ];

        foreach ($applications as $app) {
            JobApplication::create($app);
        }
    }
}
