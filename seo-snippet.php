<?php
/* Surdyke — Brand Page SEO: per-page titles + descriptions + staging noindex */

// Staging guard: noindex any non-production host (auto-stops on surdykeyamaha.com)
add_action('wp_head', function () {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (strpos($host, 'permanentbuild.com') !== false) {
        echo '<meta name="robots" content="noindex,nofollow">' . "\n";
    }
}, 1);

function syk_seo_map() {
    return [
        'axopar'     => ['Axopar Boats at Lake of the Ozarks | Surdyke Yamaha', 'Shop Axopar adventure boats at Surdyke Yamaha on Lake of the Ozarks. Scandinavian-engineered, all-weather day boats and cabin cruisers — real pricing, build and reserve online.'],
        'beneteau'   => ['Beneteau Boats at Lake of the Ozarks | Surdyke Yamaha', 'Beneteau Antares and Gran Turismo at Surdyke Yamaha, Lake of the Ozarks. 140 years of French boatbuilding — explore the lineup with real pricing and reserve online.'],
        'scarab'     => ['Scarab Jet Boats at Lake of the Ozarks | Surdyke Yamaha', 'Scarab jet boats at Surdyke Yamaha on Lake of the Ozarks. Rotax jet power, no prop, sandbar-ready — see real pricing and build yours online.'],
        'suncatcher' => ['SunCatcher Pontoons at Lake of the Ozarks | Surdyke Yamaha', 'SunCatcher pontoons and tritoons at Surdyke Yamaha, Lake of the Ozarks. Yamaha-powered, all-welded aluminum — real pricing, build and reserve online.'],
        'g3'         => ['G3 Aluminum Fishing Boats at Lake of the Ozarks | Surdyke Yamaha', 'G3 all-welded aluminum fishing boats at Surdyke Yamaha on Lake of the Ozarks. Yamaha-powered and built to fish — explore models with real pricing.'],
        'cfmoto'     => ['CFMOTO Side-by-Sides, ATVs & Motorcycles | Surdyke Yamaha', 'CFMOTO side-by-sides, ATVs and motorcycles at Surdyke Yamaha, Lake of the Ozarks. UFORCE, ZFORCE, CFORCE and Ibex — real pricing from $4,599, reserve online.'],
        'ktm'        => ['KTM Motorcycles at Lake of the Ozarks | Surdyke Yamaha', 'KTM motocross and enduro motorcycles at Surdyke Yamaha on Lake of the Ozarks. SX, SX-F, XC-W and EXC-F — real pricing, build and reserve online.'],
        'yamaha'     => ['Yamaha at Lake of the Ozarks — Boats, WaveRunners & Powersports | Surdyke', 'Your full-line Yamaha dealer at Lake of the Ozarks — WaveRunners, jet boats, motorcycles, ATVs and side-by-sides. Build, price and reserve online at Surdyke Yamaha.'],
        'yamaha-motorcycles' => ['Yamaha Motorcycles & ATVs at Lake of the Ozarks | Surdyke', 'Yamaha dirt bikes, street bikes and ATVs at Surdyke on Lake of the Ozarks — YZ motocross, MT and R-series street, plus Raptor and Grizzly ATVs. Real pricing, reserve online.'],
    ];
}

// Per-page <title> (filter renders once — no duplicate tag)
add_filter('pre_get_document_title', function ($title) {
    if (!is_page()) return $title;
    $slug = get_post_field('post_name', get_queried_object_id());
    $map = syk_seo_map();
    return isset($map[$slug]) ? $map[$slug][0] : $title;
}, 99999);

// Per-page meta description — replace the existing one cleanly via scoped output buffer
add_action('template_redirect', function () {
    if (!is_page()) return;
    $slug = get_post_field('post_name', get_queried_object_id());
    $map = syk_seo_map();
    if (!isset($map[$slug])) return;
    $desc = $map[$slug][1];
    ob_start(function ($html) use ($desc) {
        $tag = '<meta name="description" content="' . esc_attr($desc) . '">';
        if (preg_match('/<meta\s+name=["\']description["\'][^>]*>/i', $html)) {
            return preg_replace('/<meta\s+name=["\']description["\'][^>]*>/i', $tag, $html, 1);
        }
        return preg_replace('/<\/head>/i', $tag . "\n</head>", $html, 1);
    });
});
