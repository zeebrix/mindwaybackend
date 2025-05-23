<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindway EAP - Home</title>
    <style>
        /* General Body Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Navbar Styles */
        header {
            background-color: #333;
            color: #fff;
            padding: 20px 10px;
            text-align: center;
        }
        header h1 {
            margin: 0;
            font-size: 2rem;
        }

        /* Navbar Links */
        nav {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 5px 10px;
            font-size: 1rem;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #4e73df;
        }

        /* Hero Section */
        .hero {
            height: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: rgb(104, 142, 220);
            text-align: center;
            padding: 20px;
        }
        .hero h2 {
            font-size: 2.5rem;
            margin: 0;
        }
        .hero p {
            font-size: 1.1rem;
            margin-top: 10px;
        }

        /* Footer Styles */
        footer {
            background-color: #333;
            color: white;
            padding: 20px 10px;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto;
        }

        footer p {
            margin: 0;
        }

        footer .footer-links {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        footer .footer-links a {
            color: white;
            text-decoration: none;
            margin: 5px 10px;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        footer .footer-links a:hover {
            color: #4e73df;
        }

        /* Media Queries for Mobile Devices */
        @media (max-width: 600px) {
            header h1 {
                font-size: 1.5rem;
            }

            .hero h2 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            nav a, footer .footer-links a {
                font-size: 0.9rem;
                margin: 5px;
            }

            footer {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <header>
        <h1>Mindway EAP</h1>
        <nav>
            <a href="/privacy-policy" target="_blank">Privacy Policy</a>
            <a href="/terms-of-use" target="_blank">Terms of Use</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <div class="hero">
        <div>
            <h2>Welcome to Mindway EAP</h2>
            <p>Your partner in mental wellbeing</p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Mindway EAP. All Rights Reserved.</p>
        <div class="footer-links">
            <a href="/privacy-policy" target="_blank">Privacy Policy</a>
            <a href="/terms-of-use" target="_blank">Terms of Use</a>
        </div>
    </footer>

</body>
</html>
