<style>
    .breadcrumb{
        margin-top: 5px !important;
    }

    .breadcrumb-item+.breadcrumb-item::before{
        content: ">" !important;
        font-family: CalibriI !important;
        font-weight: normal !important;
    }

    .breadcrumb-item.active{
        color: rgb(0,87,95) !important;
        font-family: CalibriB !important;
    }

    .breadcrumb-item:not(.active) a{
        color: #6c757d !important;
        font-family: CalibriI !important;
        font-weight: normal !important;
    }

    .breadcrumb-item:not(.active):hover a{
        color: rgb(0,87,95) !important;
    }


    @media screen and (max-width: 992px) {
        .breadcrumb{
            margin-top: 50px !important;
        }
    }
</style>

@unless ($breadcrumbs->isEmpty())
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb->url && !$loop->last)
                    <li class="breadcrumb-item"><a style="" href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                @else
                    <li style="font-family: CalibriI; font-weight: normal;" class="breadcrumb-item active" >{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ol>
    </nav>
@endunless
