<div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
        @isset($breadcrumbs)
            @foreach ($breadcrumbs as $name =>$link)
                @if ($loop->last)
                    <li class="breadcrumb-item active">{{ $name }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $link }}">{{ $name }}</a></li>
                @endif
            @endforeach
        @endisset
    </ol>
</div>
