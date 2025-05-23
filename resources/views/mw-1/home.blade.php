<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Wellbeing & EAP Services</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      color: #333;
      line-height: 1.6;
    }
    .hero {
      background: linear-gradient(135deg, #002f5f, #005f9e);
      color: #fff;
      text-align: center;
      padding: 100px 20px;
    }
    .hero h1 {
      font-size: 48px;
      margin-bottom: 20px;
    }
    .hero p {
      font-size: 20px;
      margin-bottom: 30px;
    }
    .hero button, .cta button {
      background: #fff;
      color: #005f9e;
      border: none;
      padding: 14px 28px;
      font-size: 18px;
      cursor: pointer;
      border-radius: 5px;
    }
    .services {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      padding: 80px 20px;
      max-width: 1200px;
      margin: auto;
    }
    .service-card {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      text-align: center;
    }
    .service-card h3 {
      font-size: 22px;
      margin-bottom: 15px;
      color: #005f9e;
    }
    .service-card p {
      font-size: 16px;
      color: #555;
    }
    .about {
      background: #f9f9f9;
      padding: 80px 20px;
      text-align: center;
    }
    .about h2 {
      font-size: 32px;
      color: #005f9e;
      margin-bottom: 20px;
    }
    .about p {
      font-size: 18px;
      color: #555;
      max-width: 800px;
      margin: 0 auto;
    }
    .testimonials {
      padding: 80px 20px;
      text-align: center;
    }
    .testimonials h2 {
      font-size: 32px;
      color: #005f9e;
      margin-bottom: 30px;
    }
    .testimonial-card {
      background: #fff;
      border-left: 4px solid #005f9e;
      padding: 20px;
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    .testimonial-card p {
      font-size: 18px;
      color: #333;
      margin-bottom: 10px;
    }
    .testimonial-card strong {
      display: block;
      font-size: 16px;
      color: #555;
      margin-top: 5px;
    }
    .cta {
      background: #005f9e;
      color: #fff;
      text-align: center;
      padding: 60px 20px;
    }
    .cta h2 {
      font-size: 30px;
      margin-bottom: 20px;
    }
    footer {
      background: #f3f3f3;
      text-align: center;
      padding: 30px 20px;
      font-size: 14px;
      color: #666;
    }
    footer nav {
      margin-bottom: 15px;
    }
    footer nav a {
      color: #005f9e;
      text-decoration: none;
      margin: 0 10px;
      font-size: 14px;
    }
    footer nav a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <section class="hero">
    <div class="container">
      <h1>Supporting Mental Health & Wellbeing</h1>
      <p>Comprehensive Employee Assistance & Psychological Services</p>
      <button onclick="contactUs()">Contact Us</button>
    </div>
  </section>

  <section class="services">
    <div class="service-card">
      <h3>Employee Assistance Program</h3>
      <p>Confidential support for your team’s mental health and wellbeing.</p>
    </div>
    <div class="service-card">
      <h3>Manager Assistance</h3>
      <p>Guidance for leaders supporting their staff in challenging situations.</p>
    </div>
    <div class="service-card">
      <h3>Critical Incident Support</h3>
      <p>Immediate response to traumatic workplace events.</p>
    </div>
  </section>

  <section class="about">
    <div class="container">
      <h2>Why Choose Us?</h2>
      <p>We provide evidence-based psychological services delivered by a highly qualified and experienced team. Our programs are tailored to suit your organisation’s needs. We value confidentiality, care, and commitment to mental health and wellbeing in the workplace.</p>
    </div>
  </section>

  <section class="testimonials">
    <div class="container">
      <h2>What Our Clients Say</h2>
      <div class="testimonial-card">
        <p>"Incredible support services — highly professional and caring team."</p>
        <strong>- John D.</strong>
      </div>
    </div>
  </section>

  <section class="cta">
    <div class="container">
      <h2>Ready to Support Your Team?</h2>
      <button onclick="contactUs()">Get in Touch</button>
    </div>
  </section>

  <footer>
    <nav>
      <a href="/privacy-policy" target="_blank">Privacy Policy</a>
      <a href="/terms-of-use" target="_blank">Terms of Use</a>
    </nav>
    <p>&copy; 2025 Wellbeing Services. All rights reserved.</p>
  </footer>

  <script>
    function contactUs() {
      alert("Contact form or page coming soon!");
    }
  </script>

</body>
</html>
