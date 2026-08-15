{{--
    Website Discovery — Phase G1 (Watchlist).

    A small 5-point star icon — filled (solid) when $filled is true,
    outlined otherwise — the ⭐ Save/Watch toggle's icon on
    result-card.blade.php and discovery/show.blade.php. A single shared
    partial rather than the same SVG path duplicated in both places, so
    the icon only needs updating in one spot if it ever changes.

    Expects:
      $filled   bool
--}}
<svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $filled ?? false ? 'currentColor' : 'none' }}"
    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M12 2.5l3.09 6.26 6.91 1.01-5 4.87 1.18 6.86L12 17.77 5.82 21.5 7 14.64l-5-4.87 6.91-1.01L12 2.5z" />
</svg>
