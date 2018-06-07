<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $slug }} by {{ $name }}</title>

    <link href="{{ asset('css/all.css') }}" rel="stylesheet" type="text/css">

</head>
<body>

@yield("content")

<script>
/* don't judge my code~~! */
$ = document.querySelector;

var bucketdata = {
    slug: '{{ $slug }}',
    name: '{{ $name }}',
    tripcode: {!! $tripcode ? "'".$tripcode."'" : 'null' !!},
    saved: 'Never',
    _token: '{{ csrf_token() }}',
    editable: {{ $editable ? 'true' : 'false' }},
    lines: {{ $lines }},
    width: {{ !empty($width) ? $width : 'undefined' }},
    height: {{ !empty($height) ? $height : 'undefined' }}
};

</script>

<script src="http://d3js.org/d3.v4.min.js"></script>
<script src="{{ asset('js/bucket.js') }}"></script>



</body>
</html>
