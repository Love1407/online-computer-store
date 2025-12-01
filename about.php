<?php
require_once __DIR__ . '/includes/header.php';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        background: #f8f9fa;
    }

    /* Hero Section */
    .about-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        padding: 100px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="2" height="2" fill="white" opacity="0.1"/></svg>') repeat;
        opacity: 0.3;
    }

    .about-hero-content {
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .about-hero h1 {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 20px;
        animation: fadeInUp 0.8s ease;
    }

    .about-hero p {
        font-size: 20px;
        opacity: 0.95;
        animation: fadeInUp 0.8s ease 0.2s both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Container */
    .about-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 80px 20px;
    }

    /* Story Section */
    .story-section {
        background: white;
        border-radius: 16px;
        padding: 60px;
        margin-bottom: 60px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .story-section h2 {
        font-size: 36px;
        color: #1e3a8a;
        margin-bottom: 30px;
        text-align: center;
    }

    .story-section p {
        font-size: 18px;
        line-height: 1.8;
        color: #4b5563;
        margin-bottom: 20px;
        text-align: justify;
    }

    /* Stats Section */
    .stats-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-number {
        font-size: 48px;
        font-weight: 700;
        color: #3b82f6;
        margin-bottom: 10px;
    }

    .stat-label {
        font-size: 18px;
        color: #6b7280;
        font-weight: 500;
    }

    /* Values Section */
    .values-section {
        margin-bottom: 60px;
    }

    .values-section h2 {
        font-size: 36px;
        color: #1e3a8a;
        margin-bottom: 50px;
        text-align: center;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
    }

    .value-card {
        background: white;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-5px);
    }

    .value-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 20px;
        color: white;
    }

    .value-card h3 {
        font-size: 24px;
        color: #1f2937;
        margin-bottom: 15px;
    }

    .value-card p {
        font-size: 16px;
        color: #6b7280;
        line-height: 1.7;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 16px;
        padding: 60px 40px;
        text-align: center;
        color: white;
    }

    .cta-section h2 {
        font-size: 36px;
        margin-bottom: 20px;
    }

    .cta-section p {
        font-size: 18px;
        margin-bottom: 30px;
        opacity: 0.95;
    }

    .cta-button {
        display: inline-block;
        background: white;
        color: #1e3a8a;
        padding: 15px 40px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 18px;
        font-weight: 600;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .about-hero {
            padding: 60px 20px;
        }

        .about-hero h1 {
            font-size: 32px;
        }

        .about-hero p {
            font-size: 16px;
        }

        .story-section {
            padding: 40px 30px;
        }

        .story-section h2,
        .values-section h2,
        .cta-section h2 {
            font-size: 28px;
        }

        .values-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Hero Section -->
<div class="about-hero">
    <div class="about-hero-content">
        <h1>About Our Store</h1>
        <p>Your trusted partner for high-quality computers and technology solutions since day one</p>
    </div>
</div>

<!-- Main Content -->
<div class="about-container">
    
    <!-- Our Story -->
    <div class="story-section">
        <h2>Our Story</h2>
        <p>
            Founded with a passion for technology and a commitment to excellence, our online computer store has been serving tech enthusiasts, professionals, and businesses with top-quality computing solutions. We understand that in today's digital age, having the right technology is crucial for success.
        </p>
        <p>
            What started as a small operation has grown into a trusted destination for computer hardware, software, and accessories. We pride ourselves on offering not just products, but complete solutions that empower our customers to achieve their goals, whether it's gaming, content creation, business productivity, or everyday computing.
        </p>
        <p>
            Our team consists of tech experts who are passionate about what they do. We carefully curate every product in our catalog, ensuring that our customers receive only the best quality at competitive prices. Customer satisfaction isn't just our goal—it's our promise.
        </p>
    </div>

    <!-- Stats -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-number">10K+</div>
            <div class="stat-label">Happy Customers</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">5K+</div>
            <div class="stat-label">Products Available</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">99%</div>
            <div class="stat-label">Satisfaction Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Customer Support</div>
        </div>
    </div>

    <!-- Our Values -->
    <div class="values-section">
        <h2>Our Core Values</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">⚡</div>
                <h3>Quality First</h3>
                <p>We source products only from authorized manufacturers and trusted brands, ensuring authenticity and reliability in every purchase.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">💡</div>
                <h3>Innovation</h3>
                <p>We stay ahead of technology trends to bring you the latest and most innovative computing solutions available in the market.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3>Customer Focus</h3>
                <p>Your satisfaction is our priority. We provide exceptional service, expert advice, and support throughout your shopping journey.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🔒</div>
                <h3>Trust & Security</h3>
                <p>We implement robust security measures to protect your data and ensure safe, secure transactions every time you shop with us.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🚚</div>
                <h3>Fast Delivery</h3>
                <p>We understand urgency. Our efficient logistics ensure your products reach you quickly and in perfect condition.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">💰</div>
                <h3>Fair Pricing</h3>
                <p>We believe great technology should be accessible. We offer competitive prices without compromising on quality or service.</p>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="cta-section">
        <h2>Ready to Upgrade Your Tech?</h2>
        <p>Explore our extensive collection of computers, components, and accessories</p>
        <a href="exploreproducts.php" class="cta-button">Shop Now</a>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>