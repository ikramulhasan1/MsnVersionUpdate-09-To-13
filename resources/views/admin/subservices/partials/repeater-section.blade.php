{{--
    Reusable "repeater" section (collapsible card + dynamic add/remove rows).
    Expects a single $section array, e.g.:

    [
        'id'       => 'sec-core-features',
        'icon'     => 'fa-star',
        'title'    => 'Core Features',
        'subtitle' => 'Highlight what makes this service stand out',
        'row'      => 'features-row',      // wrapper class (kept for BC / custom CSS hooks)
        'group'    => 'features-group',    // per-row class (kept for BC / custom CSS hooks)
        'prefix'   => 'features',          // form field name prefix -> features[0][title]
        'items'    => $features,           // decoded array from the model
        'fields'   => [
            ['k' => 'icon_class', 'l' => 'Icon Class'],
            ['k' => 'title',      'l' => 'Title'],
            ['k' => 'bottom_text','l' => 'Bottom Text'],
        ],
        'collapsed' => true, // optional, defaults to true
    ]
--}}
@php $collapsed = $section['collapsed'] ?? true; @endphp
<div class="premium-section{{ $collapsed ? ' ps-collapsed' : '' }}" id="{{ $section['id'] }}">
    <div class="premium-section-head" onclick="psToggleSection(this)">
        <div class="premium-section-title"><span class="premium-icon"><i class="fa {{ $section['icon'] }}"></i></span>{{ $section['title'] }}</div>
        <div class="premium-section-sub">{{ $section['subtitle'] }}</div>
        <i class="fa fa-chevron-down premium-chevron"></i>
    </div>
    <div class="premium-section-body{{ $collapsed ? ' ps-collapsed' : '' }}">
        <div class="repeater {{ $section['row'] }}" data-prefix="{{ $section['prefix'] }}" data-group="{{ $section['group'] }}" data-fields='@json($section['fields'])'>
            @foreach (($section['items'] ?? []) as $key => $row)
                <div class="repeater-item {{ $section['group'] }}">
                    <span class="repeater-num">{{ $key + 1 }}</span>
                    <div class="repeater-fields">
                        @foreach ($section['fields'] as $f)
                            <input type="text" class="form-control mb-1"
                                name="{{ $section['prefix'] }}[{{ $key }}][{{ $f['k'] }}]"
                                value="{{ $row[$f['k']] ?? ($f['d'] ?? '') }}"
                                placeholder="{{ $key + 1 }}. {{ $f['l'] }}">
                        @endforeach
                    </div>
                    <button type="button" class="repeater-del" onclick="this.closest('.repeater-item').remove()" title="Remove">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn-add-row" onclick="addRepeaterRow(this)">
            <i class="fa fa-plus"></i> {{ __('dashboard.add_another_FAQ') }}
        </button>
    </div>
</div>
