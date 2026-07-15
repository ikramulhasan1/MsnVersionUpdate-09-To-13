@extends('web.layouts.master')

@section('content')

  <!-- Banner Section -->
  <section class="banner">
    <h2>MSN Softtech Technologies</h2>
    <h1>Power Your Business with<br>Cutting-Edge Solutions</h1>

    <div class="categories">
      <a href="#techCategories" class="pill blue">Programming Languages</a>
      <a href="#techCategories" class="pill orange">Front-End</a>
      <a href="#techCategories" class="pill green">Back-End</a>
      <a href="#techCategories" class="pill blue">CMS & E-Commerce Platforms</a>
    </div>
  </section>

  <!-- Main Technologies Section -->
  <div id="techCategories" class="container py-5">
    <div class="row">
      <!-- Sidebar -->
      <aside class="col-md-3">
        <div class="sidebar">
          <div class="accordion" id="techAccordion">

            <!-- Programming -->
            <div class="accordion-item">
              <button class="accordion-header" type="button" data-target="panel-programming" aria-expanded="false">
                Programming Languages
                <span class="arrow" aria-hidden="true"></span>
              </button>
              <div id="panel-programming" class="accordion-panel">
                <button class="subcategory-btn" data-sub="general">General purpose</button>
                <button class="subcategory-btn" data-sub="mobile">Mobile App</button>
              </div>
            </div>

            <!-- Front-End -->
            <div class="accordion-item">
              <button class="accordion-header" type="button" data-target="panel-frontend" aria-expanded="false">
                Front-End
                <span class="arrow" aria-hidden="true"></span>
              </button>
              <div id="panel-frontend" class="accordion-panel">
                <button class="subcategory-btn" data-sub="vue">Vue</button>
              </div>
            </div>

            <!-- Back-End -->
            <div class="accordion-item">
              <button class="accordion-header" type="button" data-target="panel-backend" aria-expanded="false">
                Back-End
                <span class="arrow" aria-hidden="true"></span>
              </button>
              <div id="panel-backend" class="accordion-panel">
                <button class="subcategory-btn" data-sub="laravel">Laravel</button>
              </div>
            </div>

            <!-- CMS -->
            <div class="accordion-item">
              <button class="accordion-header" type="button" data-target="panel-cms" aria-expanded="false">
                CMS & E-Commerce Platforms
                <span class="arrow" aria-hidden="true"></span>
              </button>
              <div id="panel-cms" class="accordion-panel">
                <button class="subcategory-btn" data-sub="Content Management Systems">Content Management Systems</button>
                <button class="subcategory-btn" data-sub="E-Commerce Solutions">E-Commerce Solutions</button>
              </div>
            </div>

          </div>
        </div>
      </aside>

      <!-- Content -->
      <main class="col-md-9">
        <div id="tech-content-wrapper">
          <h2 id="tech-title" class="mb-2"
              style="font-size: 24px; font-weight: 600; font-family: 'Poppins', sans-serif; color: black;">
              Select a subcategory
          </h2>
          <p id="tech-desc" class="text-muted mb-4">Choose a subcategory from the sidebar to view technologies.</p>
          <div id="tech-content" class="row g-3 tech-content">
            <div class="col-12 text-muted">No subcategory selected.</div>
          </div>
        </div>
      </main>
    </div>
  </div>

  {{-- Styles --}}
  <style>
    /* layout */
    .sidebar { border-right: 1px solid #e6e6e6; padding-right: 15px; }
    .accordion-item { margin-bottom: 12px; }

    /* header button */
    .accordion-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      text-align: left;
      padding: 12px 14px;
      background: #f8f9fa;
      border: none;
      border-radius: 6px;
      font-weight: 700;
      cursor: pointer;
      transition: background .2s ease, color .2s ease;
    }
    .accordion-header:hover { background: #eef1f5; }

    .accordion-header .arrow {
      display: inline-block;
      width: 0.7rem;
      height: 0.7rem;
      border-right: .16rem solid currentColor;
      border-top: .16rem solid currentColor;
      transform: rotate(45deg);
      margin-left: 8px;
      transition: transform .3s ease;
    }

    .accordion-header[aria-expanded="true"] .arrow {
      transform: rotate(-135deg); /* points up when expanded */
    }

    .accordion-panel {
      margin-top: 8px;
      padding-left: 8px;
      max-height: 0;
      overflow: hidden;
      opacity: 0;
      transition: all 0.35s ease;
    }
    .accordion-panel.show {
      max-height: 500px;
      opacity: 1;
    }

    /* subcategory btn */
    .subcategory-btn {
      display: block;
      width: 100%;
      text-align: left;
      padding: 9px 12px;
      margin-bottom: 8px;
      background: #fff;
      border: 1px solid #ececec;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
      transition: background .2s, color .2s, border-color .2s;
    }
    .subcategory-btn:hover { background: #f7f7f8; }
    .subcategory-btn.active {
      background: #052C58;
      color: white;
      border-color: #052C58;
    }

    /* tech cards */
    .tech-card {
      display: block;
      background: #fff;
      border: 1px solid #eee;
      border-radius: 10px;
      padding: 18px 12px;
      text-align: center;
      transition: transform .18s, box-shadow .18s;
      height: 100%;
      text-decoration: none;
      color: inherit;
    }
    .tech-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }
    .tech-card img { width: 48px; height: 48px; object-fit: contain; margin-bottom: 8px; }
    .tech-card .name { font-weight: 600; font-size: 14px; color: #222; }

    /* Banner Section */
    .banner {
      background: linear-gradient(135deg, #0d1b3d, #0d2f4f);
      color: #fff;
      text-align: center;
      padding: 80px 20px;
    }
    .banner h2 {
      font-size: 20px;
      font-weight: 600;
      color: #00a651;
      margin-bottom: 15px;
    }
    .banner h1 {
      font-size: 40px;
      font-weight: 800;
      line-height: 1.3;
      margin-bottom: 40px;
    }
    .categories {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
      max-width: 1000px;
      margin: 0 auto;
    }
    .pill {
      padding: 10px 22px;
      border-radius: 30px;
      font-size: 16px;
      font-weight: 500;
      border: 2px solid;
      background: transparent;
      color: #fff;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .pill.blue { border-color: #2c7be5; }
    .pill.orange { border-color: #f77e21; }
    .pill.green { border-color: #28a745; }
    .pill.purple { border-color: #6f42c1; }
    .pill.teal { border-color: #20c997; }
    .pill:hover { background: #fff; color: #000; }

    /* responsive */
    @media (max-width: 767px) {
      aside.col-md-3, main.col-md-9 { width: 100%; }
      .sidebar { border-right: 0; padding-right: 0; margin-bottom: 18px; }
      .banner h1 { font-size: 28px; }
      .categories { gap: 10px; }
      .pill { font-size: 14px; padding: 8px 18px; }
    }
  </style>

  {{-- Script --}}
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const techData = @json($techData ?? (object)[]);
    const contactPageUrl = "{{ url('/contact') }}";

    // Accordion
    document.querySelectorAll('.accordion-header').forEach(btn => {
      btn.addEventListener('click', function () {
        const targetId = btn.getAttribute('data-target');
        const panel = document.getElementById(targetId);

        document.querySelectorAll('.accordion-panel').forEach(p => {
          if (p !== panel) {
            p.classList.remove('show');
            const hdr = document.querySelector('.accordion-header[data-target="' + p.id + '"]');
            if (hdr) hdr.setAttribute('aria-expanded', 'false');
          }
        });

        const isShown = panel.classList.contains('show');
        if (isShown) {
          panel.classList.remove('show');
          btn.setAttribute('aria-expanded', 'false');
        } else {
          panel.classList.add('show');
          btn.setAttribute('aria-expanded', 'true');
        }
      });
    });

    // Render Tech Cards
    function renderTechCards(key) {
      const content = document.getElementById('tech-content');
      const title = document.getElementById('tech-title');
      const desc  = document.getElementById('tech-desc');

      content.innerHTML = '';

      const labelMap = {
        general: 'General-purpose',
        mobile: 'Mobile App',
        vue: 'Vue',
        laravel: 'Laravel',
        "Content Management Systems": "Content Management Systems",
        "E-Commerce Solutions": "E-Commerce Solutions"
      };

      title.textContent = labelMap[key] || (key.charAt(0).toUpperCase() + key.slice(1));
      desc.textContent = 'Technologies for ' + title.textContent + '.';

      if (!techData || !techData[key] || !Array.isArray(techData[key]) || techData[key].length === 0) {
        content.innerHTML = '<div class="col-12 text-muted">No technologies found for this subcategory.</div>';
        return;
      }

      techData[key].forEach(t => {
        const col = document.createElement('div');
        col.className = 'col-xl-3 col-lg-4 col-md-4 col-sm-6 mb-4';

        const link = document.createElement('a');
        link.className = 'tech-card';
        link.href = t.url ? t.url : contactPageUrl;
        link.target = t.url ? '' : '_self';

        const img = document.createElement('img');
        img.src = t.icon || 'https://via.placeholder.com/48?text=?';
        img.alt = t.name || 'icon';

        const name = document.createElement('div');
        name.className = 'name';
        name.textContent = t.name || 'Unknown';

        link.appendChild(img);
        link.appendChild(name);
        col.appendChild(link);
        content.appendChild(col);
      });
    }

    // Subcategory clicks
    function clearActiveSubbuttons() {
      document.querySelectorAll('.subcategory-btn').forEach(b => b.classList.remove('active'));
    }

    document.addEventListener('click', function (ev) {
      const btn = ev.target.closest('.subcategory-btn');
      if (!btn) return;

      clearActiveSubbuttons();
      btn.classList.add('active');

      const parentPanel = btn.closest('.accordion-panel');
      if (parentPanel && !parentPanel.classList.contains('show')) {
        parentPanel.classList.add('show');
        const hdr = document.querySelector('.accordion-header[data-target="' + parentPanel.id + '"]');
        if (hdr) hdr.setAttribute('aria-expanded', 'true');
      }

      const subkey = btn.getAttribute('data-sub');
      renderTechCards(subkey);
    });

    // Default open first
    (function openDefault() {
      const firstPanel = document.querySelector('.accordion-item .accordion-panel');
      const firstBtn = firstPanel ? firstPanel.querySelector('.subcategory-btn') : null;
      if (firstPanel) {
        firstPanel.classList.add('show');
        const hdr = document.querySelector('.accordion-header[data-target="' + firstPanel.id + '"]');
        if (hdr) hdr.setAttribute('aria-expanded', 'true');
      }
      if (firstBtn) {
        firstBtn.classList.add('active');
        renderTechCards(firstBtn.getAttribute('data-sub'));
      }
    })();

  });
  </script>

@endsection
