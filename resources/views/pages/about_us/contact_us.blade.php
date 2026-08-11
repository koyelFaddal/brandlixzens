<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandIxZen</title>
    <!-- <link rel="icon" href="../picture/Logo.png" type="image/x-icon"> -->
    <link rel="stylesheet" href="../style/footer.css">
    <script src="../footerTemplate/sr.js"></script>
    <link rel="stylesheet" href="../style/aboutus.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../nav2.css">
    <link rel="stylesheet" href="../style/digital_marketing_for_d2c.css">
    <link rel="stylesheet" href="../style/seo.css">
    <script src="../script/script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css" rel="stylesheet" />


 
    <style>
#main-container-get-industry-top-container{
    background-color: white;

}#only-laptop-screen-no-mobile{
    margin-top: -5%;
}
body{
    background-color: navy;
}

        /* Specific styles for mobile screens */
        @media only screen and (max-width: 768px) {
        
            .menu {
                display: none;
                list-style-type: none;
                margin: 0;
                padding: 0;
                /* padding-top: 50%; */
                z-index: 1010;
                height: 100vh;
               
                background-color: rgb(38, 55, 141);
            }

            .menu > li {
                position: relative;
                padding: 20px;
               
                background-color: rgb(38, 55, 141);
                color: white;
                cursor: pointer;
            }

            .dropdown-icon {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
            }

            .dropdown-content {
        display: none;
        list-style-type: none;
        margin: 5% 0;
        padding: 0;
        color: black;
        border-radius: 5px;
        background-color: #f0f0f0;
        max-height: 350px; /* Set a fixed height for scrolling */
        overflow-y: auto;
    }
    /* max-height: 100vh;
    overflow-y: auto; */

            .dropdown-content li {
                padding: 10px;
                margin-top: 5%;
                border-bottom: 1px solid #ccc;
            }
            .list1 {
                margin-left: 10%;
            }
        }
       
    </style>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>

    #web-dev{
        margin-left: 6%;
    }
    #top-contact-contacus-picture{
        margin-top: 15%;
    }
    .radio-group {
    display: flex;
    /* flex-direction: column; */
    margin: 5px 10px;
    gap: 5%;
}

.radio-group label {
    color: black;
    font-size: 16px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    cursor: pointer;
}

.radio-group input[type="radio"] {
    margin-right: 10px;
    cursor: pointer;
    width: 20px;
    height: 20px;
    accent-color: blue;
    /* Removed position: relative to avoid interference */
}

