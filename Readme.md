# Talent Hub

Aplikasi Human Resource Management System berbasis web yang dibangun dengan PHP Native untuk mengelola seluruh aspek kepegawaian dalam satu perusahaan.

## 📋 Deskripsi

Talent Hub adalah aplikasi web yang dirancang khusus untuk membantu perusahaan dalam mengelola data karyawan secara komprehensif, mulai dari proses rekrutmen, absensi, penggajian, hingga manajemen proyek. Aplikasi ini dibangun dengan PHP Native untuk mendemonstrasikan pemahaman fundamental dalam pengembangan web dengan PHP tanpa framework.

## 🎯 Tujuan Proyek

Proyek ini dibuat untuk:
- Menunjukkan pemahaman mendalam tentang PHP Native
- Mengimplementasikan struktur aplikasi modern yang terinspirasi dari Laravel (facades, routing, dll)
- Membuktikan kemampuan dalam membangun aplikasi enterprise-level dengan teknologi dasar

## 🚀 Tech Stack

- **Frontend**: HTML5, CSS3, Bootstrap 4, SB Admin 2
- **Backend**: PHP Native
- **Database**: MySQL
- **Caching**: Redis
- **Containerization**: Docker
- **CI/CD**: GitHub Actions

## 👥 Role & Akses

Aplikasi ini memiliki 3 level role dengan hierarki akses:

### 1. Candidate (Level Terendah)
- Melihat lowongan pekerjaan
- Melamar pekerjaan
- Mengelola data pribadi

### 2. Employee (Level Menengah)
Mencakup posisi CEO, CTO, dan posisi karyawan lainnya:
- Melakukan absensi (scan QR Code)
- Melihat data kehadiran
- Mengakses informasi perusahaan
- Melihat data terkait proyek dan departemen

### 3. HR (Level Tertinggi)
Akses penuh ke seluruh sistem:
- Manajemen kandidat dan rekrutmen
- Dashboard perusahaan
- Manajemen karyawan dan kontrak
- Pengelolaan payroll dan transaksi keuangan
- Pengaturan absensi dan aturan lembur
- Manajemen proyek dan penugasan
- Pengelolaan cuti karyawan
- Struktur departemen dan role

## ✨ Fitur Utama

### Modul User (Umum)
- **Authentication**: Login & Register
- **Profile Management**: Pengaturan data pribadi

### Modul Candidate
- **Job Listings**: Melihat daftar lowongan pekerjaan
- **Job Application**: Melamar pekerjaan
- **Data Management**: Mengatur data pribadi dan riwayat lamaran

### Modul HR (Admin)
- **Recruitment Management**
    - Kelola kandidat dan detail kandidat
    - Manajemen lowongan pekerjaan (recruitments)
    - Proses seleksi dan hiring

- **Employee Management**
    - Data karyawan lengkap
    - Detail karyawan dan kontrak kerja
    - Employee roles dan departemen
    - Penugasan proyek (project assignments)

- **Attendance & Time Management**
    - Kelola kehadiran karyawan
    - Aturan absensi (attendance rules)
    - Manajemen lembur (overtime)
    - Persetujuan cuti (leave requests)

- **Payroll & Finance**
    - Penggajian karyawan (payrolls)
    - Transaksi keuangan perusahaan (financial transactions)

- **Company Dashboard**
    - Overview statistik perusahaan
    - Monitoring proyek karyawan
    - Analitik dan laporan

### Modul Employee
- **Attendance System**
    - Absensi dengan QR Code scanning
    - Riwayat kehadiran
    - Status lembur

- **Information Access**
    - Data proyek yang ditugaskan
    - Informasi departemen
    - Slip gaji dan kontrak
    - Data perusahaan

## 🏗️ Arsitektur

Proyek ini mengadopsi struktur folder dan pola desain yang terinspirasi dari Laravel, termasuk:
- **Routing System**: Sistem routing yang terorganisir
- **Facades Pattern**: Abstraksi untuk akses ke komponen sistem
- **MVC Pattern**: Pemisahan logic, view, dan data
- **Service Layer**: Business logic yang terpisah dari controller
- **Repository Layer**: Sintaks sql terpisah

## 🐳 Docker Setup

```bash
# Clone repository
git clone https://github.com/ilhamrhmtkbr/aplikasi-talent-hub.git

# Masuk ke direktori project
cd aplikasi-talent-hub

# Jalankan Docker containers
sudo docker compose -f docker/php/docker-compose.yaml up -d

# Install dependencies (jika ada)
sudo docker compose -f docker/php/docker-compose.yaml exec talent-hub-app composer install

# Setup database
sudo docker compose -f docker/php/docker-compose.yaml exec -i talent-hub-mysql mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS talent_hub;"
sudo docker compose -f docker/php/docker-compose.yaml exec -i talent-hub-mysql mysql -u root -proot talent_hub < docker/mysql/talent_hub.sql


# Akses aplikasi
http://localhost:8000
```

## 🧪 Testing

```bash
# Run tests
./vendor/bin/phpunit
```

## 🚀 CI/CD

Proyek ini menggunakan GitHub Actions untuk continuous integration dan deployment. Setiap push ke branch `master` akan memicu:
- Integration Testing

## 🤝 Contributing

Kontribusi selalu diterima! Untuk kontribusi besar, silakan buka issue terlebih dahulu untuk mendiskusikan perubahan yang ingin dilakukan.

## 👨‍💻 Author

**[Nama Anda]**
- GitHub: [@ilhamrhmtkbr](https://github.com/ilhamrhmtkbr)
- LinkedIn: [Ilham Rahmat Akbar](https://linkedin.com/in/ilhamrhmtkbr)

## 📧 Contact

Untuk pertanyaan atau saran, silakan hubungi: ilhamrhmtkbr@gmail.com

## 🙏 Acknowledgments

- [SB Admin 2](https://startbootstrap.com/theme/sb-admin-2) - Admin template
- [Bootstrap 4](https://getbootstrap.com/) - CSS Framework
- Terinspirasi dari [Laravel Framework](https://laravel.com/) untuk struktur dan pola desain

---