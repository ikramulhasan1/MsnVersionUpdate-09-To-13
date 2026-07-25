<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Page Cache মাস্টার সুইচ (ফ্ল্যাগ ফাইল)
    |--------------------------------------------------------------------------
    | Admin panel থেকে ON/OFF করলে এই ফাইলটা create/delete হয়। .htaccess নিজে
    | এই ফাইলের অস্তিত্ব চেক করে, তাই cache বন্ধ করলে সাথে সাথে সব রিকোয়েস্ট
    | আবার Laravel দিয়েই যাবে, কোনো code deploy লাগবে না।
    */
    'flag_file' => storage_path('framework/cache/pagecache-enabled.flag'),

    /*
    |--------------------------------------------------------------------------
    | Cached HTML ফাইল কোথায় সেভ হবে
    |--------------------------------------------------------------------------
    | এই প্রজেক্টে public_path() আসলে প্রজেক্ট রুট (index.php-তে path.public
    | override করা), তাই এই ফোল্ডারটা রুটেই তৈরি হবে এবং .htaccess থেকে
    | সরাসরি এক্সেসযোগ্য।
    */
    'html_dir' => public_path('cache-html'),

    /*
    |--------------------------------------------------------------------------
    | সেফটি-নেট TTL (সেকেন্ড)
    |--------------------------------------------------------------------------
    | Observer কোনো কারণে কোনো পেজ invalidate করতে মিস করলেও, এর চেয়ে পুরনো
    | cache ফাইল থাকলে সেটা আর সার্ভ হবে না (Laravel মিডলওয়্যার লেভেলে চেক করে)।
    */
    'ttl' => 60 * 60 * 24, // ২৪ ঘণ্টা

    /*
    |--------------------------------------------------------------------------
    | যেসব route কখনো full-page cache হবে না
    |--------------------------------------------------------------------------
    | ফর্ম সাবমিশন, পেমেন্ট, ব্যক্তিগত/ডায়নামিক ডাটা দেখানো পেজ ইত্যাদি।
    | Route name pattern (wildcard * সাপোর্ট করে)।
    */
    'excluded_route_names' => [
        'get-quote',
        'get-quote.store',
        'quote.upload',
        'goToQuotePage',
        'contact.send',
        'subscribe',
        'meetings.store',
        'make.payment',
        'cancel.payment',
        'success.payment',
        'payment.feedback',
        'blog.search',
        'csrf.refresh',
        'sitemap*',
        'admin.*',
        'login',
        'logout',
        'password.*',
        'register',
    ],

    /*
    |--------------------------------------------------------------------------
    | Content version registry key
    |--------------------------------------------------------------------------
    | Query/fragment cache-এর versioned key তৈরিতে ব্যবহার হয় (Redis Cache Tags
    | না থাকায় এই প্যাটার্নে "tag flush" simulate করা হয়)।
    */
    'version_cache_prefix' => 'pagecache_version:',
];
