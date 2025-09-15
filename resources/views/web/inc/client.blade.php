<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    .client-logo-section {
        /* padding: 60px 0; */
        width: 97.60% !important;
        max-height: 80px !important;
        margin-right: 5px !important;
        margin-left: 15px !important;
        text-align: center;
    }

    .client-content {
        max-height: 80px !important;
    }

    .swiper {
        /* width: 100%; */
        /* max-width: 1400px; */
        /* margin: auto; */
    }

    .swiper-slide {
        display: flex;
        justify-content: center;
        align-items: center;
        max-height: 80px !important;
    }

    .swiper-slide img {
        width: 140px !important;
        max-height: 80px !important;
        margin: 0px !important;
        object-fit: contain;
        background: white;
        /* padding: 10px; */
        border-radius: 2px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        filter: grayscale(100%);
        transition: all 0.3s ease;
        /* normal */
    }





    .swiper-slide img {
        filter: grayscale(100%);
        transition: all 0.3s ease;
        /* normal */
    }

    .swiper-slide img:hover {
        filter: grayscale(0%);
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: all 0.1s ease-in;
        /* instant feel */
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

    /* .left-slide-cover img {

        width: 140px;
        height: auto;
        object-fit: contain;
        background: white;
        padding: 10px;
        border-radius: 2px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        filter: grayscale(100%);
        transition: all 0.3s ease;
    } */
    .left-slide-cover h6 {
        font-weight: 700;
        font-size: 20px;
        color: #000000;
    }
</style>


@if(count($clients) > 0)
    <section class="client-logo-section">
        <div class="row">
            <div style="background-color: #052C58"
                class="col-lg-2 left-slide-cover d-flex align-items-center p-0 justify-content-center client-content">
                <!-- <img src="https://dummyimage.com/200x100/ddd/000.png&text=Client+1" alt="Client 1" /> -->
                <h6 class="text-white">Our Valued Clients</h6>
            </div>
            <div class="col-lg-10 client-content pl-0">
                <!-- Swiper -->
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper mb-5">
                        @foreach($clients as $client)
                            <div class="swiper-slide">
                                <img style="height: 40px !important;" class="p-0 shadow-none"
                                    src="{{ asset('uploads/client/' . $client->image_path) }}" alt="{{ $client->title }}" loading="lazy" />
                            </div>
                        @endforeach
                        {{-- <div class="swiper-slide">
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
                        </div> --}}
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
@endif
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
        spaceBetween: 0,
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