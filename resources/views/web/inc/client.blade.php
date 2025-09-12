<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Client Logo Carousel</title>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f9fc;
        }

        section {
            padding: 60px 0;
            text-align: center;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 40px;
            font-weight: bold;
            color: #222;
        }

        .swiper {
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .swiper-slide img {
            width: 140px;
            height: auto;
            object-fit: contain;
            background: white;
            padding: 10px;
            border-radius: 2px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            filter: grayscale(100%);
            transition: all 0.3s ease;
        }

        /* .swiper-slide img:hover {
            filter: grayscale(0%);
            transform: scale(1.05);
        } */

        /* Navigation buttons */
        .swiper-button-next,
        .swiper-button-prev {
            color: #333;
            transition: 0.3s;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            color: #ff6f2c;
        }

        .left-slide-cover img {

            width: 140px;
            height: auto;
            object-fit: contain;
            background: white;
            padding: 10px;
            border-radius: 2px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            filter: grayscale(100%);
            transition: all 0.3s ease;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>

<body>
    <section>
        <h2>Enterprises & Tech Companies Worldwide Trust Us</h2>
        <div class="row">
            <div class="col-lg-2 left-slide-cover">
                <!-- <img src="https://dummyimage.com/200x100/ddd/000.png&text=Client+1" alt="Client 1" /> -->
                <h5>Worldwide Trust Us</h5>
            </div>
            <div class="col-lg-10">
                <!-- Swiper -->
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper mb-5">
                        <div class="swiper-slide">
                            <img class="p-0" src="https://dummyimage.com/200x100/ddd/000.png&text=Client+1"
                                alt="Client 1" />
                        </div>
                        <div class="swiper-slide">
                            <img class="p-0" src="https://dummyimage.com/200x100/ccc/000.png&text=Client+2"
                                alt="Client 2" />
                        </div>
                        <div class="swiper-slide">
                            <img class="p-0" src="https://dummyimage.com/200x100/bbb/000.png&text=Client+3"
                                alt="Client 3" />
                        </div>
                        <div class="swiper-slide">
                            <img class="p-0" src="https://dummyimage.com/200x100/aaa/000.png&text=Client+4"
                                alt="Client 4" />
                        </div>
                        <div class="swiper-slide">
                            <img class="p-0" src="https://dummyimage.com/200x100/999/000.png&text=Client+5"
                                alt="Client 5" />
                        </div>
                        <div class="swiper-slide">
                            <img class="p-0" src="https://dummyimage.com/200x100/888/000.png&text=Client+6"
                                alt="Client 6" />
                        </div>
                        <div class="swiper-slide">
                            <img class="p-0" src="https://dummyimage.com/200x100/777/000.png&text=Client+7"
                                alt="Client 7" />
                        </div>
                        <div class="swiper-slide">
                            <img class="p-0" src="https://dummyimage.com/200x100/666/000.png&text=Client+8"
                                alt="Client 8" />
                        </div>
                    </div>

                    <!-- Navigation -->
                    <!-- <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div> -->

                    <!-- Pagination -->
                    <!-- <div class="swiper-pagination"></div> -->
                </div>
            </div>
        </div>

    </section>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- <script>
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 5,
            spaceBetween: 0,
            loop: true,
            // autoplay: {
            //     delay: 2000,
            //     disableOnInteraction: false,
            // },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                320: { slidesPerView: 2 },
                640: { slidesPerView: 3 },
                768: { slidesPerView: 4 },
                1024: { slidesPerView: 5 },
            },
        });
    </script> -->
    <script>
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 6,
            spaceBetween: 30,
            loop: true,
            freeMode: true, // smooth effect
            speed: 4000,    // control smooth speed
            autoplay: {
                delay: 0, // no pause
                disableOnInteraction: false,
            },
            breakpoints: {
                320: { slidesPerView: 2 },
                640: { slidesPerView: 3 },
                768: { slidesPerView: 4 },
                1024: { slidesPerView: 6 },
            },
        });
    </script>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

</html>