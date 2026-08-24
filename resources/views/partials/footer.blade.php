<div id="our-client-all-page">
    OUR &nbsp; <span style="color: red">CLIENTS</span>
</div>
<div class="tickerwrapper">
    <ul class='list'>
      <li class='listitem'><span>HAR</span></li>
      <li class='listitem'><span>SRC</span></li>
      <li class='listitem'><span>QUEST DIAGNOSTICS</span></li>
      <li class='listitem'><span>CLICK BANK</span></li>
      <li class='listitem'><span>LAFARGE CEMENT</span></li>
      <li class='listitem'><span>LnT</span></li>
      <li class='listitem'><span>ROSHAN AKONTAX</span></li>
      <li class='listitem'><span>SAP</span></li>
    </ul>
</div>


<div class="testimonial-page">
    <!-- Text Testimonial Section -->
    <section class="text-testimonial">
        <div class="testimonial-container">
            <h1 id="testimonial-h1">What Our Customers Are Saying</h1>
            <div class="testimonial-content-wrapper">
                <!-- Testimonial 1 -->
                <div class="testimonial-content">
                    <div class="image-container-new">
                        <img src="https://images.pexels.com/photos/12185573/pexels-photo-12185573.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Customer Image">
                    </div>
                    <div class="testimonial-text">
                        <h3 id="testimonial-h3">Help us improve our productivity</h3>
                        <p id="testimonial-p">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                        <p class="customer-name">Samantha William</p>
                        <p class="customer-position">Senior Designer at Design Studio</p>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-content">
                    <div class="image-container-new">
                        <img src="https://images.pexels.com/photos/936043/pexels-photo-936043.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Customer Image">
                    </div>
                    <div class="testimonial-text">
                        <h3 id="testimonial-h3">Great customer support</h3>
                        <p id="testimonial-p">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        <p class="customer-name">John Doe</p>
                        <p class="customer-position">CEO at Tech Corp</p>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-content">
                    <div class="image-container-new">
                        <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Customer Image">
                    </div>
                    <div class="testimonial-text">
                        <h3 id="testimonial-h3">Highly recommended</h3>
                        <p id="testimonial-p">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                        <p class="customer-name">Emily Johnson</p>
                        <p class="customer-position">Marketing Director at Creative Studio</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="only-laptop-screen-no-mobile">
    <div class="about-container">
        <div id="heading-about-container-about">
            <h1><span style="color: red;">BRANDIXZEN</span> PROFILE</h1>
        </div>
        <div class="header-text">
            <p><span style="color: red;">BRANDIXZEN</span>, a distinguished digital marketing venture of & Faddal
                Industries
                Limited, boasts an illustrious history in the SEO domain, tracing back to the
                inception of search engines and the early days of web development. With over
                23 years of unparalleled expertise in SEO and digital marketing, we stand as
                the industry's most trusted and experienced digital marketing authority.
                <br>
                <br>
                Our commitment to excellence is reflected in our continuous adaptation to technological advancements and
                our unwavering dedication to providing optimal solutions for our partners.
            </p>
        </div>
        <div class="image-about-container">
            <div class="image-row">

                <img id="googleeed" src="../picture/1_-_ISO_9000-removebg-preview.png" alt="ISO">


                <img id="google" src="../picture/2_-_CMMI-removebg-preview.png" alt="CMMI">

            </div>
            <div class="image-row">

                <img id="Google" src="../picture/3 - Google Partner.png" alt="Google">


                <img id="meta-pni" src="../picture/4 - Meta.png" alt="Meta">


                <img id="amazon" src="../picture/WhatsApp Image 2024-07-24 at 12.04.16_36e4b743.jpg" alt="WhatsApp">

            </div>
            <div class="image-row">

                <img id="google-mg" src="../picture/6 - Microsoft.png" alt="Microsoft">

                <img id="linkedin" src="../picture/5 - linkedin.jpg" alt="LinkedIn">


                <img id="HubSpot" src="../picture/7_-_HubSpot-removebg-preview.png" alt="HubSpot">

                <img id="HootSuite" src="../picture/8_-_HootSuite-removebg-preview.png" alt="Hootsuite">

                <img id="MOZ" src="../picture/9_-_MOZ-removebg-preview.png" alt="Moz">

            </div>

        </div>
        <div id="container-readmore-all-page">
            <button id="read-more-new"><a href="/about-us/brandixzen-company">About BRANDIXZEN</a></button>
        </div>
    </div>
