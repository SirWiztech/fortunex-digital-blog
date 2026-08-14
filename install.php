<?php
/**
 * Fortunexdigital — Installer
 * Creates the database, tables, and seeds categories, author, pages, and posts.
 *
 * Usage: visit /install.php once, then delete or protect this file.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$db   = DB_NAME;

try {
    $server = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('<p>Could not connect to MySQL server. Check DB_USER/DB_PASS in includes/config.php.</p><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

$server->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$server->exec("USE `$db`");
$pdo = $server;

// ---------- Schema ----------
$schema = [
    "CREATE TABLE IF NOT EXISTS authors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        bio TEXT,
        avatar VARCHAR(255)
    )",
    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(120) NOT NULL UNIQUE,
        name VARCHAR(120) NOT NULL,
        description TEXT,
        image VARCHAR(255)
    )",
    "CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(160) NOT NULL UNIQUE,
        title VARCHAR(255) NOT NULL,
        excerpt TEXT,
        content LONGTEXT,
        category_id INT,
        author_id INT,
        featured_image VARCHAR(255),
        status VARCHAR(20) DEFAULT 'published',
        meta_title VARCHAR(255),
        meta_description VARCHAR(255),
        published_at DATETIME,
        updated_at DATETIME,
        INDEX (category_id), INDEX (status)
    )",
    "CREATE TABLE IF NOT EXISTS tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(120) NOT NULL UNIQUE,
        name VARCHAR(120) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS post_tags (
        post_id INT, tag_id INT,
        PRIMARY KEY (post_id, tag_id)
    )",
    "CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT,
        name VARCHAR(120),
        email VARCHAR(160),
        comment TEXT,
        status VARCHAR(20) DEFAULT 'pending',
        created_at DATETIME,
        INDEX (post_id)
    )",
    "CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(120) NOT NULL UNIQUE,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT,
        meta_title VARCHAR(255),
        meta_description VARCHAR(255),
        updated_at DATETIME
    )",
    "CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120),
        email VARCHAR(160),
        message TEXT,
        created_at DATETIME
    )"
];
foreach ($schema as $s) { $pdo->exec($s); }

// ---------- Author ----------
$pdo->exec("DELETE FROM authors");
$pdo->prepare("INSERT INTO authors (id, name, bio, avatar) VALUES (1, 'Alex Rivera', ?, 'author.svg')")
    ->execute(['Alex is the founder of Fortunexdigital and a full-time affiliate marketer. After leaving a 9-to-5, Alex built multiple income streams online and now shares the exact systems that work — no fluff, just real strategies.']);

// ---------- Categories ----------
$categories = [
    ['affiliate-marketing', 'Affiliate Marketing', 'Learn how to promote other people\'s products and earn commissions with honest, high-converting strategies.'],
    ['side-hustles', 'Side Hustles', 'Real ways to make extra money in your spare time, from apps that pay to creative online businesses.'],
    ['pinterest', 'Pinterest', 'Grow traffic and income with Pinterest marketing, pins, and boards that convert.'],
    ['funnels', 'Funnels', 'Build sales funnels that turn strangers into customers on autopilot.'],
    ['blogging', 'Blogging', 'Start and grow a profitable blog with SEO, content strategy, and monetization.'],
    ['save-money', 'Save Money', 'Practical frugal-living tips and smart money habits to keep more of what you earn.'],
    ['ai-hustles', 'AI Hustles', 'Use AI tools to create content, products, and services that make money faster.'],
    ['copywriting', 'Copywriting', 'Write words that sell — for emails, landing pages, and product offers.']
];
$pdo->exec("DELETE FROM categories");
$catStmt = $pdo->prepare("INSERT INTO categories (slug, name, description) VALUES (?, ?, ?)");
foreach ($categories as $c) { $catStmt->execute($c); }

// ---------- Static pages ----------
$pages = [
    'about' => [
        'About Fortunexdigital',
        'Fortunexdigital is a personal finance and make-money-online blog created to share a proven, ethical path to earning a full-time income with side hustles and online business. We test real strategies so you don\'t have to waste time on scams.',
        'About Fortunexdigital — our story, mission, and the real ways we help you make money online.',
        'Learn who is behind Fortunexdigital and why we share honest, tested money-making strategies.'
    ],
    'contact' => [
        'Contact Us',
        'Have a question, partnership idea, or just want to say hi? Reach out using the form below and we\'ll get back to you as soon as possible.',
        'Contact Fortunexdigital — get in touch with our team for questions, partnerships, or support.',
        'Questions or partnership ideas? Use our contact form to reach the Fortunexdigital team.'
    ],
    'privacy-policy' => [
        'Privacy Policy',
        'This Privacy Policy explains how Fortunexdigital collects, uses, and protects your information, including cookies, analytics, and Google AdSense advertising.',
        'Fortunexdigital Privacy Policy — how we collect, use, and protect your data, cookies, and AdSense.',
        'Read how Fortunexdigital handles your data, cookies, analytics, and advertising through Google AdSense.'
    ],
    'terms' => [
        'Terms & Conditions',
        'These Terms & Conditions govern your use of the Fortunexdigital website and the content provided herein.',
        'Fortunexdigital Terms & Conditions — the rules for using our website and content.',
        'Understand the terms that apply when you use the Fortunexdigital website and its content.'
    ],
    'disclaimer' => [
        'Disclaimer',
        'Fortunexdigital provides information for educational purposes only. We are not financial advisors, and results vary. Read our full disclaimer before acting on any content.',
        'Fortunexdigital Disclaimer — our content is educational and not financial advice.',
        'Our content is for education only. Learn about the limitations and risks in the Fortunexdigital disclaimer.'
    ],
    'cookie-policy' => [
        'Cookie Policy',
        'This Cookie Policy describes how Fortunexdigital uses cookies and similar technologies, including for Google AdSense personalized advertising.',
        'Fortunexdigital Cookie Policy — how and why we use cookies, including for ad personalization.',
        'Find out how Fortunexdigital uses cookies and tracking technologies across the site.'
    ],
    'sign-up-offers' => [
        'Sign Up Offers',
        'Grab the best bank bonuses, app referral deals, and sign-up offers that put real cash in your pocket. Updated regularly.',
        'Fortunexdigital Sign Up Offers — the best bank bonuses and referral deals to earn free cash.',
        'Discover the top sign-up bonuses and referral offers curated by Fortunexdigital.'
    ]
];
$pdo->exec("DELETE FROM pages");
$pageStmt = $pdo->prepare("INSERT INTO pages (slug, title, content, meta_title, meta_description, updated_at) VALUES (?, ?, ?, ?, ?, NOW())");
foreach ($pages as $slug => [$title, $content, $mt, $md]) {
    $pageStmt->execute([$slug, $title, "<p>$content</p>", $mt, $md]);
}

echo "<p>Tables, categories, author, and pages installed.</p>";

require_once __DIR__ . '/sql/seed_posts.php';
