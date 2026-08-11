window.addEventListener('scroll', function() {
    var dropdowns = document.querySelector('.drop-downs');
    var logo = document.querySelector('#logo img');
    var scrollPosition = window.scrollY;
    var optimization=document.querySelector(".massage-dropdown");
    var advertising=document.querySelector(".wellness-dropdown");
    var branding=document.querySelector(".sleep-dropdown");
    var marketing=document.querySelector(".second-sleep-dropdown");
  var addtianaolbutton=document.querySelector("#additional-buttons");
    // Set positions for dropdown and logo  
    dropdowns.style.position = 'fixed';
    logo.style.position = 'fixed';
  
    // Save background color and other style changes to localStorage
    if (scrollPosition > 56) {
      addtianaolbutton.style.top="17px";
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
      addtianaolbutton.style.top="22px";
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