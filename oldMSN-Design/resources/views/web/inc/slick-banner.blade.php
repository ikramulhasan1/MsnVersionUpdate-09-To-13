<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Slick Carousel Example</title>

  <!-- Slick CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

  <!-- Custom CSS -->
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f5f5f5;
    }

    .slider-container {
      width: 90%;
      max-width: 1200px;
      margin: 50px auto;
    }

    .slick-slider img {
      width: 100%;
      height: auto;
      border-radius: 10px;
    }

    .slick-dots li button:before {
      color: #000;
    }

    .slick-prev:before,
    .slick-next:before {
      color: #000;
      font-size: 30px;
    }
  </style>
</head>
<body>

  <div class="slider-container">
    <div class="slick-slider">
      <div><img src="https://via.placeholder.com/1200x500/ff7f7f/333333?text=Slide+1" alt="Slide 1"></div>
      <div><img src="https://via.placeholder.com/1200x500/7fbfff/333333?text=Slide+2" alt="Slide 2"></div>
      <div><img src="https://via.placeholder.com/1200x500/7fff7f/333333?text=Slide+3" alt="Slide 3"></div>
      <div><img src="https://via.placeholder.com/1200x500/f7ff7f/333333?text=Slide+4" alt="Slide 4"></div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Slick JS -->
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

  <!-- Slick Init -->
  <script>
    $(document).ready(function(){
      $('.slick-slider').slick({
        dots: true,
        infinite: true,
        speed: 500,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        arrows: true,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              arrows: false
            }
          }
        ]
      });
    });
  </script>

</body>
</html>