.radio-group input[type="radio"]:checked::before {
    content: '';
    position: absolute;
    width: 0; /* Adjust to remove the interference */
    height: 0;
    /* Removed the top, left, and size settings to avoid click issues */
}



   body {
            /* font-family: Arial, sans-serif; */
            margin: 0;
            padding: 0;
            background-color: navy;
        }
        .contactus-form-new-container {
            margin-top: 10%;
            margin-left: 5%;
            margin-right: 5%;
            border-radius: 44px;
            background-image: linear-gradient(to bottom, #87CEEB, #FFFFFF);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* color: white; */
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 50px;
        }
        .contactus-form-new-left-section {
            margin-left: 5%;
            max-width: 50%;
        }
        .contactus-form-new-left-section h1 {
            font-size: 48px;
            margin: 0;
        }
        .contactus-form-new-left-section p {
            font-size: 18px;
            color: #666;
        }
        .contactus-form-new-right-section {
            max-width: 40%;
            
        }
        .contactus-form-new-right-section input, .contactus-form-new-right-section textarea {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .contactus-form-new-right-section button {
            padding: 15px 30px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .illustration {
            margin-top: 20px;
        }#contactus-form-new-container-map-for-company{
        /* margin-top: 15%; */
       }#map-img{
    width: 100%;
}#our-presence{
    color: white;
    font-size: 3rem;
    font-weight: bolder;
}#our-presence-contaner{
    display: flex;
    justify-content: center;
    margin-top: 10%;
}#navbar-placeholder-mobile{
    display: none;
}@media (max-width: 768px) {
    #navbar-placeholder-mobile{
    display: block;
}#navbar-placeholder-laptop{
    display: none;
}
}@media (max-width: 768px) { /* Tablet and mobile screens */
    .contactus-form-new-container {
        margin-top: 5%; /* Reduced top margin */
        margin-left: 2%; /* Adjusted side margins */
        margin-right: 2%;
        flex-direction: column; /* Stack sections vertically */
        padding: 20px; /* Reduced padding */
        border-radius: 20px; /* Smaller border radius */
    }

    .contactus-form-new-left-section {
        max-width: 100%; /* Full width on mobile */
        margin: 0; /* Remove margin */
        margin-bottom: 15px; /* Space between sections */
    }

    .contactus-form-new-left-section h1 {
        font-size: 28px; /* Smaller heading size */
        text-align: center; /* Center the heading */
    }

    .contactus-form-new-left-section p {
        font-size: 16px; /* Adjust paragraph size */
        text-align: center; /* Center the text */
        color: #666;
    }

    .contactus-form-new-right-section {
        max-width: 100%; /* Full width on mobile */
    }

    .contactus-form-new-right-section input,
    .contactus-form-new-right-section textarea {
        width: calc(100% - 20px); /* Full width with padding adjustment */
        padding: 12px; /* Adjust padding for inputs */
        margin: 10px 0; /* Maintain margin */
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px; /* Smaller font size */
    }
    .contactus-form-new-right-section select {
    width: calc(100% - 20px); /* Full width with padding adjustment */
    padding: 12px; /* Adjust padding for inputs */
    margin: 10px 0; /* Maintain margin */
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px; /* Smaller font size */
    max-width: 100%; /* Ensure it doesn't exceed container */
    box-sizing: border-box; /* Ensure padding and width calculations are accurate */
}



    .contactus-form-new-right-section button {
        width: 100%; /* Full width for button */
        padding: 12px; /* Adjust button padding */
        font-size: 16px; /* Maintain button font size */
    }

    .illustration {
        margin-top: 10px; /* Adjust margin for illustration */
        width: 100%;
        height: 100%; /* Ensure illustration fits full width */
        max-width: 300px; /* Optional: limit max width */
        display: block; /* Ensure it behaves as a block element */
        margin-left: auto; /* Center the illustration */
        margin-right: auto; /* Center the illustration */
    }
}
/* Default: Show radio buttons, hide select */
.select-group {
    display: none;
}

