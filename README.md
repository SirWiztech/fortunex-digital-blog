# Fortunexdigital — Blog / Affiliate Site

A dynamic, SEO-optimized blog built with **PHP + MySQL** that replicates the layout and
structure of *Loud Money Moves*, rebranded as **Fortunexdigital**. It follows the
50-point SEO & Google AdSense implementation guidelines (semantic HTML, unique
meta/JSON-LD, clean URLs, dynamic sitemap, required legal pages, original content, etc.).

## Features

- Semantic HTML5 (`header`, `nav`, `main`, `article`, `section`, `footer`)
- Database-driven posts, categories, tags, authors, comments, pages, contacts
- Clean URLs via `.htaccess` mod_rewrite (`/blog/<slug>`, `/category/<slug>`, `/tag/<slug>`, `/p/<slug>`)
- Dynamic XML `sitemap.php` + `robots.txt` pointing to it
- JSON-LD structured data (Organization, BreadcrumbList, BlogPosting)
- One `<h1>` per page, unique `<title>` & `<meta description>`, canonical tags, alt text
- Pagination, breadcrumbs, related posts, author bio, on-site search, comment system
- Required AdSense pages: About, Contact, Privacy Policy, Terms, Disclaimer, Cookie Policy
- Mobile-responsive, lazy-loaded images, deferred JS, GZIP + cache headers in `.htaccess`
- Admin panel (`/admin/`) for post CRUD with login

## Local Setup (XAMPP / Apache + MySQL)

1. Place this folder in your web root, e.g. `C:\xampp\htdocs\fortunex-digital-blog`.
   > If you serve it from a different path, update `RewriteBase` in `.htaccess`
   > and `SITE_URL` in `includes/config.php` to match.
2. Create a database named `fortunexdigital` (or let the installer create it).
   Default DB config in `includes/config.php`: `root` / no password.
3. Visit `http://localhost/fortunex-digital-blog/install.php` once to build tables
   and seed 24 original posts + pages.
4. **Delete or password-protect `install.php`** afterward (security).
5. Visit the site. Admin: `/admin/` — default `admin` / `fortunex2026`
   (change in `includes/config.php`).

## Content Notes

- 24 original posts are seeded across 8 categories (Affiliate Marketing, Side Hustles,
  Pinterest, Funnels, Blogging, Save Money, AI Hustles, Copywriting).
- For best AdSense approval odds, expand posts toward **800+ words** (guideline #32).
  You can edit/add posts anytime from the admin panel.
- Keep publishing consistently and submit `sitemap.php` to Google Search Console.

## File Map

```
index.php          Homepage (hero, featured-on, categories, popular posts, CTA)
blog.php           Post listing + pagination
post.php           Single post (+ JSON-LD, comments, related)
category.php       Category archive
tag.php            Tag archive
search.php         On-site search
page.php           Static pages + contact form
404.php            Custom 404
sitemap.php        Dynamic XML sitemap
robots.txt         Crawler guidance
.htaccess          mod_rewrite + GZIP + cache
includes/          config, db, header, footer, seo helpers, functions, comments
assets/            css/style.css, js/main.js, img/ (logo, favicon, og, author)
admin/             login, logout, index (post CRUD)
sql/seed_posts.php Post seed data (used by install.php)
install.php        DB + table + content installer
```
 