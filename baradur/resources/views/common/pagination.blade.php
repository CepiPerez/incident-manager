
@if (View::pagination())
<ul class="pagination m-0">
    <li class="{{ View::pagination()->first? 'first' : 'first disabled' }}">
        @if (View::pagination()->first)
        <a href="?{{ View::pagination()->first }}">
        <span class="ri-xl ri-skip-back-line"></span><span class="pagination-text">{{ __('pagination.first') }}</span></a>
        @else
        <a><span class="ri-xl ri-skip-back-line"></span><span class="pagination-text">{{ __('pagination.first') }}</span></a>
        @endif
    </li>
    <li class="{{ View::pagination()->second? 'previous' : 'previous disabled' }}">
    @if (View::pagination()->second)
        <a href="?{{ View::pagination()->second }}">
        <span class="ri-xl ri-arrow-left-line"></span><span class="pagination-text">{{ __('pagination.previous') }}</span></a>
        @else
        <a><span class="ri-xl ri-arrow-left-line"></span><span class="pagination-text">{{ __('pagination.previous') }}</span></a>
        @endif
    </li>
    <li class="{{ View::pagination()->third? 'next' : 'next disabled' }}">
    @if (View::pagination()->third)
        <a href="?{{ View::pagination()->third }}">
        <span class="ri-xl ri-arrow-right-line"></span><span class="pagination-text">{{ __('pagination.next') }}</span></a>
        @else
        <a><span class="ri-xl ri-arrow-right-line"></span><span class="pagination-text">{{ __('pagination.next') }}</span></a>
        @endif
    </li>
    <li class="{{ View::pagination()->fourth? 'last' : 'last disabled' }}">
    @if (View::pagination()->fourth)
        <a href="?{{ View::pagination()->fourth }}">
        <span class="ri-xl ri-skip-forward-line"></span><span class="pagination-text">{{ __('pagination.last') }}</span></a>
        @else
        <a><span class="ri-xl ri-skip-forward-line"></span><span class="pagination-text">{{ __('pagination.last') }}</span></a>
        @endif
    </li>
</ul>
@endif