</div>
<div class="container-old">
    <div class="new-old-left-section">
        <img src="../picture/ofspace-llc-iprE_0Fkg5g-unsplash.jpg" alt="Google" class="new-old-background-image">
            <div class="new-conect">
                <p id="ele-new-form" style="color: white;">Ready to Elevate Your Brand? <br> Let's Make It Happen with<span style="color: red;"> BrandIxZen!</span></p>
        </div>
    </div>
    <div class="full-screen">
        <div class="form-container">
            <x-lead-form variant="footer" :with-message="true" />
        </div>
    </div>

</div>
<div id="seo-icon-container" data-newsletter-section>
    <p id="subscribe-title">Subscribe to our Newsletter</p>
    <div class="unique-container">
        <div class="content-box newsletter-content-box">
            <form class="email-subscription newsletter-subscription-form" data-newsletter-form method="POST" action="{{ route('newsletter.subscribe') }}">
                @csrf
                <div class="newsletter-input-row">
                    <input class="email-input" type="email" name="email" placeholder="Enter Email ID" required>
                    <button class="email-button" type="submit">Subscribe</button>
                </div>
                <p class="newsletter-status-message" data-newsletter-message aria-live="polite"></p>
            </form>
        </div>
        
    </div>
    <div class="mobile-only-below-top-right">
        <form id="input-form-below-top-right" class="newsletter-subscription-form" data-newsletter-form method="POST" action="{{ route('newsletter.subscribe') }}">
          @csrf
          <div class="newsletter-input-row">
            <input type="email" name="email" id="input-box-below-top-right" placeholder="Enter Email ID" required>
            <button type="submit" id="submit-button-below-top-right">Subscribe</button>
          </div>
          <p class="newsletter-status-message" data-newsletter-message aria-live="polite"></p>
        </form>
      </div>
      <div class="icon-container">
        <div class="icon-wrapper" id="facebook-wrapper">
            <img src="https://rilstaticasset.akamaized.net/sites/default/files/2022-08/facebook-icon.svg" alt="Facebook">
        </div>
        <div class="icon-wrapper" id="instagram-wrapper">
            <img src="https://rilstaticasset.akamaized.net/sites/default/files/2022-11/insta-icon.svg" alt="Instagram">
        </div>
        <div class="icon-wrapper" id="youtube-wrapper">
            <img src="https://rilstaticasset.akamaized.net/sites/default/files/2022-08/youtube-icon.svg" alt="YouTube">
        </div>
        <div class="icon-wrapper" id="twitter-wrapper">
            <img src="https://rilstaticasset.akamaized.net/sites/default/files/2023-09/twitter-icon%20%281%29.svg" alt="Twitter">
        </div>
        <div class="icon-wrapper" id="linkedin-wrapper">
            <img src="https://rilstaticasset.akamaized.net/sites/default/files/2022-08/linkedin-icon.svg" alt="LinkedIn">
        </div>
        <div class="icon-wrapper" id="pirts-wrapper">
            <img src="../picture/icons8-pinterest-logo-50.png" alt="">
        </div>
        <div class="icon-wrapper" id="whatsapp-wrapper">
            <img src="../picture/chat.png" alt="">
        </div>
    </div>
