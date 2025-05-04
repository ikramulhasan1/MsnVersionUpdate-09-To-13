@extends('web.layouts.master')
@section('content')


  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Glide.js CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css">

  <style>
    /* Hero Section */
    .caseStudy-hero-section {
    background-color: #0A2540;
    color: white;
    padding: 50px 0 40px;
    position: relative;
    }

    .caseStudy-hero-section .title {
    font-size: 40px;
    font-weight: 700;
    line-height: 1.3;
    }

    .caseStudy-btn-pdf {
    background-color: #FF5A1F;
    color: white;
    padding: 12px 20px;
    font-weight: 500;
    border-radius: 6px;
    border: none;
    display: flex;
    align-items: center;
    gap: 10px;
    }

    .caseStudy-btn-pdf img {
    height: 20px;
    }

    .caseStudy-btn-query {
    background-color: transparent;
    color: white;
    border: 1.5px solid white;
    padding: 12px 20px;
    font-weight: 500;
    font-size: 18px;
    border-radius: 6px;
    transition: 0.3s ease-in-out;
    }

    .caseStudy-btn-query:hover {
    background-color: rgba(255, 255, 255, 0.1);
    }

    .caseStudy-case-menu {
    background: white;
    color: #0A2540;
    position: absolute;
    top: 40px;
    right: 60px;
    border-radius: 10px;
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
    padding: 20px 25px;
    width: 260px;
    }

    .caseStudy-case-menu h6 {
    font-size: 21px;
    font-weight: 600;
    margin-bottom: 15px;
    }

    .caseStudy-case-menu ul {
    list-style: none;
    padding-left: 0;
    margin: 0;
    }

    .caseStudy-case-menu ul li {
    font-size: 15px;
    margin-bottom: 10px;
    position: relative;
    padding-left: 14px;
    }

    .caseStudy-case-menu ul li::before {
    content: "•";
    color: #0A2540;
    position: absolute;
    left: 0;
    }

    /* Client Section */
    .caseStudy-top-client-section {
    background-color: #F1F6FE;
    /* padding: 60px 0; */
    }

    .caseStudy-client-section {

    padding: 60px 0;
    }

    .caseStudy-section-header {
    position: relative;
    font-size: 35px;
    font-weight: 700;
    color: #262E36;
    display: inline-block;
    margin-top: 25px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    }

    .caseStudy-section-header::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0%;
    /* Centers the 50% width line */
    width: 120px;
    border-bottom: 5px solid #2ED47A;
    }

    .caseStudy-section-h1-title {
    position: relative;
    font-size: 41px;
    font-weight: 900;
    color: #262E36;
    display: inline-block;
    margin-top: 25px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    }

    .caseStudy-section-h1-title::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0%;
    /* Centers the 50% width line */
    width: 120px;
    border-bottom: 5px solid #2ED47A;
    margin-bottom: 30;

    }


    .caseStudy-explore-h1-title {
    position: relative;
    font-size: 41px;
    font-weight: 900;
    color: #ffffff;
    /* display: inline-block; */
    margin-top: 25px;
    margin-bottom: 8px;
    }

    .caseStudy-explore-h1-title::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 45%;
    /* Centers the 50% width line */
    width: 120px;
    border-bottom: 5px solid #2ED47A;
    }

    .caseStudy-client-text {
    font-size: 16px;
    color: #333;
    line-height: 1.7;
    }

    .caseStudy-tech-info h6 {
    position: relative;
    font-size: 16px;
    font-weight: 600;
    color: #0A2540;
    /* border-bottom: 2px solid #2ED47A; */
    display: inline-block;
    margin-top: 25px;
    margin-bottom: 8px;
    padding-bottom: 10px;
    }

    .caseStudy-tech-info h6::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0%;
    /* Centers the 50% width line */
    width: 40px;
    border-bottom: 3px solid #2ED47A;
    }

    .caseStudy-tech-info p {
    font-size: 16px;
    color: #444;
    margin-bottom: 0;
    line-height: 1.6;
    }

    @media (max-width: 768px) {
    .caseStudy-case-menu {
      display: none;
    }

    .caseStudy-hero-section .title {
      font-size: 28px;
    }
    }


    .caseStudy-section-box {

    margin-bottom: 40px;
    }

    .caseStudy-subheading {
    font-weight: 600;
    margin-top: 25px;
    margin-bottom: 10px;
    font-size: 20px;
    }

    .caseStudy-check-list {
    list-style: none;
    padding-left: 0;
    }

    .caseStudy-check-list li {
    position: relative;
    padding-left: 30px;
    margin-bottom: 12px;
    font-size: 16px;
    color: #333;
    }

    .caseStudy-check-list li::before {
    content: "\f26e";
    font-family: "Bootstrap-icons";
    color: #28a745;
    font-size: 18px;
    position: absolute;
    left: 0;
    top: 2px;
    }

    .caseStudy-section-image {
    border-radius: 14px;
    margin: 30px 0;
    width: 100%;
    height: auto;
    }

    /* SERVICES BOX */
    .caseStudy-services-involved {
    background-color: #2958A5;
    border-radius: 24px;
    padding: 30px 40px;
    color: #fff;
    margin-bottom: 60px;
    }

    .caseStudy-services-involved h4 {
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 20px;
    }

    .caseStudy-services-involved ul {
    padding-left: 0;
    list-style: none;
    }

    .caseStudy-services-involved li {
    margin-bottom: 12px;
    font-weight: 600;
    font-size: 24px;
    }

    .caseStudy-services-involved li::before {
    content: '\2713';
    color: #0EE4AA;
    font-weight: bold;
    margin-right: 10px;
    }

    .caseStudy-services-involved a {
    color: #fff;
    text-decoration: underline;
    }


    /* .section-divider {
      width: 70px;
      height: 3px;
      background-color: #0ee4aa;
      margin-bottom: 30px;
    } */

    .caseStudy-tech-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 24px;
    }

    .caseStudy-tech-item {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    padding: 20px;
    text-align: center;
    transition: transform 0.2s ease;
    }

    .caseStudy-tech-item:hover {
    transform: translateY(-4px);
    }

    .caseStudy-tech-item img {
    height: 40px;
    margin-bottom: 10px;
    }

    .caseStudy-tech-label {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    }



    h2 {
    text-align: center;
    padding: 1.5rem 0 0.5rem;
    }

    .caseStudy-glide {
    width: 90%;
    max-width: 1000px;
    margin: 2rem auto;
    }

    .caseStudy-glide__slide {
    background: #ffffff;
    border-radius: 0px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 0px;
    text-align: center;
    }

    .caseStudy-glide__slide img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 0px;
    margin-bottom: 1rem;
    }

    .caseStudy-glide__arrows {
    text-align: center;
    margin-top: 1rem;
    }

    .caseStudy-glide__arrow {
    background: #ffffff;
    color: rgb(0, 0, 0);
    border: none;
    padding: 8px 16px;
    margin: 0 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    }

    /* result */
    .caseStudy-results-section {
    max-width: 900px;
    margin: auto;
    }

    .caseStudy-result-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    }

    .caseStudy-result-icon {
    min-width: 40px;
    height: 40px;
    background: #eaf3ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
    }

    .caseStudy-result-icon::before {
    content: '✔';
    font-size: 18px;
    color: #003366;
    }

    .caseStudy-result-text h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    }

    .caseStudy-result-text p {
    margin: 5px 0 0;
    font-size: 0.95rem;
    color: #444;
    }

    .caseStudy-result-image {
    margin: 2rem 0;
    text-align: center;
    }

    .caseStudy-result-image img {
    width: 100%;
    max-width: 100%;
    border-radius: 12px;
    object-fit: cover;
    }

    @media (max-width: 600px) {
    .caseStudy-result-item {
      flex-direction: row;
      align-items: flex-start;
    }

    .caseStudy-result-icon {
      margin-top: 4px;
    }
    }


    .caseStudy-download-section {
    background-color: #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem 3rem;
    max-width: 100%;
    /* margin: 2rem auto; */
    border-radius: 0px;
    }

    .caseStudy-download-left {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    }

    .caseStudy-pdf-icon {
    width: 50px;
    height: 50px;
    background-image: url('https://img.icons8.com/ios/50/pdf--v1.png');
    background-size: contain;
    background-repeat: no-repeat;
    }

    .caseStudy-download-text {
    font-size: 31px;
    font-weight: 700;
    color: #1f2937;
    }

    .caseStudy-download-button {
    background-color: #ff5a00;
    color: white;
    font-weight: 600;
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: background-color 0.2s ease;
    }

    .caseStudy-download-button:hover {
    background-color: #e94e00;
    }

    .caseStudy-download-button::after {
    content: "→";
    font-weight: 600;
    font-size: 1.2rem;
    }

    @media (max-width: 768px) {
    .caseStudy-download-section {
      flex-direction: column;
      text-align: center;
      gap: 1.5rem;
    }
    }


    .caseStudy-section {
    max-width: 900px;
    margin: auto;
    }

    .caseStudy-section h2 {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    }

    .caseStudy-section p {
    font-size: 1rem;
    line-height: 1.6;
    }

    .caseStudy-section p strong {
    font-weight: 600;
    }

    .caseStudy-integrations {
    margin-top: 1rem;
    margin-bottom: 1rem;
    }

    .caseStudy-integration-item {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    margin: 0.4rem 0;
    }

    .caseStudy-dot {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    margin-top: 0.6rem;
    flex-shrink: 0;
    }

    .integration-text {
    line-height: 1.6;
    }
  </style>
  <div class="d-none d-lg-block" id="sticky-case" style="position: sticky; top: 10%; z-index: 10;">
    <div class="caseStudy-case-menu">
    <h6>In this case study</h6>
    <ul>
      <li>The Client</li>
      <li>The Challenges</li>
      <li>Solutions We Offered</li>
      <li>Key Deliverables</li>
      <li>Services Involved</li>
      <li>Technology Stack</li>
      <li>Results</li>
    </ul>
    </div>
  </div>
  <section class="caseStudy-hero-section">
    <div class="container">
    <div class="row">
      <div class="col-lg-8">
      <span class="badge bg-success mb-3">CASE STUDY</span>
      <h1 class="title">{{ $case_study->main_title }}</h1>
      <div class="d-flex flex-wrap gap-3 mt-4">
        <a href="#" class="caseStudy-btn-pdf">
        <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="PDF icon">
        Download this case study
        </a>
        <a href="#" class="caseStudy-btn-query">Send Your Query →</a>
      </div>
      </div>
    </div>


    </div>
    <!-- Case Study Menu -->
    <!-- <div class="caseStudy-case-menu d-none d-lg-block">
      <h6>In this case study</h6>
      <ul>
      <li>The Client</li>
      <li>The Challenges</li>
      <li>Solutions We Offered</li>
      <li>Key Deliverables</li>
      <li>Services Involved</li>
      <li>Technology Stack</li>
      <li>Results</li>
      </ul>
      </div> -->
  </section>

  <!-- <div class="row"> -->
  <!-- HERO SECTION -->
  <div class="">
    <div class="caseStudy-top-client-section">
    <section class=" container">
      <!-- CLIENT SECTION -->
      <div class="caseStudy-client-section col-lg-8">

      <div class="row">
        <div class="col-lg-8">
        <div class="caseStudy-section-header">{{ $case_study->the_client }}</div>
        <p class="caseStudy-client-text">{!! $case_study->the_client_desc !!}</p>
        </div>
        <div class="col-lg-4 caseStudy-tech-info">
        <h6>Industry</h6>
        <p>{{ $case_study->industry }}</p>
        <h6>Tech Stack</h6>
        <p>{{ $case_study->tech_stack }}</p>
        </div>
      </div>
      </div>
    </section>
    </div>

    <section class=" container" style="background-color: #ffffff;">
    <div class="col-lg-8">
      <!-- The Challenges Section -->
      <div class="caseStudy-section-box">
      <h2 class="caseStudy-section-h1-title">The Challenges</h2>

      <div class="caseStudy-subheading">Limited Technical Resources</div>
      <ul class="caseStudy-check-list">
        <li>The client struggled to find and hire skilled developers locally due to talent shortages and high
        recruitment costs.</li>
      </ul>

      <div class="caseStudy-subheading">Project Delays</div>
      <ul class="caseStudy-check-list">
        <li>The incomplete platform lacked essential digital tools, including e-signatures, identity verification,
        secure communication, and advanced reporting.</li>
      </ul>

      <img src="https://www.capitalnumbers.com/images/case-studies-details/case-study-185/cs185-challenge.png"
        alt="Team discussion" class="caseStudy-section-image img-fluid">

      <div class="caseStudy-subheading">Operational Inefficiencies</div>
      <ul class="caseStudy-check-list">
        <li>Manual processes such as in-person meetings and paperwork slowed down financial advisory workflows,
        reducing efficiency and scalability.</li>
        <li>Security risks emerged due to reliance on physical document handling, increasing compliance concerns.
        </li>
      </ul>

      <p>These challenges hampered the company’s ability to launch and scale its platform, delaying its market
        entry and business growth.</p>
      </div>

      <!-- Solutions We Offered Section -->
      <div class="caseStudy-section-box">
      <h2 class="caseStudy-section-h1-title">Solutions We Offered</h2>

      <p>Capital Numbers deployed a dedicated IT Staff Augmentation team to address the client’s technical and
        operational challenges through:</p>

      <div class="caseStudy-subheading">Understanding Client Needs</div>
      <ul class="caseStudy-check-list">
        <li>Conducted an in-depth analysis of project objectives, technical roadblocks, and business goals to
        tailor a strategic development plan.</li>
      </ul>

      <div class="caseStudy-subheading">Rapid Resource Allocation</div>
      <ul class="caseStudy-check-list">
        <li>Quickly onboarded highly skilled developers proficient in the required technologies, ensuring seamless
        knowledge transfer and minimal project downtime.</li>
      </ul>

      <img src="https://www.capitalnumbers.com/images/case-studies-details/case-study-185/cs185-solutions.png"
        alt="Client discussion" class="caseStudy-section-image img-fluid">

      <div class="caseStudy-subheading">Seamless Integration</div>
      <ul class="caseStudy-check-list">
        <li>Integrated our developers directly into the client’s team, working collaboratively to accelerate
        development without disrupting existing workflows.</li>
      </ul>

      <div class="caseStudy-subheading">Flexible Scaling</div>
      <ul class="caseStudy-check-list">
        <li>Allowed the client to dynamically adjust team size and expertise based on project needs, ensuring
        agility and cost-effectiveness.</li>
      </ul>
      </div>

      <!-- Key Deliverables -->
      <div class="caseStudy-section pb-5">
      <h2 class="caseStudy-section-h1-title">Key Deliverables</h2>

      <p>Capital Numbers successfully delivered the following:</p>

      <p><strong>Code Optimization:</strong> Enhanced the platform’s stability and performance (React Frontend and
        Rails Backend).</p>

      <p><strong>Microservices Architecture:</strong> Developed a scalable and adaptable infrastructure to support
        future growth.</p>

      <p><strong>Crucial Integrations:</strong></p>
      <div class="caseStudy-integrations">
        <div class="caseStudy-integration-item">
        <div class="caseStudy-dot"></div>
        <div class="integration-text"><strong>DocuSign:</strong> Enabled secure and legally binding electronic
          signatures.</div>
        </div>
        <div class="caseStudy-integration-item">
        <div class="caseStudy-dot"></div>
        <div class="integration-text"><strong>Singpass & iAM Smart:</strong> Automated client identity
          verification, ensuring regulatory compliance.</div>
        </div>
        <div class="caseStudy-integration-item">
        <div class="caseStudy-dot"></div>
        <div class="integration-text"><strong>Twilio:</strong> Integrated secure video conferencing for remote
          client-advisor interactions.</div>
        </div>
        <div class="caseStudy-integration-item">
        <div class="caseStudy-dot"></div>
        <div class="integration-text"><strong>Xero:</strong> Automated financial operations, including invoicing
          and accounting.</div>
        </div>
        <div class="caseStudy-integration-item">
        <div class="caseStudy-dot"></div>
        <div class="integration-text"><strong>SendGrid & Outlook:</strong> Enhanced email communication
          reliability.</div>
        </div>
        <div class="caseStudy-integration-item">
        <div class="caseStudy-dot"></div>
        <div class="integration-text"><strong>Chartkick:</strong> Implemented data-rich dashboards for
          performance tracking and insights.</div>
        </div>
      </div>

      <p><strong>Advanced Security Measures:</strong> Applied AES-256-GCM and SHA256 encryption to ensure data
        security and regulatory compliance.</p>

      <p><strong>AWS Cloud Deployment:</strong> Provided a reliable, scalable, and secure cloud infrastructure for
        seamless platform operation.</p>
      </div>
    </div>

    <div class="col-lg-8">
      <!-- SERVICES INVOLVED SECTION -->
      <div class="caseStudy-services-involved">
      <h4>Services Involved</h4>
      <ul>
        <li><a href="#">Financial Software Development</a></li>
        <li><a href="#">React Development Services</a></li>
        <li><a href="#">Cloud Engineering Services</a></li>
      </ul>
      </div>

      <!-- TECHNOLOGY SECTION -->
      <div class="technology-section mb-5">
      <h3 class="caseStudy-section-h1-title">Technologies Used</h3>
      <!-- <div class="section-divider"></div> -->

      <div class="caseStudy-tech-grid">
        <div class="caseStudy-tech-item">
        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg" alt="React">
        <div class="caseStudy-tech-label">React</div>
        </div>
        <div class="caseStudy-tech-item">
        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/rails/rails-plain.svg" alt="Rails">
        <div class="caseStudy-tech-label">Rails</div>
        </div>
        <div class="caseStudy-tech-item">
        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postgresql/postgresql-original.svg"
          alt="PostgreSQL">
        <div class="caseStudy-tech-label">PostgreSQL</div>
        </div>
        <div class="caseStudy-tech-item">
        <img src="https://www.capitalnumbers.com/images/technologies-icons/docusign.svg" alt="DocuSign">
        <div class="caseStudy-tech-label">DocuSign</div>
        </div>
        <div class="caseStudy-tech-item">
        <img
          src="https://img.icons8.com/external-flaticons-lineal-color-flat-icons/64/external-password-cyber-security-flaticons-lineal-color-flat-icons-2.png"
          alt="iAM Smart">
        <div class="caseStudy-tech-label">iAM Smart</div>
        </div>
        <div class="caseStudy-tech-item">
        <img src="https://www.capitalnumbers.com/images/technologies-icons/singpass.svg" alt="Singpass">
        <div class="caseStudy-tech-label">Singpass</div>
        </div>
        <div class="caseStudy-tech-item">
        <img src="https://www.capitalnumbers.com/images/technologies-icons/aws.svg" alt="AWS">
        <div class="caseStudy-tech-label">AWS</div>
        </div>
      </div>
      </div>
      <!-- RESULTS SECTION -->
      <section class="caseStudy-results-section">
      <h2 class="caseStudy-section-h1-title">Results</h2>

      <div class="caseStudy-result-item">
        <div class="caseStudy-result-icon"></div>
        <div class="caseStudy-result-text">
        <h4>Rapid Project Completion</h4>
        <p>The platform was fully developed and launched within 12 months, significantly reducing
          time-to-market.</p>
        </div>
      </div>

      <div class="caseStudy-result-item">
        <div class="caseStudy-result-icon"></div>
        <div class="caseStudy-result-text">
        <h4>Cost Savings</h4>
        <p>The client avoided high hiring and operational costs, achieving cost-efficient development.</p>
        </div>
      </div>

      <div class="caseStudy-result-item">
        <div class="caseStudy-result-icon"></div>
        <div class="caseStudy-result-text">
        <h4>Operational Efficiency</h4>
        <p>Automated processes drastically reduced manual tasks, allowing advisors to focus on client engagement
          and strategy.</p>
        </div>
      </div>

      <div class="caseStudy-result-image">
        <img src="https://www.capitalnumbers.com/images/case-studies-details/case-study-185/cs185-results.png"
        alt="Handshake Image" />
      </div>

      <div class="caseStudy-result-item">
        <div class="caseStudy-result-icon"></div>
        <div class="caseStudy-result-text">
        <h4>Improved Security & Compliance</h4>
        <p>Enhanced data security and regulatory adherence through advanced encryption and digital identity
          verification.</p>
        </div>
      </div>

      <div class="caseStudy-result-item">
        <div class="caseStudy-result-icon"></div>
        <div class="caseStudy-result-text">
        <h4>Scalable and Adaptable Solution</h4>
        <p>A microservices architecture and AWS deployment ensured long-term scalability and flexibility.</p>
        </div>
      </div>
      </section>
    </div>
    </section>

  </div>
  <!-- DOWNLOAD PDF SECTION -->
  <section class="caseStudy-download-section ">
    <div class="caseStudy-download-left">
    <div class="caseStudy-pdf-icon"></div>
    <div class="caseStudy-download-text">Download this case study in PDF</div>
    </div>
    <a href="case-study.pdf" class="caseStudy-download-button" download>Download PDF</a>
  </section>
  <section style="background-color: #0A1D4D;" class="py-5 case-end">

    <h2 class="caseStudy-explore-h1-title m-0">Explore More Case Studies</h2>
    <div class="caseStudy-glide">
    <div class="glide__track" data-glide-el="track">
      <ul class="glide__slides">
      <li class="caseStudy-glide__slide">
        <img src="https://www.capitalnumbers.com/images/case-study-home/new-thumb-148.jpg" alt="">
        <h3>Slide One</h3>
        <p>Description for slide one goes here.</p>
      </li>
      <li class="caseStudy-glide__slide">
        <img src="https://www.capitalnumbers.com/images/case-study-home/new-thumb-143.jpg" alt="">
        <h3>Slide Two</h3>
        <p>Description for slide two goes here.</p>
      </li>
      <li class="caseStudy-glide__slide">
        <img src="https://www.capitalnumbers.com/images/case-study-home/new-thumb-138.jpg" alt="">
        <h3>Slide Three</h3>
        <p>Description for slide three goes here.</p>
      </li>
      <li class="caseStudy-glide__slide">
        <img src="https://www.capitalnumbers.com/images/case-study-home/new-thumb-172.jpg" alt="">
        <h3>Slide Four</h3>
        <p>Description for slide four goes here.</p>
      </li>
      </ul>
    </div>

    <div class="caseStudy-glide__arrows" data-glide-el="controls">
      <button class="caseStudy-glide__arrow caseStudy-glide__arrow--left" data-glide-dir="<">❮</button>
      <button class="caseStudy-glide__arrow caseStudy-glide__arrow--right" data-glide-dir=">">❯</button>
    </div>
    </div>



  </section>
  <!-- Glide.js JS -->
  <script src="https://cdn.jsdelivr.net/npm/@glidejs/glide"></script>
  <script>
    new Glide('.caseStudy-glide', {
    type: 'carousel',
    perView: 3,
    focusAt: 'center',
    gap: 20,
    autoplay: 3000,
    breakpoints: {
      1024: { perView: 2 },
      600: { perView: 1 }
    }
    }).mount();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    const sticky = document.getElementById('sticky-case');
    const endTrigger = document.querySelector('.case-end');

    const observer = new IntersectionObserver(
      ([entry]) => {
      if (entry.isIntersecting) {
        // Stop sticking
        sticky.style.position = 'absolute';
        sticky.style.top = (entry.target.offsetTop - sticky.offsetHeight) + 'px';
      } else {
        // Keep sticky
        sticky.style.position = 'sticky';
        sticky.style.top = '10%';
      }
      },
      {
      root: null,
      threshold: 0,
      }
    );

    observer.observe(endTrigger);
    });
  </script>
  <!-- </div> -->

@endsection