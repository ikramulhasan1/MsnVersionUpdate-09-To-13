-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 04, 2025 at 02:20 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u154620235_msn`
--

-- --------------------------------------------------------

--
-- Table structure for table `abouts`
--

CREATE TABLE `abouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `video_id` varchar(191) DEFAULT NULL,
  `mission_title` varchar(191) DEFAULT NULL,
  `mission_desc` text DEFAULT NULL,
  `vision_title` varchar(191) DEFAULT NULL,
  `vision_desc` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `abouts`
--

INSERT INTO `abouts` (`id`, `title`, `slug`, `description`, `image_path`, `video_id`, `mission_title`, `mission_desc`, `vision_title`, `vision_desc`, `status`, `created_at`, `updated_at`) VALUES
(3, 'About Us', 'about-us', '<p>MSN SoftTech is a leading agency specializing in<b> software development, web design, mobile app development, and SEO services</b>. Over the last <b>10 years, </b>we have been delivering top-notch IT solutions both<b> locally and internationally,</b> helping businesses establish a strong digital presence and achieve sustainable growth.<p>&nbsp;</p><p><b>Our Achievements &amp; Experience:</b></p><ul>\r\n	<li>Successfully served <b>3,500+ top-rated companies</b> worldwide.</li>\r\n	<li>Expertise in <b>custom software solutions,</b> e-commerce development, and digital marketing strategies.</li>\r\n	<li>Worked with businesses across various industries, providing tailored solutions for enhanced performance.</li>\r\n</ul></p>\n', 'about.png', 'Ukf-43-hpAU', 'Our Mission', '<p>Our mission is to empower businesses with cutting-edge technology solutions that drive innovation, efficiency, and growth. We are committed to delivering high-quality software, web, and digital solutions that help companies thrive in a competitive market.</p>', 'Our Vision', '<p>Our vision is to become a global leader in IT solutions by driving digital transformation and empowering businesses with innovative technology. We aspire to create a future where businesses of all sizes can leverage advanced software, web, and digital solutions to achieve sustainable growth and success.</p>', 1, '2021-11-07 14:26:20', '2025-02-26 22:53:59');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `video_id` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `category_id`, `title`, `slug`, `description`, `image_path`, `video_id`, `status`, `created_at`, `updated_at`) VALUES
(7, 1, 'Why MSN SoftTech is the Best Web Development Company for Your Business Growth', 'why-msn-softtech-is-the-best-web-development-company-for-your-business-growth', '<p><b>Introduction</b> In today&rsquo;s digital-driven world, a strong online presence is essential for success. Choosing the right <b>web development company</b> can significantly impact your business&rsquo;s growth, visibility, and revenue. At <b>MSN SoftTech</b>, we specialize in creating high-performance, user-friendly, and SEO-optimized websites tailored to meet your business needs.<p><b>What is a Web Development Company?</b> A <b>web development company</b> is a team of experts that design, develop, and maintain websites. Whether it\'s a small business website or a large-scale enterprise platform, a professional <b>web development company</b> ensures seamless functionality, high security, and a visually appealing design that enhances user engagement.</p><h2><b>Why MSN SoftTech Stands Out as a Leading Web Development Company</b></h2><ol>\r\n	<li><b>Expertise in Advanced Web Development Technologies</b></li>\r\n	<li>Our team at <b>MSN SoftTech</b> excels in <b>PHP, Laravel, Vue.js, <a href=\"https://www.fiverr.com/advisortruerevi/build-design-or-redesign-your-professional-wordpress-website\" target=\"_blank\">WordPress, and e-commerce development</a></b>, ensuring that your website meets the latest industry standards.</li>\r\n	<li><b>Customized and Scalable Solutions</b></li>\r\n	<li>We believe that no two businesses are the same. Our <a href=\"https://www.fiverr.com/advisortruerevi/do-expert-full-stack-web-developer-php-laravel-vuejs-react-and-nodejs\" target=\"_blank\"><b>custom web development services</b></a> ensure that your website aligns with your brand, audience, and industry needs while being scalable for future growth.</li>\r\n	<li><b>SEO-Optimized Websites for Maximum Visibility</b></li>\r\n	<li>A visually appealing website is not enough&mdash;it must be optimized for search engines. Our <b>SEO-friendly web development</b> techniques help improve your website&rsquo;s rankings on <b>Google</b>, drive organic traffic, and increase conversions.</li>\r\n	<li><b>Mobile-Responsive and Fast-Loading Websites</b></li>\r\n	<li>With most users browsing on mobile, we create <b>mobile-friendly web designs</b> that work seamlessly across all devices. Plus, we optimize for speed, reducing bounce rates and improving user experience.</li>\r\n	<li><a href=\"https://www.fiverr.com/advisortruerevi/expert-php-laravel-developer\" target=\"_blank\"><b>E-commerce Website Development for Online Success</b></a></li>\r\n	<li>Want to start an online store? Our <b>e-commerce development services</b> include <b>WooCommerce, Shopify, and custom e-commerce platforms</b>, with secure payment gateways and easy navigation for enhanced shopping experiences.</li>\r\n	<li><b>Ongoing Support, Security, and Maintenance</b></li>\r\n	<li>A well-maintained website runs efficiently and securely. We provide <b>24/7 technical support, security updates, and performance monitoring</b> to ensure your website stays ahead of the competition.</li>\r\n</ol><h3><b>Our Proven Web Development Process</b></h3><ol>\r\n	<li><b>Consultation &amp; Strategy:</b> Understanding your business goals and market positioning.</li>\r\n	<li><b>Creative UI/UX Design:</b> Designing visually engaging, user-friendly interfaces.</li>\r\n	<li><b>Robust Development &amp; Coding:</b> Implementing cutting-edge technologies for seamless functionality.</li>\r\n	<li><b>SEO Implementation &amp; Performance Optimization:</b> Ensuring top search engine rankings and fast loading speeds.</li>\r\n	<li><b>Testing, Launch &amp; Continuous Support:</b> Deploying a bug-free, high-performing website with ongoing enhancements.</li>\r\n</ol><h3><b>Why Your Business Needs a Professional Web Development Company</b></h3><ul>\r\n	<li><b>Improved Search Rankings:</b> SEO-optimized websites gain higher <b>Google rankings</b> and attract more traffic.</li>\r\n	<li><b>Brand Credibility &amp; Trust:</b> A professional website enhances your business reputation.</li>\r\n	<li><b>Higher Conversion Rates:</b> Engaging, fast-loading, and mobile-friendly websites lead to more sales.</li>\r\n	<li><b>Competitive Advantage:</b> Stay ahead of competitors with cutting-edge web solutions.</li>\r\n</ul><p><b>Conclusion</b> If you&rsquo;re looking for a <b>top-rated web development company</b> that offers <b>SEO-friendly website development</b>, <b>custom design</b>, and <b>e-commerce solutions</b>, look no further than <b>MSN SoftTech</b>. We help businesses build a strong online presence and drive more leads through innovative and result-driven web development.</p><p><span class=\"marker\"><b>Get in Touch Today!</b></span><br>\r\nEmail: <b>support@msnsofttech.com</b></p></p>\n', 'Why MSN SoftTech is the Best Web Development Company for Your Business Growth (2)_1739301239.png', NULL, 1, '2025-02-11 18:42:46', '2025-02-11 19:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `article_categories`
--

CREATE TABLE `article_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_categories`
--

INSERT INTO `article_categories` (`id`, `title`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Web Development', 'web-development', NULL, 1, '2020-10-30 11:36:02', '2025-02-11 17:52:01');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `link` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `title`, `slug`, `description`, `image_path`, `link`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nomant', 'nomant', NULL, 'clients-1_1604081500.png', NULL, 1, '2020-10-30 12:07:54', '2020-10-30 12:11:40'),
(2, 'Muchmore', 'muchmore', NULL, 'clients-2_1604081536.png', 'https://www.hitechparks.com/', 1, '2020-10-30 12:12:16', '2020-11-21 01:53:22'),
(3, 'Bussinex', 'bussinex', NULL, 'clients-3_1604081554.png', NULL, 1, '2020-10-30 12:12:34', '2020-10-30 12:12:34'),
(4, 'Hitchau', 'hitchau', NULL, 'clients-4_1604081593.png', 'https://www.hitechparks.com/', 1, '2020-10-30 12:13:13', '2020-10-30 12:13:13');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`, `updated_at`) VALUES
(4, 'Habib', 'example@mail.com', '+0123456789', 'We Need Your Team Support', 'I\'ve been looking for this throughout the web and can\'t even find anyone else even asking this, let alone a solution...\r\n\r\nIs there a way to change the color of the highlight area within a text input when text is selected? Not the highlight border or the background, but the portion that appears around the text when you have the text actually selected.', 1, '2020-11-03 20:49:02', '2020-11-03 20:49:02'),
(5, 'Habib', 'admin@mail.com', '+0123456789', 'We Need Your Team Support', '<br/>', 1, '2020-11-03 20:50:35', '2020-11-03 20:50:35'),
(8, 'Hi Tech Parks', 'hitechparks@gmail.com', '', 'Need Pricing for Custom Order', 'Need Pricing for Custom Order', 1, '2020-11-24 23:27:54', '2020-11-24 23:27:54'),
(9, 'Hi Tech Parks', 'hitechparks@gmail.com', NULL, 'We Need Your Team Support', 'We Need Your Team Support', 1, '2020-11-25 00:00:16', '2020-11-25 00:00:16'),
(16, 'Mike Miguel Svensson', 'mike@monkeydigital.co', '89611927635', 'Social Ads Traffic by Country for msnsofttech.com', 'Hi there \r\nWe have a special connection with a reputable Network that gives us the possibility to offer Social Ads Country Targeted and niche traffic for just 10$ for 10000 Visits. \r\n \r\nDepending on the Country, we can send larger volumes of ads traffic. \r\n \r\nTry us today, we even use this for our SEO clients: \r\nhttps://www.monkeydigital.co/product/country-targeted-traffic/ \r\n \r\nor chat with us on Whatsapp: https://monkeydigital.co/whatsapp-us/ \r\n \r\nRegards \r\nMike Miguel Svensson\r\n \r\nmonkeydigital.co', 1, '2025-02-08 05:07:05', '2025-02-08 05:07:05'),
(17, 'Mike Jan Van Dijk', 'mike@monkeydigital.co', '81687731378', 'Collaboration Request', 'Hello, \r\n \r\nThis is Mike Roberts\r\nfrom Monkey Digital, \r\nI am reaching out to you like webmaster to webmaster, towards a mutual opportunity. How would you like to put our banners on your site and link back via your affiliate link towards hot selling services from our website, and earn a 35% residual income, month after month from any sales that comes in from your sites. \r\n \r\nThink about it, everyone needs SEO, this is a pretty major opportunity, We have over 12k affiliates already and our payouts are made each month, hefty payouts, last month we have reached 27280$ in payouts to our affiliates. \r\n \r\nIf interested, kindly chat with us: https://monkeydigital.co/affiliates-whatsapp/ \r\n \r\nOr sign up today: https://www.monkeydigital.co/join-our-affiliate-program/ \r\n \r\nCheers \r\nMike Jan Van Dijk\r\n \r\nmonkeydigital.co', 1, '2025-02-08 19:17:48', '2025-02-08 19:17:48'),
(18, 'Mike David De Vries', 'info@digitalxflow.com', '89259521349', 'Boost Your SEO with Country Targeted Backlinks!', 'Hi there, \r\n \r\nLooking to improve your website\'s local rankings? We offer Country Targeted Backlinks to help you dominate your niche. With backlinks from high-quality, local domains, your website will see increased relevance, traffic, and authority in your chosen region. \r\n \r\nCheck out our service here: \r\nhttps://www.digitalxflow.com/country-backlinks/ \r\nOr chat with us on WhatsApp: https://www.digitalxflow.com/whatsapp-us/ \r\n \r\n \r\nBest regards, \r\nMike David De Vries\r\n \r\nDgital X Flow Team', 1, '2025-02-09 19:31:52', '2025-02-09 19:31:52'),
(19, 'Louann Raven', 'raven.louann@gmail.com', '781457809', 'Dear msnsofttech.com Admin!', 'Are you still struggling to rank on Google? Our SEO experts can help. Contact https://hireseogeek.com/?src1=msnsofttech.com', 1, '2025-02-15 05:36:06', '2025-02-15 05:36:06'),
(20, 'Mike Torsten Smit', 'check-message4682@gmail.com', '81431213963', 'Semrush links for msnsofttech.com', 'Hi there \r\n \r\nHaving some bunch of links pointing to msnsofttech.com could have 0 value or worse for your website, It really doesn`t matter how many backlinks you have, what matters is the amount of keywords those websites rank for. That is the most important thing. Not the fake Moz DA or ahrefs DR score. That anyone can do these days. BUT the amount of ranking keywords the sites that link to you have. Thats it. \r\n \r\nHave such links point to your website and you will ROCK ! \r\n \r\nWe are offering this special service here: \r\nhttps://www.strictlydigital.net/product/semrush-backlinks/ \r\n \r\nIn doubts, or need more information, chat with us: https://www.strictlydigital.net/whatsapp-us/ \r\n \r\nKind regards \r\nMike Torsten Smit\r\n \r\nstrictlydigital.net \r\ninfo@strictlydigital.net', 1, '2025-02-15 14:34:38', '2025-02-15 14:34:38'),
(21, 'Tessa Younger', 'tessa.younger@gmail.com', '077 1409 1697', 'Hey msnsofttech.com, Quick Notiice', 'If you are reading this message, That means my marketing is working. I can make your ad message reach 5 million sites in the same manner for just $50. It\'s the most affordable way to market your business or services. Contact me by email virgo.t3@gmail.com or skype me at live:.cid.dbb061d1dcb9127a\r\n\r\nP.S: Speical Offer - ONLY for 24 hours - 10 Million Sites for the same money $50', 1, '2025-02-18 07:17:18', '2025-02-18 07:17:18'),
(22, 'Nicholas Doby', 'dobyfinancial@sendnow.win', '86686185279', 'Re: Explore Funding Opportunities', 'Greetings, Mr./Ms. \r\n \r\nI’m Nicholas Doby from an investment consultancy. We connect clients globally with low or no-interest loans to help achieve your goals. Whether for personal or business/project funding, we collaborate with reputable investors to turn your proposals into reality. Share your business plan and executive summary with us at: contact@dobyfinancial.com to explore funding options. \r\n \r\nSincerely, \r\nNicholas Doby \r\nSenior Financial Consultant \r\nhttps://dobyfinancial.com', 1, '2025-02-21 17:44:45', '2025-02-21 17:44:45'),
(23, 'Mike Florian Evans', 'info@speed-seo.net', '88546689224', 'Unlock Your msnsofttech.com Potential with a Free SEO Score Check', 'Hello, \r\n \r\nWant to know how your online presence is ranking? \r\nFind out its strengths and weaknesses with our Free SEO Audit! \r\n \r\nIn just 2 minutes, you’ll get a in-depth analysis of your site’s optimization and recommendations to boost your rankings. \r\n \r\nBegin towards better performance and business success. \r\n \r\nRun Your Free SEO Check Now \r\nhttps://www.speed-seo.net/check-site-seo-score/ \r\n \r\nDon’t let undetected SEO issues hold you back. \r\nFix your site today and stay ahead in your industry! \r\n \r\nNeed more info? Whatsapp with a SEO expert: https://www.speed-seo.net/whatsapp-with-us/ \r\n \r\nBest regards, \r\n \r\n \r\nMike Florian Evans\r\n \r\nSpeed SEO \r\nPhone/WhatsApp: +1 (833) 454-8622', 1, '2025-02-22 14:31:35', '2025-02-22 14:31:35'),
(24, 'Search Engine Index', 'muller.darwin20@gmail.com', '173112418', 'Add msnsofttech.com to Google Search Index!', 'Hello,\r\n\r\nfor your website do be displayed in searches your domain needs to be indexed in the Google Search Index.\r\n\r\nTo add your domain to Google Search Index now, please visit \r\n\r\nhttps://SearchRegister.net', 1, '2025-02-23 21:23:30', '2025-02-23 21:23:30'),
(25, 'kalayesia', 'laviniastrutynski@gmail.com', '82742813294', 'Unlock Bitcoin Cash. $8252 Ready Now2   layesia', 'Unlock Bitcoin Cash. $8252 Ready Now  - https://t.me/+4jZIctgD3iAyNTk1?Graipt43pet', 1, '2025-02-24 03:26:23', '2025-02-24 03:26:23'),
(26, 'Yasuhiro Yamada', 'rohtopharmaceutical@via.tokyo.jp', '89747533577', 'Re: Remote Job Opportunity with ROHTO Pharmaceutical', 'Greetings, Mr./Ms. \r\n \r\nWith all due respect. We are looking for a Spokesperson/Financial Coordinator for ROHTO Pharmaceutical Co., Ltd. based in the USA, Canada, or Europe. This part-time role offers a minimum $5k salary and requires only a few minutes of your time daily. It will not create any conflicts if you work with other companies. If interested, please contact apply@rohtopharmaceutical.com \r\n \r\nBest regards, \r\nYasuhiro Yamada \r\nSenior Executive Officer \r\nhttps://rohtopharmaceutical.com/', 1, '2025-02-25 08:11:03', '2025-02-25 08:11:03'),
(27, 'Mike Louis Dubois', 'info@professionalseocleanup.com', '84467689732', 'Improve your website`s ranks totally free', 'Hi there, \r\n \r\nWhile checking your msnsofttech.com for its ranks, I have noticed that \r\nthere are some toxic links pointing towards it. \r\n \r\nGrab your free clean up and improve ranks in no time \r\nhttps://www.professionalseocleanup.com/ \r\n \r\nAsk us how we do it: \r\nhttps://www.professionalseocleanup.com/whatsapp/ \r\n \r\nRegards \r\nMike Louis Dubois\r\n \r\nPhone: +1 (855) 221-7591', 1, '2025-02-27 13:31:35', '2025-02-27 13:31:35'),
(28, 'Mark Simmons', 'marks@nextdayworkingcapital.com', '725-867-2209', 'An Option Worth Exploring', 'What if you could get business funding—without the hassle?\r\n\r\nNo credit checks, no paperwork, no sales calls. Just instant approvals and next-day funding. \r\n\r\nSee what you qualify for in 30 seconds—it\'s fast, easy, and risk-free!\r\n\r\nDon’t wait-Apply Now: www.nextdayworkingcapital.com/approval\r\n\r\n \r\nTrusted by thousands of small business owners. Over 600 million funded to businesses like yours!\r\n\r\n\r\n\r\n\r\nIf you no longer wish to receive marketing messages from us, you can unsubscribe at nextdayworkingcapital.com/unsubscribe', 1, '2025-02-28 19:27:45', '2025-02-28 19:27:45'),
(29, 'Kevin Barber', 'roma.sugden@googlemail.com', '9799435644', 'Day 1: Why Your Marketing Is Failing (And How To Fix It Starting Today)', 'Hi Msnsofttech,\r\n\r\nLet’s face it—most marketing strategies today are ineffective, leaving business owners frustrated and wondering where all their money went. \r\n\r\nHere’s the truth: Traditional marketing doesn’t work anymore. It’s about time to shift to direct-response marketing, the proven strategy that generates results in the real world.\r\n\r\nDan Kennedy, one of the leading marketing experts, swears by direct-response marketing, and his strategies have helped thousands of business owners grow their brands. \r\n\r\nLet me show you how to apply it to your business.\r\n\r\nStep 1: Know Your Target Audience\r\n\r\nTargeting everyone is a huge mistake. You must define your ideal customer. Direct-response marketing requires you to speak directly to a specific group of people.\r\n\r\nExample 1:\r\nTarget Audience: Busy professionals\r\n\r\nOffer: “Quick and effective workout plans for busy professionals.”\r\n\r\nThis specific focus allows businesses to craft marketing messages that truly resonate.\r\n\r\nExample 2:\r\nTarget Audience: Aspiring entrepreneurs\r\n\r\nOffer: “The ultimate guide to start your e-commerce store in 30 days—no prior experience required.”\r\n\r\nThis appeals directly to the desires of this niche, making the marketing message much stronger.\r\n\r\nStep 2: Clear and Compelling Offer\r\n\r\nA great product is only as good as the offer. The offer should solve a problem and make it impossible for your ideal customer to say no.\r\n\r\nExample 1:\r\nA fitness coach offered: “Sign up for my program today and receive a free 1-hour coaching session, valued at $300.” This added value made the offer irresistible.\r\n\r\nExample 2:\r\nAn e-commerce store offered: “Free shipping on all orders over $50, plus a free product with every purchase.” The free bonus added to the deal makes it more attractive.\r\n\r\nStep 3: Track Everything\r\n\r\nIf you’re not measuring, you’re guessing. The most successful marketers track their results religiously.\r\n\r\nExample 1:\r\nA car dealership tested their email campaigns and found that subject lines with specific car models drove a 25% higher open rate than generic ones.\r\n\r\nExample 2:\r\nA SaaS company split their traffic between two landing pages: one with a video and one with text. The video version converted 40% more visitors into paying customers.\r\n\r\nYour Action Step:\r\nStart tracking your marketing results—whether it’s email opens, clicks, or conversions. If you don’t track, you can’t improve.\r\n\r\nTomorrow, we’ll dive into crafting irresistible offers and how to create something your customers can’t say no to.\r\n\r\nTo your success,\r\nKevin\r\n\r\nWho is Dan Kennedy?\r\nhttps://books.forbes.com/authors/dan-kennedy/\r\n\r\n\r\n\r\n\r\nUnsubscribe: \r\nhttps://marketersmentor.com/unsubscribe.php?d=msnsofttech.com', 1, '2025-02-28 22:54:10', '2025-02-28 22:54:10'),
(30, 'Bytouro', 'brosjonson@mail.ru', '83942499879', 'Dating in your city', 'Don\'t miss your chance! Right now, girls from your area are looking for a date. Check it out  - https://t.me/+ijV3M3OHHIowNzcx \r\n \r\nHetouro', 1, '2025-03-02 17:15:56', '2025-03-02 17:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE `counters` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `value` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `counters`
--

INSERT INTO `counters` (`id`, `title`, `slug`, `description`, `icon`, `value`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Projects Completed', 'projects-completed', '<p><br></p>', NULL, 72, 1, '2020-10-30 12:27:14', '2025-02-11 21:48:30'),
(2, 'Happy Clients', 'happy-clients', NULL, NULL, 56, 1, '2020-10-30 12:27:31', '2025-02-11 21:48:15'),
(3, 'Expert Developers', 'expert-developers', NULL, NULL, 10, 1, '2020-10-30 12:27:50', '2025-02-11 21:45:37'),
(4, 'Countries Served', 'countries-served', NULL, NULL, 9, 1, '2020-10-30 12:28:04', '2025-02-11 21:46:12');

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `department` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `title`, `slug`, `department`, `description`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Programmer', 'programmer', 'IT', NULL, 1, '2020-10-30 11:53:12', '2020-10-30 11:53:12'),
(4, 'Jr. Software Engineer', 'jr-software-engineer', 'IT', NULL, 1, '2025-02-05 18:46:57', '2025-02-05 18:46:57'),
(5, 'CEO', 'ceo', NULL, NULL, 1, '2025-02-05 19:07:02', '2025-02-05 19:07:02'),
(6, 'Jr. Web Developer', 'jr-web-developer', 'IT', NULL, 1, '2025-02-06 09:39:31', '2025-02-06 09:39:31');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `title`, `slug`, `description`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(17, 'Your Quote Request Placed', 'quote-placed', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, 1, NULL, '2021-11-08 12:45:20'),
(18, 'Your Quote Request Estimated', 'quote-estimated', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, 1, NULL, '2021-11-08 12:45:36'),
(19, 'Your Quote Request Approved', 'quote-approved', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, 1, NULL, '2021-11-08 12:45:46'),
(20, 'Your Quote Request Rejected', 'quote-rejected', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, 1, NULL, '2021-11-08 12:46:00'),
(21, 'You Received a Payment Invoice', 'invoice-send', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, 1, NULL, '2021-11-08 12:46:18'),
(22, 'Your Payment Has Been Successfully Received', 'invoice-paid', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, 1, NULL, '2021-11-08 12:46:30'),
(23, 'You Have Cancelled a Payment Request', 'invoice-cancelled', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, 1, NULL, '2021-11-08 12:46:42'),
(24, 'This email is to notify that your subscription has been successful', 'subscription', '<p>This is just to inform you that. Your subscription on our platform is successful. now. We will update you whenever we take action.<br><br>Regards<br></p>', NULL, 1, NULL, '2021-11-08 12:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `category_id`, `title`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(9, 4, '1. What is MSN SoftTech?', '1-what-is-msn-softtech', '<p>MSN SoftTech is a full-service web and software development company offering custom web solutions, mobile app development, SEO, WordPress customization, and digital marketing services.</p>\n', 1, '2025-02-11 21:06:44', '2025-02-11 21:07:39'),
(10, 4, '2. Where is MSN SoftTech located?', '2-where-is-msn-softtech-located', '<p>We are based in Bangladesh and provide services to clients worldwide.</p>\n', 1, '2025-02-11 21:08:22', '2025-02-11 21:08:22'),
(11, 4, '3. Do you work with international clients?', '3-do-you-work-with-international-clients', '<p>Yes, we work with clients globally and have experience in handling projects for businesses across various industries.</p>\n', 1, '2025-02-11 21:09:38', '2025-02-11 21:09:38'),
(12, 4, '4. How do I get started with MSN SoftTech?', '4-how-do-i-get-started-with-msn-softtech', '<p>You can contact us through our website, email, or phone. After discussing your requirements, we will provide a project proposal and timeline.</p>\n', 1, '2025-02-11 21:10:49', '2025-02-11 21:10:49'),
(13, 5, '1. What types of websites do you develop?', '1-what-types-of-websites-do-you-develop', '<p>We develop various types of websites, including:<ul>\r\n	<li>Business websites</li>\r\n	<li>E-commerce stores</li>\r\n	<li>Portfolio websites</li>\r\n	<li>Multi-vendor marketplaces</li>\r\n	<li>Custom web applications and more...</li>\r\n</ul></p>\n', 1, '2025-02-11 21:12:33', '2025-02-11 21:12:33'),
(14, 5, '2. Do you use templates or custom designs?', '2-do-you-use-templates-or-custom-designs', '<p>We offer both options. We can use pre-built templates for cost-effective solutions or create custom designs for unique branding.</p>\n', 1, '2025-02-11 21:14:46', '2025-02-11 21:14:46'),
(15, 5, '3. Will my website be mobile-friendly?', '3-will-my-website-be-mobile-friendly', '<p>Yes! All websites we develop are fully responsive and optimized for mobile, tablet, and desktop devices.</p>\n', 1, '2025-02-11 21:15:19', '2025-02-11 21:15:19'),
(16, 5, '4. Can I update my website myself after it is built?', '4-can-i-update-my-website-myself-after-it-is-built', '<p>Yes, if requested, we can build your website with a user-friendly content management system (CMS) like WordPress or a custom dashboard for easy updates.</p>\n', 1, '2025-02-11 21:17:13', '2025-02-11 21:17:13'),
(17, 5, '5. Can you redesign my existing website?', '5-can-you-redesign-my-existing-website', '<p>Yes, we provide website redesign services to improve your site\'s appearance, functionality, and SEO performance.</p>\n', 1, '2025-02-11 21:17:48', '2025-02-11 21:17:48'),
(18, 6, '1. Do you develop e-commerce websites?', '1-do-you-develop-e-commerce-websites', '<p>Yes, we develop e-commerce websites using platforms like WooCommerce, Laravel, and Shopify.</p>\n', 1, '2025-02-11 21:18:25', '2025-02-11 21:18:25'),
(19, 6, '2. Can you integrate payment gateways?', '2-can-you-integrate-payment-gateways', '<p>Yes, we integrate various payment gateways, including PayPal, Stripe, SSLCommerz, and other local payment methods.</p>\n', 1, '2025-02-11 21:19:07', '2025-02-11 21:19:07'),
(20, 6, '3. Can I manage my products and inventory?', '3-can-i-manage-my-products-and-inventory', '<p>Yes, we provide an easy-to-use admin panel for managing products, orders, and inventory.</p>\n', 1, '2025-02-11 21:20:22', '2025-02-11 21:20:22'),
(21, 6, '4. Do you support multi-vendor e-commerce platforms?', '4-do-you-support-multi-vendor-e-commerce-platforms', '<p>Yes, we build multi-vendor platforms where multiple sellers can list and sell their products.</p>\n', 1, '2025-02-11 21:21:04', '2025-02-11 21:21:04'),
(22, 11, '1. Do you develop mobile apps for both Android and iOS?', '1-do-you-develop-mobile-apps-for-both-android-and-ios', '<p>Yes, we develop native and cross-platform mobile apps for Android and iOS devices.</p>\n', 1, '2025-02-11 21:23:57', '2025-02-11 21:23:57'),
(23, 11, '2. Can you create a mobile app for my existing website?', '2-can-you-create-a-mobile-app-for-my-existing-website', '<p>Yes, we can convert your website into a fully functional mobile app.</p>\n', 1, '2025-02-11 21:24:32', '2025-02-11 21:24:32'),
(24, 11, '3. Do you offer app maintenance and updates?', '3-do-you-offer-app-maintenance-and-updates', '<p>Yes, we provide post-launch support, updates, and bug fixes to keep your app running smoothly.</p>\n', 1, '2025-02-11 21:25:01', '2025-02-11 21:25:01'),
(25, 7, '1. What SEO services do you offer?', '1-what-seo-services-do-you-offer', '<p>We provide:<ul>\r\n	<li>On-page SEO</li>\r\n	<li>Off-page SEO</li>\r\n	<li>Technical SEO</li>\r\n	<li>Keyword research</li>\r\n	<li>SEO audits</li>\r\n</ul></p>\n', 1, '2025-02-11 21:26:09', '2025-02-11 21:26:09'),
(26, 7, '2. How long does it take to see SEO results?', '2-how-long-does-it-take-to-see-seo-results', '<p>SEO results depend on competition and strategy. It usually takes 3-6 months to see significant improvements.</p>\n', 1, '2025-02-11 21:27:14', '2025-02-11 21:27:14'),
(27, 7, '3. Do you offer social media marketing?', '3-do-you-offer-social-media-marketing', '<p>Yes, we provide social media marketing and advertising services to grow your online presence.</p>\n', 1, '2025-02-11 21:28:14', '2025-02-11 21:28:14'),
(28, 9, '1. How much does a website cost?', '1-how-much-does-a-website-cost', '<p>The cost depends on the features and complexity of the website. Contact us for a custom quote.</p>\n', 1, '2025-02-11 21:29:44', '2025-02-11 21:29:44'),
(29, 9, '2. Do you offer fixed-price or hourly pricing?', '2-do-you-offer-fixed-price-or-hourly-pricing', '<p>We offer both fixed-price and hourly billing options based on project requirements.</p>\n', 1, '2025-02-11 21:30:22', '2025-02-11 21:30:22'),
(30, 9, '3. How long does it take to develop a website?', '3-how-long-does-it-take-to-develop-a-website', '<p>A basic website can take 1-2 weeks, while complex projects may take a few months.</p>\n', 1, '2025-02-11 21:32:10', '2025-02-11 21:32:10'),
(31, 9, '4. Do you require an upfront payment?', '4-do-you-require-an-upfront-payment', '<p>Yes, we typically require an initial deposit before starting the project. The remaining payment is based on project milestones.</p>\n', 1, '2025-02-11 21:33:56', '2025-02-11 21:33:56'),
(32, 9, '5. What payment methods do you accept?', '5-what-payment-methods-do-you-accept', '<p>We accept bank transfers, PayPal, Stripe, and local payment methods.</p>\n', 1, '2025-02-11 21:35:38', '2025-02-11 21:35:38'),
(33, 10, '1. Do you provide website maintenance services?', '1-do-you-provide-website-maintenance-services', '<p>Yes, we offer ongoing maintenance, security updates, and performance optimizations.</p>\n', 1, '2025-02-11 21:36:13', '2025-02-11 21:36:13'),
(34, 10, '2. What happens if my website has issues after launch?', '2-what-happens-if-my-website-has-issues-after-launch', '<p>We offer post-launch support to fix any bugs or issues that arise.</p>\n', 1, '2025-02-11 21:37:10', '2025-02-11 21:37:10'),
(35, 10, '3. Can I request additional features after the project is completed?', '3-can-i-request-additional-features-after-the-project-is-completed', '<p>Yes, we can add new features after project completion as part of a separate contract.</p>\n', 1, '2025-02-11 21:38:13', '2025-02-11 21:38:13');

-- --------------------------------------------------------

--
-- Table structure for table `faq_categories`
--

CREATE TABLE `faq_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faq_categories`
--

INSERT INTO `faq_categories` (`id`, `title`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(4, 'General', 'general', NULL, 1, '2025-02-11 21:02:46', '2025-02-11 21:02:46'),
(5, 'Web Development', 'web-development', NULL, 1, '2025-02-11 21:03:11', '2025-02-11 21:03:11'),
(6, 'E-commerce Solutions', 'e-commerce-solutions', NULL, 1, '2025-02-11 21:04:09', '2025-02-11 21:04:09'),
(7, 'SEO & Digital Marketing', 'seo-digital-marketing', NULL, 1, '2025-02-11 21:04:27', '2025-02-11 21:04:27'),
(8, 'Hosting & Security', 'hosting-security', NULL, 1, '2025-02-11 21:04:41', '2025-02-11 21:04:41'),
(9, 'Project & Pricing', 'project-pricing', NULL, 1, '2025-02-11 21:04:55', '2025-02-11 21:04:55'),
(10, 'Support & Maintenance', 'support-maintenance', NULL, 1, '2025-02-11 21:05:13', '2025-02-11 21:05:13'),
(11, 'Mobile App Development', 'mobile-app-development', NULL, 1, '2025-02-11 21:22:14', '2025-02-11 21:22:14');

-- --------------------------------------------------------

--
-- Table structure for table `get_quotes`
--

CREATE TABLE `get_quotes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `company` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `prefer_contact` int(11) DEFAULT NULL,
  `quantity` varchar(191) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `pre_delivery_time` varchar(191) DEFAULT NULL,
  `where_find` varchar(191) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `invoice_time` date DEFAULT NULL,
  `mail_status` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `get_quotes`
--

INSERT INTO `get_quotes` (`id`, `name`, `email`, `phone`, `address`, `city`, `company`, `website`, `prefer_contact`, `quantity`, `message`, `file_path`, `pre_delivery_time`, `where_find`, `amount`, `invoice_time`, `mail_status`, `status`, `created_at`, `updated_at`) VALUES
(31, 'Ikramul Hasan', 'hasanikramul926@gmail.com', '01638846367', 'Dhaka, Airport', 'Dhaka', '69TheDeal', NULL, 1, NULL, 'Web Development service need', 'WhatsApp Image 2025-02-25 at 14.12.11_b2dd9113_1740607136.jpg', NULL, NULL, 332.00, NULL, 0, 1, '2025-02-26 21:58:56', '2025-02-27 11:15:42'),
(32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', '01638846367', 'Dhaka, Airport', 'Dhaka', '69TheDeal', NULL, 1, NULL, 'Web Development service need', 'WhatsApp Image 2025-02-25 at 14.12.11_b2dd9113_1740607150.jpg', NULL, NULL, 332.00, NULL, 0, 3, '2025-02-26 21:59:10', '2025-02-26 22:46:30'),
(33, 'Ikramul Hasan', 'advisor7354@gmail.com', '01638846367', 'Dhaka, Airport', 'Dhaka', 'advisor7354', NULL, 1, NULL, 'sdsfsd', NULL, NULL, NULL, 332.00, NULL, 0, 1, '2025-02-27 12:32:38', '2025-02-27 12:36:49');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quote_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `company` varchar(191) DEFAULT NULL,
  `total_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(8,2) DEFAULT NULL,
  `invoice_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `service_charge` decimal(8,2) DEFAULT NULL,
  `tax` decimal(8,2) DEFAULT NULL,
  `shipping` decimal(8,2) DEFAULT NULL,
  `invoice_date` datetime NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `message` text DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `reference` text DEFAULT NULL,
  `attach` text DEFAULT NULL,
  `invoice_type` int(11) DEFAULT NULL,
  `estimate_flag` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `quote_id`, `name`, `email`, `phone`, `address`, `city`, `company`, `total_amount`, `discount_amount`, `invoice_amount`, `service_charge`, `tax`, `shipping`, `invoice_date`, `due_date`, `message`, `terms_conditions`, `reference`, `attach`, `invoice_type`, `estimate_flag`, `status`, `created_at`, `updated_at`) VALUES
(11, NULL, 'Habib R', 'example@mail.com', NULL, 'Mirpur', NULL, NULL, 800.00, NULL, 800.00, 780.00, 20.00, NULL, '2020-11-25 12:49:30', NULL, '<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text.<br></p>', NULL, 'Habib', 'sundrob10_1606308570.JPG', 5, 0, 1, '2020-11-25 06:49:30', '2020-11-25 06:49:30'),
(17, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, 23.00, 222.00, 222.00, 2.00, 2.00, '2025-02-26 22:06:12', '2025-02-27 00:00:00', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, 'mobile app (5)_1740607572.png', 0, 0, 1, '2025-02-26 22:06:12', '2025-02-26 22:06:12'),
(18, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, 23.00, 222.00, 222.00, 2.00, 2.00, '2025-02-26 22:11:33', '2025-02-27 00:00:00', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-26 22:11:33', '2025-02-26 22:11:33'),
(19, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, NULL, 222.00, 222.00, 2.00, 2.00, '2025-02-26 22:17:53', '2025-02-27 00:00:00', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-26 22:17:53', '2025-02-26 22:17:53'),
(20, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, NULL, 222.00, 222.00, NULL, NULL, '2025-02-26 22:20:54', NULL, '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-26 22:20:54', '2025-02-26 22:20:54'),
(21, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, 23.00, 222.00, 222.00, 2.00, 2.00, '2025-02-26 22:25:59', '2025-02-27 00:00:00', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-26 22:25:59', '2025-02-26 22:25:59'),
(22, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, 23.00, 222.00, 222.00, 2.00, 2.00, '2025-02-26 22:28:28', '2025-02-27 00:00:00', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-26 22:28:28', '2025-02-26 22:28:28'),
(23, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, NULL, 222.00, 222.00, NULL, NULL, '2025-02-26 22:33:00', NULL, '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-26 22:33:00', '2025-02-26 22:33:00'),
(24, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, NULL, 222.00, 222.00, NULL, NULL, '2025-02-26 22:41:42', NULL, '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-26 22:41:42', '2025-02-26 22:41:42'),
(25, 32, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, NULL, 222.00, 222.00, NULL, NULL, '2025-02-26 22:44:32', NULL, '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-26 22:44:32', '2025-02-26 22:44:32'),
(26, 31, 'Ikramul Hasan', 'hasanikramul926@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', '69TheDeal', 332.00, 23.00, 222.00, 222.00, 2.00, 2.00, '2025-02-27 11:15:42', NULL, '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, 'oo', NULL, 0, 0, 1, '2025-02-27 11:15:42', '2025-02-27 11:15:42'),
(27, 33, 'Ikramul Hasan', 'advisor7354@gmail.com', NULL, 'Dhaka, Airport', 'Dhaka', 'advisor7354', 332.00, 23.00, 222.00, 222.00, 2.00, 2.00, '2025-02-27 12:36:49', '2025-02-27 00:00:00', '[company]<br>Address: [address], [city].<br><br>Hi, [name]. This is just to inform you that. Your ordered [services] are on progress now. We will update you whenever we take action.<br><br>Regards', NULL, NULL, NULL, 0, 0, 1, '2025-02-27 12:36:49', '2025-02-27 12:36:49');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `direction` tinyint(1) NOT NULL DEFAULT 1,
  `default` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `description`, `direction`, `default`, `status`, `created_at`, `updated_at`) VALUES
(2, 'English', 'en', NULL, 1, 1, 1, '2021-11-07 14:26:20', '2021-11-07 14:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `live_chats`
--

CREATE TABLE `live_chats` (
  `id` int(10) UNSIGNED NOT NULL,
  `whatsapp_no` varchar(191) DEFAULT NULL,
  `whatsapp_title` text DEFAULT NULL,
  `whatsapp_greeting` text DEFAULT NULL,
  `whatsapp_color` varchar(191) DEFAULT NULL,
  `whatsapp_position` tinyint(1) NOT NULL DEFAULT 1,
  `whatsapp_status` tinyint(1) NOT NULL DEFAULT 1,
  `facebook_id` varchar(191) DEFAULT NULL,
  `facebook_greeting_in` text DEFAULT NULL,
  `facebook_greeting_out` text DEFAULT NULL,
  `facebook_color` varchar(191) DEFAULT NULL,
  `facebook_position` tinyint(1) NOT NULL DEFAULT 1,
  `facebook_status` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `live_chats`
--

INSERT INTO `live_chats` (`id`, `whatsapp_no`, `whatsapp_title`, `whatsapp_greeting`, `whatsapp_color`, `whatsapp_position`, `whatsapp_status`, `facebook_id`, `facebook_greeting_in`, `facebook_greeting_out`, `facebook_color`, `facebook_position`, `facebook_status`, `status`, `created_at`, `updated_at`) VALUES
(3, '+8801740473189', 'Chat with us on WhatsApp!', 'Hello, how can we help you?', '#ff9c00', 1, 1, '1808009959448230', 'Hello, how can we help you?', 'Hello, how can we help you?', '#ff9c00', 1, 1, 0, '2021-11-07 14:26:20', '2021-11-07 14:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `ltm_translations`
--

CREATE TABLE `ltm_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `locale` varchar(191) NOT NULL,
  `group` varchar(191) NOT NULL,
  `key` text NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `ltm_translations`
--

INSERT INTO `ltm_translations` (`id`, `status`, `locale`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'auth', 'login_title', 'Login Into Admin Panel.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(2, 1, 'en', 'auth', 'register_title', 'Create Your Account.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(3, 1, 'en', 'auth', 'verify_title', 'Please Check Your Email to Verify Yourself.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(4, 1, 'en', 'auth', 'email_title', 'Enter Your Account Email Address.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(5, 1, 'en', 'auth', 'reset_title', 'Enter Your New Password.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(6, 1, 'en', 'auth', 'login', 'Login', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(7, 1, 'en', 'auth', 'register', 'Register', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(8, 1, 'en', 'auth', 'verify', 'Verify', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(9, 1, 'en', 'auth', 'reset', 'Reset Password', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(10, 1, 'en', 'auth', 'name', 'Name', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(11, 1, 'en', 'auth', 'email', 'E-Mail Address', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(12, 1, 'en', 'auth', 'password', 'Password', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(13, 1, 'en', 'auth', 'confirm_password', 'Confirm Password', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(14, 1, 'en', 'auth', 'remember', 'Remember Me', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(15, 1, 'en', 'auth', 'forgot_password', 'Forgot Your Password?', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(16, 1, 'en', 'auth', 'dont_have_account', 'Don\'t Have An Account?', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(17, 1, 'en', 'auth', 'verify_your_email', 'Verify Your Email Address', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(18, 1, 'en', 'auth', 'verify_email_sent', 'A fresh verification link has been sent to your email address.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(19, 1, 'en', 'auth', 'check_your_email', 'Before proceeding, please check your email for a verification link.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(20, 1, 'en', 'auth', 'not_receive_email', 'If you did not receive the email', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(21, 1, 'en', 'auth', 'send_another_request', 'click here to request another', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(22, 1, 'en', 'auth', 'send_reset_link', 'Send Password Reset Link', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(23, 1, 'en', 'auth', 'failed', 'These credentials do not match our records.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(24, 1, 'en', 'auth', 'throttle', 'Too many login attempts. Please try again in :seconds seconds.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(25, 1, 'en', 'common', 'read_more', 'Read More', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(26, 1, 'en', 'common', 'view_more', 'View More', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(27, 1, 'en', 'common', 'get_start', 'Get Start', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(28, 1, 'en', 'common', 'contact_us', 'Contact Us', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(29, 1, 'en', 'common', 'go_home', 'Go Home', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(30, 1, 'en', 'common', 'category', 'Category', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(31, 1, 'en', 'common', 'categories', 'Categories', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(32, 1, 'en', 'common', 'all', 'All', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(33, 1, 'en', 'common', 'currency', '$', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(34, 1, 'en', 'common', 'footer_links', 'Useful Links', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(35, 1, 'en', 'common', 'recent_posts', 'Recent Posts', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(36, 1, 'en', 'contact', 'email', 'Email', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(37, 1, 'en', 'contact', 'phone', 'Call Us', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(38, 1, 'en', 'contact', 'office_time', 'Office Time', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(39, 1, 'en', 'contact', 'address', 'Address', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(40, 1, 'en', 'contact', 'your_name', 'Your Name *', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(41, 1, 'en', 'contact', 'phone_no', 'Phone No', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(42, 1, 'en', 'contact', 'email_address', 'Email Address *', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(43, 1, 'en', 'contact', 'subject', 'Subject *', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(44, 1, 'en', 'contact', 'your_massage', 'Your Massage *', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(45, 1, 'en', 'contact', 'send', 'Send', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(46, 1, 'en', 'dashboard', 'welcome', 'Welcome !', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(47, 1, 'en', 'dashboard', 'hello', 'Hello', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(48, 1, 'en', 'dashboard', 'home', 'Home', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(49, 1, 'en', 'dashboard', 'admin', 'Admin', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(50, 1, 'en', 'dashboard', 'navigation', 'Navigation', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(51, 1, 'en', 'dashboard', 'logout', 'Logout', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(52, 1, 'en', 'dashboard', 'list', 'List', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(53, 1, 'en', 'dashboard', 'select', 'Select', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(54, 1, 'en', 'dashboard', 'please_provide', 'Please Provide', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(55, 1, 'en', 'dashboard', 'setup', 'Setup', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(56, 1, 'en', 'dashboard', 'save', 'Save', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(57, 1, 'en', 'dashboard', 'send', 'Send', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(58, 1, 'en', 'dashboard', 'update', 'Update', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(59, 1, 'en', 'dashboard', 'change', 'Change', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(60, 1, 'en', 'dashboard', 'confirm', 'Confirm', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(61, 1, 'en', 'dashboard', 'close', 'Close', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(62, 1, 'en', 'dashboard', 'cancel', 'Cancel', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(63, 1, 'en', 'dashboard', 'create', 'Create', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(64, 1, 'en', 'dashboard', 'add_new', 'Add New', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(65, 1, 'en', 'dashboard', 'delete', 'Delete', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(66, 1, 'en', 'dashboard', 'remove', 'Remove', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(67, 1, 'en', 'dashboard', 'refresh', 'Refresh', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(68, 1, 'en', 'dashboard', 'back', 'Back', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(69, 1, 'en', 'dashboard', 'approve', 'Approve', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(70, 1, 'en', 'dashboard', 'estimate', 'Estimate', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(71, 1, 'en', 'dashboard', 'reject', 'Reject', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(72, 1, 'en', 'dashboard', 'download', 'Download', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(73, 1, 'en', 'dashboard', 'print', 'Print', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(74, 1, 'en', 'dashboard', 'attach', 'Attach', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(75, 1, 'en', 'dashboard', 'quote', 'Quote|Quotes', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(76, 1, 'en', 'dashboard', 'create_invoice', 'Create Invoice', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(77, 1, 'en', 'dashboard', 'add', 'Add', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(78, 1, 'en', 'dashboard', 'edit', 'Edit', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(79, 1, 'en', 'dashboard', 'view', 'View', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(80, 1, 'en', 'dashboard', 'are_you_sure', 'Are You Sure?', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(81, 1, 'en', 'dashboard', 'delete_warning', 'You will not be able to recover this!', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(82, 1, 'en', 'dashboard', 'success', 'Success', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(83, 1, 'en', 'dashboard', 'error', 'Error', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(84, 1, 'en', 'dashboard', 'created_successfully', 'Created Successfully!', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(85, 1, 'en', 'dashboard', 'updated_successfully', 'Updated Successfully!', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(86, 1, 'en', 'dashboard', 'deleted_successfully', 'Deleted Successfully!', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(87, 1, 'en', 'dashboard', 'sent_successfully', 'Sent Successfully!', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(88, 1, 'en', 'dashboard', 'task_updated', 'Task Updated', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(89, 1, 'en', 'dashboard', 'password_invalid', 'Current password is invalid!', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(90, 1, 'en', 'dashboard', 'email_invalid', 'You are entered same email address!', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(91, 1, 'en', 'dashboard', 'dashboard', 'Dashboard', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(92, 1, 'en', 'dashboard', 'invoice', 'Invoice', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(93, 1, 'en', 'dashboard', 'blog', 'Blog|Blogs', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(94, 1, 'en', 'dashboard', 'blog_list', 'Blog List|Blog List', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(95, 1, 'en', 'dashboard', 'blog_category', 'Blog Category|Blog Categories', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(96, 1, 'en', 'dashboard', 'portfolio', 'Portfolio|Portfolios', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(97, 1, 'en', 'dashboard', 'portfolio_list', 'Portfolio List|Portfolio List', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(98, 1, 'en', 'dashboard', 'portfolio_category', 'Portfolio Category|Portfolio Categories', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(99, 1, 'en', 'dashboard', 'service', 'Service|Services', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(100, 1, 'en', 'dashboard', 'pricing', 'Pricing|Pricings', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(101, 1, 'en', 'dashboard', 'team', 'Our Team', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(102, 1, 'en', 'dashboard', 'member', 'Member|Member List', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(103, 1, 'en', 'dashboard', 'designation', 'Designation', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(104, 1, 'en', 'dashboard', 'faq', 'FAQ|FAQs', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(105, 1, 'en', 'dashboard', 'faq_list', 'FAQ List|FAQ List', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(106, 1, 'en', 'dashboard', 'faq_category', 'FAQ Category|FAQ Categories', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(107, 1, 'en', 'dashboard', 'slider', 'Slider|Sliders', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(108, 1, 'en', 'dashboard', 'partner', 'Partner|Partners', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(109, 1, 'en', 'dashboard', 'testimonial', 'Testimonial|Testimonials', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(110, 1, 'en', 'dashboard', 'work_process', 'Work Process|Work Processes', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(111, 1, 'en', 'dashboard', 'feature', 'Why Choose Us|Why Choose Us', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(112, 1, 'en', 'dashboard', 'counter', 'Counter|Counters', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(113, 1, 'en', 'dashboard', 'email', 'Email', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(114, 1, 'en', 'dashboard', 'subscriber', 'Subscriber|Subscribers', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(115, 1, 'en', 'dashboard', 'about', 'About Us', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(116, 1, 'en', 'dashboard', 'page', 'Page|Pages', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(117, 1, 'en', 'dashboard', 'page_setup', 'Page Setup|Pages Setup', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(118, 1, 'en', 'dashboard', 'footer_page', 'Footer Page|Footer Pages', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(119, 1, 'en', 'dashboard', 'section', 'Section|Sections', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(120, 1, 'en', 'dashboard', 'template', 'Email Template', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(121, 1, 'en', 'dashboard', 'live_chat', 'LiveChat|LiveChats', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(122, 1, 'en', 'dashboard', 'language', 'Language|Languages', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(123, 1, 'en', 'dashboard', 'translation', 'Translation|Translations', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(124, 1, 'en', 'dashboard', 'setting', 'Setting|Settings', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(125, 1, 'en', 'dashboard', 'general_setting', 'General Setting|General Settings', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(126, 1, 'en', 'dashboard', 'no', 'No', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(127, 1, 'en', 'dashboard', 'sl', 'SL', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(128, 1, 'en', 'dashboard', 'title', 'Title', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(129, 1, 'en', 'dashboard', 'category', 'Category', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(130, 1, 'en', 'dashboard', 'short_desc', 'Short Details', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(131, 1, 'en', 'dashboard', 'description', 'Description', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(132, 1, 'en', 'dashboard', 'thumbnail', 'Thumbnail', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(133, 1, 'en', 'dashboard', 'youtube_video', 'Youtube Video', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(134, 1, 'en', 'dashboard', 'youtube_video_id', 'Youtube Video ID', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(135, 1, 'en', 'dashboard', 'icon', 'Icon', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(136, 1, 'en', 'dashboard', 'value', 'Value', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(137, 1, 'en', 'dashboard', 'status', 'Status', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(138, 1, 'en', 'dashboard', 'action', 'Action', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(139, 1, 'en', 'dashboard', 'logo', 'Logo', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(140, 1, 'en', 'dashboard', 'photo', 'Photo', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(141, 1, 'en', 'dashboard', 'name', 'Name', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(142, 1, 'en', 'dashboard', 'phone', 'Phone', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(143, 1, 'en', 'dashboard', 'subject', 'Subject', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(144, 1, 'en', 'dashboard', 'message', 'Message', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(145, 1, 'en', 'dashboard', 'department', 'Department', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(146, 1, 'en', 'dashboard', 'organization', 'Organization', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(147, 1, 'en', 'dashboard', 'price', 'Price', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(148, 1, 'en', 'dashboard', 'old_price', 'Old Price', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(149, 1, 'en', 'dashboard', 'duration', 'Duration', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(150, 1, 'en', 'dashboard', 'features', 'Features', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(151, 1, 'en', 'dashboard', 'feature_name', 'Feature Name', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(152, 1, 'en', 'dashboard', 'add_feature', 'Add Feature', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(153, 1, 'en', 'dashboard', 'web_link', 'Web Link', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(154, 1, 'en', 'dashboard', 'whatsapp', 'WhatsApp No', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(155, 1, 'en', 'dashboard', 'shortcode', 'Shortcode', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(156, 1, 'en', 'dashboard', 'locale', 'Locale', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(157, 1, 'en', 'dashboard', 'date', 'Date', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(158, 1, 'en', 'dashboard', 'mission_title', 'Mission Title', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(159, 1, 'en', 'dashboard', 'mission_description', 'Mission Description', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(160, 1, 'en', 'dashboard', 'vision_title', 'Vision Title', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(161, 1, 'en', 'dashboard', 'vision_description', 'Vision Description', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(162, 1, 'en', 'dashboard', 'quote_no', 'Quote No.', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(163, 1, 'en', 'dashboard', 'quote_placed', 'Quote Placed', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(164, 1, 'en', 'dashboard', 'invoice_no', 'Invoice No', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(165, 1, 'en', 'dashboard', 'invoice_date', 'Invoice Date', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(166, 1, 'en', 'dashboard', 'invoice_type', 'Invoice Type', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(167, 1, 'en', 'dashboard', 'total_blogs', 'Total Blogs', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(168, 1, 'en', 'dashboard', 'total_portfolios', 'Total Portfolios', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(169, 1, 'en', 'dashboard', 'total_services', 'Total Services', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(170, 1, 'en', 'dashboard', 'total_faqs', 'Total FAQs', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(171, 1, 'en', 'dashboard', 'total_members', 'Total Members', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(172, 1, 'en', 'dashboard', 'total_partners', 'Total Partners', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(173, 1, 'en', 'dashboard', 'total_emails', 'Total Emails', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(174, 1, 'en', 'dashboard', 'total_subscribers', 'Total Subscribers', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(175, 1, 'en', 'dashboard', 'site_info', 'Site Info', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(176, 1, 'en', 'dashboard', 'contact_info', 'Contact Info', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(177, 1, 'en', 'dashboard', 'social_info', 'Social Info', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(178, 1, 'en', 'dashboard', 'customization', 'Customization', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(179, 1, 'en', 'dashboard', 'account', 'Account', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(180, 1, 'en', 'dashboard', 'admin_mail_address', 'Change Mail Address', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(181, 1, 'en', 'dashboard', 'admin_change_password', 'Change Password', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(182, 1, 'en', 'dashboard', 'site_title', 'Site Title', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(183, 1, 'en', 'dashboard', 'meta_title', 'Meta Title', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(184, 1, 'en', 'dashboard', 'meta_description', 'Meta Description', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(185, 1, 'en', 'dashboard', 'meta_desc_length', 'Max length 160 characters', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(186, 1, 'en', 'dashboard', 'meta_keywords', 'Meta Keywords', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(187, 1, 'en', 'dashboard', 'keywords_separate', 'Separate Every Keyword by Using (,) Symbol', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(188, 1, 'en', 'dashboard', 'site_logo', 'Site Logo', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(189, 1, 'en', 'dashboard', 'site_favicon', 'Site Favicon', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(190, 1, 'en', 'dashboard', 'footer_text', 'Footer Text', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(191, 1, 'en', 'dashboard', 'phone_no_1', 'Phone No 1', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(192, 1, 'en', 'dashboard', 'phone_no_2', 'Phone No 2', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(193, 1, 'en', 'dashboard', 'email_address_1', 'Email Address 1', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(194, 1, 'en', 'dashboard', 'email_address_2', 'Email Address 2', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(195, 1, 'en', 'dashboard', 'contact_address', 'Contact Address', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(196, 1, 'en', 'dashboard', 'contact_mail', 'Contact Mail', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(197, 1, 'en', 'dashboard', 'office_hours', 'Office Hours', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(198, 1, 'en', 'dashboard', 'open_close_times', 'Open-Close Times', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(199, 1, 'en', 'dashboard', 'google_map', 'Google Map', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(200, 1, 'en', 'dashboard', 'embed_code', 'Embed Code', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(201, 1, 'en', 'dashboard', 'custom_css', 'Custom CSS', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(202, 1, 'en', 'dashboard', 'mail_address', 'Mail Address', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(203, 1, 'en', 'dashboard', 'old_password', 'Old Password', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(204, 1, 'en', 'dashboard', 'new_password', 'New Password', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(205, 1, 'en', 'dashboard', 'confirm_password', 'Confirm Password', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(206, 1, 'en', 'dashboard', 'website', 'Website URL', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(207, 1, 'en', 'dashboard', 'facebook', 'Facebook URL', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(208, 1, 'en', 'dashboard', 'twitter', 'Twitter URL', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(209, 1, 'en', 'dashboard', 'linkedin', 'Linkedin URL', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(210, 1, 'en', 'dashboard', 'instagram', 'Instagram URL', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(211, 1, 'en', 'dashboard', 'pinterest', 'Pinterest URL', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(212, 1, 'en', 'dashboard', 'youtube', 'Youtube URL', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(213, 1, 'en', 'dashboard', 'skype', 'Skype ID', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(214, 1, 'en', 'dashboard', 'inc_country_code', 'inc. country code', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(215, 1, 'en', 'dashboard', 'whatsapp_live_chat', 'WhatsApp Live Chat', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(216, 1, 'en', 'dashboard', 'whatsapp_header_title', 'Header Title', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(217, 1, 'en', 'dashboard', 'whatsapp_greeting_message', 'Greeting Message', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(218, 1, 'en', 'dashboard', 'messenger_live_chat', 'Messenger Live Chat', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(219, 1, 'en', 'dashboard', 'facebook_page_id', 'Facebook Page ID', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(220, 1, 'en', 'dashboard', 'facebook_login_greeting', 'Login Greeting', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(221, 1, 'en', 'dashboard', 'facebook_logout_greeting', 'Logout Greeting', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(222, 1, 'en', 'dashboard', 'select_status', 'Select Status', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(223, 1, 'en', 'dashboard', 'active', 'Active', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(224, 1, 'en', 'dashboard', 'inactive', 'Inactive', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(225, 1, 'en', 'dashboard', 'display', 'Display', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(226, 1, 'en', 'dashboard', 'pending', 'Pending', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(227, 1, 'en', 'dashboard', 'paid', 'Paid', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(228, 1, 'en', 'dashboard', 'canceled', 'Canceled', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(229, 1, 'en', 'dashboard', 'estimated', 'Estimated', '2022-08-21 11:13:08', '2022-08-21 11:13:08'),
(230, 1, 'en', 'dashboard', 'approved', 'Approved', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(231, 1, 'en', 'dashboard', 'rejected', 'Rejected', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(232, 1, 'en', 'dashboard', 'default', 'Default', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(233, 1, 'en', 'dashboard', 'make_default', 'Make Default', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(234, 1, 'en', 'dashboard', 'image_size', 'Best Resolution Height- :height PX, Width- :width PX', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(235, 1, 'en', 'dashboard', 'prefer_cells', 'Prefer to use :cells cells', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(236, 1, 'en', 'dashboard', 'sidebar', 'Action', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(237, 1, 'en', 'dashboard', 'advance', 'Advance', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(238, 1, 'en', 'dashboard', 'interval', 'Interval', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(239, 1, 'en', 'dashboard', 'milestone', 'Milestone', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(240, 1, 'en', 'dashboard', 'final', 'Final', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(241, 1, 'en', 'dashboard', 'full', 'Full', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(242, 1, 'en', 'dashboard', 'due_date', 'Due Date', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(243, 1, 'en', 'dashboard', 'invoice_status', 'Invoice Status', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(244, 1, 'en', 'dashboard', 'billing_address', 'Billing Address', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(245, 1, 'en', 'dashboard', 'company', 'Company', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(246, 1, 'en', 'dashboard', 'address', 'Address', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(247, 1, 'en', 'dashboard', 'city', 'City', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(248, 1, 'en', 'dashboard', 'quote_files', 'Customer Files', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(249, 1, 'en', 'dashboard', 'reference', 'Reference', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(250, 1, 'en', 'dashboard', 'services', 'Services', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(251, 1, 'en', 'dashboard', 'note', 'Note', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(252, 1, 'en', 'dashboard', 'service_bill', 'Service Bill', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(253, 1, 'en', 'dashboard', 'tax_charge', 'Tax Charge', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(254, 1, 'en', 'dashboard', 'shipping_charge', 'Shipping Charge', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(255, 1, 'en', 'dashboard', 'total', 'Total', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(256, 1, 'en', 'dashboard', 'discount', 'Discount', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(257, 1, 'en', 'dashboard', 'payable', 'Payable', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(258, 1, 'en', 'dashboard', 'total_amount', 'Total Amount', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(259, 1, 'en', 'dashboard', 'discount_amount', 'Discount Amount', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(260, 1, 'en', 'dashboard', 'invoice_amount', 'Invoice Amount', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(261, 1, 'en', 'dashboard', 'send_mail', 'Send Mail', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(262, 1, 'en', 'dashboard', 'prefer_contact', 'Prefer contact?', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(263, 1, 'en', 'dashboard', 'no_value', 'No value', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(264, 1, 'en', 'dashboard', 'template-quote-placed', 'Quote Placed', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(265, 1, 'en', 'dashboard', 'template-quote-estimated', 'Quote Estimated', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(266, 1, 'en', 'dashboard', 'template-quote-approved', 'Quote Approved', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(267, 1, 'en', 'dashboard', 'template-quote-rejected', 'Quote Rejected', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(268, 1, 'en', 'dashboard', 'template-invoice-send', 'Invoice Send', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(269, 1, 'en', 'dashboard', 'template-invoice-paid', 'Invoice Paid', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(270, 1, 'en', 'dashboard', 'template-invoice-cancelled', 'Invoice Cancelled', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(271, 1, 'en', 'dashboard', 'template-subscription', 'Subscription', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(272, 1, 'en', 'email', 'pay_btn', 'Pay By Paypal', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(273, 1, 'en', 'email', 'attach_btn', 'Download Attachments', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(274, 1, 'en', 'email', 'hello', 'Hello', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(275, 1, 'en', 'email', 'name', 'Name', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(276, 1, 'en', 'email', 'email', 'Email', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(277, 1, 'en', 'email', 'phone', 'Phone', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(278, 1, 'en', 'email', 'company', 'Company', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(279, 1, 'en', 'email', 'address', 'Address', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(280, 1, 'en', 'email', 'city', 'City', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(281, 1, 'en', 'email', 'reference', 'Reference', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(282, 1, 'en', 'email', 'bill', 'Total Bill', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(283, 1, 'en', 'email', 'service_charge', 'Service Bill', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(284, 1, 'en', 'email', 'tax', 'Tax Charge', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(285, 1, 'en', 'email', 'shipping', 'Shipping Charge', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(286, 1, 'en', 'email', 'total_amount', 'Total Amount', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(287, 1, 'en', 'email', 'discount_amount', 'Discount Amount', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(288, 1, 'en', 'email', 'payable_amount', 'Payable Amount', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(289, 1, 'en', 'email', 'quote_id', 'Quote ID', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(290, 1, 'en', 'email', 'invoice_id', 'Invoice ID', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(291, 1, 'en', 'email', 'service_bill', 'Service Bill', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(292, 1, 'en', 'email', 'invoice', 'Invoice', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(293, 1, 'en', 'email', 'invoice_date', 'Invoice Date', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(294, 1, 'en', 'email', 'due_date', 'Due Date', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(295, 1, 'en', 'email', 'quote', 'Quote', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(296, 1, 'en', 'email', 'services', 'Services', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(297, 1, 'en', 'email', 'invoice_type', 'Invoice Type', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(298, 1, 'en', 'email', 'estimate', 'Estimate', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(299, 1, 'en', 'email', 'advance', 'Advance', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(300, 1, 'en', 'email', 'interval', 'Interval', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(301, 1, 'en', 'email', 'milestone', 'Milestone', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(302, 1, 'en', 'email', 'final', 'Final', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(303, 1, 'en', 'email', 'full', 'Full', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(304, 1, 'en', 'email', 'thanks', 'Thanks', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(305, 1, 'en', 'email', 'send_successfully', 'Mail Send Successfully!', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(306, 1, 'en', 'email', 'receiver_not_found', 'Receiver Not Configured!', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(307, 1, 'en', 'email', 'quote_submitted', 'Quote Request Submitted', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(308, 1, 'en', 'email', 'new_quote_request', 'You Have New Quote Request!', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(309, 1, 'en', 'email', 'payment_cancelled', 'Your payment request has cancelled!', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(310, 1, 'en', 'email', 'got_new_payment', 'You got a new payment!', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(311, 1, 'en', 'email', 'something_is_wrong', 'Something is wrong.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(312, 1, 'en', 'email', 'payment_successfull', 'You have successfully make the payment.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(313, 1, 'en', 'email', 'login_dashboard_to_check', 'Please login your application dashboard to see the more details. Thank you.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(314, 1, 'en', 'form', 'your_name', 'Your Name *', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(315, 1, 'en', 'form', 'phone_no', 'Phone No *', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(316, 1, 'en', 'form', 'email_address', 'Email Address *', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(317, 1, 'en', 'form', 'address', 'Address *', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(318, 1, 'en', 'form', 'city', 'City *', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(319, 1, 'en', 'form', 'company', 'Company (Optional)', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(320, 1, 'en', 'form', 'prefer_contact', 'What do you prefer for contact? *', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(321, 1, 'en', 'form', 'phone', 'Phone', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(322, 1, 'en', 'form', 'email', 'Email', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(323, 1, 'en', 'form', 'services', 'Services (You can choose multiple)', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(324, 1, 'en', 'form', 'your_massage', 'Write Your Quotation Detail Here... *', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(325, 1, 'en', 'form', 'upload_file', 'Upload File (Optional)', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(326, 1, 'en', 'form', 'submit', 'Submit Now', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(327, 1, 'en', 'navbar', 'home', 'Home', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(328, 1, 'en', 'navbar', 'about', 'About Us', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(329, 1, 'en', 'navbar', 'services', 'Services', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(330, 1, 'en', 'navbar', 'service-detail', 'Service Detail', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(331, 1, 'en', 'navbar', 'portfolios', 'Portfolios', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(332, 1, 'en', 'navbar', 'portfolio-detail', 'Portfolio Detail', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(333, 1, 'en', 'navbar', 'pricing', 'Pricing', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(334, 1, 'en', 'navbar', 'blog', 'Blog', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(335, 1, 'en', 'navbar', 'blog-detail', 'Blog Detail', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(336, 1, 'en', 'navbar', 'faqs', 'FAQs', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(337, 1, 'en', 'navbar', 'contact', 'Contact Us', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(338, 1, 'en', 'navbar', 'get_quote', 'Get A Quote', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(339, 1, 'en', 'navbar', 'error', 'Error 404', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(340, 1, 'en', 'navbar', 'payment_feedback', 'Payment Feedback', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(341, 1, 'en', 'pagination', 'previous', '&laquo; Previous', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(342, 1, 'en', 'pagination', 'next', 'Next &raquo;', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(343, 1, 'en', 'passwords', 'password', 'Passwords must be at least eight characters and match the confirmation.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(344, 1, 'en', 'passwords', 'reset', 'Your password has been reset!', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(345, 1, 'en', 'passwords', 'sent', 'We have e-mailed your password reset link!', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(346, 1, 'en', 'passwords', 'token', 'This password reset token is invalid.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(347, 1, 'en', 'passwords', 'user', 'We can\'t find a user with that e-mail address.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(348, 1, 'en', 'search', 'search_field', 'Search.....', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(349, 1, 'en', 'search', 'no_result', 'No Result Found!', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(350, 1, 'en', 'validation', 'accepted', 'The :attribute must be accepted.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(351, 1, 'en', 'validation', 'active_url', 'The :attribute is not a valid URL.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(352, 1, 'en', 'validation', 'after', 'The :attribute must be a date after :date.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(353, 1, 'en', 'validation', 'after_or_equal', 'The :attribute must be a date after or equal to :date.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(354, 1, 'en', 'validation', 'alpha', 'The :attribute may only contain letters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(355, 1, 'en', 'validation', 'alpha_dash', 'The :attribute may only contain letters, numbers, dashes and underscores.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(356, 1, 'en', 'validation', 'alpha_num', 'The :attribute may only contain letters and numbers.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(357, 1, 'en', 'validation', 'array', 'The :attribute must be an array.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(358, 1, 'en', 'validation', 'before', 'The :attribute must be a date before :date.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(359, 1, 'en', 'validation', 'before_or_equal', 'The :attribute must be a date before or equal to :date.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(360, 1, 'en', 'validation', 'between.numeric', 'The :attribute must be between :min and :max.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(361, 1, 'en', 'validation', 'between.file', 'The :attribute must be between :min and :max kilobytes.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(362, 1, 'en', 'validation', 'between.string', 'The :attribute must be between :min and :max characters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(363, 1, 'en', 'validation', 'between.array', 'The :attribute must have between :min and :max items.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(364, 1, 'en', 'validation', 'boolean', 'The :attribute field must be true or false.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(365, 1, 'en', 'validation', 'confirmed', 'The :attribute confirmation does not match.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(366, 1, 'en', 'validation', 'date', 'The :attribute is not a valid date.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(367, 1, 'en', 'validation', 'date_equals', 'The :attribute must be a date equal to :date.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(368, 1, 'en', 'validation', 'date_format', 'The :attribute does not match the format :format.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(369, 1, 'en', 'validation', 'different', 'The :attribute and :other must be different.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(370, 1, 'en', 'validation', 'digits', 'The :attribute must be :digits digits.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(371, 1, 'en', 'validation', 'digits_between', 'The :attribute must be between :min and :max digits.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(372, 1, 'en', 'validation', 'dimensions', 'The :attribute has invalid image dimensions.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(373, 1, 'en', 'validation', 'distinct', 'The :attribute field has a duplicate value.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(374, 1, 'en', 'validation', 'email', 'The :attribute must be a valid email address.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(375, 1, 'en', 'validation', 'ends_with', 'The :attribute must end with one of the following: :values', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(376, 1, 'en', 'validation', 'exists', 'The selected :attribute is invalid.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(377, 1, 'en', 'validation', 'file', 'The :attribute must be a file.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(378, 1, 'en', 'validation', 'filled', 'The :attribute field must have a value.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(379, 1, 'en', 'validation', 'gt.numeric', 'The :attribute must be greater than :value.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(380, 1, 'en', 'validation', 'gt.file', 'The :attribute must be greater than :value kilobytes.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(381, 1, 'en', 'validation', 'gt.string', 'The :attribute must be greater than :value characters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(382, 1, 'en', 'validation', 'gt.array', 'The :attribute must have more than :value items.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(383, 1, 'en', 'validation', 'gte.numeric', 'The :attribute must be greater than or equal :value.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(384, 1, 'en', 'validation', 'gte.file', 'The :attribute must be greater than or equal :value kilobytes.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(385, 1, 'en', 'validation', 'gte.string', 'The :attribute must be greater than or equal :value characters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(386, 1, 'en', 'validation', 'gte.array', 'The :attribute must have :value items or more.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(387, 1, 'en', 'validation', 'image', 'The :attribute must be an image.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(388, 1, 'en', 'validation', 'in', 'The selected :attribute is invalid.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(389, 1, 'en', 'validation', 'in_array', 'The :attribute field does not exist in :other.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(390, 1, 'en', 'validation', 'integer', 'The :attribute must be an integer.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(391, 1, 'en', 'validation', 'ip', 'The :attribute must be a valid IP address.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(392, 1, 'en', 'validation', 'ipv4', 'The :attribute must be a valid IPv4 address.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(393, 1, 'en', 'validation', 'ipv6', 'The :attribute must be a valid IPv6 address.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(394, 1, 'en', 'validation', 'json', 'The :attribute must be a valid JSON string.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(395, 1, 'en', 'validation', 'lt.numeric', 'The :attribute must be less than :value.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(396, 1, 'en', 'validation', 'lt.file', 'The :attribute must be less than :value kilobytes.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(397, 1, 'en', 'validation', 'lt.string', 'The :attribute must be less than :value characters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(398, 1, 'en', 'validation', 'lt.array', 'The :attribute must have less than :value items.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(399, 1, 'en', 'validation', 'lte.numeric', 'The :attribute must be less than or equal :value.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(400, 1, 'en', 'validation', 'lte.file', 'The :attribute must be less than or equal :value kilobytes.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(401, 1, 'en', 'validation', 'lte.string', 'The :attribute must be less than or equal :value characters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(402, 1, 'en', 'validation', 'lte.array', 'The :attribute must not have more than :value items.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(403, 1, 'en', 'validation', 'max.numeric', 'The :attribute may not be greater than :max.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(404, 1, 'en', 'validation', 'max.file', 'The :attribute may not be greater than :max kilobytes.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(405, 1, 'en', 'validation', 'max.string', 'The :attribute may not be greater than :max characters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(406, 1, 'en', 'validation', 'max.array', 'The :attribute may not have more than :max items.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(407, 1, 'en', 'validation', 'mimes', 'The :attribute must be a file of type: :values.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(408, 1, 'en', 'validation', 'mimetypes', 'The :attribute must be a file of type: :values.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(409, 1, 'en', 'validation', 'min.numeric', 'The :attribute must be at least :min.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(410, 1, 'en', 'validation', 'min.file', 'The :attribute must be at least :min kilobytes.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(411, 1, 'en', 'validation', 'min.string', 'The :attribute must be at least :min characters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(412, 1, 'en', 'validation', 'min.array', 'The :attribute must have at least :min items.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(413, 1, 'en', 'validation', 'not_in', 'The selected :attribute is invalid.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(414, 1, 'en', 'validation', 'not_regex', 'The :attribute format is invalid.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(415, 1, 'en', 'validation', 'numeric', 'The :attribute must be a number.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(416, 1, 'en', 'validation', 'present', 'The :attribute field must be present.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(417, 1, 'en', 'validation', 'regex', 'The :attribute format is invalid.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(418, 1, 'en', 'validation', 'required', 'The :attribute field is required.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(419, 1, 'en', 'validation', 'required_if', 'The :attribute field is required when :other is :value.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(420, 1, 'en', 'validation', 'required_unless', 'The :attribute field is required unless :other is in :values.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(421, 1, 'en', 'validation', 'required_with', 'The :attribute field is required when :values is present.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(422, 1, 'en', 'validation', 'required_with_all', 'The :attribute field is required when :values are present.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(423, 1, 'en', 'validation', 'required_without', 'The :attribute field is required when :values is not present.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(424, 1, 'en', 'validation', 'required_without_all', 'The :attribute field is required when none of :values are present.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(425, 1, 'en', 'validation', 'same', 'The :attribute and :other must match.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(426, 1, 'en', 'validation', 'size.numeric', 'The :attribute must be :size.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(427, 1, 'en', 'validation', 'size.file', 'The :attribute must be :size kilobytes.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(428, 1, 'en', 'validation', 'size.string', 'The :attribute must be :size characters.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(429, 1, 'en', 'validation', 'size.array', 'The :attribute must contain :size items.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(430, 1, 'en', 'validation', 'starts_with', 'The :attribute must start with one of the following: :values', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(431, 1, 'en', 'validation', 'string', 'The :attribute must be a string.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(432, 1, 'en', 'validation', 'timezone', 'The :attribute must be a valid zone.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(433, 1, 'en', 'validation', 'unique', 'The :attribute has already been taken.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(434, 1, 'en', 'validation', 'uploaded', 'The :attribute failed to upload.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(435, 1, 'en', 'validation', 'url', 'The :attribute format is invalid.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(436, 1, 'en', 'validation', 'uuid', 'The :attribute must be a valid UUID.', '2022-08-21 11:13:09', '2022-08-21 11:13:09'),
(437, 1, 'en', 'validation', 'custom.attribute-name.rule-name', 'custom-message', '2022-08-21 11:13:09', '2022-08-21 11:13:09');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `designation_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `facebook` varchar(191) DEFAULT NULL,
  `twitter` varchar(191) DEFAULT NULL,
  `instagram` varchar(191) DEFAULT NULL,
  `linkedin` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `whatsapp` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `designation_id`, `title`, `slug`, `description`, `image_path`, `facebook`, `twitter`, `instagram`, `linkedin`, `email`, `phone`, `whatsapp`, `website`, `status`, `created_at`, `updated_at`) VALUES
(7, 6, 'Ikramul Hasan', 'ikramul-hasan', NULL, 'Untitled design_1738781784.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-02-05 18:56:25', '2025-02-12 10:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_06_21_034842_create_article_categories_table', 1),
(4, '2019_06_21_174850_create_articles_table', 1),
(5, '2019_06_23_085924_create_faq_categories_table', 1),
(6, '2019_06_23_090734_create_faqs_table', 1),
(7, '2019_06_23_125726_create_settings_table', 1),
(8, '2020_10_19_181445_create_portfolio_categories_table', 1),
(9, '2020_10_20_054101_create_portfolios_table', 1),
(10, '2020_10_20_064637_create_portfolio_category_table', 1),
(11, '2020_10_20_065345_create_designations_table', 1),
(12, '2020_10_20_160810_create_members_table', 1),
(13, '2020_10_20_190635_create_clients_table', 1),
(14, '2020_10_21_065124_create_testimonials_table', 1),
(15, '2020_10_21_073444_create_sliders_table', 1),
(16, '2020_10_21_081243_create_services_table', 1),
(17, '2020_10_21_160828_create_work_processes_table', 1),
(18, '2020_10_22_155439_create_why_choose_us_table', 1),
(19, '2020_10_22_163117_create_counters_table', 1),
(20, '2020_10_22_171933_create_contacts_table', 1),
(21, '2020_10_22_175247_create_subscribers_table', 1),
(22, '2020_10_22_182912_create_socials_table', 1),
(23, '2020_10_23_132746_create_pages_table', 1),
(24, '2020_10_23_140659_create_pricings_table', 1),
(25, '2020_10_23_172412_create_sections_table', 1),
(26, '2020_10_27_181842_create_abouts_table', 1),
(27, '2020_11_10_174625_create_live_chats_table', 2),
(28, '2020_11_14_081146_create_email_templates_table', 3),
(29, '2020_11_12_171920_create_get_quotes_table', 4),
(30, '2020_11_12_181128_create_serviceables_table', 4),
(31, '2020_11_14_183701_create_invoices_table', 5),
(32, '2019_03_21_160417_create_languages_table', 6),
(33, '2021_10_20_191833_create_page_setups_table', 6),
(34, '2014_04_02_193005_create_translations_table', 7),
(35, '2019_12_14_000001_create_personal_access_tokens_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `type` enum('casestudy','resources','footer','') NOT NULL DEFAULT 'casestudy',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `description`, `image_path`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Privacy & Policy', 'privacy-policy', '<p>Effective Date: 12-02-2025<p><b>1. Introduction</b><br>\r\nWelcome to MSN SoftTech. We are committed to protecting your privacy and ensuring that your personal information is handled securely. This Privacy Policy explains how we collect, use, and protect your data when you use our website and services.</p><p><b>2. Information We Collect</b><br>\r\nWe may collect the following types of information:</p><ul>\r\n	<li><b>Personal Information:</b> Name, email address, phone number, and other details provided when you contact us or use our services.</li>\r\n	<li><b>Technical Information:</b> IP address, browser type, device details, and usage data to improve our website performance.</li>\r\n	<li><b>Payment Information:</b> If you purchase our services, we collect payment details through secure third-party payment processors.</li>\r\n	<li><b>Cookies and Tracking Technologies:</b> We use cookies to enhance your browsing experience and analyze site traffic.</li>\r\n	<li><b>User-Generated Content:</b> Any content, reviews, or feedback you provide on our website.</li>\r\n	<li><b>Communication Data:</b> Records of correspondence between you and MSN SoftTech, including support inquiries and service requests.</li>\r\n	<li><b>Location Data:</b> If enabled, we may collect location data to provide localized services.</li>\r\n	<li><b>Social Media Data:</b> If you interact with us through social media platforms, we may collect publicly available information from your profile.</li>\r\n</ul><p><b>3. How We Use Your Information</b><br>\r\nWe use your information for the following purposes:</p><ul>\r\n	<li>To provide and improve our web development and graphic design services.</li>\r\n	<li>To communicate with you regarding inquiries, support, or updates.</li>\r\n	<li>To process transactions securely.</li>\r\n	<li>To enhance our website functionality and user experience.</li>\r\n	<li>To comply with legal obligations and prevent fraudulent activities.</li>\r\n	<li>To personalize your experience based on your preferences and interactions with our services.</li>\r\n	<li>To conduct research, surveys, and customer feedback analysis to enhance service offerings.</li>\r\n	<li>To monitor and analyze trends, usage, and activities for business intelligence.</li>\r\n	<li>To deliver targeted advertisements or promotional materials based on your preferences.</li>\r\n</ul><p><b>4. Data Sharing and Security</b></p><ul>\r\n	<li>We do not sell, trade, or rent your personal information to third parties.</li>\r\n	<li>We may share your data with trusted service providers for hosting, analytics, or payment processing.</li>\r\n	<li>We implement industry-standard security measures to protect your information from unauthorized access.</li>\r\n	<li>We ensure that any third-party services comply with strict data protection standards.</li>\r\n	<li>We conduct regular security audits to maintain the integrity of your information.</li>\r\n	<li>If required by law or in response to valid legal requests, we may disclose your personal data.</li>\r\n</ul><p><b>5. Your Rights and Choices</b></p><ul>\r\n	<li>You can request access, correction, or deletion of your personal data.</li>\r\n	<li>You may opt out of marketing communications at any time.</li>\r\n	<li>You can manage cookie preferences through your browser settings.</li>\r\n	<li>You have the right to data portability, allowing you to request a copy of your personal data.</li>\r\n	<li>You can request restrictions on certain processing of your data if you have concerns about its accuracy or usage.</li>\r\n	<li>You can withdraw consent for data collection where applicable by contacting us directly.</li>\r\n</ul><p><b>6. Third-Party Links</b><br>\r\nOur website may contain links to third-party websites. We are not responsible for their privacy policies or content. Please review their policies before providing any personal data.</p><p><b>7. Data Retention Policy</b></p><ul>\r\n	<li>We retain personal data only for as long as necessary to fulfill the purposes outlined in this Privacy Policy.</li>\r\n	<li>When data is no longer needed, we securely delete or anonymize it.</li>\r\n	<li>Some information may be retained for compliance with legal or regulatory obligations.</li>\r\n	<li>If you close your account or discontinue using our services, we may retain limited information as required by law.</li>\r\n</ul><p><b>8. Cookies and Tracking Technologies</b></p><ul>\r\n	<li>We use cookies to collect usage data and improve site functionality.</li>\r\n	<li>You can manage cookie preferences through your browser settings.</li>\r\n	<li>Some third-party services we use, such as analytics and advertising networks, may also use cookies.</li>\r\n	<li>Disabling cookies may affect the functionality of our website.</li>\r\n</ul><p><b>9. Changes to This Privacy Policy</b><br>\r\nWe may update this policy from time to time. Any changes will be posted on this page with an updated effective date. If the changes are significant, we will notify you through email or a website announcement.</p><p><b>10. Children\'s Privacy</b><br>\r\nOur services are not directed at children under the age of 13. We do not knowingly collect personal information from children. If you believe a child has provided us with their information, please contact us for removal.</p><p><b>11. Contact Us</b><br>\r\nIf you have any questions or concerns regarding this Privacy Policy, please contact us at:</p><p><b>MSN SoftTech</b><br>\r\nEmail: <b>support@msnsofttech.com</b></p></p>\n', 'noimage.jpg', 'footer', 1, '2020-10-30 12:47:49', '2025-02-11 20:19:56'),
(2, 'Terms & Conditions', 'terms-conditions', '<p>Effective Date: 12-02-2025<p><b>1. Introduction</b><br>\r\nWelcome to MSN SoftTech. By accessing and using our website and services, you agree to comply with and be bound by these Terms &amp; Conditions. If you do not agree with any part of these terms, please do not use our services.</p><p><b>2. Services</b><br>\r\nMSN SoftTech provides web development and graphic design-related services. We reserve the right to modify or discontinue any service without prior notice.</p><p><b>3. User Responsibilities</b></p><ul>\r\n	<li>You must be at least 18 years old to use our services.</li>\r\n	<li>You agree to provide accurate and complete information when using our services.</li>\r\n	<li>You are responsible for maintaining the confidentiality of your account details and activities.</li>\r\n	<li>You must not engage in any illegal activities or violate applicable laws while using our services.</li>\r\n</ul><p><b>4. Payments &amp; Refunds</b></p><ul>\r\n	<li>20% of our services must be paid in full before the project begins, unless otherwise agreed.We use third-party payment processors to ensure secure transactions.</li>\r\n	<li>Refunds will be issued at our discretion based on service agreements and project scope.</li>\r\n	<li>No refunds will be provided once a project has been completed or delivered.</li>\r\n</ul><p><b>5. Intellectual Property</b></p><ul>\r\n	<li>All content, including website design, logos, and materials created by MSN SoftTech, remain our intellectual property unless otherwise stated.</li>\r\n	<li>Clients receive the right to use the delivered designs and code, but redistribution or resale is prohibited without our consent.</li>\r\n	<li>Any third-party content used in projects will be properly licensed or attributed.</li>\r\n</ul><p><b>6. Limitation of Liability</b></p><ul>\r\n	<li>MSN SoftTech is not liable for any indirect, incidental, or consequential damages resulting from the use of our services.</li>\r\n	<li>We do not guarantee uninterrupted or error-free service and are not responsible for any data loss or security breaches.</li>\r\n	<li>Our maximum liability for any claim arising from our services will not exceed the amount paid for the respective service.</li>\r\n</ul><p><b>7. Confidentiality</b></p><ul>\r\n	<li>We will not disclose any client-provided confidential information without prior consent.</li>\r\n	<li>Clients must not disclose any proprietary information about our services without our written approval.</li>\r\n</ul><p><b>8. Termination</b></p><ul>\r\n	<li>We reserve the right to terminate services or suspend accounts without prior notice if users violate these Terms &amp; Conditions.</li>\r\n	<li>Clients may discontinue using our services at any time, but no refunds will be provided after project completion.</li>\r\n</ul><p><b>9. Third-Party Links &amp; Services</b></p><ul>\r\n	<li>Our website may contain links to third-party websites. We are not responsible for their content, privacy policies, or practices.</li>\r\n	<li>We may use third-party services for hosting, analytics, and payment processing, and users agree to their respective terms.</li>\r\n</ul><p><b>10. Changes to Terms</b><br>\r\nWe reserve the right to update or modify these Terms &amp; Conditions at any time. Changes will be effective immediately upon posting.</p><p><b>11. Contact Us</b><br>\r\nFor any questions or concerns regarding these Terms &amp; Conditions, please contact us at:</p><p><b>MSN SoftTech</b><br>\r\nEmail: <b>support@msnsofttech.com</b></p></p>\n', 'noimage.jpg', 'footer', 1, '2020-10-30 12:48:49', '2025-02-11 20:10:50'),
(3, 'Disclaimer', 'disclaimer', '<p><b>Effective Date:</b> 12-02-2025<h2><b>1. General Information</b></h2><p>The information provided by <b>MSN SoftTech</b> (&ldquo;Company,&rdquo; &ldquo;we,&rdquo; &ldquo;our,&rdquo; or &ldquo;us&rdquo;) on our website <b><a href=\"https://msnsofttech.com/\" target=\"_blank\">https://msnsofttech.com/</a></b>&nbsp;and through our services is for general informational purposes only. While we make every effort to ensure the accuracy and reliability of the information, we <b>make no representations or warranties</b> of any kind, express or implied, regarding the completeness, accuracy, reliability, or availability of our website or services. Any reliance you place on such information is strictly at your own risk.</p><h2><b>2. No Professional or Legal Advice</b></h2><p>The content available on our website or through our services does not constitute <b>legal, financial, business, or professional advice</b>. You should not rely solely on the information provided by us. Instead, you should consult with a qualified professional before making any decisions related to your business, finances, or legal matters.</p><h2><b>3. Third-Party Links &amp; External Content</b></h2><p>Our website may contain links to third-party websites, applications, or services. These links are provided for convenience and informational purposes only. We do not <b>own, control, endorse, or assume responsibility</b> for the content, policies, or practices of any third-party websites.</p><p>If you access a third-party website through a link on our website, you do so at your <b>own risk</b>, and we recommend reviewing their terms and privacy policies before engaging with them.</p><h2><b>4. Limitation of Liability</b></h2><p>To the fullest extent permitted by applicable law, <b>MSN SoftTech</b> shall not be liable for any <b>direct, indirect, incidental, consequential, special, or punitive damages</b> arising from:</p><ul>\r\n	<li>The use of, or inability to use, our website or services.</li>\r\n	<li>Errors, inaccuracies, or omissions in the content provided.</li>\r\n	<li>Any unauthorized access to or use of our servers and personal information.</li>\r\n	<li>Any service interruptions, delays, or performance issues.</li>\r\n</ul><p>We <b>do not guarantee</b> that our website will be free of viruses, malware, or other harmful components. Users are responsible for implementing <b>security measures</b> on their devices before accessing our website.</p><h2><b>5. Service Availability &amp; Changes</b></h2><p>We reserve the right to modify, suspend, or discontinue any <b>service, product, or feature</b> offered on our website at any time without prior notice. We are not responsible for any <b>losses or damages</b> that may result from such changes or interruptions.</p><h2><b>6. Intellectual Property Rights</b></h2><ul>\r\n	<li>All content on our website, including text, graphics, images, logos, and code, is the <b>intellectual property</b> of <b>MSN SoftTech</b>, unless otherwise stated.</li>\r\n	<li>Unauthorized reproduction, modification, or distribution of our content <b>without prior written consent</b> is strictly prohibited.</li>\r\n	<li>Clients who purchase design or development services receive <b>a license to use the delivered work</b>, but redistribution, resale, or modification beyond the agreed terms is not allowed.</li>\r\n</ul><h2><b>7. Testimonials &amp; Results Disclaimer</b></h2><p>Our website may include testimonials or case studies showcasing the experiences of past clients. These are provided for informational purposes and <b>do not guarantee similar results</b> for future clients. Each project is unique, and outcomes may vary based on individual requirements, circumstances, and external factors.</p><h2><b>8. Earnings &amp; Business Results Disclaimer</b></h2><p>Any references to <b>earnings, profits, revenue, or business success</b> on our website are <b>illustrative only</b> and should not be considered as guarantees or typical results. Success depends on a variety of factors, including market conditions, individual effort, competition, and external circumstances beyond our control.</p><h2><b>9. Confidentiality &amp; Data Protection</b></h2><p>We respect our clients&rsquo; confidentiality and take reasonable measures to <b>protect sensitive information</b> shared with us. However, we <b>do not guarantee absolute security</b> of any data transmitted through our website or services.</p><p>For details on how we handle personal data, please review our <b>Privacy Policy</b>.</p><h2><b>10. Force Majeure</b></h2><p>We shall not be liable for any delays or failures in performance resulting from events beyond our reasonable control, including but not limited to:</p><ul>\r\n	<li>Natural disasters (earthquakes, floods, fires, etc.).</li>\r\n	<li>Government regulations, pandemics, or national emergencies.</li>\r\n	<li>Cyber-attacks, server failures, or technical disruptions.</li>\r\n	<li>Labor strikes, supply chain issues, or business shutdowns.</li>\r\n</ul><h2><b>11. Updates &amp; Modifications</b></h2><p>We reserve the right to update or modify this Disclaimer <b>at any time without prior notice</b>. Any changes will be effective <b>immediately</b> upon posting on our website. You are encouraged to review this page periodically to stay informed.</p><h2><b>12. Contact Information</b></h2><p>If you have any questions regarding this Disclaimer, please contact us at:</p><p><b>MSN SoftTech</b><br>\r\nEmail: <b>support@msnsofttech.com</b></p></p>\n', 'noimage.jpg', 'footer', 1, '2020-10-30 12:49:28', '2025-02-11 20:27:43');

-- --------------------------------------------------------

--
-- Table structure for table `page_setups`
--

CREATE TABLE `page_setups` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_setups`
--

INSERT INTO `page_setups` (`id`, `title`, `slug`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `created_at`, `updated_at`) VALUES
(10, 'Home', 'home', 'Home', NULL, NULL, 1, NULL, NULL),
(11, 'About Us', 'about-us', 'About Us', NULL, NULL, 1, NULL, NULL),
(12, 'Services', 'services', 'Services', NULL, NULL, 1, NULL, NULL),
(13, 'Portfolio', 'portfolio', 'Portfolio', NULL, NULL, 1, NULL, NULL),
(14, 'Pricing', 'pricing', 'Pricing', NULL, NULL, 1, NULL, NULL),
(15, 'Blog', 'blog', 'Blog', NULL, NULL, 1, NULL, NULL),
(16, 'FAQs', 'faqs', 'FAQs', NULL, NULL, 1, NULL, NULL),
(17, 'Contact Us', 'contact-us', 'Contact Us', NULL, NULL, 1, NULL, NULL),
(18, 'Get A Quote', 'get-quote', 'Get A Quote', NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolios`
--

CREATE TABLE `portfolios` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` longtext DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `video_id` varchar(191) DEFAULT NULL,
  `link` varchar(191) DEFAULT NULL,
  `link2` varchar(255) DEFAULT NULL,
  `link3` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolios`
--

INSERT INTO `portfolios` (`id`, `title`, `slug`, `description`, `image_path`, `video_id`, `link`, `link2`, `link3`, `status`, `created_at`, `updated_at`) VALUES
(9, 'Multipurpose Business Website CMS', 'multipurpose-business-website-cms', '<p>It has almost all the features that a business website has to offer. You can manage and change the Title of any section / feature of this website. You are also able to hide or show any section or pages of this website dynamically from the admin panel. You can active or deactivate your Quote Request page and manage invoices and payment. So you can easily and completely rearrange this website as your business structure. Also this website has a very professional looks &amp; fully responsive design so that this website can attract your targeted visitors and display smoothly in all kinds of devices &amp; browsers.</p>\r\n\r\n<h2>Which type of business can it be used?</h2>\r\n\r\n<p>This Website can be used for multipurpose business such as Creative Agency, Automotive, Electronics, Power, Gym, Spa, Salon, Parlour, Hotels, Restaurants, Cleaning Service, Car Painter, Servicing Center, Garments, Leather, Textile, Construction, Architecture, Interior, Chemical, Food, Personal, Freelancer Portfolio, Software, IT Company, Lawyer, Transport, Financial services, Bank, Consulting Firm and many more Industry or Factory.</p>\r\n\r\n<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\" style=\"width:700px\">\r\n	\r\n		<tr>\r\n			<td>\r\n			<h2><b>Website Features:</b></h2>\r\n\r\n			<ul>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<h2><b>Application Features:</b></h2>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>100% Dynamic Contents.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Easy Installation.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>About Us &amp; Contact Page</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Simple But Powerful Admin Panel.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Services Page</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Dynamic Management System.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Portfolios &amp; Category Page</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Quote Request</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>FAQs &amp; Category Page</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Send Invoices</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Pricing Plan Page</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Get Payment via PayPal</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Blog Pages &amp; Search</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Email Template</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Sliders Section</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Live Chat</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Features Section</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Customer Notification via Email</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Work Process Section</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Admin Notification via Email</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Counter Section</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Contact Details</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Testimonial Section</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage About Us (Mission &amp; Vision)</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Clients / Partners Section</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Sliders</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Team Members Section</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Portfolios / Projects</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Get A Quote Request</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Services</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Invoice &amp; Payment</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Partners / Clients</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Subscribe Section</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Pricing Packages / Plan</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Messenger Live Chat Integrated</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Testimonials / Reviews</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>WhatsApp Live Chat Integrated</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Team Members</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Translatable Content</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage FAQ&rsquo;s / Knowledge Base</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Google Font Integration</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Blog / News Posts</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Built with Bootstrap v4</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Work Process</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Responsive Design</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Features / Why Choose Us</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Font Awesome Icons</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Counters</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Animated CSS Integration</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Subscribers List</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Fully Customizable</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Messages / Emails</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Contact Mail System Integration</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Manage Pages &amp; Sections</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Custom Logo &amp; Icons</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Add Unlimited Pages</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>SEO Optimized</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Admin Settings</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Cross Browser Support.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Website Settings</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Custom CSS Settings</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Social Profiles Settings</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Custom Meta Keyword Settings</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>PayPal Payment Method Integrated</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>SEO Friendly URL Slug</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Forgot Password</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Social Meta Tags Integrated</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Password Change</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>&nbsp;</td>\r\n			<td>\r\n			<ul>\r\n				<li>All Files are Well Commented</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>&nbsp;</td>\r\n			<td>\r\n			<ul>\r\n				<li>Documentation Included</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n	\r\n</table>\r\n\r\n<p>&nbsp;</p>', '2ae5447e-e3dd-4815-af9a-088be21c3a2c_1739232450.png', NULL, 'https://msnsofttech.freepikpro.com/', 'https://msnsofttech.freepikpro.com/', 'https://msnsofttech.freepikpro.com/', 1, '2025-02-05 13:05:01', '2025-02-11 00:07:30'),
(10, 'TrendTechMart - eCommerce CMS', 'trendtechmart-ecommerce-cms', '<p>TrendTechMart &ndash; is All in One eCommerce Shopping Platform. If you have planned to buy a single vendor eCommerce shopping platform. You can choose TrendTechMart as the most suitable platform for single-vendor eCommerce.</p>\r\n\r\n<p><b>You can use it for :</b>&nbsp;Man &amp; Women Fashion Shop, Electronics &amp; Computers Shop, Toys &amp; Kids Shop, Food &amp; Grocery Shop, Tools &amp; Parts Shop, Beauty &amp; Health Shop, Watch &amp; Jewelry Shop, Home &amp; Furniture Shop, Sports &amp; Outdoors Shop, Digital Marketplace, Digital Product Shop. Affiliate Product Shop, Software Licence key Shop, etc.</p>\r\n\r\n<p>eCommerce platforms are gaining more and more popularity nowadays and we keep maintaining all the demands of our users. The script has unlimited category, brands, products, attribute. coupons, orders, category create options. It comes with 12 payment gateways, full content management system, SEO, order tracking system, and more&hellip;&nbsp;Read highlighted features from down.</p>\r\n\r\n<h2>Physical Product Sell :</h2>\r\n\r\n<p>Physical product means an Identified Product and its packaging as shipped or delivered to consumers.<br />\r\n<b>TrendTechMart </b>allows you to sell any kind of physical product. You can add product attributes. You can add attribute-wise price. You can also add the attribute-wise stock.</p>\r\n\r\n<h2>Digital Product Sell :</h2>\r\n\r\n<p>A digital product is an intangible asset or piece of media that can be sold and distributed repeatedly online.<br />\r\n<b>TrendTechMart </b>allows you to sell digital product (web themes &amp; templates, code, video, audio, graphics, photos, 3D files, etc) Without licence code.</p>\r\n\r\n<h2>Affiliate Product Sell :</h2>\r\n\r\n<p><b>TrendTechMart </b>allows you to sell affiliate products using affiliate links. You can add product with affiliate link from (Amazon, Shopify, eBay, Alibaba, etc like this). When users clicks on your added product they will redirect your affiliate link.</p>\r\n\r\n<h2>Software License key Sell :</h2>\r\n\r\n<p><b>TrendTechMart </b>allows you to sell license key. You can sell software or games license keys. Example: You can add many unique license keys for windows 11. If the user buys windows 11 license key. The user will get a unique Windows 11 license key. Same you can sell other software license keys.</p>\r\n\r\n<h2>Product Attribute Option :</h2>\r\n\r\n<p><b>TrendTechMart </b>has fully functional attribute management system. You can add unlimitade product attribute. You can add unlimitade attribute options. Attribute wise product price add option. Attribute wise product stock add option.</p>\r\n\r\n<h2>CSV Product Upload :</h2>\r\n\r\n<p><b>TrendTechMart </b>allows you to upload CSV products. For that you need to follow our sample CSV file structure. In product CSV upload page you will see sample CSV download option. Click this button and download file.<br />\r\n<b>Video:</b>&nbsp;<a href=\"https://youtu.be/Cidd0jvqBe8?si=tGMs8sndcYVNpp8M\">Click here to watch the video.</a><br />\r\n<b>Demo CSV file:</b>&nbsp;<a href=\"https://docs.google.com/spreadsheets/d/1NQJMoi2gJj46mA3qz-OetZ7u_1aS8UhoWKlW7p6x-2A/edit?usp=sharing\">Click here.</a></p>\r\n\r\n<h2>All Access :</h2>\r\n\r\n<p>Admin Access :<br />\r\nAdmin Link:&nbsp;<a href=\"https://msnsofttech.com/TrendTechMart/admin/login\" target=\"_blank\"><b>View Link</b></a><br />\r\nEmail:&nbsp;<b>admin@gmail.com</b><br />\r\nPassword: <b>password</b><br />\r\n<br />\r\nUser Access :<br />\r\nUser Link:&nbsp;<a href=\"https://msnsofttech.com/TrendTechMart/user/login\" target=\"_blank\"><b>View Link</b></a><br />\r\nEmail:&nbsp;<b>user@gmail.com</b><br />\r\nPassword: <b>password</b></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">\r\n	\r\n		<tr>\r\n			<td>\r\n			<h3><b>Highlighted Features</b></h3>\r\n			</td>\r\n			<td>\r\n			<h3><b>Admin Features</b></h3>\r\n			</td>\r\n			<td>\r\n			<h3><b>User Features</b></h3>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>\r\n				<p>Clean and Modern Fronted &amp; Admin Interface.</p>\r\n				</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>100% Secure Admin Dashboard.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Clean User Interface.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>100% Responsive Design.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Categories Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Dashboard Manager.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>4 Home Page.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Product Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Profile Manager.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Physical Product Sale.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Product Brands Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Tickets Manager.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Digital Product Sale.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Product Attribute Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Order Log.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Affiliate Product Sale.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Product Create Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Address Manager.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Licence Product Sale.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Product Campaign Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Wishlist Manager.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Product Attribute Option.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Product CSV Import &amp; Export Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Delete Account Option.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Attribute Wise Product Price.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Product Reviews Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>And More&hellip;.</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Attribute Wise Product Stock.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Product Order Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Multi Currency Support.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Transaction Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>12 Payment Method.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Coupon Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Guest Checkout Option.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Shipping Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>State-wise Tax.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Tax Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Currency/Price Separator option with Dot and Comma.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Currency Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Ajax Product Load.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Payment Gateway Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>CSV Product Upload.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Customer Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>CSV Product Export.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Ticket Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>CSV Order Export.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>General Settings Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>CSV Transactions Export.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Home Page Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Bulk Delete Option.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Slider Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Image Lazy Load.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>SEO Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Sitemap Generator.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Service Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Google Adsence.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Dynamic Color Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Google Analytics.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Slider Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Google reCaptcha.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Banner Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Facebook Pixel.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Page Visibility (On/Off section) Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Facebook Messenger.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Social Login Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Database Backup System.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Email Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>System Backup System.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>SMS Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Currency Left Or Right Show.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Announcement Popup Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Sale Analytic.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Cookie Alert Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Coupon Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Maintainance Mode Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Product Variants.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Sitemap Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>RTL Support.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Language Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Email Notification.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>FAQ Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>SMS Notification.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Blog Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Product Wishlist.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Dynamic Page Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Product Compare.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>Subscribers Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Campaign Offer.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>User Roll Permission Management.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Flash Deal.</li>\r\n			</ul>\r\n			</td>\r\n			<td>\r\n			<ul>\r\n				<li>And More&hellip;.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Unlimited Color Option.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Tax Module.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Shipping Module.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Maintains Module.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Custom CSS Settings.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>GDPR Cookie Alert.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Translate Frontend &amp; Admin Dashboard.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Announcement &amp; Popup Module .</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Support Modern Browser and Cross-browser Compatibility.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Video Tutorials.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Regular Updates Facilities.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Lifetime Free Update.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<ul>\r\n				<li>Premium and Quick Support.</li>\r\n			</ul>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n	\r\n</table>\r\n\r\n<p>&nbsp;</p>', '601f9575-95c8-43d0-bac4-6f1831d95569_1739232277.png', NULL, 'https://msnsofttech.com/TrendTechMart/', 'https://msnsofttech.com/TrendTechMart/admin/login', 'https://msnsofttech.com/TrendTechMart/user/login', 1, '2025-02-08 20:14:06', '2025-02-11 16:12:14'),
(11, 'Grocery - Multivendor Organic & Grocery Laravel eCommerce', 'grocery-multivendor-organic-grocery-laravel-ecommerce', '<p>Grocery is an attractive Laravel multivendor eCommerce script specially designed for the multipurpose shops like mega store, grocery store, supermarket, organic shop, and online stores selling products like beverages, vegetables, fruits, ice creams, paste, herbs, juice, meat, cold drinks, sausages, cocktails, soft drinks, cookies&hellip;</p>\r\n\r\n<p>We have a dedicated support center for all of your support needs. It includes our Documentation and Ticket system for any questions you have. We usually get back to you within 12-24 hours.</p>\r\n\r\n<table>\r\n	\r\n		<tr>\r\n			<td><img alt=\"support\" src=\"https://camo.envatousercontent.com/9c50a0e689a017bb74ff8ae1f1646241be7bd30e/68747470733a2f2f626f74626c652e636f6d2f73746f726167652f656e7661746f2f737570706f72742d63656e7465722d312e6a706567\" /></td>\r\n			<td><img alt=\"document online\" src=\"https://camo.envatousercontent.com/f3f7d2f8a4c657db69f3eac521ca6c947efe3b17/68747470733a2f2f626f74626c652e636f6d2f73746f726167652f656e7661746f2f6f6e6c696e652d646f63756d656e746174696f6e2e6a7065673f763d312e30\" /></td>\r\n			<td><img alt=\"change log\" src=\"https://camo.envatousercontent.com/36013960a602761c5453e36fd2015cf0f949cf6d/68747470733a2f2f626f74626c652e636f6d2f73746f726167652f656e7661746f2f6368616e67652d6c6f672e6a706567\" /></td>\r\n		</tr>\r\n	\r\n</table>\r\n\r\n<p>Grocery includes a lot of pre-designed layouts for the home page, product page to give you the best selections in customization. Grocery is built based on the Technology shop website. But Grocery is also suitable for other eCommerce websites such as fashion, electronic, organic, sneaker, shoes, glasses, accessories, supermarket&hellip; or anything you want.</p>\r\n\r\n<p>Grocery includes the administration system, all of which are consistent in terms of UI / UX design, which makes it easy to set up a complete e-commerce system quickly and professionally.</p>\r\n\r\n<p>Grocery is built with Bootstrap 5, HTML5, CSS3 &amp; jQuery. It is Responsive, Retina ready &amp; Multi-Device supported. All code is beautifully written &amp; W3C Validated. Grocery was written by SASS, with a 7-1 pattern.</p>\r\n\r\n<h2>Demo</h2>\r\n\r\n<ul>\r\n	<li>Homepage:&nbsp;<a href=\"https://grocery.msnsofttech.com/\" target=\"_blank\"><b>https://grocery.msnsofttech.com/</b></a></li>\r\n	<li>Admin panel:&nbsp;<a href=\"https://grocery.msnsofttech.com/admin\" target=\"_blank\"><b>https://grocery.msnsofttech.com/admin</b></a>\r\n	<ul>\r\n		<li>Admin account:&nbsp;<b>admin &ndash; 12345678</b>&nbsp;(username &amp; password are autofilled)</li>\r\n	</ul>\r\n	</li>\r\n	<li>Customer login page:&nbsp;<a href=\"https://grocery.msnsofttech.com/login\" target=\"_blank\"><b>https://grocery.msnsofttech.com/login</b></a>\r\n	<ul>\r\n		<li>Customer account:&nbsp;customer@botble.com&nbsp;&ndash; 12345678</li>\r\n	</ul>\r\n	</li>\r\n	<li>Vendor dashboard:&nbsp;<b><a href=\"https://msnsofttech.com/vendor/dashboard\" target=\"_blank\">https://grocery.msnsofttech.com/vendor/dashboard</a></b>\r\n	<ul>\r\n		<li>Vendor account:&nbsp;vendor@botble.com&nbsp;&ndash; 12345678</li>\r\n	</ul>\r\n	</li>\r\n</ul>\r\n\r\n<h2>&nbsp;</h2>\r\n\r\n<h2>Test accounts for payment</h2>\r\n\r\n<ul>\r\n	<li>PayPal:&nbsp;<b>test@botble.com&nbsp;&ndash; 12345678</b></li>\r\n	<li>Credit Card for Stripe:&nbsp;<b>4242 4242 4242 4242 &ndash; Anything in the CVV and expiration date</b></li>\r\n	<li>Credit Card for SSLCommerz &amp; Razorpay:&nbsp;<b>4111111111111111, Exp: 12/25, CVV: 111</b></li>\r\n</ul>\r\n\r\n<h2>&nbsp;</h2>\r\n\r\n<h2>Key Features</h2>\r\n\r\n<ul>\r\n	<li>Buy One Time &amp;&nbsp;<b>Get Free Updates Forever</b>&nbsp;</li>\r\n	<li><b>Free Theme Installation</b>&nbsp;&ndash; If you will face any problem during installation &ndash; we will help you and It&rsquo;s&nbsp;<b>FREE</b></li>\r\n	<li>Multi-language, unlimited languages.</li>\r\n	<li>RTL support, both admin panel and front theme.</li>\r\n	<li>Fully Ecommerce features: product catalog, product attributes, product variations, product collections, discounts, shipping&hellip;\r\n	<ul>\r\n		<li>Sell Simple or Variable Products</li>\r\n		<li>Built-in Order Tracking page</li>\r\n		<li>Unlimited Categories &amp; Sub-Categories</li>\r\n		<li>Filter Products (e.g. by size, color, brands, categories, etc.)</li>\r\n		<li>Optional Wishlist</li>\r\n		<li>Color, Label, and Image Swatches</li>\r\n		<li>Frequently Bought Together</li>\r\n		<li>Advanced Typography</li>\r\n		<li>Single checkout page</li>\r\n		<li>Support many payment methods: PayPal, Stripe, Paystack, Razorpay, Mollie, SSLCommerz&hellip;</li>\r\n		<li>Multi-currency</li>\r\n		<li>Guest checkout</li>\r\n	</ul>\r\n	</li>\r\n	<li>Page, blog, menu, contact, newsletter, slider&hellip; modules are provided with the use of components to avoid boilerplate code.</li>\r\n	<li>Powerful media system, also support Amazon S3, DigitalOcean Spaces, Wasabi.</li>\r\n	<li>SEO &amp; sitemap support: access&nbsp;sitemap.xml&nbsp;to see more.</li>\r\n	<li>Google Analytics: display analytics data in admin panel.</li>\r\n	<li>Translation tool: easy to translate front theme and admin panel to your language.</li>\r\n	<li>Beautiful theme is ready to use.</li>\r\n	<li>Powerful Permission System: Manage user, team, role by permissions. Easy to manage user by permissions.</li>\r\n	<li>Admin template comes with color schemes to match your taste.</li>\r\n	<li>Fully Responsive: Compatible with all screen resolutions.</li>\r\n	<li>Coding Standard: All code follow coding standards PSR-2 and best practices.</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h2>Requirements</h2>\r\n\r\n<ul>\r\n	<li>Apache, nginx, or another compatible web server.</li>\r\n	<li>PHP = 8.2 Higher</li>\r\n	<li>MySQL Database server</li>\r\n	<li>PDO PHP Extension</li>\r\n	<li>OpenSSL PHP Extension</li>\r\n	<li>Mbstring PHP Extension</li>\r\n	<li>Exif PHP Extension</li>\r\n	<li>Fileinfo Extension</li>\r\n	<li>XML PHP Extension</li>\r\n	<li>Ctype PHP Extension</li>\r\n	<li>JSON PHP Extension</li>\r\n	<li>Tokenizer PHP Extension</li>\r\n	<li>Module Re_write server</li>\r\n	<li>PHP_CURL Module Enable</li>\r\n</ul>', 'Untitled design (2)_1739228869.png', NULL, 'https://grocery.msnsofttech.com/', 'https://grocery.msnsofttech.com/admin/login', 'https://grocery.msnsofttech.com/login', 1, '2025-02-10 11:06:15', '2025-02-10 23:33:44');
INSERT INTO `portfolios` (`id`, `title`, `slug`, `description`, `image_path`, `video_id`, `link`, `link2`, `link3`, `status`, `created_at`, `updated_at`) VALUES
(12, 'Multipurpose Business - Multipurpose Website CMS & Business CMS', 'multipurpose-business-multipurpose-website-cms-business-cms', '<p><b>Multipurpose Business&nbsp;</b>Multipurpose Website &amp; Agency Business CMS is the perfect agency business or any kind of website with this PHP Script. <b>Multipurpose Business&nbsp;</b>is a better way to present your business, corporate website, construction website, interior, agency, events, event ticket selling, donation website, crowdfunding, fund rising, job posting, manage your customer, quotation, clients feedback, product selling, digital product selling, physical product selling, downloadable product selling, various kind of website etc. It&rsquo;s easy to customise and also well documented. it also compatible with Desktop, laptop, mobile and also compatible with major browsers.</p>\r\n\r\n<h2><b>Demo Link</b></h2>\r\n\r\n<p><b>Frontend Demo:</b>&nbsp;<a href=\"https://multipurpose-business.msnsofttech.com/\" target=\"_blank\"><b>Multipurpose Business</b></a></p>\r\n\r\n<p><b>Super Admin Demo:</b>&nbsp;<a href=\"https://multipurpose-business.msnsofttech.com/login/admin\" target=\"_blank\">Super Admin Login</a><br />\r\n<b>Username</b>&nbsp;super_admin<br />\r\n<b>Password</b>&nbsp;12345678</p>\r\n\r\n<p><b>Admin Demo ( super admin can add admin by their role ):</b>&nbsp;<a href=\"https://multipurpose-business.msnsofttech.com/login/admin\" target=\"_blank\">Admin Login</a><br />\r\n<b>Username</b>&nbsp;admin<br />\r\n<b>Password</b>&nbsp;12345678</p>\r\n\r\n<p><b>Editor Demo ( super admin can add admin by their role ):</b>&nbsp;<a href=\"https://multipurpose-business.msnsofttech.com/login/admin\" target=\"_blank\">Editor Login</a><br />\r\n<b>Username</b>&nbsp;editor<br />\r\n<b>Password</b>&nbsp;12345678</p>\r\n\r\n<p><b>Frontend User Dashboard Demo:</b>&nbsp;<a href=\"https://multipurpose-business.msnsofttech.com/login\" target=\"_blank\">Demo User Login</a><br />\r\n<b>Username</b>&nbsp;demouser<br />\r\n<b>Password</b>&nbsp;12345678</p>\r\n\r\n<p><br />\r\n<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/f8a75c5f1b9eefbe88419864918574804ace13ff/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332e312f70726f647563742d76617269616e742d776974682d70726963652e706e67\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/8336944aaac2c28e4037eee36619ce6371f80da5/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f647261672d64726f702d706167652d6275696c6465722e706e67\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/7bb737d78e0b555a435218d9114cbf2ff4b7e101/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f657874656e6465642d706167652d6275696c6465722d7769646765742d73657474696e67732e706e67\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/a6280d62776055483fb776d4fc17070325a67f29/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f637573746f6d2d666f726d2d6275696c6465722e706e67\" /><br />\r\n&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/90d8982ec2545af76e373bed19ea15e0de9ee194/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f6e6578656c69742d70726f64756374732d73686f77636173652e706e67\" /></p>\r\n\r\n<p><img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/364d89c47c2d9034bd58c09a42a78d234e9af170/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f66616365626f6f6b2d676f6f676c652d6c6f67696e2e706e67\" />&nbsp;&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/cf20cfff3eb997a003ed867b9e8e25874fd0bf45/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f7769646765742d6275696c6465722d657874656e6465642e706e67\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/537f15115cc1f381a52ed8cc7255630808d2b255/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f6d656469612d75706c6f61642d6f7074696d697a65642e706e67\" /></p>\r\n\r\n<p><img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/162c142a200106707f63dfa45fc2cddc603d359c/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f6e6578656c69742d6d6567612d6d656e752e706e67\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/994a58603560f9456789020282c705189eed312a/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f6d6567612d6d656e752d736f7274696e672d73657474696e67732e706e67\" /></p>\r\n\r\n<p>&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/7b6cb3afd02b7ca3db6e6e2c2a62ec5fee94d73f/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f6e6578656c69742d6d6f64756c652e6a7067\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/dbf91421582eb05d34a3464e40f6c2615de8ed9a/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f666f6f7465722d636f6c6f722d73657474696e67732e706e67\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/8a32f638397a75a22c442d582019b47251c26432/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f76332f636f6c6f722d73657474696e67732e706e67\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/312f74730410f3a251efbd42ad44fe6568f34fc1/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f6e6578656c69742d6b65792d66656174757265732d7632342e706e67\" />&nbsp;<img alt=\"online documentation\" src=\"https://camo.envatousercontent.com/211d0d74a1cad1965252f323f665f5447b578100/68747470733a2f2f6e6578656c69742e7867656e696f75732e636f6d2f696d616765732f6e6578656c69742d6c656176652d7265766965772e706e67\" /></p>\r\n\r\n<h2>Security</h2>\r\n\r\n<ul>\r\n	<li>Cross-Site Request Forgery (CSRF) Prevention</li>\r\n	<li>Cross-Site Scripting (XSS) Prevention</li>\r\n	<li>Password Hashing</li>\r\n	<li>Avoiding SQL Injection</li>\r\n</ul>\r\n\r\n<h2>System Requirement</h2>\r\n\r\n<ul>\r\n	<li>System Requirement</li>\r\n	<li>Backend Framework: Built</li>\r\n	<li>on Laravel 7x</li>\r\n	<li>PHP Composer</li>\r\n	<li>Frontend Framework: Built on Bootstrap 4x</li>\r\n	<li>Requires PHP 7.4</li>\r\n	<li>Supports MySQL, Mysqli.</li>\r\n</ul>\r\n\r\n<h2>Product Module</h2>\r\n\r\n<p><b>Multipurpose Business&nbsp;</b>has product selling module. Which help you to sell your digital or physical product. You can sell both of them. User can give their ratings. Also it has a beautiful products page with multiple filter options. It has attribute option, which help you to describe your product easily. Also have stock system, if the product is out of stock add to cart button will be disabled for it. You can also offer coupon code to your customer. There is also option to put your shipping details separately you can select default shipping method. It also support cash on delivery along with PayPal/paytm/paystack/razorpay/stripe/flutterwave rave/mollie/manual payment etc. you can enable disable any payment gateway.</p>\r\n\r\n<h2>Events Module</h2>\r\n\r\n<p>you can showcase your event also you can sell you event ticket with this script. It has event organiser details with venue details. it also have option to put google map location for event Venues.</p>\r\n\r\n<h2>Appointment Module</h2>\r\n\r\n<p>you can add appointment system such as doctor, advocate etc, user can select data and time and set appointment dynamically, you can manage it from admin panel, it also support 8 payment gateway comes with the cms.</p>\r\n\r\n<h2>Course Selling Module</h2>\r\n\r\n<p>you can sale online &amp; offline courses using this cms, it has support set maximum applicant, also it comes with 8 payment gateway support, you can set lesson as preview.</p>\r\n\r\n<h2>Donation Module</h2>\r\n\r\n<p>Multipurpose Business&nbsp;comes with support of donation system. You can set your cause to rise fund from user. There has option to show raised amount as well as goal amount. It also support PayPal/paytm/paystack/razorpay/stripe/flutterwave rave/mollie payment gateway along with manual payment gateway. It has progress bar to show your cause funding percentage. You can check all the transaction made at the backend.</p>\r\n\r\n<h2>Jobs Module</h2>\r\n\r\n<p>Multipurpose Business&nbsp;comet with jobs posting support. You can post any job by category. Any one can apply using mail or using application form. You can set it form admin panel either use applicant form or use mail to get applicant cv.</p>\r\n\r\n<h2>Knowledge Base Module</h2>\r\n\r\n<p>It has knowledge base module to make your customer support easy, you can create topic assign them to any article. User can search article form knowledgebase.</p>\r\n\r\n<h2>Package Selling module</h2>\r\n\r\n<p>Multipurpose Business&nbsp;has option to sell your custom service though package. User can send you quote of their work. Or the can directly order your package form price plan or home page, you have option to make your own order form. you have option to get clients feedback using feedback page, also has option to show user feedback using client&rsquo;s feedback page</p>\r\n\r\n<h2>Support Ticket Module</h2>\r\n\r\n<p>user can easily create support ticket from their user dashboard as well as from support ticket page. they can reply their support ticket from their admin panel, can set priority and able to change ticket status. user can attach a zip file while reply message. has notify option for user.</p>\r\n\r\n<h2>Drag &amp; Drop Menu Builder With Mega Menu</h2>\r\n\r\n<p>Multipurpose Business&nbsp;comes with drag &amp; drop menu builder with mega menu support. You can drag &amp; drop any menu item to short it, Or make it dropdown if you want. There has option to add mega menu and you can also select mega menu items.</p>\r\n\r\n<h2>Popup Builder</h2>\r\n\r\n<p>This script has popup builder module. There is 4 predefine popup style. You can create as many as popup you want, then set one popup for show. You can set delay time of popup show also you can change all content.</p>\r\n\r\n<h2>Preloader Builder</h2>\r\n\r\n<p>Multipurpose Business&nbsp;has 12 pre made preloader. you can choose any of them. Also there has option to use your own preloader image. You can get preloader option in general settings preloader.</p>\r\n\r\n<h2>Drag &amp; Drop Form Builder</h2>\r\n\r\n<p>Multipurpose Business&nbsp;comes with a great feature for your need is Drag &amp; Drop Form Builder, it has text, number, email, select, checkbox, textarea, file etc fields. You can easily customise any form of this script. Also you can set is this field is required or not.</p>\r\n\r\n<h2>Drag &amp; Drop Widget Builder</h2>\r\n\r\n<p>This Script comes with Widget Builder with 09 pre Made widget. You can build footer widget area with your needed widget. you can also use raw html for widget area, you can show newsletter widget or you can just show an image here. It&rsquo;s up to you, you have full control over it.</p>\r\n\r\n<h2>Currency Available for Multipurpose Business&nbsp;</h2>\r\n\r\n<p>[&lsquo;USD&rsquo; = &rsquo;$&rsquo;, &lsquo;EUR&rsquo; = &lsquo;&euro;&rsquo;, &lsquo;INR&rsquo; = &lsquo;₹&rsquo;, &lsquo;IDR&rsquo; = &lsquo;Rp&rsquo;, &lsquo;AUD&rsquo; = &lsquo;A$&rsquo;, &lsquo;SGD&rsquo; = &lsquo;S$&rsquo;, &lsquo;JPY&rsquo; = &lsquo;&yen;&rsquo;, &lsquo;GBP&rsquo; = &lsquo;&pound;&rsquo;, &lsquo;MYR&rsquo; = &lsquo;RM&rsquo;, &lsquo;PHP&rsquo; = &lsquo;₱&rsquo;, &lsquo;THB&rsquo; = &lsquo;฿&rsquo;, &lsquo;KRW&rsquo; = &lsquo;₩&rsquo;, &lsquo;NGN&rsquo; = &lsquo;₦&rsquo;, &lsquo;GHS&rsquo; = &lsquo;GH₵&rsquo;, &lsquo;BRL&rsquo; = &lsquo;R$&rsquo;,&rsquo;BIF&rsquo; = &lsquo;FBu&rsquo;, &lsquo;CAD&rsquo; = &lsquo;C$&rsquo;, &lsquo;CDF&rsquo; = &lsquo;FC&rsquo;, &lsquo;CVE&rsquo; = &lsquo;Esc&rsquo;, &lsquo;GHP&rsquo; = &lsquo;GH₵&rsquo;, &lsquo;GMD&rsquo; = &lsquo;D&rsquo;, &lsquo;GNF&rsquo; = &lsquo;FG&rsquo;, &lsquo;KES&rsquo; = &lsquo;K&rsquo;, &lsquo;LRD&rsquo; = &lsquo;L$&rsquo;, &lsquo;MWK&rsquo; = &lsquo;MK&rsquo;, &lsquo;MZN&rsquo; = &lsquo;MT&rsquo;, &lsquo;RWF&rsquo; = &lsquo;R₣&rsquo;, &lsquo;SLL&rsquo; = &lsquo;Le&rsquo;, &lsquo;STD&rsquo; = &lsquo;Db&rsquo;, &lsquo;TZS&rsquo; = &lsquo;TSh&rsquo;, &lsquo;UGX&rsquo; = &lsquo;USh&rsquo;, &lsquo;XAF&rsquo; = &lsquo;FCFA&rsquo;, &lsquo;XOF&rsquo; = &lsquo;CFA&rsquo;, &lsquo;ZMK&rsquo; = &lsquo;ZK&rsquo;, &lsquo;ZMW&rsquo; = &lsquo;ZK&rsquo;, &lsquo;ZWD&rsquo; = &lsquo;Z$&rsquo;, &lsquo;AED&rsquo; = &lsquo;د.إ&rsquo;, &lsquo;AFN&rsquo; = &lsquo;؋&rsquo;, &lsquo;ALL&rsquo; = &lsquo;L&rsquo;, &lsquo;AMD&rsquo; = &lsquo;֏&rsquo;, &lsquo;ANG&rsquo; = &lsquo;NAf&rsquo;, &lsquo;AOA&rsquo; = &lsquo;Kz&rsquo;, &lsquo;ARS&rsquo; = &rsquo;$&rsquo;, &lsquo;AWG&rsquo; = &lsquo;&fnof;&rsquo;, &lsquo;AZN&rsquo; = &lsquo;₼&rsquo;, &lsquo;BAM&rsquo; = &lsquo;KM&rsquo;, &lsquo;BBD&rsquo; = &lsquo;Bds$&rsquo;, &lsquo;BDT&rsquo; = &lsquo;৳&rsquo;, &lsquo;BGN&rsquo; = &lsquo;Лв&rsquo;, &lsquo;BMD&rsquo; = &rsquo;$&rsquo;, &lsquo;BND&rsquo; = &lsquo;B$&rsquo;, &lsquo;BOB&rsquo; = &lsquo;Bs&rsquo;, &lsquo;BSD&rsquo; = &lsquo;B$&rsquo;, &lsquo;BWP&rsquo; = &lsquo;P&rsquo;, &lsquo;BZD&rsquo; = &rsquo;$&rsquo;, &lsquo;CHF&rsquo; = &lsquo;CHf&rsquo;, &lsquo;CNY&rsquo; = &lsquo;&yen;&rsquo;, &lsquo;CLP&rsquo; = &rsquo;$&rsquo;, &lsquo;COP&rsquo; = &rsquo;$&rsquo;, &lsquo;CRC&rsquo; = &lsquo;₡&rsquo;, &lsquo;CZK&rsquo; = &lsquo;Kč&rsquo;, &lsquo;DJF&rsquo; = &lsquo;Fdj&rsquo;, &lsquo;DKK&rsquo; = &lsquo;Kr&rsquo;, &lsquo;DOP&rsquo; = &lsquo;RD$&rsquo;, &lsquo;DZD&rsquo; = &lsquo;دج&rsquo;, &lsquo;EGP&rsquo; = &lsquo;E&pound;&rsquo;, &lsquo;ETB&rsquo; = &lsquo;ብር&rsquo;, &lsquo;FJD&rsquo; = &lsquo;FJ$&rsquo;, &lsquo;FKP&rsquo; = &lsquo;&pound;&rsquo;, &lsquo;GEL&rsquo; = &lsquo;ლ&rsquo;, &lsquo;GIP&rsquo; = &lsquo;&pound;&rsquo;, &lsquo;GTQ&rsquo; = &lsquo;Q&rsquo;, &lsquo;GYD&rsquo; = &lsquo;G$&rsquo;, &lsquo;HKD&rsquo; = &lsquo;HK$&rsquo;, &lsquo;HNL&rsquo; = &lsquo;L&rsquo;, &lsquo;HRK&rsquo; = &lsquo;kn&rsquo;, &lsquo;HTG&rsquo; = &lsquo;G&rsquo;, &lsquo;HUF&rsquo; = &lsquo;Ft&rsquo;, &lsquo;ILS&rsquo; = &lsquo;₪&rsquo;, &lsquo;ISK&rsquo; = &lsquo;kr&rsquo;, &lsquo;JMD&rsquo; = &rsquo;$&rsquo;, &lsquo;KGS&rsquo; = &lsquo;Лв&rsquo;, &lsquo;KHR&rsquo; = &lsquo;៛&rsquo;, &lsquo;KMF&rsquo; = &lsquo;CF&rsquo;, &lsquo;KYD&rsquo; = &rsquo;$&rsquo;, &lsquo;KZT&rsquo; = &lsquo;₸&rsquo;, &lsquo;LAK&rsquo; = &lsquo;₭&rsquo;, &lsquo;LBP&rsquo; = &lsquo;ل.ل.&rsquo;, &lsquo;LKR&rsquo; = &lsquo;ரூ&rsquo;, &lsquo;LSL&rsquo; = &lsquo;L&rsquo;,&rsquo;MAD&rsquo; = &lsquo;MAD&rsquo;, &lsquo;MDL&rsquo; = &lsquo;L&rsquo;, &lsquo;MGA&rsquo; = &lsquo;Ar&rsquo;, &lsquo;MKD&rsquo; = &lsquo;Ден&rsquo;, &lsquo;MMK&rsquo; = &lsquo;K&rsquo;, &lsquo;MNT&rsquo; = &lsquo;₮&rsquo;, &lsquo;MOP&rsquo; = &lsquo;MOP$&rsquo;, &lsquo;MRO&rsquo; = &lsquo;MRU&rsquo;, &lsquo;MUR&rsquo; = &lsquo;₨&rsquo;, &lsquo;MVR&rsquo; = &lsquo;Rf&rsquo;, &lsquo;MXN&rsquo; = &lsquo;Mex$&rsquo;, &lsquo;NAD&rsquo; = &lsquo;N$&rsquo;, &lsquo;NIO&rsquo; = &lsquo;C$&rsquo;, &lsquo;NOK&rsquo; = &lsquo;kr&rsquo;, &lsquo;NPR&rsquo; = &lsquo;रू&rsquo;, &lsquo;NZD&rsquo; = &rsquo;$&rsquo;, &lsquo;PAB&rsquo; = &lsquo;B/.&rsquo;, &lsquo;PEN&rsquo; = &lsquo;S/&rsquo;, &lsquo;PGK&rsquo; = &lsquo;K&rsquo;, &lsquo;PKR&rsquo; = &lsquo;₨&rsquo;, &lsquo;PLN&rsquo; = &lsquo;zł&rsquo;, &lsquo;PYG&rsquo; = &lsquo;₲&rsquo;, &lsquo;QAR&rsquo; = &lsquo;QR&rsquo;, &lsquo;RON&rsquo; = &lsquo;lei&rsquo;, &lsquo;RSD&rsquo; = &lsquo;din&rsquo;, &lsquo;RUB&rsquo; = &lsquo;₽&rsquo;, &lsquo;SAR&rsquo; = &lsquo;SR&rsquo;, &lsquo;SBD&rsquo; = &lsquo;Si$&rsquo;, &lsquo;SCR&rsquo; = &lsquo;SR&rsquo;, &lsquo;SEK&rsquo; = &lsquo;kr&rsquo;, &lsquo;SHP&rsquo; = &lsquo;&pound;&rsquo;, &lsquo;SOS&rsquo; = &lsquo;Sh.so.&rsquo;, &lsquo;SRD&rsquo; = &rsquo;$&rsquo;, &lsquo;SZL&rsquo; = &lsquo;E&rsquo;, &lsquo;TJS&rsquo; = &lsquo;ЅM&rsquo;, &lsquo;TRY&rsquo; = &lsquo;₺&rsquo;, &lsquo;TTD&rsquo; = &lsquo;TT$&rsquo;, &lsquo;TWD&rsquo; = &lsquo;NT$&rsquo;, &lsquo;UAH&rsquo; = &lsquo;₴&rsquo;, &lsquo;UYU&rsquo; = &rsquo;$U&rsquo;, &lsquo;UZS&rsquo; = &lsquo;so\\&rsquo;m&rsquo;, &lsquo;VND&rsquo; = &lsquo;₫&rsquo;, &lsquo;VUV&rsquo; = &lsquo;VT&rsquo;, &lsquo;WST&rsquo; = &lsquo;WS$&rsquo;, &lsquo;XCD&rsquo; = &rsquo;$&rsquo;, &lsquo;XPF&rsquo; = &lsquo;₣&rsquo;, &lsquo;YER&rsquo; = &lsquo;﷼&rsquo;, &lsquo;ZAR&rsquo; = &lsquo;R&rsquo;]</p>\r\n\r\n<h2>Features</h2>\r\n\r\n<ul>\r\n	<li>Unique Design</li>\r\n	<li>Powerful Admin Dashboard</li>\r\n	<li>04 Home Variant</li>\r\n	<li>06 Payment Gateway\r\n	<ul>\r\n		<li>Paypal, Paytm, Razorpay, Stripe, Paystack, Flutterwave Rave/ Mollie/ Manual Payment, Cash On Delivery ( only for product sell )</li>\r\n	</ul>\r\n	</li>\r\n	<li>Supported Currency [&lsquo;USD&rsquo; = &rsquo;$&rsquo;, &lsquo;EUR&rsquo; = &lsquo;&euro;&rsquo;, &lsquo;INR&rsquo; = &lsquo;₹&rsquo;, &lsquo;IDR&rsquo; = &lsquo;Rp&rsquo;, &lsquo;AUD&rsquo; = &lsquo;A$&rsquo;, &lsquo;SGD&rsquo; = &lsquo;S$&rsquo;, &lsquo;JPY&rsquo; = &lsquo;&yen;&rsquo;, &lsquo;GBP&rsquo; = &lsquo;&pound;&rsquo;, &lsquo;MYR&rsquo; = &lsquo;RM&rsquo;, &lsquo;PHP&rsquo; = &lsquo;₱&rsquo;, &lsquo;THB&rsquo; = &lsquo;฿&rsquo;, &lsquo;KRW&rsquo; = &lsquo;₩&rsquo;, &lsquo;NGN&rsquo; = &lsquo;₦&rsquo;, &lsquo;GHS&rsquo; = &lsquo;GH₵&rsquo;, &lsquo;BRL&rsquo; = &lsquo;R$&rsquo;,&rsquo;BIF&rsquo; = &lsquo;FBu&rsquo;, &lsquo;CAD&rsquo; = &lsquo;C$&rsquo;, &lsquo;CDF&rsquo; = &lsquo;FC&rsquo;, &lsquo;CVE&rsquo; = &lsquo;Esc&rsquo;, &lsquo;GHP&rsquo; = &lsquo;GH₵&rsquo;, &lsquo;GMD&rsquo; = &lsquo;D&rsquo;, &lsquo;GNF&rsquo; = &lsquo;FG&rsquo;, &lsquo;KES&rsquo; = &lsquo;K&rsquo;, &lsquo;LRD&rsquo; = &lsquo;L$&rsquo;, &lsquo;MWK&rsquo; = &lsquo;MK&rsquo;, &lsquo;MZN&rsquo; = &lsquo;MT&rsquo;, &lsquo;RWF&rsquo; = &lsquo;R₣&rsquo;, &lsquo;SLL&rsquo; = &lsquo;Le&rsquo;, &lsquo;STD&rsquo; = &lsquo;Db&rsquo;, &lsquo;TZS&rsquo; = &lsquo;TSh&rsquo;, &lsquo;UGX&rsquo; = &lsquo;USh&rsquo;, &lsquo;XAF&rsquo; = &lsquo;FCFA&rsquo;, &lsquo;XOF&rsquo; = &lsquo;CFA&rsquo;, &lsquo;ZMK&rsquo; = &lsquo;ZK&rsquo;, &lsquo;ZMW&rsquo; = &lsquo;ZK&rsquo;, &lsquo;ZWD&rsquo; = &lsquo;Z$&rsquo;, &lsquo;AED&rsquo; = &lsquo;د.إ&rsquo;, &lsquo;AFN&rsquo; = &lsquo;؋&rsquo;, &lsquo;ALL&rsquo; = &lsquo;L&rsquo;, &lsquo;AMD&rsquo; = &lsquo;֏&rsquo;, &lsquo;ANG&rsquo; = &lsquo;NAf&rsquo;, &lsquo;AOA&rsquo; = &lsquo;Kz&rsquo;, &lsquo;ARS&rsquo; = &rsquo;$&rsquo;, &lsquo;AWG&rsquo; = &lsquo;&fnof;&rsquo;, &lsquo;AZN&rsquo; = &lsquo;₼&rsquo;, &lsquo;BAM&rsquo; = &lsquo;KM&rsquo;, &lsquo;BBD&rsquo; = &lsquo;Bds$&rsquo;, &lsquo;BDT&rsquo; = &lsquo;৳&rsquo;, &lsquo;BGN&rsquo; = &lsquo;Лв&rsquo;, &lsquo;BMD&rsquo; = &rsquo;$&rsquo;, &lsquo;BND&rsquo; = &lsquo;B$&rsquo;, &lsquo;BOB&rsquo; = &lsquo;Bs&rsquo;, &lsquo;BSD&rsquo; = &lsquo;B$&rsquo;, &lsquo;BWP&rsquo; = &lsquo;P&rsquo;, &lsquo;BZD&rsquo; = &rsquo;$&rsquo;, &lsquo;CHF&rsquo; = &lsquo;CHf&rsquo;, &lsquo;CNY&rsquo; = &lsquo;&yen;&rsquo;, &lsquo;CLP&rsquo; = &rsquo;$&rsquo;, &lsquo;COP&rsquo; = &rsquo;$&rsquo;, &lsquo;CRC&rsquo; = &lsquo;₡&rsquo;, &lsquo;CZK&rsquo; = &lsquo;Kč&rsquo;, &lsquo;DJF&rsquo; = &lsquo;Fdj&rsquo;, &lsquo;DKK&rsquo; = &lsquo;Kr&rsquo;, &lsquo;DOP&rsquo; = &lsquo;RD$&rsquo;, &lsquo;DZD&rsquo; = &lsquo;دج&rsquo;, &lsquo;EGP&rsquo; = &lsquo;E&pound;&rsquo;, &lsquo;ETB&rsquo; = &lsquo;ብር&rsquo;, &lsquo;FJD&rsquo; = &lsquo;FJ$&rsquo;, &lsquo;FKP&rsquo; = &lsquo;&pound;&rsquo;, &lsquo;GEL&rsquo; = &lsquo;ლ&rsquo;, &lsquo;GIP&rsquo; = &lsquo;&pound;&rsquo;, &lsquo;GTQ&rsquo; = &lsquo;Q&rsquo;, &lsquo;GYD&rsquo; = &lsquo;G$&rsquo;, &lsquo;HKD&rsquo; = &lsquo;HK$&rsquo;, &lsquo;HNL&rsquo; = &lsquo;L&rsquo;, &lsquo;HRK&rsquo; = &lsquo;kn&rsquo;, &lsquo;HTG&rsquo; = &lsquo;G&rsquo;, &lsquo;HUF&rsquo; = &lsquo;Ft&rsquo;, &lsquo;ILS&rsquo; = &lsquo;₪&rsquo;, &lsquo;ISK&rsquo; = &lsquo;kr&rsquo;, &lsquo;JMD&rsquo; = &rsquo;$&rsquo;, &lsquo;KGS&rsquo; = &lsquo;Лв&rsquo;, &lsquo;KHR&rsquo; = &lsquo;៛&rsquo;, &lsquo;KMF&rsquo; = &lsquo;CF&rsquo;, &lsquo;KYD&rsquo; = &rsquo;$&rsquo;, &lsquo;KZT&rsquo; = &lsquo;₸&rsquo;, &lsquo;LAK&rsquo; = &lsquo;₭&rsquo;, &lsquo;LBP&rsquo; = &lsquo;ل.ل.&rsquo;, &lsquo;LKR&rsquo; = &lsquo;ரூ&rsquo;, &lsquo;LSL&rsquo; = &lsquo;L&rsquo;,&rsquo;MAD&rsquo; = &lsquo;MAD&rsquo;, &lsquo;MDL&rsquo; = &lsquo;L&rsquo;, &lsquo;MGA&rsquo; = &lsquo;Ar&rsquo;, &lsquo;MKD&rsquo; = &lsquo;Ден&rsquo;, &lsquo;MMK&rsquo; = &lsquo;K&rsquo;, &lsquo;MNT&rsquo; = &lsquo;₮&rsquo;, &lsquo;MOP&rsquo; = &lsquo;MOP$&rsquo;, &lsquo;MRO&rsquo; = &lsquo;MRU&rsquo;, &lsquo;MUR&rsquo; = &lsquo;₨&rsquo;, &lsquo;MVR&rsquo; = &lsquo;Rf&rsquo;, &lsquo;MXN&rsquo; = &lsquo;Mex$&rsquo;, &lsquo;NAD&rsquo; = &lsquo;N$&rsquo;, &lsquo;NIO&rsquo; = &lsquo;C$&rsquo;, &lsquo;NOK&rsquo; = &lsquo;kr&rsquo;, &lsquo;NPR&rsquo; = &lsquo;रू&rsquo;, &lsquo;NZD&rsquo; = &rsquo;$&rsquo;, &lsquo;PAB&rsquo; = &lsquo;B/.&rsquo;, &lsquo;PEN&rsquo; = &lsquo;S/&rsquo;, &lsquo;PGK&rsquo; = &lsquo;K&rsquo;, &lsquo;PKR&rsquo; = &lsquo;₨&rsquo;, &lsquo;PLN&rsquo; = &lsquo;zł&rsquo;, &lsquo;PYG&rsquo; = &lsquo;₲&rsquo;, &lsquo;QAR&rsquo; = &lsquo;QR&rsquo;, &lsquo;RON&rsquo; = &lsquo;lei&rsquo;, &lsquo;RSD&rsquo; = &lsquo;din&rsquo;, &lsquo;RUB&rsquo; = &lsquo;₽&rsquo;, &lsquo;SAR&rsquo; = &lsquo;SR&rsquo;, &lsquo;SBD&rsquo; = &lsquo;Si$&rsquo;, &lsquo;SCR&rsquo; = &lsquo;SR&rsquo;, &lsquo;SEK&rsquo; = &lsquo;kr&rsquo;, &lsquo;SHP&rsquo; = &lsquo;&pound;&rsquo;, &lsquo;SOS&rsquo; = &lsquo;Sh.so.&rsquo;, &lsquo;SRD&rsquo; = &rsquo;$&rsquo;, &lsquo;SZL&rsquo; = &lsquo;E&rsquo;, &lsquo;TJS&rsquo; = &lsquo;ЅM&rsquo;, &lsquo;TRY&rsquo; = &lsquo;₺&rsquo;, &lsquo;TTD&rsquo; = &lsquo;TT$&rsquo;, &lsquo;TWD&rsquo; = &lsquo;NT$&rsquo;, &lsquo;UAH&rsquo; = &lsquo;₴&rsquo;, &lsquo;UYU&rsquo; = &rsquo;$U&rsquo;, &lsquo;UZS&rsquo; = &lsquo;so\\&rsquo;m&rsquo;, &lsquo;VND&rsquo; = &lsquo;₫&rsquo;, &lsquo;VUV&rsquo; = &lsquo;VT&rsquo;, &lsquo;WST&rsquo; = &lsquo;WS$&rsquo;, &lsquo;XCD&rsquo; = &rsquo;$&rsquo;, &lsquo;XPF&rsquo; = &lsquo;₣&rsquo;, &lsquo;YER&rsquo; = &lsquo;﷼&rsquo;, &lsquo;ZAR&rsquo; = &lsquo;R&rsquo;]</li>\r\n	<li>User Dashboard</li>\r\n	<li>RTL Support</li>\r\n	<li>Product Selling Module</li>\r\n	<li>Physical product Selling</li>\r\n	<li>Downloadable Product Selling</li>\r\n	<li>Event Ticket Selling Module</li>\r\n	<li>Donation/ Crowdfunding</li>\r\n	<li>Knowledgebase</li>\r\n	<li>Jobs Posting</li>\r\n	<li>Dynamic Page</li>\r\n	<li>Page Slug Change Options</li>\r\n	<li>Page Meta Tag Options</li>\r\n	<li>Drag &amp; Drop Menu Builder</li>\r\n	<li>Drag &amp; Drop Mega Menu Builder</li>\r\n	<li>Preloader Builder</li>\r\n	<li>Drag &amp; Drop Form Builder</li>\r\n	<li>Drag &amp; Drop Widgets Area Builder</li>\r\n	<li>Gallery Page</li>\r\n	<li>FAQ Page with google schema markup support</li>\r\n	<li>Popup Builder</li>\r\n	<li>Mutlilanguage Options</li>\r\n	<li>Page Slug &amp; Name Change</li>\r\n	<li>950+ Google Fonts</li>\r\n	<li>Cache Settings</li>\r\n	<li>Pre Made Pages Slug change</li>\r\n	<li>Pre Made Pages Name change</li>\r\n	<li>Custom CSS Settings</li>\r\n	<li>Custom JS Settings</li>\r\n	<li>Sitemap Generator</li>\r\n	<li>RSS feed Settings</li>\r\n	<li>Maintains Mode</li>\r\n	<li>GDPR Cookie Settings</li>\r\n	<li>404 Page Customize</li>\r\n	<li>Feedback Page</li>\r\n	<li>Clients Feedback Show Page</li>\r\n	<li>Database Backup &amp; Restore and Download</li>\r\n	<li>Email Template Settings</li>\r\n	<li>SEO Settings Available</li>\r\n	<li>OG Meta Added</li>\r\n	<li>User Role Permission System (Super Admin Can Assign Role or Can Add New Admin)</li>\r\n	<li>Google Analytics Settings</li>\r\n	<li>Live Chat Options</li>\r\n	<li>Unlimited Color Option</li>\r\n	<li>Disqus Comment System</li>\r\n	<li>Google Captcha V3</li>\r\n	<li>Subscriber Settings</li>\r\n	<li>Admin Dark Mode</li>\r\n	<li>Newsletter Mail Send</li>\r\n	<li>Social Share Options</li>\r\n	<li>Quote Page</li>\r\n	<li>Dynamic Order Page</li>\r\n	<li>Service Details Page</li>\r\n	<li>FAQ Page</li>\r\n	<li>Work Details page</li>\r\n	<li>Typography Settings</li>\r\n	<li>Well Documented</li>\r\n	<li>Quality Support</li>\r\n	<li>Video Tutorial</li>\r\n	<li>Lifetime Update</li>\r\n	<li>Language Settings</li>\r\n</ul>', '8de112c3-16d7-443a-b30c-6dfb9607fcd3_1739211369.png', NULL, 'https://multipurpose-business.msnsofttech.com/', 'https://multipurpose-business.msnsofttech.com/login/admin', 'https://multipurpose-business.msnsofttech.com/login', 1, '2025-02-10 12:00:16', '2025-02-10 19:52:00'),
(13, 'Digi Products - Single-Vendor Digital Marketplace', 'digi-products-single-vendor-digital-marketplace', '<p>Digiproducts is a robust PHP script designed to create a dynamic digital marketplace for a single vendor. This versatile platform allows you to effortlessly upload and sell a wide range of digital products, including WordPress themes, plugins, stock footage, music, audio, graphics, and more.</p>\r\n\r\n<p>With a focus on user experience, Digiproducts offers an intuitive interface that simplifies product management. The comprehensive admin panel provides full control over your marketplace, ensuring efficient oversight and management. Secure user authentication and effective database management create a reliable and safe environment for transactions.</p>\r\n\r\n<p>Digiproducts features customizable functionalities and a fully responsive design, ensuring your marketplace looks professional and operates smoothly across all devices. Built with SEO best practices, the platform enhances product visibility in search engine results, helping to attract more visitors and boost sales.</p>\r\n\r\n<p>Regular updates and thorough documentation make Digiproducts accessible to users of all levels, streamlining the setup and customization process. This makes it an excellent choice for anyone looking to launch a successful single-vendor digital marketplace, allowing you to effectively showcase and sell high-quality digital products.</p>\r\n\r\n<h3><b>Demo and documentation</b></h3>\r\n\r\n<ul>\r\n	<li><b>Frontend</b>\r\n\r\n	<ul>\r\n		<li><b>Link</b>&nbsp;:&nbsp;<a href=\"https://digiproducts.msnsofttech.com/\" target=\"_blank\">https://digiproducts.msnsofttech.com/</a></li>\r\n	</ul>\r\n	</li>\r\n	<li><b>User Panel</b>\r\n	<ul>\r\n		<li><b>Link</b>&nbsp;:&nbsp;<a href=\"https://digiproducts.msnsofttech.com/login\" target=\"_blank\">https://digiproducts.msnsofttech.com/login</a></li>\r\n		<li><b>Email</b>&nbsp;:&nbsp;<a href=\"mailto:user@gmail.com\">user@gmail.com</a></li>\r\n		<li><b>Password</b>&nbsp;: user1234</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Admin Panel</b>\r\n	<ul>\r\n		<li><b>Link</b>&nbsp;:&nbsp;<a href=\"https://digiproducts.msnsofttech.com/login/admin\" target=\"_blank\">https://digiproducts.msnsofttech.com/login/admin</a></li>\r\n		<li><b>Email</b>&nbsp;:&nbsp;<a href=\"mailto:admin@gmail.com\">admin@gmail.com</a></li>\r\n		<li><b>Password</b>&nbsp;: admin1234</li>\r\n	</ul>\r\n	</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3><b>Top Features</b></h3>\r\n\r\n<ul>\r\n	<li><b>Single-Vendor System</b>:\r\n\r\n	<ul>\r\n		<li>The admin can upload and sell various digital products including themes, PHP scripts, plugins, videos, audio, etc.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Discount Management</b>:\r\n	<ul>\r\n		<li>The admin can create and manage discounts for items to boost sales.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Admin Support Earnings</b>:\r\n	<ul>\r\n		<li>The admin can add more profit by providing support for sold items.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Video And Audio Preview</b>:\r\n	<ul>\r\n		<li>Besides image previews, video, and audio previews are provided for a smooth user experience.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Premium Subscriptions (Included in extended license)</b>:\r\n	<ul>\r\n		<li>Sell weekly, monthly, yearly, and lifetime subscription plans.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Refunds &amp; Statements</b>:\r\n	<ul>\r\n		<li>Comprehensive refund management and detailed sales statements for transparency and trust.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Support Ticket System</b>:\r\n	<ul>\r\n		<li>Integrated support ticket system for efficient communication and issue resolution between users and admin.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Free Items</b>:\r\n	<ul>\r\n		<li>The admin can offer items to be downloaded by everyone for free.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Favorites</b>:\r\n	<ul>\r\n		<li>Users can add items to their favorites for easy access and future purchases.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Newsletter</b>:\r\n	<ul>\r\n		<li>Users can subscribe to the newsletter to receive notifications about new items, special promotions, and updates.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>WEBP Image Converting</b>:\r\n	<ul>\r\n		<li>Convert item images and screenshots to WEBP for fast loading.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Blog Integration</b>:\r\n	<ul>\r\n		<li>The admin can write and manage blog articles to engage with the community and share updates.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Advertisements</b>:\r\n	<ul>\r\n		<li>Designated places to put ads, allowing for monetization through advertisements.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Full Email Notifications</b>:\r\n	<ul>\r\n		<li>Comprehensive email notifications for users about purchases, transactions, tickets, and more.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Secure Transactions</b>:\r\n	<ul>\r\n		<li>Secure payment processing to ensure safe transactions for both buyers and the admin.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>KYC Verification</b>:\r\n	<ul>\r\n		<li>Know Your Customer (KYC) verification for enhanced security and trust.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>SEO Friendly</b>:\r\n	<ul>\r\n		<li>SEO-optimized structure to help your marketplace and items rank higher in search engine results.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Sitemap Auto-Generation</b>:\r\n	<ul>\r\n		<li>Automatically generates a sitemap every day to keep search engines updated with the latest content.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Responsive Design</b>:\r\n	<ul>\r\n		<li>Fully responsive design to provide an optimal viewing experience across all devices.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Customization Options</b>:\r\n	<ul>\r\n		<li>Flexible and customizable to meet the specific needs and branding of your marketplace.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Analytics and Reporting</b>:\r\n	<ul>\r\n		<li>In-depth analytics and reporting tools to track sales performance and user engagement.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>RTL Support &amp; Easy Translation</b>:\r\n	<ul>\r\n		<li>Full right-to-left (RTL) language support and easy translation from the admin panel.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>API With Documentation</b>:\r\n	<ul>\r\n		<li>API for admin to validate purchase codes, load items, and more with full documentation.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Two-Factor Authentication (2FA)</b>:\r\n	<ul>\r\n		<li>Provides additional security for users and the admin by requiring a second form of authentication.</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Admin Dashboard</b>:\r\n	<ul>\r\n		<li>Powerful admin dashboard for managing the entire marketplace, including users, items, site settings, and more.</li>\r\n	</ul>\r\n	</li>\r\n</ul>\r\n\r\n<h3><b>Requirements</b></h3>\r\n\r\n<ul>\r\n	<li><b>Server Requirements</b>\r\n\r\n	<ul>\r\n		<li>PHP 8.2x</li>\r\n		<li>Operating System : Linux or Windows</li>\r\n		<li>Shared, VPS or Dedicated Server</li>\r\n		<li>MySql: 5.7+</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>Required Upgrades</b>\r\n	<ul>\r\n		<li>allow_url_fopen = on</li>\r\n		<li>max_execution_time = 600</li>\r\n		<li>max_input_time = 600</li>\r\n		<li>post_max_size = 1G</li>\r\n		<li>memory_limit = 1024M</li>\r\n		<li>upload_max_filesize = 1G</li>\r\n	</ul>\r\n	</li>\r\n	<li><b>PHP Extensions</b>\r\n	<ul>\r\n		<li>BCMath</li>\r\n		<li>Ctype</li>\r\n		<li>Fileinfo</li>\r\n		<li>JSON</li>\r\n		<li>Mbstring</li>\r\n		<li>OpenSSL</li>\r\n		<li>PDO</li>\r\n		<li>pdo_mysql</li>\r\n		<li>Tokenizer</li>\r\n		<li>XML</li>\r\n		<li>cURL</li>\r\n		<li>zip</li>\r\n		<li>GD</li>\r\n	</ul>\r\n	</li>\r\n</ul>', 'Untitled design (3)_1739479140.png', NULL, 'https://digiproducts.msnsofttech.com/', 'https://digiproducts.msnsofttech.com/admin', 'https://digiproducts.msnsofttech.com/login', 1, '2025-02-13 20:15:42', '2025-02-15 00:42:34');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_categories`
--

CREATE TABLE `portfolio_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_categories`
--

INSERT INTO `portfolio_categories` (`id`, `title`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(5, 'WordPress', 'wordpress', NULL, 0, '2025-02-04 23:28:32', '2025-02-11 09:18:46'),
(6, 'Shopify', 'shopify', NULL, 0, '2025-02-04 23:28:50', '2025-02-05 13:06:27'),
(7, 'PHP, Laravel', 'php-laravel', NULL, 0, '2025-02-04 23:30:14', '2025-02-11 09:30:17'),
(8, 'eCommerce', 'ecommerce', NULL, 1, '2025-02-04 23:32:45', '2025-02-04 23:32:45'),
(9, 'Agency', 'agency', NULL, 1, '2025-02-04 23:33:40', '2025-02-04 23:33:40'),
(10, 'Portfolio', 'portfolio', NULL, 0, '2025-02-04 23:35:45', '2025-02-11 09:20:57'),
(13, 'Education & Coaching', 'education-coaching', NULL, 0, '2025-02-04 23:41:55', '2025-02-05 13:06:37'),
(15, 'Blog & News', 'blog-news', NULL, 0, '2025-02-04 23:45:38', '2025-02-05 13:06:58'),
(16, 'Hotel & Travel', 'hotel-travel', NULL, 0, '2025-02-04 23:45:54', '2025-02-05 13:07:07'),
(17, 'Multi Vendor', 'multi-vendor', NULL, 1, '2025-02-11 09:19:02', '2025-02-11 09:19:02'),
(18, 'Clothing', 'clothing', NULL, 1, '2025-02-11 09:21:08', '2025-02-11 09:21:08'),
(19, 'Electronics', 'electronics', NULL, 1, '2025-02-11 09:22:44', '2025-02-11 09:22:44'),
(21, 'Digital Products', 'digital-products', NULL, 1, '2025-02-15 00:54:28', '2025-02-15 00:54:28');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_category`
--

CREATE TABLE `portfolio_category` (
  `portfolio_id` int(10) UNSIGNED NOT NULL,
  `portfolio_category_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_category`
--

INSERT INTO `portfolio_category` (`portfolio_id`, `portfolio_category_id`) VALUES
(9, 7),
(9, 9),
(10, 8),
(11, 8),
(12, 7),
(12, 9),
(10, 18),
(10, 19),
(11, 17),
(11, 7),
(13, 8),
(13, 21);

-- --------------------------------------------------------

--
-- Table structure for table `pricings`
--

CREATE TABLE `pricings` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `price` varchar(191) NOT NULL,
  `old_price` varchar(191) DEFAULT NULL,
  `duration` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pricings`
--

INSERT INTO `pricings` (`id`, `title`, `slug`, `price`, `old_price`, `duration`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Basic', 'basic', '12', NULL, 'Year', '[\"Demo file\",\"Update\",\"File compressed\",\"Commercial use\",\"Support\",\"2 database\",\"Documetation\"]', 1, '2020-10-30 11:48:52', '2020-10-30 11:48:52'),
(2, 'Regular', 'regular', '29', NULL, 'Year', '[\"Demo file\",\"Update\",\"File compressed\",\"Commercial use\",\"Support\",\"5 database\",\"Documetation\"]', 1, '2020-10-30 11:50:12', '2020-10-30 11:50:12'),
(3, 'Extended', 'extended', '59', NULL, 'Year', '[\"Demo file\",\"Update\",\"File compressed\",\"Commercial use\",\"Support\",\"8 database\",\"Documetation\"]', 1, '2020-10-30 11:51:24', '2020-10-30 11:51:24'),
(4, 'Distinctio Reprehen', 'distinctio-reprehen', '932', '44', '22', '[\"erwewe\"]', 1, '2025-02-05 13:29:15', '2025-02-05 13:29:15');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `title`, `slug`, `description`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(31, 'Latest Blog', 'blog', '<p>While mirth large of on front. Ye he greater related adapted proceed entered an.<br></p>', NULL, 1, NULL, '2021-11-08 12:36:33'),
(32, 'Our Portfolios', 'portfolio', NULL, NULL, 1, NULL, '2021-11-08 12:36:43'),
(33, 'Our Services', 'services', '<p>Smart Solutions for a Smarter Tomorrow.</p>', NULL, 1, NULL, '2025-02-11 16:11:00'),
(34, 'Our Pricing', 'pricing', '<p>While mirth large of on front. Ye he greater related adapted proceed entered an.<br></p>', NULL, 1, NULL, '2021-11-08 12:37:04'),
(35, 'Meet Our Teams', 'team', '<p>While mirth large of on front. Ye he greater related adapted proceed entered an.<br></p>', NULL, 1, NULL, '2021-11-08 12:37:17'),
(36, 'Answer & Questions', 'faqs', NULL, NULL, 1, NULL, '2021-11-08 12:37:29'),
(37, 'Our Partners', 'clients', NULL, NULL, 1, NULL, '2021-11-08 12:37:42'),
(38, 'Our Clients Reviews', 'testimonials', NULL, NULL, 1, NULL, '2021-11-08 12:37:52'),
(39, 'How We Make Work Successful', 'process', NULL, NULL, 1, NULL, '2021-11-08 12:38:10'),
(40, 'Why Choose Us', 'why-us', 'At MSN SoftTech, we are dedicated to providing top-tier web &amp; software solutions that help businesses succeed in the digital world. With 10+ years of experience and 3,500+ satisfied clients worldwide, we stand out as a trusted partner for businesses looking to grow and innovate.', NULL, 1, NULL, '2025-02-09 08:15:36'),
(41, 'Newsletter - Get Updates & Latest News', 'subscribe', '<p>Get in your inbox the latest News and Offers from<br></p>', NULL, 1, NULL, '2021-11-08 12:38:47'),
(42, 'Get in Touch', 'contact', NULL, NULL, 1, NULL, '2021-11-08 12:39:04'),
(43, 'Let\'s Talk About Your Idea', 'mail', NULL, NULL, 1, NULL, '2021-11-08 12:39:20'),
(44, 'Get A Quote', 'get-quote', '<p>Get a quote in just 30 minutes<br></p>', NULL, 1, NULL, '2021-11-08 12:39:55'),
(45, 'Page Not Found', 'error', 'The page you are Looking for was Moved, Removed, Renamed or Might Never Existed', NULL, 1, NULL, NULL),
(46, 'Payment Feedback', 'payment', '', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `serviceables`
--

CREATE TABLE `serviceables` (
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `serviceable_type` varchar(191) NOT NULL,
  `serviceable_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `serviceables`
--

INSERT INTO `serviceables` (`service_id`, `serviceable_type`, `serviceable_id`) VALUES
(11, 'App\\Models\\Invoice', 16),
(11, 'App\\Models\\Invoice', 27),
(11, 'App\\Models\\GetQuote', 33),
(12, 'App\\Models\\Invoice', 15),
(12, 'App\\Models\\Invoice', 16),
(12, 'App\\Models\\Invoice', 17),
(12, 'App\\Models\\Invoice', 18),
(12, 'App\\Models\\Invoice', 19),
(12, 'App\\Models\\Invoice', 20),
(12, 'App\\Models\\Invoice', 21),
(12, 'App\\Models\\Invoice', 22),
(12, 'App\\Models\\Invoice', 23),
(12, 'App\\Models\\Invoice', 24),
(12, 'App\\Models\\Invoice', 25),
(12, 'App\\Models\\Invoice', 26),
(12, 'App\\Models\\GetQuote', 31),
(12, 'App\\Models\\GetQuote', 32),
(15, 'App\\Models\\Invoice', 17),
(15, 'App\\Models\\Invoice', 18),
(15, 'App\\Models\\Invoice', 19),
(15, 'App\\Models\\Invoice', 20),
(15, 'App\\Models\\Invoice', 21),
(15, 'App\\Models\\Invoice', 22),
(15, 'App\\Models\\Invoice', 23),
(15, 'App\\Models\\Invoice', 24),
(15, 'App\\Models\\Invoice', 25),
(15, 'App\\Models\\Invoice', 26),
(15, 'App\\Models\\GetQuote', 31),
(15, 'App\\Models\\GetQuote', 32);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `short_desc` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `slug`, `short_desc`, `description`, `image_path`, `file_path`, `status`, `created_at`, `updated_at`) VALUES
(11, 'WordPress Customization & Development', 'wordpress-customization-development', '<p>Get a fully functional, custom WordPress website tailored to your needs. Whether it&#39;s an eCommerce store, business website, blog, or membership platform, I deliver high-performance, SEO-optimized, and responsive solutions. Let&#39;s build your dream website today!</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>', '<p>At <b>MSN Softtech</b>, we specialize in customizing and developing high-performing WordPress websites tailored to your business needs. Whether you need a simple blog, corporate site, or complex e-commerce platform, our expert team delivers innovative solutions for growth and engagement.<h3><b>Our WordPress Services:</b></h3><ol>\r\n	<li><b>Custom Development</b>: Tailored WordPress websites with top performance and security.</li>\r\n	<li><b>Theme Customization</b>: Enhance design and functionality with custom themes.</li>\r\n	<li><b>WooCommerce Development</b>: Secure, feature-rich e-commerce stores.</li>\r\n	<li><b>Plugin Development</b>: Custom plugins and modifications for unique needs.</li>\r\n	<li><b>Performance Optimization</b>: Fast load times and improved SEO.</li>\r\n	<li><b>Security &amp; Maintenance</b>: Ongoing security and updates.</li>\r\n	<li><b>SEO Optimization</b>: Built with SEO best practices for better rankings.</li>\r\n	<li><b>API Integration</b>: Seamless third-party integrations.</li>\r\n</ol><h3><b>Why Choose Us?</b></h3><ul>\r\n	<li><b>SEO-Friendly Websites</b>: Optimized for better rankings.</li>\r\n	<li><b>Mobile-Responsive Design</b>: Perfectly functional across devices.</li>\r\n	<li><b>Fast Loading Speed</b>: Ensuring optimal performance.</li>\r\n	<li><b>Secure &amp; Scalable Solutions</b>: Robust security measures.</li>\r\n	<li><b>24/7 Support &amp; Maintenance</b>: Ongoing updates and protection.</li>\r\n</ul><h3><b>Industries We Serve:</b></h3><ul>\r\n	<li>E-Commerce</li>\r\n	<li>Healthcare</li>\r\n	<li>Education</li>\r\n	<li>Real Estate</li>\r\n	<li>Finance</li>\r\n	<li>Travel &amp; Hospitality</li>\r\n	<li>Startups</li>\r\n	<li>Corporate</li>\r\n</ul><p><b>Get Started Today!</b><br>\r\nContact us for a free consultation, and let\'s take your business to the next level with a powerful, SEO-optimized WordPress site!</p></p>\n', 'mobile app (2)_1739281096.png', NULL, 1, '2025-02-04 23:00:28', '2025-02-11 13:39:10'),
(12, 'Website Development', 'website-development', '<p>I offer expert web and software development with modern technologies, ensuring speed, security, and SEO optimization. Let&rsquo;s build something amazing!</p>\r\n\r\n<p>&nbsp;</p>', '<p>At <b>MSN Softtech</b>, we specialize in building high-performing, user-friendly websites tailored to your business. From simple websites to complex web applications and e-commerce stores, our expert team delivers innovative solutions for growth and engagement.<h3><b>Our Web Development Services:</b></h3><ol>\r\n	<li><b>Custom Website Development</b>: Fully customized websites with seamless performance, security, and scalability.</li>\r\n	<li><b>E-Commerce Development</b>: Feature-rich online stores with payment integration and product management.</li>\r\n	<li><b>WordPress Development</b>: SEO-friendly, performance-optimized WordPress websites.</li>\r\n	<li><b>Laravel &amp; PHP Development</b>: Secure and scalable web applications using Laravel and PHP.</li>\r\n	<li><b>Frontend Development</b>: Dynamic UIs with Vue.js and React.js for an exceptional user experience.</li>\r\n	<li><b>Web App Development</b>: Custom SaaS platforms and CRM solutions to enhance productivity.</li>\r\n	<li><b>API Development &amp; Integration</b>: Secure APIs for seamless system and third-party integrations.</li>\r\n	<li><b>Website Redesign &amp; Optimization</b>: Fresh, modern designs with improved speed and SEO.</li>\r\n</ol><h3><b>Why Choose Us?</b></h3><ul>\r\n	<li><b>SEO-Friendly Websites</b>: Optimized for better visibility and ranking.</li>\r\n	<li><b>Mobile-Responsive Design</b>: Perfectly functional on all devices.</li>\r\n	<li><b>Fast Loading Speed</b>: Optimal performance for quick load times.</li>\r\n	<li><b>Secure &amp; Scalable Solutions</b>: Top-notch security and scalable infrastructure.</li>\r\n	<li><b>24/7 Support &amp; Maintenance</b>: Ongoing updates and security.</li>\r\n</ul><h3><b>Industries We Serve:</b></h3><ul>\r\n	<li>E-Commerce</li>\r\n	<li>Healthcare</li>\r\n	<li>Education</li>\r\n	<li>Real Estate</li>\r\n	<li>Finance</li>\r\n	<li>Travel &amp; Hospitality</li>\r\n	<li>Startups</li>\r\n	<li>Corporate</li>\r\n</ul><p>&nbsp;</p><p><b>Get Started Today!&nbsp;</b><br>\r\nContact us for a free consultation, and let\'s elevate your online presence with a powerful, SEO-optimized website!</p></p>\n', 'Web Design (7)_1739281136.png', NULL, 1, '2025-02-05 00:30:33', '2025-02-11 13:38:56'),
(13, 'Android App Development', 'android-app-development', '<h3>Looking for <b>expert Android app development services?</b> We provide top-notch solutions to help businesses and individuals launch innovative and user-friendly <b>mobile applications</b>. Our team ensures seamless functionality, engaging user experiences, and top-tier security.</h3>', '<p><b>Android App Development Services &ndash; Build High-Performance Mobile Apps</b><p>Looking for expert Android app development services? We provide top-notch solutions to help businesses and individuals launch innovative and user-friendly mobile applications. Our team ensures seamless functionality, engaging user experiences, and top-tier security.</p><h3><b>Our Android Development Services</b></h3><ul>\r\n	<li><b>Custom Android App Development</b> &ndash; Scalable, feature-rich apps tailored to your business needs.</li>\r\n	<li><b>UI/UX Design</b> &ndash; Visually appealing, user-centric designs that enhance user engagement.</li>\r\n	<li><b>App Testing &amp; Quality Assurance</b> &ndash; Ensuring flawless performance with rigorous testing.</li>\r\n	<li><b>App Maintenance &amp; Support</b> &ndash; Ongoing updates, security enhancements, and feature upgrades.</li>\r\n	<li><b>API Integration &amp; Backend Development</b> &ndash; Secure and efficient backend solutions for smooth operations.</li>\r\n	<li><b>E-commerce &amp; Business Apps</b> &ndash; Custom solutions for online stores and corporate management.</li>\r\n	<li><b>Wearable &amp; IoT App Development</b> &ndash; Cutting-edge apps for smart devices and connected systems.</li>\r\n	<li><b>Migration &amp; Upgrades</b> &ndash; Upgrade existing apps with the latest technologies for better security and performance.</li>\r\n</ul><h3><b>Why Choose Our Android App Development Services?</b></h3><ul>\r\n	<li><b>Expert Android Developers</b> &ndash; Experienced professionals in mobile app development.</li>\r\n	<li><b>Client-Focused Approach</b> &ndash; Customized solutions tailored to business goals.</li>\r\n	<li><b>Advanced Technologies</b> &ndash; Implementing modern frameworks and industry best practices.</li>\r\n	<li><b>Timely Project Delivery</b> &ndash; On-time completion with a commitment to quality.</li>\r\n	<li><b>Cost-Effective Solutions</b> &ndash; Affordable pricing models suitable for startups and enterprises.</li>\r\n</ul><h3><b>Let&rsquo;s Build Your Android App Today!</b></h3><p>Ready to take your business to the next level? Contact us now to discuss your Android app development project!</p></p>\n', 'mobile app (4)_1739281363.png', NULL, 1, '2025-02-11 11:30:12', '2025-02-11 16:05:59'),
(14, 'iOS Development Services', 'ios-development-services', '<p>At <b>MSN Softtech</b>, we specialize in creating high-performance iOS applications tailored to meet your business needs. Our skilled team of developers leverages the latest technologies to build intuitive, engaging, and scalable iOS apps.</p>', '<p>At MSN Softtech, we craft high-performance iOS applications tailored to your business needs. Our team uses the latest technologies to build intuitive, scalable apps that engage users and drive success.<h3><b>Our iOS Development Process:</b></h3><ul>\r\n	<li><b>Consultation &amp; Analysis:</b> We define project goals and requirements.</li>\r\n	<li><b>App Design:</b> We create brand-aligned, user-friendly interfaces.</li>\r\n	<li><b>Development:</b> Using Swift and iOS technologies, we build efficient, scalable apps.</li>\r\n	<li><b>Testing &amp; QA:</b> Rigorous testing ensures bug-free, high-quality apps.</li>\r\n	<li><b>Deployment &amp; Support:</b> We launch and maintain your app for optimal performance.</li>\r\n</ul><h3><b>Expertise Includes:</b></h3><ul>\r\n	<li><b>Native iOS Apps</b>: High-performance, native apps for the best experience.</li>\r\n	<li><b>Cross-Platform Development</b>: Using React Native and Flutter for seamless iOS and Android apps.</li>\r\n	<li><b>App Integration</b>: Integrating third-party APIs, services, and databases.</li>\r\n	<li><b>Enterprise Solutions</b>: Robust apps to streamline operations.</li>\r\n	<li><b>App Maintenance</b>: Regular updates, performance optimization, and bug fixes.</li>\r\n</ul><h3><b>Industries We Serve:</b></h3><ul>\r\n	<li><b>E-commerce</b>: Apps to drive sales and improve engagement.</li>\r\n	<li><b>Healthcare</b>: Secure, user-friendly healthcare apps.</li>\r\n	<li><b>Education</b>: Interactive apps for schools and colleges.</li>\r\n	<li><b>Finance</b>: Secure apps for banking and transactions.</li>\r\n	<li><b>Entertainment</b>: Media and entertainment apps for streaming and gaming.</li>\r\n</ul><p>&nbsp;</p><p><b>Let\'s Build Your iOS App:&nbsp;</b>Contact us today for a consultation, and let\'s turn your iOS app idea into reality!</p></p>\n', 'mobile app (3)_1739281398.png', NULL, 1, '2025-02-11 12:18:26', '2025-02-11 13:43:27'),
(15, 'SEO Services', 'seo-services', '<p>At <b>MSN Softtech</b>, we specialize in providing comprehensive SEO solutions that increase your online visibility, drive traffic, and help your business grow. Our expert team of SEO professionals uses proven strategies and the latest industry trends to help you achieve long-term success.</p>', '<p>At <b>MSN Softtech</b>, we help businesses increase organic traffic, improve search rankings, and convert visitors into customers with our proven SEO strategies.<h3>Our Key SEO Services:</h3><ul>\r\n	<li><b>SEO Audits</b>: Identify and fix SEO issues on your website.</li>\r\n	<li><b>Keyword Research</b>: Target high-value, relevant keywords for your business.</li>\r\n	<li><b>On-Page SEO</b>: Optimize content, meta tags, and internal linking for better search rankings.</li>\r\n	<li><b>Technical SEO</b>: Enhance website speed, mobile optimization, and crawlability.</li>\r\n	<li><b>Content Optimization</b>: Improve content for both users and search engines.</li>\r\n	<li><b>Link Building</b>: Strengthen your domain authority with quality backlinks.</li>\r\n	<li><b>Local SEO</b>: Improve your visibility in local search results and Google My Business.</li>\r\n	<li><b>E-commerce SEO</b>: Optimize product pages and drive sales.</li>\r\n	<li><b>SEO Reporting</b>: Track performance with transparent reports and actionable insights.</li>\r\n</ul><h3>Why Choose Us?</h3><ul>\r\n	<li><b>Tailored Strategies</b>: Custom SEO plans aligned with your business goals.</li>\r\n	<li><b>Experienced Team</b>: Stay updated with the latest SEO trends and algorithm changes.</li>\r\n	<li><b>Long-Term Results</b>: Achieve sustainable growth through ongoing optimization.</li>\r\n	<li><b>Proven Success</b>: Our clients experience measurable improvements in traffic and rankings.</li>\r\n</ul><h3>Industries We Serve:</h3><ul>\r\n	<li><b>E-commerce</b>: Boost product sales and conversions with optimized listings.</li>\r\n	<li><b>Healthcare</b>: Drive patient inquiries through targeted local SEO.</li>\r\n	<li><b>Real Estate</b>: Increase property inquiries and generate more leads.</li>\r\n	<li><b>Legal</b>: Improve online visibility for law firms and attract clients.</li>\r\n	<li><b>Travel &amp; Hospitality</b>: Boost bookings with better search visibility.</li>\r\n	<li><b>Finance &amp; Insurance</b>: Improve credibility and rank for competitive keywords.</li>\r\n	<li><b>Education</b>: Help schools and colleges attract more students with optimized content.</li>\r\n	<li><b>Technology</b>: Enhance visibility for tech companies and SaaS products.</li>\r\n	<li><b>Automotive</b>: Optimize dealership sites to drive more leads and sales.</li>\r\n	<li><b>Nonprofits</b>: Improve donations and engagement with effective SEO strategies.</li>\r\n</ul></p>\n', '1_1739278991.jpg', NULL, 1, '2025-02-11 12:25:09', '2025-02-11 13:03:12');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `logo_path` varchar(500) DEFAULT NULL,
  `favicon_path` varchar(500) DEFAULT NULL,
  `phone_one` varchar(50) DEFAULT NULL,
  `phone_two` varchar(50) DEFAULT NULL,
  `email_one` varchar(191) DEFAULT NULL,
  `email_two` varchar(191) DEFAULT NULL,
  `contact_address` text DEFAULT NULL,
  `contact_mail` varchar(191) DEFAULT NULL,
  `office_hours` text DEFAULT NULL,
  `google_map` text DEFAULT NULL,
  `google_analytics` text DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `custom_css` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `title`, `description`, `keywords`, `logo_path`, `favicon_path`, `phone_one`, `phone_two`, `email_one`, `email_two`, `contact_address`, `contact_mail`, `office_hours`, `google_map`, `google_analytics`, `footer_text`, `custom_css`, `status`, `created_at`, `updated_at`) VALUES
(3, 'MSN SOFTTECH - Unlock Your Business Potential with Us.', 'MSN SoftTech is a leading agency specializing in software development, web design, mobile app development, and SEO services. Over the last 10 years, we have been delivering top-notch IT solutions both locally and internationally, helping businesses establish a strong digital presence and achieve sustainable growth.', 'MSN SoftTech, Software Development, Web Design, App Development, SEO Services, IT Solutions, Digital Marketing, E-commerce Development, Mobile App Development, WordPress Development, Custom Software Solutions, Website Design Agency, Online Business Growth, Top IT Company, Tech Solutions, Business Digitalization, Web Development Services, Local and International IT Services, Professional SEO, IT Consultancy, Software Company, Digital Transformation.', 'Untitled-4_1739083515.png', 'FAV-2_1739083274.png', '+8801325359909', '+8801575727387', 'support@msnsofttech.com', NULL, 'Anowara Loz 140/2, Cumilla', 'support@msnsofttech.com', 'Saturday to Thursday 10:00 am -10:00 pm', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d933861.3784271893!2d89.78547141117329!3d23.893305939219363!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c0b333d4522f%3A0xaf35afef663e4696!2sBangladesh%20Hi-Tech%20Park%20Authority%20(BHTPA)!5e0!3m2!1sen!2sbd!4v1604083381308!5m2!1sen!2sbd\" width=\"600\" height=\"450\" frameborder=\"0\" style=\"border:0;\" allowfullscreen=\"\" aria-hidden=\"false\" tabindex=\"0\"></iframe>', NULL, '2025 - MSN SOFTTECH - Multipurpose Business | Created By_ <a href=\"https://msnsofttech.com/\" target=\"_blank\">MSN SOFTTECH</a>', ' /** theme customize css **/ ', 1, '2021-11-07 14:26:20', '2025-02-09 21:52:35');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `link` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `title`, `slug`, `description`, `image_path`, `link`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Unlock Your Business Potential with Us.', 'unlock-your-business-potential-with-us', '<p>We provide innovative solutions to help your business grow, scale, and succeed. From IT services, web and software development to digital marketing and automation, we offer tailored strategies to maximize efficiency and drive results. Partner with us to transform your vision into reality!</p>', '4Jq-Oo--Q6SY5p6YB0OJ2A_1739080712.png', 'https://msnsofttech.com/services', 1, '2020-10-30 11:12:39', '2025-02-09 05:58:32');

-- --------------------------------------------------------

--
-- Table structure for table `socials`
--

CREATE TABLE `socials` (
  `id` int(10) UNSIGNED NOT NULL,
  `facebook` varchar(191) DEFAULT NULL,
  `twitter` varchar(191) DEFAULT NULL,
  `linkedin` varchar(191) DEFAULT NULL,
  `instagram` varchar(191) DEFAULT NULL,
  `pinterest` varchar(191) DEFAULT NULL,
  `youtube` varchar(191) DEFAULT NULL,
  `skype` varchar(191) DEFAULT NULL,
  `whatsapp` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `socials`
--

INSERT INTO `socials` (`id`, `facebook`, `twitter`, `linkedin`, `instagram`, `pinterest`, `youtube`, `skype`, `whatsapp`, `status`, `created_at`, `updated_at`) VALUES
(3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '+8801575727387', 1, '2021-11-07 14:26:20', '2025-02-09 21:52:56');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `name`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'example@mail.com', 1, '2020-10-30 06:38:27', '2020-10-30 06:38:27'),
(2, NULL, 'admin@mail.com', 1, '2020-10-30 06:38:35', '2020-10-30 06:38:35'),
(18, NULL, 'andrea.briede@gmail.com', 1, '2025-02-07 12:00:22', '2025-02-07 12:00:22'),
(19, NULL, 'bholly72@hotmail.com', 1, '2025-02-07 12:18:24', '2025-02-07 12:18:24'),
(20, NULL, 'elaine.binks@gmail.com', 1, '2025-02-07 12:22:28', '2025-02-07 12:22:28'),
(21, NULL, 'cleo0124@gmail.com', 1, '2025-02-07 12:28:47', '2025-02-07 12:28:47'),
(22, NULL, 'amack@asi-mi.org', 1, '2025-02-07 12:29:08', '2025-02-07 12:29:08'),
(23, NULL, 'howardjm92@gmail.com', 1, '2025-02-07 12:36:04', '2025-02-07 12:36:04'),
(24, NULL, 'thuydung157@gmail.com', 1, '2025-02-07 12:50:51', '2025-02-07 12:50:51'),
(25, NULL, 'w.nasir7@gmail.com', 1, '2025-02-07 13:12:16', '2025-02-07 13:12:16'),
(26, NULL, 'arfernandez4@miners.utep.edu', 1, '2025-02-07 13:26:35', '2025-02-07 13:26:35'),
(27, NULL, 'charknes320@gmail.com', 1, '2025-02-07 13:52:18', '2025-02-07 13:52:18'),
(28, NULL, 'kateaobrien9@gmail.com', 1, '2025-02-07 13:57:09', '2025-02-07 13:57:09'),
(29, NULL, 'tpotter923@gmail.com', 1, '2025-02-07 14:00:15', '2025-02-07 14:00:15'),
(30, NULL, 'michelepatrices@gmail.com', 1, '2025-02-07 14:03:15', '2025-02-07 14:03:15'),
(31, NULL, 'oscar.g.cruz74@gmail.com', 1, '2025-02-07 14:18:30', '2025-02-07 14:18:30'),
(32, NULL, 'plavine@comcast.net', 1, '2025-02-07 14:32:16', '2025-02-07 14:32:16'),
(33, NULL, 'jesuscorporation42@gmail.com', 1, '2025-02-07 14:40:16', '2025-02-07 14:40:16'),
(34, NULL, 'robertaduhl59@gmail.com', 1, '2025-02-07 14:51:06', '2025-02-07 14:51:06'),
(35, NULL, 'jal.courtney@gmail.com', 1, '2025-02-07 14:51:11', '2025-02-07 14:51:11'),
(36, NULL, 'ricviccom@twc.com', 1, '2025-02-07 15:01:26', '2025-02-07 15:01:26'),
(37, NULL, 'jpfund28@gmail.com', 1, '2025-02-07 15:02:12', '2025-02-07 15:02:12'),
(38, NULL, 'yidwaz77@hotmail.co.uk', 1, '2025-02-07 15:08:04', '2025-02-07 15:08:04'),
(39, NULL, 'lovinstories102@gmail.com', 1, '2025-02-07 15:15:20', '2025-02-07 15:15:20'),
(40, NULL, 'bryanvictoria25@gmail.com', 1, '2025-02-07 15:19:24', '2025-02-07 15:19:24'),
(41, NULL, 'rsilverman157@comcast.net', 1, '2025-02-07 15:23:13', '2025-02-07 15:23:13'),
(42, NULL, 'suepfund@gmail.com', 1, '2025-02-07 15:27:39', '2025-02-07 15:27:39'),
(43, NULL, 'mazetoscar@gmail.com', 1, '2025-02-07 15:27:58', '2025-02-07 15:27:58'),
(44, NULL, 'james.stogin@gmail.com', 1, '2025-02-07 15:29:11', '2025-02-07 15:29:11'),
(45, NULL, 'breanna@skinbylovely.com', 1, '2025-02-07 15:30:49', '2025-02-07 15:30:49'),
(46, NULL, 'dmoody870@gmail.com', 1, '2025-02-07 15:38:20', '2025-02-07 15:38:20'),
(47, NULL, 'carolecrane04@gmail.com', 1, '2025-02-07 15:38:43', '2025-02-07 15:38:43'),
(48, NULL, 'recardosi@gmail.com', 1, '2025-02-07 15:41:06', '2025-02-07 15:41:06'),
(49, NULL, 'steve.cross@okstate.edu', 1, '2025-02-07 15:50:04', '2025-02-07 15:50:04'),
(50, NULL, 'ladycroft_mj.pyt@icloud.com', 1, '2025-02-07 15:50:57', '2025-02-07 15:50:57'),
(51, NULL, 'marcijodiehl@gmail.com', 1, '2025-02-07 16:08:12', '2025-02-07 16:08:12'),
(52, NULL, 'kburdine55@outlook.com', 1, '2025-02-07 16:08:28', '2025-02-07 16:08:28'),
(53, NULL, 'jeramy.ringwolski@gmail.com', 1, '2025-02-07 16:13:30', '2025-02-07 16:13:30'),
(54, NULL, 'sdmartin472@gmail.com', 1, '2025-02-07 16:13:32', '2025-02-07 16:13:32'),
(55, NULL, 'lindsey.rhoden.89@gmail.com', 1, '2025-02-07 16:32:21', '2025-02-07 16:32:21'),
(56, NULL, 'prkeith@hardynet.com', 1, '2025-02-07 16:33:17', '2025-02-07 16:33:17'),
(57, NULL, 'joanne@joannebates.com', 1, '2025-02-07 16:38:25', '2025-02-07 16:38:25'),
(58, NULL, 'everettstephens50@gmail.com', 1, '2025-02-07 16:40:27', '2025-02-07 16:40:27'),
(59, NULL, 'mlking1414@gmail.com', 1, '2025-02-07 16:47:22', '2025-02-07 16:47:22'),
(60, NULL, 'cataylor1173@gmail.com', 1, '2025-02-07 17:05:05', '2025-02-07 17:05:05'),
(61, NULL, 'ganeshkumar.jayaram@centricconsulting.com', 1, '2025-02-07 17:08:11', '2025-02-07 17:08:11'),
(62, NULL, 'difuntorum@cox.net', 1, '2025-02-07 17:16:34', '2025-02-07 17:16:34'),
(63, NULL, 'deon.minor@gmail.com', 1, '2025-02-07 17:17:05', '2025-02-07 17:17:05'),
(64, NULL, 'socarroll@wcas.com', 1, '2025-02-07 17:20:08', '2025-02-07 17:20:08'),
(65, NULL, 'natipetit@gmail.com', 1, '2025-02-07 17:30:16', '2025-02-07 17:30:16'),
(66, NULL, 'earonoff@pearlcohen.com', 1, '2025-02-07 17:31:11', '2025-02-07 17:31:11'),
(67, NULL, 'jbrocke@financialguide.com', 1, '2025-02-07 17:35:21', '2025-02-07 17:35:21'),
(68, NULL, 'christopher.powell19@us.army.mil', 1, '2025-02-07 17:41:57', '2025-02-07 17:41:57'),
(69, NULL, 'xixiku90@gmail.com', 1, '2025-02-07 17:49:12', '2025-02-07 17:49:12'),
(70, NULL, 'kingpamela1966@gmail.com', 1, '2025-02-07 17:57:19', '2025-02-07 17:57:19'),
(71, NULL, 'jennifer.schaeffer@hotmail.co.uk', 1, '2025-02-07 18:20:07', '2025-02-07 18:20:07'),
(72, NULL, 'lcunningham710@gmail.com', 1, '2025-02-07 18:21:08', '2025-02-07 18:21:08'),
(73, NULL, 'jmccormack@topco.com', 1, '2025-02-07 18:22:47', '2025-02-07 18:22:47'),
(74, NULL, 'lavellecalloway55@gmail.com', 1, '2025-02-07 18:30:18', '2025-02-07 18:30:18'),
(75, NULL, 'nathan@home-pro-inspections.com', 1, '2025-02-07 18:30:28', '2025-02-07 18:30:28'),
(76, NULL, 'wguo@belvederetrading.com', 1, '2025-02-07 18:35:09', '2025-02-07 18:35:09'),
(77, NULL, 'muruganm@metriqe.com', 1, '2025-02-07 18:39:14', '2025-02-07 18:39:14'),
(78, NULL, 'cherimurray76@gmail.com', 1, '2025-02-07 18:40:44', '2025-02-07 18:40:44'),
(79, NULL, 'miss-kerr@hotmail.co.uk', 1, '2025-02-07 18:43:10', '2025-02-07 18:43:10'),
(80, NULL, 'blairman@polarblairsden.com', 1, '2025-02-07 18:45:11', '2025-02-07 18:45:11'),
(81, NULL, 'gracebarnard9@gmail.com', 1, '2025-02-07 18:46:04', '2025-02-07 18:46:04'),
(82, NULL, 'jovan@expeditedplus.com', 1, '2025-02-07 19:56:11', '2025-02-07 19:56:11'),
(83, NULL, 'nakagawa@prime-system.jp', 1, '2025-02-07 19:58:27', '2025-02-07 19:58:27'),
(84, NULL, 'soothingtouch23@gmail.com', 1, '2025-02-07 20:28:30', '2025-02-07 20:28:30'),
(85, NULL, 'jsteiner2010@gmail.com', 1, '2025-02-07 20:32:00', '2025-02-07 20:32:00'),
(86, NULL, 'will.dewind@gmail.com', 1, '2025-02-07 20:45:18', '2025-02-07 20:45:18'),
(87, NULL, 'nubia.tamayo@maverickcap.com', 1, '2025-02-07 20:50:17', '2025-02-07 20:50:17'),
(88, NULL, 'mcfeelysgourmetchocolate@gmail.com', 1, '2025-02-07 20:51:09', '2025-02-07 20:51:09'),
(89, NULL, 'podrinjka84@hotmail.com', 1, '2025-02-07 20:58:12', '2025-02-07 20:58:12'),
(90, NULL, 'clarepark71@gmail.com', 1, '2025-02-07 21:10:57', '2025-02-07 21:10:57'),
(91, NULL, 'rweatherford@gmail.com', 1, '2025-02-07 21:11:06', '2025-02-07 21:11:06'),
(92, NULL, 'manfredschillings@gmail.com', 1, '2025-02-07 21:24:14', '2025-02-07 21:24:14'),
(93, NULL, 'federico.massa01@gmail.com', 1, '2025-02-07 21:37:22', '2025-02-07 21:37:22'),
(94, NULL, 'lollip30@gmail.com', 1, '2025-02-07 21:42:06', '2025-02-07 21:42:06'),
(95, NULL, 'meeganmackay@gmail.com', 1, '2025-02-07 21:44:08', '2025-02-07 21:44:08'),
(96, NULL, 'gerung.ness@gmail.com', 1, '2025-02-07 21:45:08', '2025-02-07 21:45:08'),
(97, NULL, 'mikelle@m-kon.com', 1, '2025-02-07 22:09:24', '2025-02-07 22:09:24'),
(98, NULL, 'annblue116@gmail.com', 1, '2025-02-07 22:12:28', '2025-02-07 22:12:28'),
(99, NULL, 'philipdharvey@gmail.com', 1, '2025-02-07 22:26:25', '2025-02-07 22:26:25'),
(100, NULL, 'kelly.watson1@icloud.com', 1, '2025-02-07 22:36:14', '2025-02-07 22:36:14'),
(101, NULL, 'g_horst@hotmail.com', 1, '2025-02-07 22:40:16', '2025-02-07 22:40:16'),
(102, NULL, 'ccritchey@gmail.com', 1, '2025-02-07 22:44:20', '2025-02-07 22:44:20'),
(103, NULL, 'tattedchik04@hotmail.com', 1, '2025-02-07 22:48:16', '2025-02-07 22:48:16'),
(104, NULL, 'donna.bricker@bnymellon.com', 1, '2025-02-07 23:05:17', '2025-02-07 23:05:17'),
(105, NULL, 'mct1213@gmail.com', 1, '2025-02-07 23:16:13', '2025-02-07 23:16:13'),
(106, NULL, 'jerwin@midweststeelworks.com', 1, '2025-02-07 23:18:36', '2025-02-07 23:18:36'),
(107, NULL, 'jill.nelson03@gmail.com', 1, '2025-02-07 23:24:18', '2025-02-07 23:24:18'),
(108, NULL, 'alharp40@fuse.net', 1, '2025-02-07 23:34:04', '2025-02-07 23:34:04'),
(109, NULL, 'brhanaboreslan@gmail.com', 1, '2025-02-07 23:39:40', '2025-02-07 23:39:40'),
(110, NULL, 'ciyoni27@gmail.com', 1, '2025-02-07 23:39:49', '2025-02-07 23:39:49'),
(111, NULL, 'abbiecalladine@hotmail.co.uk', 1, '2025-02-07 23:41:04', '2025-02-07 23:41:04'),
(112, NULL, 'athern@mail.com', 1, '2025-02-07 23:45:06', '2025-02-07 23:45:06'),
(113, NULL, 'pamela.terp@gmail.com', 1, '2025-02-07 23:52:08', '2025-02-07 23:52:08'),
(114, NULL, 'sakazar@gmail.com', 1, '2025-02-08 00:00:11', '2025-02-08 00:00:11'),
(115, NULL, 'cptjack79@icloud.com', 1, '2025-02-08 00:05:05', '2025-02-08 00:05:05'),
(116, NULL, 'ashton.tullbent@gmail.com', 1, '2025-02-08 00:10:26', '2025-02-08 00:10:26'),
(117, NULL, 'anne.b-k@hotmail.co.uk', 1, '2025-02-08 00:32:28', '2025-02-08 00:32:28'),
(118, NULL, 'dead_skeptic@hotmail.com', 1, '2025-02-08 00:32:53', '2025-02-08 00:32:53'),
(119, NULL, 'dbcovert14@gmail.com', 1, '2025-02-08 00:33:04', '2025-02-08 00:33:04'),
(120, NULL, 'mserkin@stny.rr.com', 1, '2025-02-08 00:51:24', '2025-02-08 00:51:24'),
(121, NULL, 'topunulove@gmail.com', 1, '2025-02-08 00:53:08', '2025-02-08 00:53:08'),
(122, NULL, 'melodiegarrison@hotmail.co.uk', 1, '2025-02-08 00:56:53', '2025-02-08 00:56:53'),
(123, NULL, 'williamsimpkins@gmail.com', 1, '2025-02-08 00:57:03', '2025-02-08 00:57:03'),
(124, NULL, 'brannonlong3@gmail.com', 1, '2025-02-08 01:07:08', '2025-02-08 01:07:08'),
(125, NULL, 'joel.savitzky@gmail.com', 1, '2025-02-08 01:07:21', '2025-02-08 01:07:21'),
(126, NULL, 'tom.andradibrown@gmail.com', 1, '2025-02-08 01:11:15', '2025-02-08 01:11:15'),
(127, NULL, 'joseph.b.lantz3@gmail.com', 1, '2025-02-08 01:13:10', '2025-02-08 01:13:10'),
(128, NULL, 'golfordiecb@hotmail.com', 1, '2025-02-08 01:17:03', '2025-02-08 01:17:03'),
(129, NULL, 'jl1993926@gmail.com', 1, '2025-02-08 02:05:29', '2025-02-08 02:05:29'),
(130, NULL, 'alexander.moen10@gmail.com', 1, '2025-02-08 02:17:00', '2025-02-08 02:17:00'),
(131, NULL, 'brehmcf@gmail.com', 1, '2025-02-08 02:21:40', '2025-02-08 02:21:40'),
(132, NULL, 'cgrossfootball1980@gmail.com', 1, '2025-02-08 02:48:19', '2025-02-08 02:48:19'),
(133, NULL, 'brehmcf@zoominternet.net', 1, '2025-02-08 02:54:22', '2025-02-08 02:54:22'),
(134, NULL, 'gasper2@msn.com', 1, '2025-02-08 03:07:29', '2025-02-08 03:07:29'),
(135, NULL, 'donjanekarches@gmail.com', 1, '2025-02-08 03:12:11', '2025-02-08 03:12:11'),
(136, NULL, 'gamachan115@gmail.com', 1, '2025-02-08 03:22:13', '2025-02-08 03:22:13'),
(137, NULL, 'rkay_in_sd@hotmail.com', 1, '2025-02-08 03:32:36', '2025-02-08 03:32:36'),
(138, NULL, 'doreen.mccarthy@uscg.mil', 1, '2025-02-08 03:47:15', '2025-02-08 03:47:15'),
(139, NULL, 'nfbisceglie@gmail.com', 1, '2025-02-08 03:48:19', '2025-02-08 03:48:19'),
(140, NULL, 'agosonsavnelli@yahoo.com', 1, '2025-02-08 04:06:04', '2025-02-08 04:06:04'),
(141, NULL, 'moralesjon09@gmail.com', 1, '2025-02-08 04:06:14', '2025-02-08 04:06:14'),
(142, NULL, 'dreamcatcherstudio@comcast.net', 1, '2025-02-08 04:36:12', '2025-02-08 04:36:12'),
(143, NULL, 'teineker@gmail.com', 1, '2025-02-08 04:48:11', '2025-02-08 04:48:11'),
(144, NULL, 'teineker+supp@gmail.com', 1, '2025-02-08 04:51:20', '2025-02-08 04:51:20'),
(145, NULL, 'cpappa7@gmail.com', 1, '2025-02-08 04:52:09', '2025-02-08 04:52:09'),
(146, NULL, 'kenscholz72@gmail.com', 1, '2025-02-08 04:52:11', '2025-02-08 04:52:11'),
(147, NULL, 'cpmbwmiller@gmail.com', 1, '2025-02-08 05:21:43', '2025-02-08 05:21:43'),
(148, NULL, 'hiromi0329@i.softbank.jp', 1, '2025-02-08 05:23:41', '2025-02-08 05:23:41'),
(149, NULL, 'cpappa7+online@gmail.com', 1, '2025-02-08 05:42:03', '2025-02-08 05:42:03'),
(150, NULL, 'gayzurh@srt.com', 1, '2025-02-08 05:45:06', '2025-02-08 05:45:06'),
(151, NULL, 'igoraraujoferreira@gmail.com', 1, '2025-02-08 05:58:19', '2025-02-08 05:58:19'),
(152, NULL, 'eclipseuy11vergeea@gmail.com', 1, '2025-02-08 06:18:04', '2025-02-08 06:18:04'),
(153, NULL, 'jacob.dale1991@gmail.com', 1, '2025-02-08 06:27:21', '2025-02-08 06:27:21'),
(154, NULL, 'liloexz5@gmail.com', 1, '2025-02-08 06:32:11', '2025-02-08 06:32:11'),
(155, NULL, 'jhklawgroup@gmail.com', 1, '2025-02-08 06:34:04', '2025-02-08 06:34:04'),
(156, NULL, 'dbolarin@gmail.com', 1, '2025-02-08 08:21:43', '2025-02-08 08:21:43'),
(157, NULL, 'jmpk251@gmail.com', 1, '2025-02-08 08:30:42', '2025-02-08 08:30:42'),
(158, NULL, 'artur.dessoy@gmail.com', 1, '2025-02-08 09:07:57', '2025-02-08 09:07:57'),
(159, NULL, 'fmcmahon3@optimum.net', 1, '2025-02-08 10:00:15', '2025-02-08 10:00:15'),
(160, NULL, 'gchapin38@gmail.com', 1, '2025-02-08 10:28:04', '2025-02-08 10:28:04'),
(161, NULL, 'dannywendywade@hotmail.com', 1, '2025-02-08 10:33:38', '2025-02-08 10:33:38'),
(162, NULL, 'laducasse@hotmail.com', 1, '2025-02-08 10:58:42', '2025-02-08 10:58:42'),
(163, NULL, 'knhaynes10@gmail.com', 1, '2025-02-08 11:15:43', '2025-02-08 11:15:43'),
(164, NULL, 'stefanielitau96@hotmail.de', 1, '2025-02-08 11:28:03', '2025-02-08 11:28:03'),
(165, NULL, 'goci@hotmail.de', 1, '2025-02-08 11:46:15', '2025-02-08 11:46:15'),
(166, NULL, 'smaeron77@gmail.com', 1, '2025-02-08 11:47:05', '2025-02-08 11:47:05'),
(167, NULL, 'ndbranse@web.de', 1, '2025-02-08 11:50:04', '2025-02-08 11:50:04'),
(168, NULL, 'pkleem47@gmail.com', 1, '2025-02-08 11:57:09', '2025-02-08 11:57:09'),
(169, NULL, 'cherylk20@icloud.com', 1, '2025-02-08 12:04:09', '2025-02-08 12:04:09'),
(170, NULL, 'danalynreyes@gmail.com', 1, '2025-02-08 12:11:06', '2025-02-08 12:11:06'),
(171, NULL, 'herrickbd@gmail.com', 1, '2025-02-08 12:15:31', '2025-02-08 12:15:31'),
(172, NULL, 'laura@seasalt.com.py', 1, '2025-02-08 12:20:40', '2025-02-08 12:20:40'),
(173, NULL, 'peter.hansch@me.com', 1, '2025-02-08 13:44:10', '2025-02-08 13:44:10'),
(174, NULL, 'aariekris80@gmail.com', 1, '2025-02-08 13:45:42', '2025-02-08 13:45:42'),
(175, NULL, 'pattanaboon@hotmail.com', 1, '2025-02-08 13:48:48', '2025-02-08 13:48:48'),
(176, NULL, 'levent@sunsky.be', 1, '2025-02-08 14:08:12', '2025-02-08 14:08:12'),
(177, NULL, 'kfosberg@juno.com', 1, '2025-02-08 14:41:09', '2025-02-08 14:41:09'),
(178, NULL, 'mertensinbox@gmail.com', 1, '2025-02-08 14:46:33', '2025-02-08 14:46:33'),
(179, NULL, 'auroraobrien@hotmail.co.uk', 1, '2025-02-08 15:05:54', '2025-02-08 15:05:54'),
(180, NULL, 'papamic@outlook.com', 1, '2025-02-08 15:10:14', '2025-02-08 15:10:14'),
(181, NULL, 'dhaslam@rivr.com', 1, '2025-02-08 15:22:46', '2025-02-08 15:22:46'),
(182, NULL, 'wangsbird@gmail.com', 1, '2025-02-08 15:49:43', '2025-02-08 15:49:43'),
(183, NULL, 'donovanm92@gmail.com', 1, '2025-02-08 15:58:29', '2025-02-08 15:58:29'),
(184, NULL, 'ellenelizabeth1984@gmail.com', 1, '2025-02-08 16:00:23', '2025-02-08 16:00:23'),
(185, NULL, 'jbenaissa@hotmail.de', 1, '2025-02-08 16:25:16', '2025-02-08 16:25:16'),
(186, NULL, 'dark@pc.117.cx', 1, '2025-02-08 16:43:24', '2025-02-08 16:43:24'),
(187, NULL, 'levina.batehup@hotmail.com', 1, '2025-02-08 16:43:47', '2025-02-08 16:43:47'),
(188, NULL, 'yogi_kapadia@hotmail.co.uk', 1, '2025-02-08 17:15:57', '2025-02-08 17:15:57'),
(189, NULL, 'withnail_and_pie@hotmail.co.uk', 1, '2025-02-08 17:18:17', '2025-02-08 17:18:17'),
(190, NULL, 'kimbrownmarcum513@gmail.com', 1, '2025-02-08 17:30:12', '2025-02-08 17:30:12'),
(191, NULL, 'michellemmullin2004@gmail.com', 1, '2025-02-08 17:32:04', '2025-02-08 17:32:04'),
(192, NULL, 'ljcruick@gmail.com', 1, '2025-02-08 17:48:29', '2025-02-08 17:48:29'),
(193, NULL, 'natalie.kalteis@web.de', 1, '2025-02-08 17:52:23', '2025-02-08 17:52:23'),
(194, NULL, 'darrentimon@gmail.com', 1, '2025-02-08 18:10:04', '2025-02-08 18:10:04'),
(195, NULL, 'justineupton@live.co.uk', 1, '2025-02-08 18:11:12', '2025-02-08 18:11:12'),
(196, NULL, 'franalanwilson@gmail.com', 1, '2025-02-08 18:29:06', '2025-02-08 18:29:06'),
(197, NULL, 'lucie-wood@hotmail.co.uk', 1, '2025-02-08 18:34:59', '2025-02-08 18:34:59'),
(198, NULL, 'barncord101@comcast.net', 1, '2025-02-08 18:35:03', '2025-02-08 18:35:03'),
(199, NULL, 'clare.sturgess13@hotmail.co.uk', 1, '2025-02-08 18:36:50', '2025-02-08 18:36:50'),
(200, NULL, 'krowe1954@gmail.com', 1, '2025-02-08 18:37:10', '2025-02-08 18:37:10'),
(201, NULL, 'jay_lancaster@hotmail.co.uk', 1, '2025-02-08 18:37:39', '2025-02-08 18:37:39'),
(202, NULL, 'emma-21@hotmail.co.uk', 1, '2025-02-08 18:48:27', '2025-02-08 18:48:27'),
(203, NULL, 'lsofiadb@gmail.com', 1, '2025-02-08 18:49:17', '2025-02-08 18:49:17'),
(204, NULL, 'sjcreagh@gmail.com', 1, '2025-02-08 18:50:05', '2025-02-08 18:50:05'),
(205, NULL, 'oconnor128@comcast.net', 1, '2025-02-08 19:06:24', '2025-02-08 19:06:24'),
(206, NULL, 'marli3@hotmail.co.uk', 1, '2025-02-08 19:19:40', '2025-02-08 19:19:40'),
(207, NULL, 'azadking1@hotmail.de', 1, '2025-02-08 19:21:29', '2025-02-08 19:21:29'),
(208, NULL, 'mcburns0812@gmail.com', 1, '2025-02-08 19:23:31', '2025-02-08 19:23:31'),
(209, NULL, 'yuukey_1128regrets@outlook.jp', 1, '2025-02-08 19:46:53', '2025-02-08 19:46:53'),
(210, NULL, 'griffinroberts08@gmail.com', 1, '2025-02-08 19:47:07', '2025-02-08 19:47:07'),
(211, NULL, 'kallgeier@windstream.net', 1, '2025-02-08 19:47:20', '2025-02-08 19:47:20'),
(212, NULL, 'udoplank@gmx.de', 1, '2025-02-08 19:53:06', '2025-02-08 19:53:06'),
(213, NULL, 'teresa.m.meehan@gmail.com', 1, '2025-02-08 20:12:40', '2025-02-08 20:12:40'),
(214, NULL, 'pmaloney@washjeff.edu', 1, '2025-02-08 20:28:16', '2025-02-08 20:28:16'),
(215, NULL, 'wbuck@leechtishman.com', 1, '2025-02-08 20:38:30', '2025-02-08 20:38:30'),
(216, NULL, 'ameerah.whitaker@gmail.com', 1, '2025-02-08 20:53:04', '2025-02-08 20:53:04'),
(217, NULL, 'jaspermeehan1@gmail.com', 1, '2025-02-08 20:55:09', '2025-02-08 20:55:09'),
(218, NULL, 'clegrow27@gmail.com', 1, '2025-02-08 20:57:17', '2025-02-08 20:57:17'),
(219, NULL, 'kirstyhurley@gmail.com', 1, '2025-02-08 20:57:58', '2025-02-08 20:57:58'),
(220, NULL, 'toriibabbi92@icloud.com', 1, '2025-02-08 21:01:21', '2025-02-08 21:01:21'),
(221, NULL, 'apurbarc19@gmail.com', 1, '2025-02-08 21:09:08', '2025-02-08 21:09:08'),
(222, NULL, 'bhamel@maine.rr.com', 1, '2025-02-08 21:19:23', '2025-02-08 21:19:23'),
(223, NULL, 'victoriam.gonz@gmail.com', 1, '2025-02-08 21:22:15', '2025-02-08 21:22:15'),
(224, NULL, 'remonty.3145@gmail.com', 1, '2025-02-08 21:22:58', '2025-02-08 21:22:58'),
(225, NULL, 'gansgter@o2.pl', 1, '2025-02-08 21:27:22', '2025-02-08 21:27:22'),
(226, NULL, 'suemeryavuz@gmail.com', 1, '2025-02-08 21:31:41', '2025-02-08 21:31:41'),
(227, NULL, 'raffle1219@gmail.com', 1, '2025-02-08 21:37:05', '2025-02-08 21:37:05'),
(228, NULL, 'keithknaggs@hotmail.com', 1, '2025-02-08 21:47:16', '2025-02-08 21:47:16'),
(229, NULL, 'csapp4@gmail.com', 1, '2025-02-08 21:54:09', '2025-02-08 21:54:09'),
(230, NULL, 'sophypeacock@hotmail.co.uk', 1, '2025-02-08 22:01:31', '2025-02-08 22:01:31'),
(231, NULL, 'rbaldizon@gmail.com', 1, '2025-02-08 22:02:40', '2025-02-08 22:02:40'),
(232, NULL, 'kailind3499@gmail.com', 1, '2025-02-08 22:29:13', '2025-02-08 22:29:13'),
(233, NULL, 'brittany.paul@gmail.com', 1, '2025-02-08 22:54:40', '2025-02-08 22:54:40'),
(234, NULL, 'abematsu.02@gmail.com', 1, '2025-02-08 23:11:44', '2025-02-08 23:11:44'),
(235, NULL, 'luise.euli@web.de', 1, '2025-02-08 23:16:43', '2025-02-08 23:16:43'),
(236, NULL, 'eljones08@gmail.com', 1, '2025-02-08 23:23:04', '2025-02-08 23:23:04'),
(237, NULL, 'plumislander332@gmail.com', 1, '2025-02-08 23:24:07', '2025-02-08 23:24:07'),
(238, NULL, 'dingxm@ucla.edu', 1, '2025-02-08 23:38:13', '2025-02-08 23:38:13'),
(239, NULL, 'dlmanuel88@gmail.com', 1, '2025-02-08 23:53:13', '2025-02-08 23:53:13'),
(240, NULL, 'suntan4145@gmail.com', 1, '2025-02-08 23:57:10', '2025-02-08 23:57:10'),
(241, NULL, 'jayga@highhorsestudios.com', 1, '2025-02-08 23:59:03', '2025-02-08 23:59:03'),
(242, NULL, 'disneylion032@gmail.com', 1, '2025-02-09 00:10:18', '2025-02-09 00:10:18'),
(243, NULL, 'jamirenae@hotmail.com', 1, '2025-02-09 00:25:00', '2025-02-09 00:25:00'),
(244, NULL, 'kra21@protonmail.com', 1, '2025-02-09 00:38:48', '2025-02-09 00:38:48'),
(245, NULL, 'shavonda.funderburk@sheratoncharlotte.com', 1, '2025-02-09 00:45:04', '2025-02-09 00:45:04'),
(246, NULL, 'laceyfranco5@icloud.com', 1, '2025-02-09 00:59:12', '2025-02-09 00:59:12'),
(247, NULL, 'hcestaric55@gmail.com', 1, '2025-02-09 01:11:29', '2025-02-09 01:11:29'),
(248, NULL, 'orutorosu05@gmail.com', 1, '2025-02-09 01:22:26', '2025-02-09 01:22:26'),
(249, NULL, 'mcranley@sympatico.ca', 1, '2025-02-09 01:22:57', '2025-02-09 01:22:57'),
(250, NULL, 'jen_a@accesscomm.ca', 1, '2025-02-09 01:37:33', '2025-02-09 01:37:33'),
(251, NULL, 'louisaraujo1995@gmail.com', 1, '2025-02-09 02:33:09', '2025-02-09 02:33:09'),
(252, NULL, 'sakagami@ca2.so-net.ne.jp', 1, '2025-02-09 02:42:36', '2025-02-09 02:42:36'),
(253, NULL, 'matthewlloydrogers@gmail.com', 1, '2025-02-09 04:55:18', '2025-02-09 04:55:18'),
(254, NULL, 'danasttraker@gmail.com', 1, '2025-02-09 06:50:22', '2025-02-09 06:50:22'),
(255, NULL, 'kim.marrion@gmail.com', 1, '2025-02-09 07:12:55', '2025-02-09 07:12:55'),
(256, NULL, 'cripa677@gmail.com', 1, '2025-02-09 07:16:23', '2025-02-09 07:16:23'),
(257, NULL, 'kaleebrink@gmail.com', 1, '2025-02-09 08:03:34', '2025-02-09 08:03:34'),
(258, NULL, '3dogsarf@gmail.com', 1, '2025-02-09 08:44:32', '2025-02-09 08:44:32'),
(259, NULL, 'adamvardy@hotmail.com', 1, '2025-02-09 09:25:04', '2025-02-09 09:25:04'),
(260, NULL, 'faythdyer@hotmail.co.uk', 1, '2025-02-09 09:50:01', '2025-02-09 09:50:01'),
(261, NULL, 'chefrza@nycap.rr.com', 1, '2025-02-09 10:18:20', '2025-02-09 10:18:20'),
(262, NULL, 'pamcinva@gmail.com', 1, '2025-02-09 10:47:07', '2025-02-09 10:47:07'),
(263, NULL, 'cwendttaczak@gmail.com', 1, '2025-02-09 11:00:13', '2025-02-09 11:00:13'),
(264, NULL, 'ttmankottil@gmail.com', 1, '2025-02-09 11:46:12', '2025-02-09 11:46:12'),
(265, NULL, 'mustaqimshoppingemail@gmail.com', 1, '2025-02-09 12:13:32', '2025-02-09 12:13:32'),
(266, NULL, 'luis.guilherme@dotworldtour.pt', 1, '2025-02-09 12:25:27', '2025-02-09 12:25:27'),
(267, NULL, 'sikoravernon@gmail.com', 1, '2025-02-09 12:45:09', '2025-02-09 12:45:09'),
(268, NULL, 'jvoisine1@cox.net', 1, '2025-02-09 12:47:14', '2025-02-09 12:47:14'),
(269, NULL, 'jssullenstrucking@windstream.net', 1, '2025-02-09 12:47:41', '2025-02-09 12:47:41'),
(270, NULL, 'browncricket00@gmail.com', 1, '2025-02-09 12:51:07', '2025-02-09 12:51:07'),
(271, NULL, 'tswloveless@gmail.com', 1, '2025-02-09 12:53:42', '2025-02-09 12:53:42'),
(272, NULL, 'kelli825@hotmail.com', 1, '2025-02-09 13:08:13', '2025-02-09 13:08:13'),
(273, NULL, 'charlotteamcox@hotmail.co.uk', 1, '2025-02-09 13:27:24', '2025-02-09 13:27:24'),
(274, NULL, 'beccavickers07@gmail.com', 1, '2025-02-09 13:55:37', '2025-02-09 13:55:37'),
(275, NULL, 'bucci.alexa1@gmail.com', 1, '2025-02-09 13:56:13', '2025-02-09 13:56:13'),
(276, NULL, 'klineal@upmc.edu', 1, '2025-02-09 13:57:08', '2025-02-09 13:57:08'),
(277, NULL, 'devynsosa@icloud.com', 1, '2025-02-09 13:58:13', '2025-02-09 13:58:13'),
(278, NULL, 'carolinegraham@hotmail.co.uk', 1, '2025-02-09 14:00:32', '2025-02-09 14:00:32'),
(279, NULL, 'jasmin.holler032@gmail.com', 1, '2025-02-09 14:06:06', '2025-02-09 14:06:06'),
(280, NULL, 'sikoravernon@hotmail.com', 1, '2025-02-09 14:12:05', '2025-02-09 14:12:05'),
(281, NULL, 'mollie.renfrow@gmail.com', 1, '2025-02-09 14:21:12', '2025-02-09 14:21:12'),
(282, NULL, 'mike.woodford@talktalk.net', 1, '2025-02-09 14:43:56', '2025-02-09 14:43:56'),
(283, NULL, 'twincityproduce@gmail.com', 1, '2025-02-09 14:45:41', '2025-02-09 14:45:41'),
(284, NULL, 't.kipka@web.de', 1, '2025-02-09 15:03:20', '2025-02-09 15:03:20'),
(285, NULL, 'shauna_smyth@hotmail.co.uk', 1, '2025-02-09 15:29:03', '2025-02-09 15:29:03'),
(286, NULL, 'ming96251@gmail.com', 1, '2025-02-09 16:03:51', '2025-02-09 16:03:51'),
(287, NULL, 'ruthjones2704@gmail.com', 1, '2025-02-09 16:53:05', '2025-02-09 16:53:05'),
(288, NULL, 'sdbaldwin68@icloud.com', 1, '2025-02-09 16:59:36', '2025-02-09 16:59:36'),
(289, NULL, 'melissa.urquhart.mu@googlemail.com', 1, '2025-02-09 17:02:30', '2025-02-09 17:02:30'),
(290, NULL, 'vincent.charlton65@gmail.com', 1, '2025-02-09 17:06:06', '2025-02-09 17:06:06'),
(291, NULL, 'supasgon@gmail.com', 1, '2025-02-09 17:10:04', '2025-02-09 17:10:04'),
(292, NULL, 'jametriamays@gmail.com', 1, '2025-02-09 17:12:17', '2025-02-09 17:12:17'),
(293, NULL, 'northlad84@gmail.com', 1, '2025-02-09 17:14:03', '2025-02-09 17:14:03'),
(294, NULL, 'virtuallyjules@gmail.com', 1, '2025-02-09 17:16:52', '2025-02-09 17:16:52'),
(295, NULL, 'kathrin.weiler1988@gmail.com', 1, '2025-02-09 17:20:18', '2025-02-09 17:20:18'),
(296, NULL, 'lamerouani@gmail.com', 1, '2025-02-09 17:33:32', '2025-02-09 17:33:32'),
(297, NULL, 'scu271281@gmail.com', 1, '2025-02-09 17:51:11', '2025-02-09 17:51:11'),
(298, NULL, 'eliasrossler76@gmail.com', 1, '2025-02-09 18:01:42', '2025-02-09 18:01:42'),
(299, NULL, 'jtkidder22@gmail.com', 1, '2025-02-09 18:14:15', '2025-02-09 18:14:15'),
(300, NULL, 'oliverbibic22@gmail.com', 1, '2025-02-09 18:19:03', '2025-02-09 18:19:03'),
(301, NULL, 'annagronen@web.de', 1, '2025-02-09 18:33:12', '2025-02-09 18:33:12'),
(302, NULL, 'emma_neary@hotmail.co.uk', 1, '2025-02-09 18:40:57', '2025-02-09 18:40:57'),
(303, NULL, 'samira.hussaini@gmail.com', 1, '2025-02-09 18:44:11', '2025-02-09 18:44:11'),
(304, NULL, 'joshuaharrison812@gmail.com', 1, '2025-02-09 18:55:06', '2025-02-09 18:55:06'),
(305, NULL, 'aaronford@windstream.net', 1, '2025-02-09 19:04:26', '2025-02-09 19:04:26'),
(306, NULL, 'izzizigan@gmail.com', 1, '2025-02-09 19:13:35', '2025-02-09 19:13:35'),
(307, NULL, 'demassez.elysabeth@gmail.com', 1, '2025-02-09 19:32:55', '2025-02-09 19:32:55'),
(308, NULL, 'helenj25@frontier.com', 1, '2025-02-09 19:37:58', '2025-02-09 19:37:58'),
(309, NULL, 'victoriacaston@hotmail.com', 1, '2025-02-09 19:43:03', '2025-02-09 19:43:03'),
(310, NULL, 'joyyazel@gmail.com', 1, '2025-02-09 19:45:16', '2025-02-09 19:45:16'),
(311, NULL, 'delfi.betancourt@gmail.com', 1, '2025-02-09 19:51:04', '2025-02-09 19:51:04'),
(312, NULL, 'crenee5066@gmail.com', 1, '2025-02-09 19:54:06', '2025-02-09 19:54:06'),
(313, NULL, 'gonzaleslr@gmail.com', 1, '2025-02-09 19:55:10', '2025-02-09 19:55:10'),
(314, NULL, 'jessica.v.oliveira@outlook.com', 1, '2025-02-09 20:51:53', '2025-02-09 20:51:53'),
(315, NULL, 'minna.zumsteg@gmail.com', 1, '2025-02-09 20:53:24', '2025-02-09 20:53:24'),
(316, NULL, 'zeljkog36@gmail.com', 1, '2025-02-09 21:01:39', '2025-02-09 21:01:39'),
(317, NULL, 'johnjobling153@hotmail.com', 1, '2025-02-09 21:03:21', '2025-02-09 21:03:21'),
(318, NULL, 'mnmettee@gmail.com', 1, '2025-02-09 21:04:15', '2025-02-09 21:04:15'),
(319, NULL, 'janemoxam@gmail.com', 1, '2025-02-09 21:11:21', '2025-02-09 21:11:21'),
(320, NULL, 'chukundur@gmail.com', 1, '2025-02-09 21:16:11', '2025-02-09 21:16:11'),
(321, NULL, 'slbigshot@msn.com', 1, '2025-02-09 21:24:15', '2025-02-09 21:24:15'),
(322, NULL, 'bhebert01@gmail.com', 1, '2025-02-09 21:31:13', '2025-02-09 21:31:13'),
(323, NULL, 'ema.klemmen2@gmail.com', 1, '2025-02-09 21:39:15', '2025-02-09 21:39:15'),
(324, NULL, 'jdshockeymom@comcast.net', 1, '2025-02-09 21:45:12', '2025-02-09 21:45:12'),
(325, NULL, 'tateedens12@icloud.com', 1, '2025-02-09 21:52:10', '2025-02-09 21:52:10'),
(326, NULL, 'emichellelewis@gmail.com', 1, '2025-02-09 22:05:58', '2025-02-09 22:05:58'),
(327, NULL, 'benchontos@gmail.com', 1, '2025-02-09 22:09:05', '2025-02-09 22:09:05'),
(328, NULL, 'ij@hamannclan.net', 1, '2025-02-09 22:37:15', '2025-02-09 22:37:15'),
(329, NULL, 'kmiller@millereng.net', 1, '2025-02-09 22:50:32', '2025-02-09 22:50:32'),
(330, NULL, 'mzzfish@comcast.net', 1, '2025-02-09 23:21:46', '2025-02-09 23:21:46'),
(331, NULL, 'uofmfannnc@hotmail.com', 1, '2025-02-10 00:00:08', '2025-02-10 00:00:08'),
(332, NULL, 'nicole.yerrid@icloud.com', 1, '2025-02-10 00:01:41', '2025-02-10 00:01:41'),
(333, NULL, 'mccarthycharice@gmail.com', 1, '2025-02-10 00:07:11', '2025-02-10 00:07:11'),
(334, NULL, 'anna.bezkrovnaja@gmail.com', 1, '2025-02-10 00:35:08', '2025-02-10 00:35:08'),
(335, NULL, 'howardjones50@hotmail.com', 1, '2025-02-10 00:36:06', '2025-02-10 00:36:06'),
(336, NULL, 'amethyst888@comcast.net', 1, '2025-02-10 00:38:48', '2025-02-10 00:38:48'),
(337, NULL, 'landjdito@comcast.net', 1, '2025-02-10 00:39:08', '2025-02-10 00:39:08'),
(338, NULL, 'rwjcconnell@gmail.com', 1, '2025-02-10 01:10:19', '2025-02-10 01:10:19'),
(339, NULL, 'shrinkingwithpink@gmail.com', 1, '2025-02-10 01:11:04', '2025-02-10 01:11:04'),
(340, NULL, 'sinpuri1227@gmail.com', 1, '2025-02-10 01:41:25', '2025-02-10 01:41:25'),
(341, NULL, 'csoyyocarito@gmail.com', 1, '2025-02-10 01:45:34', '2025-02-10 01:45:34'),
(342, NULL, 'stephen.newman@telus.net', 1, '2025-02-10 02:31:54', '2025-02-10 02:31:54'),
(343, NULL, 'garoncontreras@comcast.net', 1, '2025-02-10 02:45:52', '2025-02-10 02:45:52'),
(344, NULL, 'robinsontornando@gmail.com', 1, '2025-02-10 02:48:37', '2025-02-10 02:48:37'),
(345, NULL, 'duhnassoc42@gmail.com', 1, '2025-02-10 02:52:09', '2025-02-10 02:52:09'),
(346, NULL, 'dpolito49@gmail.com', 1, '2025-02-10 03:33:49', '2025-02-10 03:33:49'),
(347, NULL, 'charles940@gmail.com', 1, '2025-02-10 03:45:06', '2025-02-10 03:45:06'),
(348, NULL, 'carlamorgan08+file@gmail.com', 1, '2025-02-10 03:51:18', '2025-02-10 03:51:18'),
(349, NULL, 'delbice@webbworks.com', 1, '2025-02-10 04:02:13', '2025-02-10 04:02:13'),
(350, NULL, 'carlamorgan08@gmail.com', 1, '2025-02-10 04:12:28', '2025-02-10 04:12:28'),
(351, NULL, 'hrz360@gmail.com', 1, '2025-02-10 04:14:45', '2025-02-10 04:14:45'),
(352, NULL, 'darin.dalbom@npval.com', 1, '2025-02-10 04:30:18', '2025-02-10 04:30:18'),
(353, NULL, 'benjamin.hauptmann@web.de', 1, '2025-02-10 05:37:05', '2025-02-10 05:37:05'),
(354, NULL, 'yusun.song7921@gmail.com', 1, '2025-02-10 05:53:07', '2025-02-10 05:53:07'),
(355, NULL, 'marief.locke@gmail.com', 1, '2025-02-10 06:32:38', '2025-02-10 06:32:38'),
(356, NULL, 'yellowbird007@msn.com', 1, '2025-02-10 06:34:01', '2025-02-10 06:34:01'),
(357, NULL, 'andreas.mueller@skam-design.de', 1, '2025-02-10 07:17:24', '2025-02-10 07:17:24'),
(358, NULL, 'lenaboldt17@gmail.com', 1, '2025-02-10 08:08:56', '2025-02-10 08:08:56'),
(359, NULL, 'yitong.phou@gmail.com', 1, '2025-02-10 08:26:40', '2025-02-10 08:26:40'),
(360, NULL, 'kristin.witthaus@t-online.de', 1, '2025-02-10 08:52:18', '2025-02-10 08:52:18'),
(361, NULL, 'andreas.tratnig@gmx.at', 1, '2025-02-10 09:18:31', '2025-02-10 09:18:31'),
(362, NULL, 'support@vdsina.com', 1, '2025-02-10 09:43:51', '2025-02-10 09:43:51'),
(363, NULL, 'emanuele.giombelli@corno.eu', 1, '2025-02-10 10:41:23', '2025-02-10 10:41:23'),
(364, NULL, 'rachaelrhea.smith@gmail.com', 1, '2025-02-10 10:51:07', '2025-02-10 10:51:07'),
(365, NULL, 'heinbouwens@gmail.com', 1, '2025-02-10 11:11:08', '2025-02-10 11:11:08'),
(366, NULL, 'pizza101911@gmail.com', 1, '2025-02-10 11:18:06', '2025-02-10 11:18:06'),
(367, NULL, 'jim.greatroofing@gmail.com', 1, '2025-02-10 11:52:10', '2025-02-10 11:52:10'),
(368, NULL, 'maubin8@gmail.com', 1, '2025-02-10 12:07:14', '2025-02-10 12:07:14'),
(369, NULL, 'jimmyrosser+connect@gmail.com', 1, '2025-02-10 12:07:24', '2025-02-10 12:07:24'),
(370, NULL, 'biktu@gmx.de', 1, '2025-02-10 12:21:05', '2025-02-10 12:21:05'),
(371, NULL, 'robertocmyers@gmail.com', 1, '2025-02-10 12:31:11', '2025-02-10 12:31:11'),
(372, NULL, 'dodgydavid@greenroute.co.uk', 1, '2025-02-10 12:40:56', '2025-02-10 12:40:56'),
(373, NULL, 'sapape6617@gmail.com', 1, '2025-02-10 12:47:35', '2025-02-10 12:47:35'),
(374, NULL, 'ulyssesdpt@gmail.com', 1, '2025-02-10 13:09:12', '2025-02-10 13:09:12'),
(375, NULL, 'war72fighter@gmail.com', 1, '2025-02-10 13:27:50', '2025-02-10 13:27:50'),
(376, NULL, 'jeff.thompson@averycountync.gov', 1, '2025-02-10 13:34:32', '2025-02-10 13:34:32'),
(377, NULL, 'jenniferdesai71@gmail.com', 1, '2025-02-10 13:46:10', '2025-02-10 13:46:10'),
(378, NULL, 'f.schlaich@gmx.de', 1, '2025-02-10 14:00:30', '2025-02-10 14:00:30'),
(379, NULL, 'coupduhasard@gmail.com', 1, '2025-02-10 14:04:31', '2025-02-10 14:04:31'),
(380, NULL, 'tkcass@gmail.com', 1, '2025-02-10 14:25:13', '2025-02-10 14:25:13'),
(381, NULL, 'k-mueller-mainz@t-online.de', 1, '2025-02-10 14:52:06', '2025-02-10 14:52:06'),
(382, NULL, 'opera2go@hotmail.com', 1, '2025-02-10 14:54:30', '2025-02-10 14:54:30'),
(383, NULL, 'bekkicraft@live.co.uk', 1, '2025-02-10 15:12:54', '2025-02-10 15:12:54'),
(384, NULL, 'jodi@onelearningcommunity.com', 1, '2025-02-10 15:20:05', '2025-02-10 15:20:05'),
(385, NULL, 'peter-elies@t-online.de', 1, '2025-02-10 15:38:13', '2025-02-10 15:38:13'),
(386, NULL, 'flpcnanny@gmail.com', 1, '2025-02-10 15:38:35', '2025-02-10 15:38:35'),
(387, NULL, 'mwbenoist@gmail.com', 1, '2025-02-10 15:46:05', '2025-02-10 15:46:05'),
(388, NULL, 'orvigon@gmail.com', 1, '2025-02-10 15:57:26', '2025-02-10 15:57:26'),
(389, NULL, 'mrosey16@hotmail.com', 1, '2025-02-10 16:00:39', '2025-02-10 16:00:39'),
(390, NULL, 'kevinlamair@gmail.com', 1, '2025-02-10 16:24:11', '2025-02-10 16:24:11'),
(391, NULL, 'shane.richmond@lfishman.com', 1, '2025-02-10 16:25:10', '2025-02-10 16:25:10'),
(392, NULL, 'kerdellthomas04@gmail.com', 1, '2025-02-10 16:27:07', '2025-02-10 16:27:07'),
(393, NULL, 'joseph.zaccardo@gmail.com', 1, '2025-02-10 16:32:06', '2025-02-10 16:32:06'),
(394, NULL, 'smludwig@elbeco.com', 1, '2025-02-10 16:37:04', '2025-02-10 16:37:04'),
(395, NULL, 'jesscoxx@hotmail.com', 1, '2025-02-10 16:37:18', '2025-02-10 16:37:18'),
(396, NULL, 'busyredhed@gmail.com', 1, '2025-02-10 16:42:43', '2025-02-10 16:42:43'),
(397, NULL, 'lisnamarselina.v@gmail.com', 1, '2025-02-10 16:47:26', '2025-02-10 16:47:26'),
(398, NULL, 'gareth.mills@mail.com', 1, '2025-02-10 17:04:23', '2025-02-10 17:04:23'),
(399, NULL, 'banana.fraa@gmail.com', 1, '2025-02-10 17:10:43', '2025-02-10 17:10:43'),
(400, NULL, 'anissa.sanchez@bartonmalow.com', 1, '2025-02-10 17:12:14', '2025-02-10 17:12:14'),
(401, NULL, 'ianschoenberger2009@gmail.com', 1, '2025-02-10 17:15:09', '2025-02-10 17:15:09'),
(402, NULL, 'huertagg@gmail.com', 1, '2025-02-10 17:18:16', '2025-02-10 17:18:16'),
(403, NULL, 'mitolo@gmail.com', 1, '2025-02-10 17:20:06', '2025-02-10 17:20:06'),
(404, NULL, 'adam.zeltwanger@riverviewllp.com', 1, '2025-02-10 17:22:24', '2025-02-10 17:22:24'),
(405, NULL, 'barry.eby@electrolux.com', 1, '2025-02-10 17:27:24', '2025-02-10 17:27:24'),
(406, NULL, 'red21461@netzero.net', 1, '2025-02-10 17:37:12', '2025-02-10 17:37:12'),
(407, NULL, 'mike.smith@mcness.com', 1, '2025-02-10 17:40:07', '2025-02-10 17:40:07'),
(408, NULL, 'pierre@piloncommunications.com', 1, '2025-02-10 17:41:30', '2025-02-10 17:41:30'),
(409, NULL, 'stoimenov.dimitar@gmail.com', 1, '2025-02-10 17:53:20', '2025-02-10 17:53:20'),
(410, NULL, 'lifeisgr8t90@gmail.com', 1, '2025-02-10 17:53:23', '2025-02-10 17:53:23'),
(411, NULL, 'leenapatel@hotmail.com', 1, '2025-02-10 18:04:28', '2025-02-10 18:04:28'),
(412, NULL, 'cls0808@gmail.com', 1, '2025-02-10 18:05:30', '2025-02-10 18:05:30'),
(413, NULL, 'elsydney@hotmail.com', 1, '2025-02-10 18:25:35', '2025-02-10 18:25:35'),
(414, NULL, 'craigrune@hotmail.co.uk', 1, '2025-02-10 18:39:20', '2025-02-10 18:39:20'),
(415, NULL, 'cjswogh1008@gmail.com', 1, '2025-02-10 18:44:11', '2025-02-10 18:44:11'),
(416, NULL, 'marcoeasyconcrete@gmail.com', 1, '2025-02-10 18:57:08', '2025-02-10 18:57:08'),
(417, NULL, 'terri.deavor@volkert.com', 1, '2025-02-10 18:57:26', '2025-02-10 18:57:26'),
(418, NULL, 'angie.boyd@leister.com', 1, '2025-02-10 19:00:19', '2025-02-10 19:00:19'),
(419, NULL, 'remyjhall@gmail.com', 1, '2025-02-10 19:01:07', '2025-02-10 19:01:07'),
(420, NULL, 'christina.jaeger@wmeng.com', 1, '2025-02-10 19:06:27', '2025-02-10 19:06:27'),
(421, NULL, 'leidyleidy@gmail.com', 1, '2025-02-10 19:06:49', '2025-02-10 19:06:49'),
(422, NULL, 'sawhalen@gmail.com', 1, '2025-02-10 19:37:31', '2025-02-10 19:37:31'),
(423, NULL, 'j.whurr@btopenworld.com', 1, '2025-02-10 19:38:50', '2025-02-10 19:38:50'),
(424, NULL, 'juanjmunoz5005@gmail.com', 1, '2025-02-10 19:39:43', '2025-02-10 19:39:43'),
(425, NULL, 'pam.batta69@gmail.com', 1, '2025-02-10 19:48:04', '2025-02-10 19:48:04'),
(426, NULL, 'crawford.r.w2008@zoominternet.net', 1, '2025-02-10 19:50:42', '2025-02-10 19:50:42'),
(427, NULL, 'sheam_p@hotmail.com', 1, '2025-02-10 19:51:37', '2025-02-10 19:51:37'),
(428, NULL, 'jenebatarawally72@gmail.com', 1, '2025-02-10 19:53:30', '2025-02-10 19:53:30'),
(429, NULL, 'joecabral@comcast.net', 1, '2025-02-10 19:54:05', '2025-02-10 19:54:05'),
(430, NULL, 'aaronmobarak@gmail.com', 1, '2025-02-10 20:04:08', '2025-02-10 20:04:08'),
(431, NULL, 'lpackard@zoominternet.net', 1, '2025-02-10 20:04:10', '2025-02-10 20:04:10'),
(432, NULL, 'wolfmanix@gmail.com', 1, '2025-02-10 20:18:03', '2025-02-10 20:18:03'),
(433, NULL, 'jjermyn@dlfpickseed.com', 1, '2025-02-10 20:23:46', '2025-02-10 20:23:46'),
(434, NULL, 'ymanmanning@gmail.com', 1, '2025-02-10 20:28:55', '2025-02-10 20:28:55'),
(435, NULL, 'akeith@devilmountainnursery.com', 1, '2025-02-10 20:31:41', '2025-02-10 20:31:41'),
(436, NULL, 'whernandez@gmail.com', 1, '2025-02-10 20:41:19', '2025-02-10 20:41:19'),
(437, NULL, 'becky@devilmountainnursery.com', 1, '2025-02-10 20:42:07', '2025-02-10 20:42:07'),
(438, NULL, 'greatid.u@gmail.com', 1, '2025-02-10 20:42:08', '2025-02-10 20:42:08'),
(439, NULL, 'nepurcell@charter.net', 1, '2025-02-10 20:42:36', '2025-02-10 20:42:36'),
(440, NULL, 'rosann.summerlin@savistarcm.com', 1, '2025-02-10 20:43:06', '2025-02-10 20:43:06'),
(441, NULL, 'ozzztek765@gmail.com', 1, '2025-02-10 21:20:13', '2025-02-10 21:20:13'),
(442, NULL, 'alhajibahm@gmail.com', 1, '2025-02-10 21:40:12', '2025-02-10 21:40:12'),
(443, NULL, 'alexander701415@gmail.com', 1, '2025-02-10 21:44:26', '2025-02-10 21:44:26'),
(444, NULL, 'jaegereugen1985@gmail.com', 1, '2025-02-10 21:51:20', '2025-02-10 21:51:20'),
(445, NULL, 'jimmyrosser+osffer@gmail.com', 1, '2025-02-10 21:54:46', '2025-02-10 21:54:46'),
(446, NULL, 'mongkonjat@gmail.com', 1, '2025-02-10 22:33:18', '2025-02-10 22:33:18'),
(447, NULL, 'curleyq74@gmail.com', 1, '2025-02-10 22:34:32', '2025-02-10 22:34:32'),
(448, NULL, 'jaime850.jz@gmail.com', 1, '2025-02-10 22:44:09', '2025-02-10 22:44:09'),
(449, NULL, 'natalie808love@gmail.com', 1, '2025-02-10 22:48:11', '2025-02-10 22:48:11'),
(450, NULL, 'martinpuhr19@gmail.com', 1, '2025-02-10 22:49:34', '2025-02-10 22:49:34'),
(451, NULL, 'jimmyrosser+fill@gmail.com', 1, '2025-02-10 23:01:53', '2025-02-10 23:01:53'),
(452, NULL, 'tboghos@hotmail.com', 1, '2025-02-10 23:17:12', '2025-02-10 23:17:12'),
(453, NULL, 'andreaebatty@gmail.com', 1, '2025-02-10 23:20:08', '2025-02-10 23:20:08'),
(454, NULL, 'iona.moncrieff@gmail.com', 1, '2025-02-10 23:21:57', '2025-02-10 23:21:57'),
(455, NULL, 'audiovideoworkshop@gmail.com', 1, '2025-02-10 23:23:49', '2025-02-10 23:23:49'),
(456, NULL, 'thepalmettopalace@gmail.com', 1, '2025-02-10 23:28:35', '2025-02-10 23:28:35'),
(457, NULL, 'lkilbywhelan88@gmail.com', 1, '2025-02-10 23:31:42', '2025-02-10 23:31:42'),
(458, NULL, 'chloeregenerated@gmail.com', 1, '2025-02-10 23:35:29', '2025-02-10 23:35:29'),
(459, NULL, 'kara_wade@hotmail.com', 1, '2025-02-10 23:35:51', '2025-02-10 23:35:51'),
(460, NULL, 'lisajacksondavis@gmail.com', 1, '2025-02-10 23:54:24', '2025-02-10 23:54:24'),
(461, NULL, 'pwsanders78@gmail.com', 1, '2025-02-11 00:08:10', '2025-02-11 00:08:10'),
(462, NULL, 'halfhull@gmail.com', 1, '2025-02-11 00:18:23', '2025-02-11 00:18:23'),
(463, NULL, 'nellygalicia00@gmail.com', 1, '2025-02-11 00:21:27', '2025-02-11 00:21:27'),
(464, NULL, 'jaidabug37@gmail.com', 1, '2025-02-11 00:21:41', '2025-02-11 00:21:41'),
(465, NULL, 'g.wohlb@googlemail.com', 1, '2025-02-11 00:26:13', '2025-02-11 00:26:13'),
(466, NULL, 'theschechters@tampabay.rr.com', 1, '2025-02-11 00:26:38', '2025-02-11 00:26:38'),
(467, NULL, 'kathynathan@me.com', 1, '2025-02-11 00:33:02', '2025-02-11 00:33:02'),
(468, NULL, 'mtapia547+support@gmail.com', 1, '2025-02-11 00:34:05', '2025-02-11 00:34:05'),
(469, NULL, 'damienmayo@gmail.com', 1, '2025-02-11 01:01:59', '2025-02-11 01:01:59'),
(470, NULL, 'julie.k.smith64@gmail.com', 1, '2025-02-11 01:19:58', '2025-02-11 01:19:58'),
(471, NULL, 'rmolleran@gmail.com', 1, '2025-02-11 01:22:55', '2025-02-11 01:22:55'),
(472, NULL, 'smithey253@gmail.com', 1, '2025-02-11 01:28:12', '2025-02-11 01:28:12'),
(473, NULL, 'kristeneworkman@gmail.com', 1, '2025-02-11 01:51:39', '2025-02-11 01:51:39'),
(474, NULL, 'rick.rangel05@gmail.com', 1, '2025-02-11 01:53:17', '2025-02-11 01:53:17'),
(475, NULL, 'lbroos@telus.net', 1, '2025-02-11 01:55:28', '2025-02-11 01:55:28'),
(476, NULL, 'tocher.michelle@gmail.com', 1, '2025-02-11 01:56:19', '2025-02-11 01:56:19'),
(477, NULL, 'sean.gray@grayelectric.ca', 1, '2025-02-11 01:57:28', '2025-02-11 01:57:28'),
(478, NULL, 'lszilagyi@gpwealth.ca', 1, '2025-02-11 02:05:20', '2025-02-11 02:05:20'),
(479, NULL, 'philjeannie@gmail.com', 1, '2025-02-11 02:19:30', '2025-02-11 02:19:30'),
(480, NULL, 'serkan.eskinazi@gmail.com', 1, '2025-02-11 02:21:25', '2025-02-11 02:21:25'),
(481, NULL, 'hamiltoncam37@gmail.com', 1, '2025-02-11 02:31:33', '2025-02-11 02:31:33'),
(482, NULL, 'info@immo-vio.de', 1, '2025-02-11 02:37:47', '2025-02-11 02:37:47'),
(483, NULL, 'davidboehm09@gmail.com', 1, '2025-02-11 02:38:17', '2025-02-11 02:38:17'),
(484, NULL, 'kimberlyshae.s@gmail.com', 1, '2025-02-11 03:09:33', '2025-02-11 03:09:33'),
(485, NULL, 'helenc913+osffer@gmail.com', 1, '2025-02-11 03:14:48', '2025-02-11 03:14:48'),
(486, NULL, 'dfw787@gmail.com', 1, '2025-02-11 03:33:01', '2025-02-11 03:33:01'),
(487, NULL, 'kimberlyb9396@gmail.com', 1, '2025-02-11 03:34:52', '2025-02-11 03:34:52'),
(488, NULL, 'bohemient@hotmail.com', 1, '2025-02-11 03:44:33', '2025-02-11 03:44:33'),
(489, NULL, 'missywabash@gmail.com', 1, '2025-02-11 03:46:29', '2025-02-11 03:46:29'),
(490, NULL, 'maureenchilinskas@gmail.com', 1, '2025-02-11 03:48:38', '2025-02-11 03:48:38'),
(491, NULL, 'kanone2002@gmail.com', 1, '2025-02-11 04:20:09', '2025-02-11 04:20:09'),
(492, NULL, 'cassani.luca@gmail.com', 1, '2025-02-11 06:03:03', '2025-02-11 06:03:03'),
(493, NULL, 'dlhawley100@gmail.com', 1, '2025-02-11 06:18:20', '2025-02-11 06:18:20'),
(494, NULL, 'sentrydown1963@gmail.com', 1, '2025-02-11 06:59:32', '2025-02-11 06:59:32'),
(495, NULL, 'dendan16@gmail.com', 1, '2025-02-11 07:21:05', '2025-02-11 07:21:05'),
(496, NULL, 'tylerewhite@gmail.com', 1, '2025-02-11 07:25:21', '2025-02-11 07:25:21'),
(497, NULL, 'jimmyrosser+ren@gmail.com', 1, '2025-02-11 07:32:31', '2025-02-11 07:32:31'),
(498, NULL, 'mtapia547+supp@gmail.com', 1, '2025-02-11 07:47:54', '2025-02-11 07:47:54'),
(499, NULL, 'kdmwave@gmail.com', 1, '2025-02-11 08:07:48', '2025-02-11 08:07:48'),
(500, NULL, 'ghiya.abouzeid@lau.edu', 1, '2025-02-11 08:13:13', '2025-02-11 08:13:13'),
(501, NULL, 'coletownsend5th@gmail.com', 1, '2025-02-11 08:14:09', '2025-02-11 08:14:09'),
(502, NULL, 'jonathanpomerantz@gmail.com', 1, '2025-02-11 08:40:14', '2025-02-11 08:40:14'),
(503, NULL, 'julia@kjtaxandfinancial.com', 1, '2025-02-11 08:46:47', '2025-02-11 08:46:47'),
(504, NULL, 'easterlingt2001@gmail.com', 1, '2025-02-11 08:51:28', '2025-02-11 08:51:28'),
(505, NULL, 'beasley.nick@gmail.com', 1, '2025-02-11 08:54:58', '2025-02-11 08:54:58'),
(506, NULL, 'svelpk@gmail.com', 1, '2025-02-11 09:00:12', '2025-02-11 09:00:12'),
(507, NULL, 'superiortileworksllc@gmail.com', 1, '2025-02-11 09:15:19', '2025-02-11 09:15:19'),
(508, NULL, 'ayombetsa@gmail.com', 1, '2025-02-11 09:19:13', '2025-02-11 09:19:13'),
(509, NULL, 'ednakano808@gmail.com', 1, '2025-02-11 09:44:06', '2025-02-11 09:44:06'),
(510, NULL, 'ricardogomez660@gmail.com', 1, '2025-02-11 09:57:09', '2025-02-11 09:57:09'),
(511, NULL, 'edcrowston@gmail.com', 1, '2025-02-11 10:11:24', '2025-02-11 10:11:24'),
(512, NULL, 'tarakotta@gmail.com', 1, '2025-02-11 10:12:07', '2025-02-11 10:12:07'),
(513, NULL, 'intltech77@gmail.com', 1, '2025-02-11 10:12:11', '2025-02-11 10:12:11'),
(514, NULL, 'cbdiehlio@hotmail.com', 1, '2025-02-11 10:41:06', '2025-02-11 10:41:06'),
(515, NULL, 'john.antal@gmail.com', 1, '2025-02-11 10:46:40', '2025-02-11 10:46:40'),
(516, NULL, 'masebedo05@gmail.com', 1, '2025-02-11 10:57:48', '2025-02-11 10:57:48'),
(517, NULL, 'phdeng11@gmail.com', 1, '2025-02-11 11:03:15', '2025-02-11 11:03:15'),
(518, NULL, 'superiortileworksllc+ons@gmail.com', 1, '2025-02-11 11:19:05', '2025-02-11 11:19:05'),
(519, NULL, 'rdalcastello@gmail.com', 1, '2025-02-11 11:31:19', '2025-02-11 11:31:19'),
(520, NULL, 'danlaking@hotmail.co.uk', 1, '2025-02-11 11:36:07', '2025-02-11 11:36:07'),
(521, NULL, 'f4npilot@gmail.com', 1, '2025-02-11 11:44:20', '2025-02-11 11:44:20'),
(522, NULL, 'ctrundle@gmail.com', 1, '2025-02-11 11:58:24', '2025-02-11 11:58:24'),
(523, NULL, 'alejandro.lizardi@pinknoise.es', 1, '2025-02-11 12:17:11', '2025-02-11 12:17:11'),
(524, NULL, 'urchell.krisa@gmail.com', 1, '2025-02-11 12:31:04', '2025-02-11 12:31:04'),
(525, NULL, 'travis@bestthom.com', 1, '2025-02-11 12:34:03', '2025-02-11 12:34:03'),
(526, NULL, 'krisaurchell@gmail.com', 1, '2025-02-11 12:46:18', '2025-02-11 12:46:18'),
(527, NULL, 'jaidizzlp@gmail.com', 1, '2025-02-11 12:54:40', '2025-02-11 12:54:40'),
(528, NULL, 'hbarclay85@gmail.com', 1, '2025-02-11 13:01:09', '2025-02-11 13:01:09'),
(529, NULL, 'alexispriscilla13@gmail.com', 1, '2025-02-11 13:05:43', '2025-02-11 13:05:43'),
(530, NULL, 'hanzdiner@gmail.com', 1, '2025-02-11 13:11:33', '2025-02-11 13:11:33'),
(531, NULL, 'kristen.bentivegna@gmail.com', 1, '2025-02-11 13:32:16', '2025-02-11 13:32:16'),
(532, NULL, 'ninastrullerdix@gmail.com', 1, '2025-02-11 13:33:13', '2025-02-11 13:33:13'),
(533, NULL, 'ramrode34@gmail.com', 1, '2025-02-11 13:51:10', '2025-02-11 13:51:10'),
(534, NULL, 'kaianddavid79@gmail.com', 1, '2025-02-11 13:56:25', '2025-02-11 13:56:25'),
(535, NULL, 'dt9924704@gmail.com', 1, '2025-02-11 14:05:38', '2025-02-11 14:05:38'),
(536, NULL, 'cerimegandavies@gmail.com', 1, '2025-02-11 14:12:20', '2025-02-11 14:12:20'),
(537, NULL, 'dwayneflournoy@gmail.com', 1, '2025-02-11 14:25:28', '2025-02-11 14:25:28'),
(538, NULL, 'info@handy-computerladen.de', 1, '2025-02-11 14:33:44', '2025-02-11 14:33:44'),
(539, NULL, 'devilbaga@msn.com', 1, '2025-02-11 14:44:19', '2025-02-11 14:44:19'),
(540, NULL, 'leaandmack@gmail.com', 1, '2025-02-11 14:54:10', '2025-02-11 14:54:10'),
(541, NULL, 'lindsey.frasqueteyre@gmail.com', 1, '2025-02-11 14:58:09', '2025-02-11 14:58:09'),
(542, NULL, 'jimmyrosser+lak@gmail.com', 1, '2025-02-11 15:12:34', '2025-02-11 15:12:34'),
(543, NULL, 'donledford@charter.net', 1, '2025-02-11 15:13:26', '2025-02-11 15:13:26'),
(544, NULL, 'bzicheck@gmail.com', 1, '2025-02-11 15:17:01', '2025-02-11 15:17:01'),
(545, NULL, 'tisha7709@gmail.com', 1, '2025-02-11 15:40:12', '2025-02-11 15:40:12'),
(546, NULL, 'nikkiinthesun@hotmail.com', 1, '2025-02-11 15:43:51', '2025-02-11 15:43:51'),
(547, NULL, 'office@tenbellsnyc.com', 1, '2025-02-11 15:56:22', '2025-02-11 15:56:22'),
(548, NULL, 'ljmclaughlin88@gmail.com', 1, '2025-02-11 16:00:04', '2025-02-11 16:00:04'),
(549, NULL, 'cantolini@urbanscience.com', 1, '2025-02-11 16:36:55', '2025-02-11 16:36:55'),
(550, NULL, 'wwinn@benchmarkintl.com', 1, '2025-02-11 17:09:59', '2025-02-11 17:09:59'),
(551, NULL, 'lxx331@gmail.com', 1, '2025-02-11 17:10:54', '2025-02-11 17:10:54'),
(552, NULL, 'chauncey1899@gmail.com', 1, '2025-02-11 17:20:23', '2025-02-11 17:20:23'),
(553, NULL, 'mojbro76@hotmail.com', 1, '2025-02-11 17:22:14', '2025-02-11 17:22:14'),
(554, NULL, 'pcyclamen@hotmail.co.uk', 1, '2025-02-11 17:46:19', '2025-02-11 17:46:19'),
(555, NULL, 'brianveloz209@gmail.com', 1, '2025-02-11 17:49:37', '2025-02-11 17:49:37'),
(556, NULL, 'jaymuise1123@gmail.com', 1, '2025-02-11 17:51:19', '2025-02-11 17:51:19'),
(557, NULL, 'lauren.dye@ahss.org', 1, '2025-02-11 17:58:25', '2025-02-11 17:58:25'),
(558, NULL, 'yche.nci@gmail.com', 1, '2025-02-11 18:04:15', '2025-02-11 18:04:15'),
(559, NULL, 'anjaloas@gmx.net', 1, '2025-02-11 18:05:47', '2025-02-11 18:05:47'),
(560, NULL, 'federico.belli@hitachirail.com', 1, '2025-02-11 18:16:52', '2025-02-11 18:16:52'),
(561, NULL, 'ann@easybench.org', 1, '2025-02-11 18:25:16', '2025-02-11 18:25:16'),
(562, NULL, 'petvalencia@gmail.com', 1, '2025-02-11 18:25:50', '2025-02-11 18:25:50'),
(563, NULL, 'beth.p.avery@gmail.com', 1, '2025-02-11 18:28:48', '2025-02-11 18:28:48'),
(564, NULL, 'mrmiguel805555@gmail.com', 1, '2025-02-11 18:34:09', '2025-02-11 18:34:09'),
(565, NULL, 'mpukansky001@gvtc.com', 1, '2025-02-11 18:34:42', '2025-02-11 18:34:42'),
(566, NULL, 'gabrielabenton01@gmail.com', 1, '2025-02-11 18:37:34', '2025-02-11 18:37:34'),
(567, NULL, 'rcouch1234@gmail.com', 1, '2025-02-11 18:38:04', '2025-02-11 18:38:04'),
(568, NULL, 'gjustinfong@gmail.com', 1, '2025-02-11 18:42:03', '2025-02-11 18:42:03'),
(569, NULL, 'armen.topchyan@castandcrew.com', 1, '2025-02-11 19:21:12', '2025-02-11 19:21:12'),
(570, NULL, 'francis@dblcigars.com', 1, '2025-02-11 19:35:07', '2025-02-11 19:35:07'),
(571, NULL, 'ramon.deguinion@gmail.com', 1, '2025-02-11 19:36:59', '2025-02-11 19:36:59'),
(572, NULL, 'edupare@uol.com', 1, '2025-02-11 19:37:33', '2025-02-11 19:37:33'),
(573, NULL, 'angela.barnes@castandcrew.com', 1, '2025-02-11 19:39:25', '2025-02-11 19:39:25'),
(574, NULL, 'jrgroff@gmail.com', 1, '2025-02-11 19:40:18', '2025-02-11 19:40:18'),
(575, NULL, 'obgcc@hotmail.com', 1, '2025-02-11 19:44:42', '2025-02-11 19:44:42'),
(576, NULL, 'tracey.glasgow1@btinternet.com', 1, '2025-02-11 19:53:07', '2025-02-11 19:53:07'),
(577, NULL, 'beccapriddy@gmail.com', 1, '2025-02-11 19:55:27', '2025-02-11 19:55:27'),
(578, NULL, 'joshuamstewart@gmail.com', 1, '2025-02-11 20:00:18', '2025-02-11 20:00:18'),
(579, NULL, 'gctres@deltasigmachi.org', 1, '2025-02-11 20:07:23', '2025-02-11 20:07:23'),
(580, NULL, 'sami.qayyum@gmail.com', 1, '2025-02-11 20:09:12', '2025-02-11 20:09:12'),
(581, NULL, 'adrian.nestor@bench.com', 1, '2025-02-11 20:17:35', '2025-02-11 20:17:35'),
(582, NULL, 'murphycharissa03@gmail.com', 1, '2025-02-11 20:22:32', '2025-02-11 20:22:32'),
(583, NULL, 'gundy0369@gmail.com', 1, '2025-02-11 20:28:14', '2025-02-11 20:28:14'),
(584, NULL, 'robertjaycurry@gmail.com', 1, '2025-02-11 20:31:23', '2025-02-11 20:31:23'),
(585, NULL, 'creary@hotmail.com', 1, '2025-02-11 20:48:49', '2025-02-11 20:48:49'),
(586, NULL, 'butronja@hotmail.com', 1, '2025-02-11 20:49:31', '2025-02-11 20:49:31'),
(587, NULL, 'johnandkarenw@msn.com', 1, '2025-02-11 21:07:24', '2025-02-11 21:07:24'),
(588, NULL, 'mvswee@outlook.com', 1, '2025-02-11 21:10:13', '2025-02-11 21:10:13'),
(589, NULL, 'quachqhuy@gmail.com', 1, '2025-02-11 21:18:18', '2025-02-11 21:18:18'),
(590, NULL, 'tomandsuemulvey@gmail.com', 1, '2025-02-11 21:21:20', '2025-02-11 21:21:20'),
(591, NULL, 'zacharykurnellas@gmail.com', 1, '2025-02-11 21:28:58', '2025-02-11 21:28:58'),
(592, NULL, 'sjsliwka2@comcast.net', 1, '2025-02-11 21:30:27', '2025-02-11 21:30:27'),
(593, NULL, 'barrybland47@hotmail.com', 1, '2025-02-11 21:36:29', '2025-02-11 21:36:29');
INSERT INTO `subscribers` (`id`, `name`, `email`, `status`, `created_at`, `updated_at`) VALUES
(594, NULL, 'kategilbey@mail.com', 1, '2025-02-11 21:43:21', '2025-02-11 21:43:21'),
(595, NULL, 'nina.r.adkins@gmail.com', 1, '2025-02-11 21:59:01', '2025-02-11 21:59:01'),
(596, NULL, 'cydneyadams93@gmail.com', 1, '2025-02-11 22:08:37', '2025-02-11 22:08:37'),
(597, NULL, 'deven.holowczak@gmail.com', 1, '2025-02-11 22:11:14', '2025-02-11 22:11:14'),
(598, NULL, 'kim1hailes@gmail.com', 1, '2025-02-11 22:13:08', '2025-02-11 22:13:08'),
(599, NULL, 'liliana.x.mejia@questdiagnostics.com', 1, '2025-02-11 22:45:12', '2025-02-11 22:45:12'),
(600, NULL, 'jennifer.g20@outlook.com', 1, '2025-02-11 22:45:37', '2025-02-11 22:45:37'),
(601, NULL, 'luciana.diaz@gmail.com', 1, '2025-02-11 22:54:21', '2025-02-11 22:54:21'),
(602, NULL, 'srlllctransport@gmail.com', 1, '2025-02-11 22:55:09', '2025-02-11 22:55:09'),
(603, NULL, 'njharvey88@hotmail.co.uk', 1, '2025-02-11 23:21:22', '2025-02-11 23:21:22'),
(604, NULL, 'paulinebeckford130760@gmail.com', 1, '2025-02-11 23:22:16', '2025-02-11 23:22:16'),
(605, NULL, 'kuromiyasanagu@gmail.com', 1, '2025-02-11 23:24:10', '2025-02-11 23:24:10'),
(606, NULL, 'soufynranoto@hotmail.com', 1, '2025-02-11 23:27:09', '2025-02-11 23:27:09'),
(607, NULL, 'mfvillanueva619@gmail.com', 1, '2025-02-11 23:34:31', '2025-02-11 23:34:31'),
(608, NULL, 'expow65g@gmail.com', 1, '2025-02-11 23:39:08', '2025-02-11 23:39:08'),
(609, NULL, 'rossiandjack@gmail.com', 1, '2025-02-11 23:39:25', '2025-02-11 23:39:25'),
(610, NULL, 'cvmc208@gmail.com', 1, '2025-02-11 23:42:40', '2025-02-11 23:42:40'),
(611, NULL, 'electrician2502@gmail.com', 1, '2025-02-11 23:43:45', '2025-02-11 23:43:45'),
(612, NULL, 'karinkomyu@live.jp', 1, '2025-02-11 23:52:00', '2025-02-11 23:52:00'),
(613, NULL, 'christinepearce@hotmail.com', 1, '2025-02-11 23:52:38', '2025-02-11 23:52:38'),
(614, NULL, 'lindafod@gmail.com', 1, '2025-02-11 23:55:30', '2025-02-11 23:55:30'),
(615, NULL, 'yuuta00014@gmail.com', 1, '2025-02-11 23:58:03', '2025-02-11 23:58:03'),
(616, NULL, 'nicolefinn@live.com', 1, '2025-02-12 00:03:32', '2025-02-12 00:03:32'),
(617, NULL, 'angelicaskiles@gmail.com', 1, '2025-02-12 00:03:58', '2025-02-12 00:03:58'),
(618, NULL, 'mdadude22@comcast.net', 1, '2025-02-12 00:07:22', '2025-02-12 00:07:22'),
(619, NULL, 'fiv5stars.booking@gmail.com', 1, '2025-02-12 00:07:35', '2025-02-12 00:07:35'),
(620, NULL, 'rdimaso@gmail.com', 1, '2025-02-12 00:15:22', '2025-02-12 00:15:22'),
(621, NULL, 'teshawilliams@icloud.com', 1, '2025-02-12 00:44:16', '2025-02-12 00:44:16'),
(622, NULL, 'info@dcsp.de', 1, '2025-02-12 00:52:16', '2025-02-12 00:52:16'),
(623, NULL, 'wolfman1.mw@gmail.com', 1, '2025-02-12 00:58:10', '2025-02-12 00:58:10'),
(624, NULL, 'james.helen.young@gmail.com', 1, '2025-02-12 01:14:02', '2025-02-12 01:14:02'),
(625, NULL, 'marksfriedman@gmail.com', 1, '2025-02-12 01:14:13', '2025-02-12 01:14:13'),
(626, NULL, 'stephjrobinson@hotmail.com', 1, '2025-02-12 01:16:18', '2025-02-12 01:16:18'),
(627, NULL, 'evenstar85@hotmail.co.uk', 1, '2025-02-12 01:23:45', '2025-02-12 01:23:45'),
(628, NULL, 'ksmoove1988@gmail.com', 1, '2025-02-12 01:28:10', '2025-02-12 01:28:10'),
(629, NULL, 'mrlz.venter@gmail.com', 1, '2025-02-12 01:37:05', '2025-02-12 01:37:05'),
(630, NULL, 'andrewhrynewich23@gmail.com', 1, '2025-02-12 01:42:07', '2025-02-12 01:42:07'),
(631, NULL, 'dchapple@uams.edu', 1, '2025-02-12 01:42:49', '2025-02-12 01:42:49'),
(632, NULL, 'dali.86@icloud.com', 1, '2025-02-12 01:44:20', '2025-02-12 01:44:20'),
(633, NULL, 'keisan.goldsmith@gmail.com', 1, '2025-02-12 01:50:45', '2025-02-12 01:50:45'),
(634, NULL, 'tonsaw@msn.com', 1, '2025-02-12 02:10:16', '2025-02-12 02:10:16'),
(635, NULL, 'marsteele77@gmail.com', 1, '2025-02-12 02:10:21', '2025-02-12 02:10:21'),
(636, NULL, 'mi.hamelynck@gmail.com', 1, '2025-02-12 02:21:20', '2025-02-12 02:21:20'),
(637, NULL, 'michaelhaff86@gmail.com', 1, '2025-02-12 02:28:19', '2025-02-12 02:28:19'),
(638, NULL, 'ace1@zoomtown.com', 1, '2025-02-12 02:34:07', '2025-02-12 02:34:07'),
(639, NULL, 'magchief@hotmail.com', 1, '2025-02-12 02:37:08', '2025-02-12 02:37:08'),
(640, NULL, 'edia-k@daum.net', 1, '2025-02-12 03:11:18', '2025-02-12 03:11:18'),
(641, NULL, 'patience1575@gmail.com', 1, '2025-02-12 03:14:15', '2025-02-12 03:14:15'),
(642, NULL, 'justinew21@msn.com', 1, '2025-02-12 03:14:26', '2025-02-12 03:14:26'),
(643, NULL, 'djones006@sympatico.ca', 1, '2025-02-12 03:53:14', '2025-02-12 03:53:14'),
(644, NULL, 'cappettajordan@gmail.com', 1, '2025-02-12 03:54:55', '2025-02-12 03:54:55'),
(645, NULL, 'akkhtartania@gmail.com', 1, '2025-02-12 03:55:12', '2025-02-12 03:55:12'),
(646, NULL, 'peter@schulmanco.com', 1, '2025-02-12 04:17:26', '2025-02-12 04:17:26'),
(647, NULL, 'diamond112796@gmail.com', 1, '2025-02-12 04:19:17', '2025-02-12 04:19:17'),
(648, NULL, 'jfundy1946@gmail.com', 1, '2025-02-12 04:27:23', '2025-02-12 04:27:23'),
(649, NULL, 'navyclarkes@gmail.com', 1, '2025-02-12 04:42:07', '2025-02-12 04:42:07'),
(650, NULL, 'michaelv404@gmail.com', 1, '2025-02-12 04:42:08', '2025-02-12 04:42:08'),
(651, NULL, 'paolodaino7888@gmail.com', 1, '2025-02-12 05:02:38', '2025-02-12 05:02:38'),
(652, NULL, 'mzmiaromano@gmail.com', 1, '2025-02-12 05:08:26', '2025-02-12 05:08:26'),
(653, NULL, 'smithclanof6123@charter.net', 1, '2025-02-12 05:17:37', '2025-02-12 05:17:37'),
(654, NULL, 'mark.a.bartlett@googlemail.com', 1, '2025-02-12 05:47:07', '2025-02-12 05:47:07'),
(655, NULL, 'kaiyamhotchkiss@gmail.com', 1, '2025-02-12 05:49:36', '2025-02-12 05:49:36'),
(656, NULL, 'likisha.t.coleman.mil@army.mil', 1, '2025-02-12 05:54:39', '2025-02-12 05:54:39'),
(657, NULL, 'kawa-taku0825@outlook.com', 1, '2025-02-12 06:30:50', '2025-02-12 06:30:50'),
(658, NULL, 'randygoodness@gmail.com', 1, '2025-02-12 06:31:03', '2025-02-12 06:31:03'),
(659, NULL, 'garyhcarmona@gmail.com', 1, '2025-02-12 06:33:22', '2025-02-12 06:33:22'),
(660, NULL, 'amanda.mcmahon@btopenworld.com', 1, '2025-02-12 06:48:04', '2025-02-12 06:48:04'),
(661, NULL, 'anthony.l.jackson1@gmail.com', 1, '2025-02-12 06:57:16', '2025-02-12 06:57:16'),
(662, NULL, 'pstxj001@gmail.com', 1, '2025-02-12 07:11:33', '2025-02-12 07:11:33'),
(663, NULL, 'ctabe@msn.com', 1, '2025-02-12 07:21:49', '2025-02-12 07:21:49'),
(664, NULL, 'info@alacartekuechen.de', 1, '2025-02-12 07:52:07', '2025-02-12 07:52:07'),
(665, NULL, 'elivety@web.de', 1, '2025-02-12 09:04:04', '2025-02-12 09:04:04'),
(666, NULL, 'neilhall31@hotmail.com', 1, '2025-02-12 09:11:52', '2025-02-12 09:11:52'),
(667, NULL, 'thomasbeckmann64@gmail.com', 1, '2025-02-12 10:09:11', '2025-02-12 10:09:11'),
(668, NULL, 'keith.westergaard@gmail.com', 1, '2025-02-12 10:20:07', '2025-02-12 10:20:07'),
(669, NULL, 'c.citak@gmx.de', 1, '2025-02-12 10:42:31', '2025-02-12 10:42:31'),
(670, NULL, 'kannarathlle@yahoo.com', 1, '2025-02-12 11:26:05', '2025-02-12 11:26:05'),
(671, NULL, 'phdeng11+nj@gmail.com', 1, '2025-02-12 11:35:25', '2025-02-12 11:35:25'),
(672, NULL, 'mkasap299@gmail.com', 1, '2025-02-12 11:39:25', '2025-02-12 11:39:25'),
(673, NULL, 'cassiepowers99@gmail.com', 1, '2025-02-12 12:01:04', '2025-02-12 12:01:04'),
(674, NULL, 'kirsch0976@gmail.com', 1, '2025-02-12 12:17:17', '2025-02-12 12:17:17'),
(675, NULL, 'canlenote4@gmail.com', 1, '2025-02-12 12:18:03', '2025-02-12 12:18:03'),
(676, NULL, 'fronitb@gmail.com', 1, '2025-02-12 12:26:06', '2025-02-12 12:26:06'),
(677, NULL, 'lisa.judson@hotmail.co.uk', 1, '2025-02-12 12:45:13', '2025-02-12 12:45:13'),
(678, NULL, 'myburra29@gmail.com', 1, '2025-02-12 13:04:16', '2025-02-12 13:04:16'),
(679, NULL, 'jackson.j@aricent.com', 1, '2025-02-12 13:13:48', '2025-02-12 13:13:48'),
(680, NULL, 'will.littlewood@gmail.com', 1, '2025-02-12 13:40:05', '2025-02-12 13:40:05'),
(681, NULL, 'natashacrabtree@hotmail.com', 1, '2025-02-12 17:20:10', '2025-02-12 17:20:10'),
(682, NULL, 'azexofukif519@gmail.com', 1, '2025-02-13 00:41:06', '2025-02-13 00:41:06'),
(683, NULL, 'uwizoles96@gmail.com', 1, '2025-02-13 04:03:12', '2025-02-13 04:03:12'),
(684, NULL, 'unayonidux466@gmail.com', 1, '2025-02-13 07:43:06', '2025-02-13 07:43:06'),
(685, NULL, 'ugojowi031@gmail.com', 1, '2025-02-13 13:10:04', '2025-02-13 13:10:04'),
(686, NULL, 'fehkqaeqdyfne@yahoo.com', 1, '2025-02-14 04:56:09', '2025-02-14 04:56:09'),
(687, NULL, 'sojivelijo256@gmail.com', 1, '2025-02-14 08:08:04', '2025-02-14 08:08:04'),
(688, NULL, 'wg3cw7dycii9o8r@yahoo.com', 1, '2025-02-14 09:11:18', '2025-02-14 09:11:18'),
(689, NULL, 'eyerewaba96@gmail.com', 1, '2025-02-14 18:35:02', '2025-02-14 18:35:02'),
(690, NULL, 'hgalvanej1993@gmail.com', 1, '2025-02-15 12:32:45', '2025-02-15 12:32:45'),
(691, NULL, 'kc74q49fkv8g5u@yahoo.com', 1, '2025-02-22 03:34:12', '2025-02-22 03:34:12'),
(692, NULL, 'bautistakristif29@gmail.com', 1, '2025-02-22 15:28:57', '2025-02-22 15:28:57'),
(693, NULL, 'nikkjhqeeieaeilt2@yahoo.com', 1, '2025-02-22 20:48:16', '2025-02-22 20:48:16'),
(694, NULL, 'bloweryq18@gmail.com', 1, '2025-02-23 00:40:57', '2025-02-23 00:40:57'),
(695, NULL, 'sevignjohnghar@yahoo.com', 1, '2025-02-23 03:56:18', '2025-02-23 03:56:18'),
(696, NULL, 'ndiknbtqhhauti@yahoo.com', 1, '2025-02-23 08:47:41', '2025-02-23 08:47:41'),
(697, NULL, 'expanseeasylvanou@gmail.com', 1, '2025-02-24 01:46:47', '2025-02-24 01:46:47'),
(698, NULL, 'volfbartonhk@gmail.com', 1, '2025-03-02 10:52:05', '2025-03-02 10:52:05'),
(699, NULL, 'simfpricepe1992@gmail.com', 1, '2025-03-03 07:06:28', '2025-03-03 07:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `designation` varchar(191) DEFAULT NULL,
  `organization` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(3, 'Super Admin', 'hasanikramul926@gmail.com', NULL, '$2y$10$bRm70Uu0cP50oFBC5E6xnu6Qe6DZicNUfV3Tr87UOjNuLHnwGytY.', '03cMbhDnUQv82z0KX8GS9QLhlRwP537UJFUbQBOQP1RY0DiQkBXzbkTnxr8D', '2021-11-07 14:26:20', '2025-02-07 12:27:02');

-- --------------------------------------------------------

--
-- Table structure for table `why_choose_us`
--

CREATE TABLE `why_choose_us` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `why_choose_us`
--

INSERT INTO `why_choose_us` (`id`, `title`, `slug`, `description`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Proven Expertise', 'proven-expertise', NULL, NULL, 1, '2020-10-30 12:31:05', '2025-02-09 08:17:41'),
(2, 'Dedicated Team', 'dedicated-team', NULL, NULL, 1, '2020-10-30 12:31:13', '2020-10-30 12:31:13'),
(3, 'Global Reach', 'global-reach', NULL, NULL, 1, '2020-10-30 12:31:23', '2025-02-09 08:17:55'),
(4, 'Customized Solutions', 'customized-solutions', NULL, NULL, 1, '2020-10-30 12:31:34', '2025-02-09 08:18:08'),
(5, '24/7 Supports', '247-supports', NULL, NULL, 1, '2020-10-30 12:31:47', '2020-10-30 12:31:47'),
(6, 'Work Deadline', 'work-deadline', NULL, NULL, 1, '2020-10-30 12:31:58', '2020-10-30 12:31:58'),
(7, 'Latest Technologies', 'latest-technologies', NULL, NULL, 1, '2025-02-09 08:18:33', '2025-02-09 08:18:33'),
(8, 'Affordable Pricing', 'affordable-pricing', NULL, NULL, 1, '2025-02-09 08:18:54', '2025-02-09 08:18:54'),
(9, 'Guaranteed Results', 'guaranteed-results', NULL, NULL, 1, '2025-02-09 08:19:02', '2025-02-09 08:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `work_processes`
--

CREATE TABLE `work_processes` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_processes`
--

INSERT INTO `work_processes` (`id`, `title`, `slug`, `description`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Planning', 'planning', '<p>Designing a custom layout that fits your brand’s needs, vision, and goals.</p>', NULL, 1, '2020-10-30 12:29:23', '2025-02-11 22:09:22'),
(2, 'Development', 'development', '<p>Creating a fast, responsive, and easy-to-use website with smooth and intuitive navigation.</p>', NULL, 1, '2020-10-30 12:29:38', '2025-02-11 22:09:57'),
(3, 'Testing', 'testing', '<p>Checking that everything works perfectly on all devices, browsers, and platforms.</p>', NULL, 1, '2020-10-30 12:29:52', '2025-02-11 22:10:22'),
(4, 'Launch & Support', 'launch-support', '<p>Launching your site and offering ongoing support, maintenance, and timely updates.</p>', NULL, 1, '2020-10-30 12:30:08', '2025-02-11 22:11:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abouts`
--
ALTER TABLE `abouts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `abouts_title_unique` (`title`),
  ADD UNIQUE KEY `abouts_slug_unique` (`slug`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `articles_title_unique` (`title`),
  ADD UNIQUE KEY `articles_slug_unique` (`slug`),
  ADD KEY `articles_category_id_foreign` (`category_id`);

--
-- Indexes for table `article_categories`
--
ALTER TABLE `article_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article_categories_title_unique` (`title`),
  ADD UNIQUE KEY `article_categories_slug_unique` (`slug`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_title_unique` (`title`),
  ADD UNIQUE KEY `clients_slug_unique` (`slug`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `counters`
--
ALTER TABLE `counters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `counters_title_unique` (`title`),
  ADD UNIQUE KEY `counters_slug_unique` (`slug`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `designations_title_unique` (`title`),
  ADD UNIQUE KEY `designations_slug_unique` (`slug`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_templates_title_unique` (`title`),
  ADD UNIQUE KEY `email_templates_slug_unique` (`slug`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faqs_title_unique` (`title`),
  ADD UNIQUE KEY `faqs_slug_unique` (`slug`),
  ADD KEY `faqs_category_id_foreign` (`category_id`);

--
-- Indexes for table `faq_categories`
--
ALTER TABLE `faq_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faq_categories_title_unique` (`title`),
  ADD UNIQUE KEY `faq_categories_slug_unique` (`slug`);

--
-- Indexes for table `get_quotes`
--
ALTER TABLE `get_quotes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_quote_id_foreign` (`quote_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `live_chats`
--
ALTER TABLE `live_chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ltm_translations`
--
ALTER TABLE `ltm_translations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `members_title_unique` (`title`),
  ADD UNIQUE KEY `members_slug_unique` (`slug`),
  ADD KEY `members_designation_id_foreign` (`designation_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_title_unique` (`title`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`);

--
-- Indexes for table `page_setups`
--
ALTER TABLE `page_setups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_setups_title_unique` (`title`),
  ADD UNIQUE KEY `page_setups_slug_unique` (`slug`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `portfolios_title_unique` (`title`),
  ADD UNIQUE KEY `portfolios_slug_unique` (`slug`);

--
-- Indexes for table `portfolio_categories`
--
ALTER TABLE `portfolio_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `portfolio_categories_title_unique` (`title`),
  ADD UNIQUE KEY `portfolio_categories_slug_unique` (`slug`);

--
-- Indexes for table `portfolio_category`
--
ALTER TABLE `portfolio_category`
  ADD KEY `portfolio_category_portfolio_id_foreign` (`portfolio_id`),
  ADD KEY `portfolio_category_portfolio_category_id_foreign` (`portfolio_category_id`);

--
-- Indexes for table `pricings`
--
ALTER TABLE `pricings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pricings_title_unique` (`title`),
  ADD UNIQUE KEY `pricings_slug_unique` (`slug`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sections_title_unique` (`title`),
  ADD UNIQUE KEY `sections_slug_unique` (`slug`);

--
-- Indexes for table `serviceables`
--
ALTER TABLE `serviceables`
  ADD UNIQUE KEY `serviceables_service_id_serviceable_id_serviceable_type_unique` (`service_id`,`serviceable_id`,`serviceable_type`),
  ADD KEY `serviceables_serviceable_type_serviceable_id_index` (`serviceable_type`,`serviceable_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_title_unique` (`title`),
  ADD UNIQUE KEY `services_slug_unique` (`slug`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sliders_title_unique` (`title`),
  ADD UNIQUE KEY `sliders_slug_unique` (`slug`);

--
-- Indexes for table `socials`
--
ALTER TABLE `socials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscribers_email_unique` (`email`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `testimonials_title_unique` (`title`),
  ADD UNIQUE KEY `testimonials_slug_unique` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `why_choose_us`
--
ALTER TABLE `why_choose_us`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `why_choose_us_title_unique` (`title`),
  ADD UNIQUE KEY `why_choose_us_slug_unique` (`slug`);

--
-- Indexes for table `work_processes`
--
ALTER TABLE `work_processes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_processes_title_unique` (`title`),
  ADD UNIQUE KEY `work_processes_slug_unique` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abouts`
--
ALTER TABLE `abouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `article_categories`
--
ALTER TABLE `article_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `counters`
--
ALTER TABLE `counters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `faq_categories`
--
ALTER TABLE `faq_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `get_quotes`
--
ALTER TABLE `get_quotes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `live_chats`
--
ALTER TABLE `live_chats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ltm_translations`
--
ALTER TABLE `ltm_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=438;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `page_setups`
--
ALTER TABLE `page_setups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolios`
--
ALTER TABLE `portfolios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `portfolio_categories`
--
ALTER TABLE `portfolio_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pricings`
--
ALTER TABLE `pricings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `socials`
--
ALTER TABLE `socials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=700;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `why_choose_us`
--
ALTER TABLE `why_choose_us`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `work_processes`
--
ALTER TABLE `work_processes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `faqs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `faq_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `get_quotes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_category`
--
ALTER TABLE `portfolio_category`
  ADD CONSTRAINT `portfolio_category_portfolio_category_id_foreign` FOREIGN KEY (`portfolio_category_id`) REFERENCES `portfolio_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `portfolio_category_portfolio_id_foreign` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `serviceables`
--
ALTER TABLE `serviceables`
  ADD CONSTRAINT `serviceables_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