</div>
    <footer>
    <div class="footer-container">
    
    <!-- <div class="footer-section">
    <h2>Amazon Services</h2>
    <ul>
    <li><a href="#">Amazon Registration</a></li>
    <li><a href="#">Amazon Listing Optimization</a></li>
    <li><a href="#">Amazon A+ Listing</a></li>
    <li><a href="#">Amazon Brand Store Creation</a></li>
    <li><a href="#">Amazon Advertising</a></li>
    <li><a href="#">Amazon Account Management</a></li>
    </ul>
    </div> -->
    <div class="footer-section">
        <h2>About Us</h2>
        <ul>
        <li><a href="/about-us/brandixzen-company">The Company</a></li>
        <li><a href="/about-us/logo">The Logo</a></li>
        <li><a href="/about-us/brandixzen-name">The Name</a></li>
        <li><a href="#">Governing Board</a></li>
        <li><a href="#">Group Businesses</a></li>
        <li><a href="#">In Media</a></li>
        <!-- <li><a href="/about-us/brandixzen-CSR">Our CRS</a></li> -->
        <!-- <li><a href="#">Our Social Responsibilities</a></li> -->
        <li><a href="/about-us/contact-us">Contact Us</a></li>
        </ul>
        </div>
    
    
    <!-- <div class="footer-section">
    <h2>Advertising</h2>
    <ul>
    <li><a href="/advertising/search-engine-marketing">Search Engine Marketing (Google PPC)</a></li>
    <li><a href="/advertising/social-media-marketing">Social Media Marketing</a></li>
    <li><a href="/advertising/meta-advertising">Meta Advertising</a></li>
    <li><a href="/advertising/linkedin-advertising">LinkedIn Advertising</a></li>
    <li><a href="../advertising/">Media Buying</a></li>
    <li><a href="../advertising/">Conversion Rate Optimization</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Branding</h2>
    <ul>
    <li><a href="#">Online Reputation Management</a></li>
    <li><a href="#">Online Reviews Management</a></li>
    <li><a href="#">Influencers Marketing</a></li>
    <li><a href="#">Wikipedia Optimization</a></li>
    <li><a href="#">Digital PR</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Domains, Hosting & Servers</h2>
    <ul>
    <li><a href="#">Domain Registrations</a></li>
    <li><a href="#">Web Hosting</a></li>
    <li><a href="#">Virtual Private Servers</a></li>
    <li><a href="#">Dedicated Servers</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Other Services</h2>
    <ul>
    <li><a href="#">Content Development Services</a></li>
    <li><a href="#">Content Writing</a></li>
    <li><a href="#">Blog Management</a></li>
    <li><a href="#">Social Media Graphics Creations</a></li>
    <li><a href="#">Videos / Reels / Shorts Creations</a></li>
    <li><a href="#">Submission Services</a></li>
    <li><a href="#">Product Directory Submissions</a></li>
    <li><a href="#">Link Building</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Resource Augmentation</h2>
    <ul>
    <li><a href="#">Hire Dedicated SEO Professional</a></li>
    <li><a href="#">Hire Dedicated Digital Marketing Professional</a></li>
    <li><a href="#">Hire Dedicated Web Developer</a></li>
    <li><a href="#">Hire Dedicated Content Writer</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Data Extraction Services</h2>
    <ul>
    <li><a href="#">GMap Data</a></li>
    <li><a href="#">Social Media Data</a></li>
    <li><a href="#">Business Directories Data</a></li>
    <li><a href="#">Web Data</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Premium Service</h2>
    <ul>
    <li><a href="#">Consolidated Tailor-made Digital Marketing Solution</a></li>
    <li><a href="#">SEO Optimized Fully Automated Lead Generating Website</a></li>
    </ul>
    </div> -->
    
    <!-- <div class="footer-section">
    <h2>Database Products</h2>
    <ul>
    <li><a href="#">Email Database</a></li>
    <li><a href="#">Mobile Number Database</a></li>
    <li><a href="#">Industry Specific Database</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Automation Services</h2>
    <ul>
    <li><a href="#">Marketing Automation</a></li>
    <li><a href="#">Email Automation</a></li>
    <li><a href="#">WhatsApp API Integration</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Marketing</h2>
    <ul>
    <li><a href="#">Content Marketing</a></li>
    <li><a href="#">eMail Marketing</a></li>
    <li><a href="#">WhatsApp Marketing</a></li>
    </ul>
    </div> -->
    <!-- <div class="footer-section">
    <h2>Optimization</h2>
    <ul>
    <li><a href="/optimization/local-seo">Search Engine Optimization</a></li>
    <li><a href="/optimization/social-media-optimization">Social Media Optimization</a></li>
    <li><a href="/optimization/local-seo">Local SEO</a></li>
    <li><a href="#">Technical SEO</a></li>
    <li><a href="#">Programmatic SEO</a></li>
    <li><a href="#">Voice Search SEO</a></li>
    <li><a href="#">GMB Optimization</a></li>
    <li><a href="/optimization/reverse-seo">Reverse SEO</a></li>
    <li><a href="/optimization/featured-snippet-optimization">Featured Snippet Optimization</a></li>
    <li><a href="#">App Store Optimization</a></li>
    <li><a href="#">Social Media Calendar Optimization</a></li>
    </ul>
    </div> -->
    <div class="footer-section">
        <h2 id="newiindustry">Industry</h2>
        <ul>
        <li><a href="/industry/digital-marketing-for-celebrities">Celebrities</a></li>
        <li><a href="/industry/digital-marketing-for-election-campaign">Election Campaign</a></li>
        <li><a href="/industry/digital-marketing-for-politicians">Political</a></li>
        <li><a href="/industry/digital-marketing-for-startups">Start-Ups</a></li>
        <li><a href="/industry/digital-marketing-for-b2c">D2C</a></li>
        <li><a href="/industry/digital-marketing-for-b2c">B2C</a></li>
        <li><a href="/industry/digital-marketing-for-b2b">B2B</a></li>
        <li><a href="/industry/digital-marketing-for-educational-institutes">Educational</a></li>
        <li><a href="/industry/digital-marketing-for-real-healthcare-companies">Hospitals</a></li>
        
        
        </ul>
        </div>
        <div class="footer-section" id="industry">
        <!-- <p id="industry"></p> -->
        <ul>
            <li><a href="/industry/digital-marketing-for-hospitality-companies">Hotels</a></li>
        <li><a href="/industry/digital-marketing-for-real-estate-companies">Real-estate</a></li>
        <li><a href="/industry/digital-marketing-for-fintech-companies">Fin-Tech</a></li>
        <li><a href="/industry/digital-marketing-for-large-enterprises">Large Enterprises</a></li>
        <li><a href="/industry/digital-marketing-for-ecommerce-companies">Ecommerce Businesses</a></li>
        <li><a href="/industry/digital-marketing-for-consultants">Consultants</a></li>
        <li><a href="/industry/digital-marketing-for-crowdfunding-companies">Crowd-funding</a></li>
        <li><a href="/industry/digital-marketing-for-media-companies">Media</a></li>
        <li><a href="/industry/digital-marketing-for-mobile-apps">Mobile Apps</a></li>
        </ul>
        </div>
    
    <div class="footer-section">
        <h2>Web Development</h2>
        <ul>
        <li><a href="/design-development/website-design-development">Web Design and Development</a></li>
        <li><a href="/design-development/logo-designing">Logo Design</a></li>
        <li><a href="/design-development/ecommerce-development">E-Commerce Development</a></li>
        <li><a href="/design-development/landing-page-creation">Landing Page Creation</a></li>
        <!-- <li><a href="#">Mobile App Development</a></li> -->
        </ul>
        </div>
    
  
        <div class="footer-section">
            <h2><a href="/about-us/process-methodology">Our Methodology</a></h2>
            </div>
            <div class="footer-section">
                <h2><a href="/about-us/careers">Careers</a></h2>
                </div>
                <div class="footer-section">
                    <h2><a href="">News and Article</a></h2>
                    </div>
                    <div class="footer-section">
                        <h2><a href="">Snippets</a></h2>
                        </div>
    <!-- <div class="footer-section-logo">
    <img  src="./picture/image14180.png" alt="BrandIxZen Logo" class="footer-logo">
    </div> -->
    </div>
   
    <!-- <div id="last-section" class="footer-section">
    <h2>Think-tank Services</h2>
    <ul>
    <li><a href="#">Business Consulting</a></li>
    <li><a href="#">Online Research</a></li>
    </ul>
    </div>
     -->
    
    
    
    <!-- <div class="footer-bottom">
    <p>Copyright © 2024 BrandIxZen Industries Limited. All Rights Reserved.</p>
    </div> -->
    <!-- <div id="main-bellowfooter"> -->
    
    <!-- </div> -->
    </footer>
    <div id="mny-kkilohguhjg-imhgutki-mjj">
    <div class="new-footer-section">
    <div class="footer-line-container">
    <hr class="footer-line">
    <img src="../picture/image14180.png" alt="BrandIxZen Logo" class="footer-logo">
    <hr class="footer-line">
    </div>
    </div>
    <div id="bottom-footer-logo">
    <div id="very-bellow-topper">
    Copyright © 2024 BrandIxZen Industries Limited. All Rights Reserved.
    </div>
    <div id="secondvery-bellow-topper">
    <!-- Design partner OIS software solution -->
    </div>
    </div>
    </div>