/* Mobile screen: Show select, hide radio buttons */
@media only screen and (max-width: 768px) {
    .radio-group {
        display: none;
    }
    .select-group {
        display: block;
    }#selecteed-item-new{
        width: 100%;
    }
}
.contactus-form-new-right-section select option {
    font-size: 14px; /* Consistent font size for options */
    padding: 10px; /* Adjust padding for readability */
    max-width: 100%; /* Ensure options don’t overflow */
}
  </style>
 </head>
 <body>
    <div id="navbar-placeholder-mobile"></div>
    
    <!-- <div id="main-topconinter-topper"> -->
        <div id="navbar-placeholder-laptop"></div>
        <div id="our-presence-contaner">
            <h1 id="our-presence">Our <span style="color: red;">Presence</span></h1>
        </div>
        <div id="container-map-for-company">
            <img id="map-img" src="../picture/Company (1)map.png" alt="">
                      </div>

                      <div class="contactus-form-new-container">
                        <div class="contactus-form-new-left-section">
                         <h1>
                          Let's talk about everything!
                         </h1>
                         <p>
                             Got questions or need assistance? We're here to help. Reach out, and we'll get back to you shortly!
                         </p>
                         <img alt="Illustration of a person holding a file with a location pin on it" class="illustration" height="300" src="../picture/960x0.gif" width="500"/>
                        </div>
                        <div class="contactus-form-new-right-section">
                         <input placeholder="Your name" type="text"/>
                         <input placeholder="Email" type="email"/>
                         <input placeholder="Phone Number" type="text"/>
                         <div class="radio-group">
                            <label><input type="radio" name="option" value="option1"/> Optimization</label>
                            <label><input type="radio" name="option" value="option2"/> Advertising</label>
                            <label><input type="radio" name="option" value="option3"/> Branding</label>
                        </div>
                        <div class="radio-group">
                            <label><input type="radio" name="option" value="option4"/> Marketing</label>
                            <label id="web-dev"><input type="radio" name="option" value="option5"/> Web Development</label>
                        </div>
                        
                        <!-- Select for Mobile Only -->
                        <div class="select-group">
                            <select name="option-mobile" id="selecteed-item-new">
                                <option value="option1">Optimization</option>
                                <option value="option2">Advertising</option>
                                <option value="option3">Branding</option>
                                <option value="option4">Marketing</option>
                                <option value="option5">Web Development</option>
                            </select>
                        </div>
                        
                         <input placeholder="Subject" type="text"/>
                         <textarea placeholder="Write your message" rows="5"></textarea>
                         <button>
                          Send Message
                         </button>
                        </div>
                       </div>
  <div id="footer-placeholder"></div>

    <script src="../footerTemplate/sr.js"></script>
    <script src="../script/templatefooter.js"></script>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- GSAP -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.2/gsap.min.js"></script>
    

    </body>
    <script>
const dropdownHideTimers = new Map();

const showDropdown = (dropdownClass) => {
    clearTimeout(dropdownHideTimers.get(dropdownClass));
    document.querySelector(dropdownClass).classList.remove("hidden");
}

