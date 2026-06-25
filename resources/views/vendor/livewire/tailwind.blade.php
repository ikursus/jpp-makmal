@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; flex-direction: column; gap: 12px; align-items: center;">

            {{-- Page links --}}
            <ul style="display: flex; flex-wrap: wrap; gap: 6px; list-style: none; margin: 0; padding: 0; justify-content: center;">

                {{-- Previous --}}
                <li>
                    @if ($paginator->onFirstPage())
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 8px; font-size: 14px; font-weight: 500; background: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb; cursor: not-allowed;">
                            &laquo;
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 8px; font-size: 14px; font-weight: 500; background: white; color: #374151; border: 1px solid #d1d5db; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#9ca3af'" onmouseout="this.style.background='white';this.style.borderColor='#d1d5db'">
                            &laquo;
                        </button>
                    @endif
                </li>

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li>
                            <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 8px; font-size: 14px; font-weight: 500; background: white; color: #9ca3af; border: 1px solid #e5e7eb; cursor: default;">
                                {{ $element }}
                            </span>
                        </li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 8px; font-size: 14px; font-weight: 600; background: #003366; color: white; border: 1px solid #003366; box-shadow: 0 2px 6px rgba(0,51,102,0.25);">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 8px; font-size: 14px; font-weight: 500; background: white; color: #374151; border: 1px solid #d1d5db; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#9ca3af'" onmouseout="this.style.background='white';this.style.borderColor='#d1d5db'" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                <li>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 8px; font-size: 14px; font-weight: 500; background: white; color: #374151; border: 1px solid #d1d5db; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#9ca3af'" onmouseout="this.style.background='white';this.style.borderColor='#d1d5db'">
                            &raquo;
                        </button>
                    @else
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 8px; font-size: 14px; font-weight: 500; background: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb; cursor: not-allowed;">
                            &raquo;
                        </span>
                    @endif
                </li>

            </ul>
        </nav>
    @endif
</div>
