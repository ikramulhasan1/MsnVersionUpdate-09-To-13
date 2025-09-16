 <!-- Stylesheets -->
    <link href="{{ asset('web/css/bootstrap.css') }}" rel="stylesheet">
    @if($livechat->status == 1)
        <link href="{{ asset('web/css/floating-wpp.min.css') }}" rel="stylesheet">
    @endif
    <link href="{{ asset('web/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('web/css/responsive.css') }}" rel="stylesheet">


    <link rel="preload" href="//fonts.googleapis.com">
    <link rel="preload" href="//fonts.gstatic.com" crossorigin>
    <link href="//fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;1,400;1,500;1,600&display=swap"
        rel="stylesheet">

    <!-- ✅ Owl Carousel CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <style>
        .poppins-regular {
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .poppins-medium {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-style: normal;
        }

        .poppins-semibold {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-style: normal;
        }

        .poppins-regular-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            font-style: italic;
        }

        .poppins-medium-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-style: italic;
        }

        .poppins-semibold-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-style: italic;
        }


        * {
            font-family: 'Poppins';
        }




        /* Base Mega Menu Styling */
        .mega-menu {
            /* position: relative; */
        }

        .mega-menu-trigger {
            /* position: relative; */
            /* display: inline-block; */
        }

        .mega-menu-link {
            padding-top: 15px;
            display: inline-block;
            color: #222222;
            font-size: 16px;
            /* text-decoration: none; */
            font-weight: 500;
        }

        .mega-menu-link:hover {
            color: red !important;
        }

        .mega-menu-content {
            display: none;
            position: absolute;
            top: 100%;
            /* right: 50%; */
            left: -350%;
            width: 900px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .mega-menu-content2 {
            display: none;
            position: absolute;
            top: 100%;
            /* right: 50%; */
            right: -450%;
            width: 900px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        /* Show the menu on hover of the link */
        .mega-menu-trigger:hover .mega-menu-content {
            display: grid;
        }

        .mega-menu-trigger:hover .mega-menu-content2 {
            display: grid;
        }

        .mega-links:hover {
            color: #ffffff !important;
            background-color: #000000;
            padding: 7px;
        }

        .mega-menu-column h4 {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #111;
        }

        .mega-menu-column ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .mega-menu-column ul li {
            margin-bottom: 8px;
        }

        .mega-menu-column ul li a {
            color: #555;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .mega-menu-column ul li a:hover {
            color: #007BFF;
        }


        .mega-menu-column .service-item {
            display: flex;
            align-items: center;
        }

        .mega-menu-column .service-item img {
            margin-right: 10px;
            /* Space between image and title */
        }

        .mega-menu-column .mega-links {
            font-size: 14px;
            /* Adjust size as necessary */
            text-decoration: none;
        }
    </style>
    
    <!-- Custom Style -->
    @if(isset($setting->custom_css))
        <style type="text/css">
            {
                ! ! strip_tags($setting->custom_css) ! !
            }

            .page-title .bread-crumb {
                background: black !important;
            }
        </style>
    @endif

    <style>
        /* Floating WhatsApp Button */
        .whatsapp-button {
            position: fixed;
            bottom: 15px;
            right: 15px;
            z-index: 1000;
            width: 50px;
            height: 50px;
            background-color: #25d366;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            animation: bounce 3s infinite;
        }

        .whatsapp-button img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            transition: transform 0.3s ease-in-out;
        }

        /* Hover Effects */
        .whatsapp-button:hover {
            background-color: #1ebe5d;
            transform: scale(1.1);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
        }

        .whatsapp-button:hover img {
            transform: rotate(10deg);
        }

        /* Bounce Animation */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-25px);
            }
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .whatsapp-button {
                width: 45px;
                height: 45px;
                bottom: 10px;
                right: 10px;
            }

            .whatsapp-button img {
                width: 30px;
                height: 30px;
            }
        }



        /* footer section */
        .custom-footer {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #0B2447;
            color: #ffffff !important;
        }

        .custom-footer {
            background: radial-gradient(circle at top left, #1A3C63, #0B2447);
            padding: 60px 0 30px;
        }

        .custom-footer-section h5 {
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 25px;
            position: relative;
            color: #ffffff !important;
        }

        .custom-footer-section h5::after {
            content: '';
            width: 40px;
            height: 3px;
            background: #32CD32;
            position: absolute;
            bottom: -10px;
            left: 0;
        }

        .custom-footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .custom-footer-section ul li {
            margin-bottom: 15px;
        }

        .custom-footer-section ul li a {
            text-decoration: none;
            color: #e0e0e0;
            font-size: 16px;
            transition: 0.3s;
        }

        .custom-footer-section ul li a:hover {
            color: #32CD32;
        }

        .custom-footer-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 50px 0 20px;
        }

        .custom-footer-bottom {
            text-align: left;
            color: #aaa;
            font-size: 14px;
            line-height: 1.6;
        }

        .custom-footer-bottom p a {
            color: #ffffff
        }

        .custom-footer-social-icons {
            margin-top: 20px;
        }

        .custom-footer-social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            margin: 0 5px;
            text-align: center;
            border-radius: 50%;
            font-size: 20px;
            background: #fff;
            color: #000;
            transition: 0.3s;
        }

        .custom-footer-social-icons a:hover {
            transform: scale(1.1);
        }

        .custom-footer-social-icons a.whatsapp {
            background: #32CD32;
            color: #fff;
        }

        .custom-footer-social-icons a.facebook {
            background: #1877f2;
            color: #fff;
        }

        .custom-footer-social-icons a.twitter {
            background: #000;
            color: #fff;
        }

        .custom-footer-social-icons a.linkedin {
            background: #0a66c2;
            color: #fff;
        }

        .custom-footer-social-icons a.youtube {
            background: #ff0000;
            color: #fff;
        }

        .custom-footer-social-icons a.instagram {
            background: #E1306C;
            color: #fff;
        }

        .custom-footer-social-icons a.behance {
            background: #1769ff;
            color: #fff;
        }

        .custom-footer-social-icons a.pinterest {
            background: #e60023;
            color: #fff;
        }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- google analytics --}}
    <!-- Google tag (gtag.js) -->
    <script async src="//www.googletagmanager.com/gtag/js?id=G-FQTTGFBMBE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-FQTTGFBMBE');
    </script>
    <link href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">