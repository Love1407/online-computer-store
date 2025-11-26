
<?php require_once __DIR__ . '/includes/header.php'; ?>


<section class="slider">

    <button class="arrow left" onclick="prevSlide()">❮</button>

    <div class="slides">

        <div class="slide active">
            <div class="slide-content">
                <img src="images/slide1.png" class="slide-img">
                <div class="text-box">
                    <h1>Easy Your Life<br>With Smart Device</h1>
                    <p class="price-text">Only <span>$24.00</span></p>
                    <button class="shop-btn">Shop All Devices</button>
                </div>
            </div>
        </div>

        <div class="slide">
            <div class="slide-content">
                <img src="images/slide2.png" class="slide-img">
                <div class="text-box">
                    <h1>Upgrade Your Gadgets</h1>
                    <p class="price-text">Starting from <span>$49.00</span></p>
                    <button class="shop-btn">Shop Now</button>
                </div>
            </div>
        </div>

        <div class="slide">
            <div class="slide-content">
                <img src="images/slide3.png" class="slide-img">
                <div class="text-box">
                    <h1>Latest Smart Accessories</h1>
                    <p class="price-text">Grab at <span>$19.00</span></p>
                    <button class="shop-btn">Explore</button>
                </div>
            </div>
        </div>

    </div>

    <button class="arrow right" onclick="nextSlide()">❯</button>
</section>

<section class="category-box">
    <div class="cat-card">
        <h2>Speaker</h2>
        <p>From $69.00</p>
        <button class="blue-btn">Shop Now</button>
    </div>

    <div class="cat-card">
        <h2>Smartphone</h2>
        <p>From $95.00</p>
        <button class="blue-btn">Shop Now</button>
    </div>
</section>

<section class="products">
    <h2>New Products</h2>
    <p class="sub-text">Choose from the best trending items</p>

    <div class="product-grid">

        <?php  
        $products = [
            ["img"=>"p1.png", "tag"=>"NEW", "title"=>"Modern Smart Phone"],
            ["img"=>"p2.png", "tag"=>"SALE", "title"=>"Bluetooth Headphone"],
            ["img"=>"p3.png", "tag"=>"NEW", "title"=>"Smart Music Box"],
            ["img"=>"p4.png", "tag"=>"30%", "title"=>"Air Pods Pro"]
        ];

        foreach($products as $p){
            echo "
                <div class='product'>
                    <span class='badge'>{$p['tag']}</span>
                    <img src='images/{$p['img']}' alt=''>
                    <h4>{$p['title']}</h4>
                    <p class='price'>$38.50</p>
                </div>
            ";
        }
        ?>

    </div>
</section>


<section class="featured">
    <h2>Featured Offers</h2>
    <p class="sub-text">There are many variations of Lorem Ipsum available</p>

    <div class="featured-grid">

        <div class="offer-card">
            <div class="offer-left">
                <img src="images/watch.png" alt="">
            </div>
            <div class="offer-right">
                <span class="end-tag">End In: 0 : 00 : 00</span>
                <h3>Ladies Smart Watch</h3>
                <p class="old-price">$48.50 <span class="new-price">$38.50</span></p>

                <ul>
                    <li><b>Predecessor:</b> None</li>
                    <li><b>Support Type:</b> Neutral</li>
                    <li><b>Cushioning:</b> High Energizing</li>
                    <li><b>Total Weight:</b> 300gm</li>
                </ul>
            </div>
        </div>

        <div class="offer-card">
            <div class="offer-left">
                <img src="images/phone.png" alt="">
            </div>
            <div class="offer-right">
                <span class="end-tag">End In: 0 : 00 : 00</span>
                <h3>Smart Phone</h3>
                <p class="old-price">$48.50 <span class="new-price">$38.50</span></p>

                <ul>
                    <li><b>Predecessor:</b> None</li>
                    <li><b>Support Type:</b> Neutral</li>
                    <li><b>Cushioning:</b> High Energizing</li>
                    <li><b>Total Weight:</b> 300gm</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="red-banner">
    <div class="red-content">
        <h2>Smart Fashion</h2>
        <h1>With Smart Devices</h1>
        <button class="white-btn">Shop All Devices</button>
    </div>

    <img class="red-left-img" src="images/headphones.png">
    <img class="red-right-img" src="images/phones.png">
</section>

<section class="services">
    <div class="service-box">
        <img src="images/icon1.png" alt="">
        <h3>Free Shipping</h3>
        <p>Capped at $39 per order</p>
    </div>

    <div class="service-box">
        <img src="images/icon2.png" alt="">
        <h3>Card Payments</h3>
        <p>12 Months Installments</p>
    </div>

    <div class="service-box">
        <img src="images/icon3.png" alt="">
        <h3>Easy Returns</h3>
        <p>Shop With Confidence</p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


<script>
let currentSlide = 0;
const slides = document.querySelectorAll(".slide");

function showSlide(index) {
    slides.forEach(slide => slide.classList.remove("active"));
    slides[index].classList.add("active");
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
}

setInterval(nextSlide, 4000);
</script>