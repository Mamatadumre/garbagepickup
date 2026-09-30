<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Garbage Pickup System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5fdf7;
            color: #333;
        }

        /* Navigation Bar */
        .navbar {
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 5%;
            box-shadow: 0 2px 8px #ddd;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 23px;
            font-weight: bold;
            color: green;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }

        .nav-links a:hover {
            color: green;
        }

        .login-btn {
            background: green;
            color: white !important;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .admin-btn {
            background: #555;
            color: white !important;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .worker-btn {
            background: #176b2c;
            color: white !important;
            padding: 10px 15px;
            border-radius: 5px;
        }

        /* Hero Section */
        .hero {
            min-height: 500px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 70px 8%;
            background: linear-gradient(135deg, #e8f5e9, #ffffff);
        }

        .hero-text {
            width: 55%;
        }

        .hero-text h1 {
            font-size: 45px;
            color: #176b2c;
            margin-bottom: 20px;
        }

        .hero-text p {
            font-size: 18px;
            line-height: 1.7;
            margin-bottom: 30px;
            color: #555;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 13px 25px;
            border-radius: 6px;
            font-weight: bold;
            margin-right: 10px;
        }

        .primary-btn {
            background: green;
            color: white;
        }

        .secondary-btn {
            border: 2px solid green;
            color: green;
            background: white;
        }

        .hero-image {
            width: 35%;
            text-align: center;
            font-size: 140px;
        }

        /* Common Section */
        .section {
            padding: 65px 8%;
            text-align: center;
        }

        .section h2 {
            color: green;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .section-intro {
            max-width: 750px;
            margin: auto;
            line-height: 1.7;
            color: #555;
        }

        /* Services */
        .services {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
            margin-top: 35px;
        }

        .service-card {
            background: white;
            width: 250px;
            padding: 30px 20px;
            border-radius: 10px;
            box-shadow: 0 3px 12px #ddd;
        }

        .service-card .icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        .service-card h3 {
            color: green;
            margin-bottom: 10px;
        }

        .service-card p {
            line-height: 1.6;
            color: #666;
        }

        /* Contact */
        .contact-box {
            background: #176b2c;
            color: white;
            padding: 45px 20px;
            border-radius: 10px;
        }

        .contact-box h2 {
            color: white;
        }

        .contact-box p {
            margin: 12px 0 20px;
            font-size: 17px;
        }

        .contact-btn {
            background: white;
            color: green;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        /* Footer */
        footer {
            background: #222;
            color: white;
            text-align: center;
            padding: 25px;
        }

        footer p {
            margin: 5px;
        }

        /* Mobile Responsive */
        @media (max-width: 900px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 12px;
            }

            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-text {
                width: 100%;
            }

            .hero-text h1 {
                font-size: 34px;
            }

            .hero-image {
                width: 100%;
                margin-top: 30px;
            }
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar">

        <div class="logo">
            ♻ Garbage Pickup System
        </div>

        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>

            <!-- User Login -->
            <li>
                <a href="user/login.php" class="login-btn">
                    User Login
                </a>
            </li>

            <!-- Admin Login -->
            <li>
                <a href="admin/admin_login.php" class="admin-btn">
                    Admin Login
                </a>
            </li>

            <!-- Worker Login -->
            <li>
                <a href="worker/worker_login.php" class="worker-btn">
                    Worker Login
                </a>
            </li>
        </ul>

    </nav>


    <!-- Home Section -->
    <section class="hero" id="home">

        <div class="hero-text">

            <h1>Keep Your Environment Clean and Green</h1>

            <p>
                Welcome to Garbage Pickup System, a simple and efficient platform
                for requesting garbage collection services. Schedule your pickup,
                manage your requests, and help create a cleaner environment.
            </p>

            <a href="user/register.php" class="btn primary-btn">
                Get Started
            </a>

            <a href="#services" class="btn secondary-btn">
                Explore Services
            </a>

        </div>

        <div class="hero-image">
            ♻️
        </div>

    </section>


    <!-- About Us Section -->
    <section class="section" id="about">

        <h2>About Us</h2>

        <p class="section-intro">
            Garbage Pickup System is an online platform designed to make waste
            collection easier and more organized. Users can submit pickup requests,
            administrators can manage requests, and workers can complete garbage
            collection services efficiently.
        </p>

    </section>


    <!-- Services Section -->
    <section class="section" id="services">

        <h2>Our Services</h2>

        <p class="section-intro">
            Our system provides simple and convenient garbage collection services
            for users and helps manage waste collection activities effectively.
        </p>

        <div class="services">

            <div class="service-card">
                <div class="icon">🗑️</div>
                <h3>Garbage Pickup</h3>
                <p>
                    Submit a request for garbage collection from your location.
                </p>
            </div>

            <div class="service-card">
                <div class="icon">📅</div>
                <h3>Schedule Pickup</h3>
                <p>
                    Select a suitable date for your garbage pickup request.
                </p>
            </div>

            <div class="service-card">
                <div class="icon">📊</div>
                <h3>Track Requests</h3>
                <p>
                    View the status of your garbage pickup requests easily.
                </p>
            </div>

        </div>

    </section>


    <!-- Contact Section -->
    <section class="section" id="contact">

        <div class="contact-box">

            <h2>Contact Us</h2>

            <p>
                Have any questions about garbage pickup services?
                We are here to help create a cleaner environment.
            </p>

            <a href="mailto:garbagepickup@gmail.com" class="contact-btn">
                Contact Us
            </a>

        </div>

    </section>


    <!-- Footer -->
    <footer>

        <p>© 2026 Garbage Pickup System. All Rights Reserved.</p>

        <p>Clean Environment | Healthy Life</p>

    </footer>

</body>
</html>