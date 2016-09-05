@extends ('base')

@section ('scripts')
    <script type="text/javascript" src="{{ asset('/js/api.js') }}"></script>
@stop

@section('subheader')
    <div class="col-xs-12 col-md-4 col-md-offset-4">
        <h2>@lang('app.demo.title')</h2>
    </div>
@stop

@section ('content')
    @if ($isError)
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <div class="alert alert-danger">
                    Whoops! Something went terribly wrong here!
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-xs-12 col-md-4">
            @include('transfugio::api.sidebar')
        </div>
        <div class="col-xs-12 col-md-8">
            @include('transfugio::api.main')
        </div>
    </div>

    @if (isset($paginationCode))
    <div class="row">
        <div class="col-xs-12">
            <div class="text-center">
                {!! $paginationCode or '' !!}
            </div>
        </div>
    </div>
    @endif
@stop
