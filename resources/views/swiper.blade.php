<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swiper Coverflow</title>

    <link href="{{ asset('css/swiper-bundle.min.css') }}" rel="stylesheet" />

    <style>
        /* Swiper container styling */
        .swiper-container {
            width: 100%;
            height: 500px;
            padding-top: 50px;
            padding-bottom: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            margin-left: auto;
            margin-right: auto;
        }

        /* Adjust slide size and perspective */
        .swiper-slide {
            width: 600px; /* Adjust width as needed */
            height: 400px; /* Adjust height as needed */
            display: flex;
            justify-content: center;
            align-items: center;
            perspective: 800px; /* Add perspective for 3D effect */
        }

        /* Style the image container for the 3D rotation */
        .slide-content {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d; /* Important for nested 3D */
            transition: transform 0.5s ease; /* Smooth transitions */
        }

        .swiper-slide-active .slide-content {
            transform: rotateY(0deg) scale(1.1); /* Slightly scale up the active slide */
        }
        .swiper-slide-next .slide-content {
            transform: rotateY(-30deg) scale(0.9); /* Slightly scale up the active slide */
        }
        .swiper-slide-prev .slide-content {
            transform: rotateY(30deg) scale(0.9); /* Slightly scale up the active slide */
        }


        /* Ensure image fits properly and add rotation */
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
            position: absolute;
            top: 0;
            left: 0;
            backface-visibility: hidden; /* Hide the back of the image */
        }

        .swiper-slide .text-content { /* Style for the text overlay */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0; /* Initially hidden */
            transition: opacity 0.3s ease; /* Smooth transition for opacity */
        }

        .swiper-slide:hover .text-content {
            opacity: 1; /* Show on hover */
        }

        .swiper-slide .text-content h2,
        .swiper-slide .text-content p {
            display:none;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: black; /* Make arrows clearly visible */
            font-size: 40px;
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            z-index: 10; /* Ensure they're above the slides */
        }

        .swiper-button-next {
            right: -80px; /* Move outside */
        }

        .swiper-button-prev {
            left: -80px; /* Move outside */
        }

        /* Pagination styling */
        .swiper-pagination-bullet {
            background: white;
        }

        @media screen and (max-width:768px){
            .swiper-slide img {
                width: 83%;
                height: 46%;
                object-fit: contain;
                border-radius: 15px;
                position: absolute;
                top: 0;
                left: 51px;
                backface-visibility: hidden;
            }
        }
    </style>
</head>
<body>

<div class="swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-1.jpg') }}" alt="Slide 1">
                <div class="text-content">
                    <h2>Slide 1 Title</h2>
                    <p>Slide 1 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-2.jpg') }}" alt="Slide 2">
                <div class="text-content">
                    <h2>Slide 2 Title</h2>
                    <p>Slide 2 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-3.jpg') }}" alt="Slide 3">
                <div class="text-content">
                    <h2>Slide 3 Title</h2>
                    <p>Slide 3 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-4.jpg') }}" alt="Slide 4">
                <div class="text-content">
                    <h2>Slide 4 Title</h2>
                    <p>Slide 4 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-1.jpg') }}" alt="Slide 1">
                <div class="text-content">
                    <h2>Slide 1 Title</h2>
                    <p>Slide 1 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-2.jpg') }}" alt="Slide 2">
                <div class="text-content">
                    <h2>Slide 2 Title</h2>
                    <p>Slide 2 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-3.jpg') }}" alt="Slide 3">
                <div class="text-content">
                    <h2>Slide 3 Title</h2>
                    <p>Slide 3 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-4.jpg') }}" alt="Slide 4">
                <div class="text-content">
                    <h2>Slide 4 Title</h2>
                    <p>Slide 4 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-1.jpg') }}" alt="Slide 1">
                <div class="text-content">
                    <h2>Slide 1 Title</h2>
                    <p>Slide 1 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-2.jpg') }}" alt="Slide 2">
                <div class="text-content">
                    <h2>Slide 2 Title</h2>
                    <p>Slide 2 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-3.jpg') }}" alt="Slide 3">
                <div class="text-content">
                    <h2>Slide 3 Title</h2>
                    <p>Slide 3 description text.</p>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="slide-content">
                <img src="{{ asset('images/fade-4.jpg') }}" alt="Slide 4">
                <div class="text-content">
                    <h2>Slide 4 Title</h2>
                    <p>Slide 4 description text.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>

    <div class="swiper-pagination"></div>
</div>

<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var swiper = new Swiper('.swiper-container', {
            effect: 'coverflow',
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: 'auto', // Important: Use 'auto' to allow varying widths
            spaceBetween: -50, // Adjust space between slides
            coverflowEffect: {
                rotate: 0, // Reduced rotation for a clean look
                stretch: 0,
                depth: 200, // Adjust depth for desired 3D effect
                modifier: 1,
                slideShadows: false,
            },
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    });
</script>

</body>
</html>