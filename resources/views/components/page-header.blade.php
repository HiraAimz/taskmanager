@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
])

<div class="mb-6">
    <!-- Breadcrumbs -->
    @if(count($breadcrumbs) > 0)
    <nav class="flex mb-3" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm">
            @foreach($breadcrumbs as $index => $crumb)
                <li class="inline-flex items-center">
                    @if($index > 0)
                        <svg class="w-4 h-4 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="text-slate-600 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400">
                            {{ $crumb['label'] }}
                        </a>
                    @else
                        <span class="text-slate-900 dark:text-slate-100 font-medium">{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
    @endif

    <!-- Title and Actions -->
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-page-title text-slate-900 dark:text-slate-50">{{ $title }}</h1>
            @if($subtitle)
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>
        
        @if(isset($actions))
            <div class="flex items-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