const hideDropdown = (dropdownClass) => {
    clearTimeout(dropdownHideTimers.get(dropdownClass));
    dropdownHideTimers.set(dropdownClass, setTimeout(() => {
        document.querySelector(dropdownClass).classList.add("hidden");
    }, 150));
}
    
    
    const MassagePopup = () => showDropdown(".massage-dropdown");
    const Massageleave = () => hideDropdown(".massage-dropdown");
    
    const WellnessPopup = () => showDropdown(".wellness-dropdown");
    const Wellnessleave = () => hideDropdown(".wellness-dropdown");
    
    const SleepPopup = () => showDropdown(".sleep-dropdown");
    const Sleepleave = () => hideDropdown(".sleep-dropdown");
    
    const secondSleepPopup = () => showDropdown(".second-sleep-dropdown");
    const secondSleepleave = () => hideDropdown(".second-sleep-dropdown");
    
    const otherSleepPopup = () => showDropdown(".other-sleep-dropdown");
    const otherSleepleave = () => hideDropdown(".other-sleep-dropdown");
    
    const aboutSleepPopup = () => showDropdown(".about-sleep-dropdown");
    const aboutSleepleave = () => hideDropdown(".about-sleep-dropdown");
    
    const indusSleepPopup = () => showDropdown(".indus-sleep-dropdown");
    const indusSleepleave = () => hideDropdown(".indus-sleep-dropdown");
    
    
    
    document.addEventListener('DOMContentLoaded', () => {
        function generateCaptcha() {
            const charsArray = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            const lengthOtp = 6;
            let captcha = [];
            for (let i = 0; i < lengthOtp; i++) {
                let index = Math.floor(Math.random() * charsArray.length);
                captcha.push(charsArray[index]);
            }
            return captcha.join('');
        }
    
        function refreshCaptcha(formId) {
            const captchaText = document.querySelector(`#${formId} #captcha-text`);
            captchaText.textContent = generateCaptcha();
        }
    
        function initializeCaptcha(formId) {
            refreshCaptcha(formId);
    
            const refreshButton = document.querySelector(`#${formId} #refresh-captcha`);
            refreshButton.addEventListener('click', () => {
                refreshCaptcha(formId);
            });
    
            const form = document.getElementById(formId);
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                const captchaInput = form.querySelector('#captcha').value;
                const captchaText = form.querySelector('#captcha-text').textContent;
                if (captchaInput === captchaText) {
                    alert('Captcha matched');
                    // Perform form submission or other necessary actions
                } else {
                    alert('Captcha not matched. Please try again.');
                }
            });
        }
    
        // Initialize CAPTCHA for both forms
    });
    
    
    document.querySelectorAll('.faq-question').forEach(item => {
        item.addEventListener('click', () => {
            const answer = item.nextElementSibling;
            const btn = item.querySelector('.toggle-btn');
            
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                btn.textContent = '+';
            } else {
                answer.style.display = 'block';
                btn.textContent = '-';
            }
        });
    });
    
    
    
        document.getElementById('input-form-below-top-right').addEventListener('submit', function(event) {
          event.preventDefault();
          const inputText = document.getElementById('input-box-below-top-right').value;
          alert(`Submitted: ${inputText}`);
        });
    
        document.getElementById("shoot-email").addEventListener("click", function() {
        // Redirects to the default email app with the recipient pre-filled
        window.location.href = "mailto:sharmapujan209@gmail.com";
    });
    
    
    function openWhatsApp() {
      var phoneNumber = '+918348101800';
      var message = encodeURIComponent('');
    
      // Check if the user is on an Android device
      var isAndroid = /Android/i.test(navigator.userAgent);
    
      if (isAndroid) {
        // If on Android, use the WhatsApp intent URL
        var url = 'whatsapp://send?phone=' + phoneNumber + '&text=' + message;
        
        // Try to open the WhatsApp app
        window.location.href = url;
      } else {
        // If not on Android (or WhatsApp not available), fallback to WhatsApp Web
        var webUrl = 'https://web.whatsapp.com/send?phone=' + phoneNumber + '&text=' + message;
        
        // Open WhatsApp Web in a new tab
        window.open(webUrl, '_blank');
      }
    }
    
    $('#whatsappLink').on('click', function(event) {
      event.preventDefault();
    
      // Call the function to open WhatsApp
      openWhatsApp();
    });
    </script>
    <script src="../footerTemplate/sr.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/partials/navbar-desktop')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('navbar-placeholder-laptop').innerHTML = data;
                    const logo = document.querySelector("#logo");
        logo.addEventListener("click", () => {
            window.location.href = "/";
        });
           
                })
                .catch(error => console.error('Error loading footer:', error));
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/partials/navbar-mobile')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('navbar-placeholder-mobile').innerHTML = data;
                    const logo = document.querySelector(".logo");
    logo.addEventListener("click", () => {
        window.location.href = "/";
    });
                    
                    // Initialize dropdowns after HTML content is loaded
                    const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
                    
                    dropdownTriggers.forEach(trigger => {
                        const dropdownIcon = trigger.querySelector('.dropdown-icon');
                        const dropdownContent = trigger.querySelector('.dropdown-content');
                        
                        trigger.addEventListener('click', function() {
                            const isVisible = dropdownContent.style.display === 'block';
                            
                            dropdownContent.style.display = isVisible ? 'none' : 'block';
                            dropdownIcon.classList.toggle('fa-caret-down', isVisible);
                            dropdownIcon.classList.toggle('fa-caret-up', !isVisible);
                        });
                    });
                })
                .catch(error => console.error('Error loading footer:', error));
        });
    
        function toggleMenu() {
            const menu = document.getElementById("menu");
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }
    </script>
    <script>
        window.addEventListener('scroll', function() {
      var dropdowns = document.querySelector('.drop-downs');
      var logo = document.querySelector('#logo img');
      var scrollPosition = window.scrollY;
      var optimization=document.querySelector(".massage-dropdown");
      var advertising=document.querySelector(".wellness-dropdown");
      var branding=document.querySelector(".sleep-dropdown");
      var marketing=document.querySelector(".second-sleep-dropdown");
    
      // Set positions for dropdown and logo
      dropdowns.style.position = 'fixed';
      logo.style.position = 'fixed';
    
      // Save background color and other style changes to localStorage
      if (scrollPosition > 56) {
        dropdowns.style.backgroundColor = '#1c1c1c'; 
        dropdowns.style.top = '0'; 
        optimization.style.top='7%'
        advertising.style.top='7%'
        branding.style.top='7%'
        marketing.style.top='7%'
        dropdowns.style.marginLeft = '5%'; 
        dropdowns.style.width = '70%';
        dropdowns.style.fontSize = '15px';
        dropdowns.style.padding = '5px 10%';
        dropdowns.style.gap = '5%';
        dropdowns.style.justifyContent = 'flex-start';
    
        // Save state to localStorage
        localStorage.setItem('dropdownState', 'scrolled');
        
        // Hide specific elements as needed
        document.querySelector("#other-services").style.display = "none";
        document.querySelector("#industries").style.display = "none";
        document.querySelector("#vertical-hrline").style.display = "none";
      } else {
        dropdowns.style.justifyContent = 'space-between';
        dropdowns.style.backgroundColor = 'transparent';
        dropdowns.style.gap = '0';
        optimization.style.top='19%';
        advertising.style.top='19%';
        branding.style.top='19%';
        marketing.style.top='19%';
        dropdowns.style.top = '3.5rem'; 
        dropdowns.style.width = '70%';
        dropdowns.style.marginLeft = '19%'; 
        dropdowns.style.fontSize = '18px'; 
        dropdowns.style.padding = '10px 20px'; 
    
        // Save state to localStorage
        localStorage.setItem('dropdownState', 'notScrolled');
        
        document.querySelector("#other-services").style.display="block";
        document.querySelector("#industries").style.display="block";
        document.querySelector("#vertical-hrline").style.display="block";
      }
    
      // Logo styling for smooth transition
      if (scrollPosition > 56) {
        logo.style.width = '30px';
        logo.style.top = '10px';
      } else {
        logo.style.width = '80px';
        logo.style.top = '20px';
      }
    });
    
    // Apply saved state on page load
    window.addEventListener('load', function() {
      var dropdowns = document.querySelector('.drop-downs');
      var logo = document.querySelector('#logo img');
      var savedState = localStorage.getItem('dropdownState');
    
      if (savedState === 'scrolled') {
        dropdowns.style.backgroundColor = '#312c26'; 
        dropdowns.style.top = '0'; 
        dropdowns.style.marginLeft = '5%'; 
        dropdowns.style.width = '70%';
        dropdowns.style.fontSize = '15px';
        dropdowns.style.padding = '5px 10%';
        dropdowns.style.gap = '5%';
        dropdowns.style.justifyContent = 'flex-start';
        
        // Hide specific elements
        document.querySelector("#other-services").style.display = "none";
        document.querySelector("#industries").style.display = "none";
        document.querySelector("#vertical-hrline").style.display = "none";
        
        logo.style.width = '30px';
        logo.style.top = '10px';
      } else {
        dropdowns.style.justifyContent = 'space-between';
        dropdowns.style.backgroundColor = 'transparent';
        dropdowns.style.gap = '0';
        dropdowns.style.top = '3.5rem'; 
        dropdowns.style.width = '70%';
        dropdowns.style.marginLeft = '19%'; 
        dropdowns.style.fontSize = '18px'; 
        dropdowns.style.padding = '10px 20px'; 
    
        // Show specific elements
        document.querySelector("#other-services").style.display="block";
        document.querySelector("#industries").style.display="block";
        document.querySelector("#vertical-hrline").style.display="block";
        
        logo.style.width = '80px';
        logo.style.top = '20px';
      }
    });
    
    </script>
    </html>
    
