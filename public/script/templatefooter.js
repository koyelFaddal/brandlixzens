
    document.addEventListener("DOMContentLoaded", function () {
        if (!window.brandixzenSiteActionsLoaded) {
            const siteActionsScript = document.createElement('script');
                siteActionsScript.src = '/script/site-actions.js?v=3';
            document.head.appendChild(siteActionsScript);
        }

        // Load footer template
        fetch('/partials/footer')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Unable to load footer (${response.status})`);
                }

                return response.text();
            })
            .then(data => {
                document.getElementById('footer-placeholder').innerHTML = data;

                if (window.initializeNewsletterForms) {
                    window.initializeNewsletterForms();
                } else {
                    const newsletterScript = document.createElement('script');
                    newsletterScript.src = '/script/newsletter-subscription.js?v=1';
                    document.head.appendChild(newsletterScript);
                }

                if (window.initializeLeadForms) {
                    window.initializeLeadForms();
                } else {
                    const leadFormScript = document.createElement('script');
                    leadFormScript.src = '/script/lead-form.js?v=2';
                    leadFormScript.onload = () => window.initializeLeadForms();
                    document.head.appendChild(leadFormScript);
                }

                var $tickerWrapper = $(".tickerwrapper");
                var $list = $tickerWrapper.find("ul.list");
                var $clonedList = $list.clone();
                var listWidth = 10;
        
                $list.find("li").each(function (i) {
                    listWidth += $(this).outerWidth(true);
                });
        
                var endPos = $tickerWrapper.width() - listWidth;
        
                $list.add($clonedList).css({
                    "width": listWidth + "px"
                });
        
                $clonedList.addClass("cloned").appendTo($tickerWrapper);
        
                var infinite = gsap.timeline({ repeat: -1, paused: true });
                var time = 40;
        
                infinite
                    .fromTo($list, time, { x: 0 }, { x: -listWidth, ease: "none" }, 0)
                    .fromTo($clonedList, time, { x: listWidth }, { x: 0, ease: "none" }, 0)
                    .set($list, { x: listWidth })
                    .to($clonedList, time, { x: -listWidth, ease: "none" }, time)
                    .to($list, time, { x: 0, ease: "none" }, time)
                    .progress(1).progress(0)
                    .play();
        
                // Pause/Play on hover
                $tickerWrapper.on("mouseenter", function() {
                    infinite.pause();
                }).on("mouseleave", function() {
                    infinite.play();
                });


    let currentSlide = 0;
    const slides = document.querySelectorAll('.testimonial-content');
    const totalSlides = slides.length;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.style.display = (i === index) ? 'flex' : 'none'; // Only display the current slide
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides; // Loop back to the first slide
        showSlide(currentSlide);
    }

    // Show the first slide initially
    showSlide(currentSlide);

    // Automatically move to the next slide every 5 seconds
    setInterval(nextSlide, 7000);


            })
            .catch(error => console.error('Error loading footer:', error));
    });



   
