<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentHub - Smart HR Management System</title>
    <link rel="stylesheet" href="/assets/css/fontawesome-all.min.css"/>
    <link rel="stylesheet" href="/assets/css/sb-admin-2.min.css"/>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <style>
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255,255,255,0.95);
            margin-bottom: 2rem;
        }

        .hero-image {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));
            animation: float 3s ease-in-out infinite;
            border-radius: 2rem;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .btn-hero {
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .features-section {
            padding: 5rem 0;
            background: #f8f9fc;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s;
            border: 1px solid #e3e6f0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon i {
            font-size: 1.8rem;
            color: white;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #5a5c69;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #858796;
            margin-bottom: 3rem;
        }

        .tech-stack {
            padding: 4rem 0;
            background: white;
        }

        .tech-badge {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            background: #f8f9fc;
            border: 2px solid #e3e6f0;
            border-radius: 50px;
            font-weight: 600;
            color: #5a5c69;
            transition: all 0.3s;
        }

        .tech-badge:hover {
            background: #4e73df;
            color: white;
            border-color: #4e73df;
            transform: scale(1.05);
        }

        .cta-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        }

        .role-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }

        .role-card:hover {
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
        }

        .role-card h4 {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .role-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .role-card li {
            padding: 0.5rem 0;
            color: rgba(255,255,255,0.9);
        }

        .role-card li i {
            color: #1cc88a;
            margin-right: 0.5rem;
        }

        footer {
            background: #2c2f3e;
            color: white;
            padding: 2rem 0;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title">TalentHub</h1>
                <p class="hero-subtitle">Smart HR Management System untuk Mengelola Talenta, Karyawan & Rekrutmen dengan Mudah</p>
                <div class="mt-4">
                    <a href="/user/login" class="btn btn-light btn-hero mr-3">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="/user/register" class="btn btn-success btn-hero">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <img class="hero-image" src="/assets/img/talenthub.png" alt="TalentHub">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Fitur Lengkap untuk Semua Role</h2>
            <p class="section-subtitle">Sistem yang dirancang untuk memenuhi kebutuhan HR, Kandidat, dan Karyawan</p>
        </div>

        <div class="row">
            <!-- HR Features -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3">HR Management</h4>
                    <p class="text-muted mb-3">Kontrol penuh untuk mengelola seluruh aspek HR perusahaan</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success mr-2"></i>Monitoring Kandidat</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Employee Management</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Project & Department</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Financial Tracking</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Recruitment Process</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Attendance System</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Contract Management</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Leave Request</li>
                    </ul>
                </div>
            </div>

            <!-- Candidate Features -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3">Candidate Portal</h4>
                    <p class="text-muted mb-3">Portal khusus untuk kandidat yang melamar pekerjaan</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success mr-2"></i>Browse Jobs</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Easy Application</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Track Application Status</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Application History</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Profile Management</li>
                    </ul>
                </div>
            </div>

            <!-- Employee Features -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3">Employee Dashboard</h4>
                    <p class="text-muted mb-3">Akses untuk karyawan memantau data pribadi mereka</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success mr-2"></i>Attendance Tracking</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Personal Data</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Leave Balance</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Project Assignment</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Self-Service Portal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tech Stack Section -->
<section class="tech-stack">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Tech Stack</h2>
            <p class="section-subtitle">Dibangun dengan teknologi modern dan reliable</p>
        </div>
        <div class="text-center">
            <span class="tech-badge"><i class="fab fa-php mr-2"></i>PHP Native</span>
            <span class="tech-badge"><i class="fas fa-database mr-2"></i>MySQL</span>
            <span class="tech-badge"><i class="fab fa-bootstrap mr-2"></i>Bootstrap (SB Admin 2)</span>
            <span class="tech-badge"><i class="fab fa-docker mr-2"></i>Docker</span>
            <span class="tech-badge"><i class="fab fa-github mr-2"></i>GitHub Actions CI/CD</span>
            <span class="tech-badge"><i class="fab fa-js mr-2"></i>JavaScript/jQuery</span>
        </div>
        <div class="text-center mt-4">
            <p class="text-muted">
                <i class="fas fa-info-circle mr-2"></i>
                Aplikasi ini dikembangkan tanpa framework untuk menunjukkan pemahaman fundamental dalam web development
            </p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title text-white">Mulai Kelola Talenta Anda Sekarang</h2>
            <p class="section-subtitle text-white-50">Daftar sekarang dan rasakan kemudahan mengelola HR dengan TalentHub</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="role-card text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-shield fa-3x"></i>
                            </div>
                            <h4>HR Admin</h4>
                            <p class="mb-0">Akses penuh ke semua fitur management</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="role-card text-center">
                            <div class="mb-3">
                                <i class="fas fa-briefcase fa-3x"></i>
                            </div>
                            <h4>Kandidat</h4>
                            <p class="mb-0">Lamar pekerjaan dan track status aplikasi</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="role-card text-center">
                            <div class="mb-3">
                                <i class="fas fa-id-card fa-3x"></i>
                            </div>
                            <h4>Karyawan</h4>
                            <p class="mb-0">Monitoring data personal dan attendance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="/user/register" class="btn btn-light btn-hero btn-lg">
                <i class="fas fa-rocket mr-2"></i>Get Started Now
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-left">
                <h5 class="font-weight-bold mb-3">TalentHub</h5>
                <p class="text-white-50">Smart HR Management System untuk perusahaan modern</p>
            </div>
            <div class="col-md-6 text-center text-md-right">
                <p class="text-white-50 mb-2">Portfolio Project</p>
                <p class="text-white-50 mb-0">&copy; 2025 ilhamrhmtkbr. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap core JavaScript-->
<script src="/assets/js/sb-admin-2/jquery.min.js"></script>
<script src="/assets/js/sb-admin-2/bootstrap.bundle.min.js"></script>
<script src="/assets/js/sb-admin-2/jquery.easing.min.js"></script>
<script src="/assets/js/sb-admin-2/sb-admin-2.min.js"></script>

<script>
    // Smooth scroll untuk anchor links
    $(document).ready(function() {
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            var target = this.hash;
            var $target = $(target);
            $('html, body').animate({
                'scrollTop': $target.offset().top - 70
            }, 1000, 'swing');
        });

        // Animasi fade in saat scroll
        $(window).scroll(function() {
            $('.feature-card').each(function() {
                var imagePos = $(this).offset().top;
                var topOfWindow = $(window).scrollTop();
                if (imagePos < topOfWindow + 700) {
                    $(this).addClass('fadeIn');
                }
            });
        });
    });
</script>
</body>

</html>