<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $slug }} by {{ $name }}</title>

    <style>

    html, body {
        padding: 0; margin: 0;
        background: seashell;
        text-align: center;
    }

    svg {
        shape-rendering: crispEdges;
        margin: 10px auto;
        border-radius: 5px;
        box-shadow: 0 0 5px -2px;
    }

    path {
        fill: none;
        stroke-linejoin: round;
        stroke-linecap: round;
    }

    circle.nib {
        pointer-events: none;
    }
    circle.palette-circle {
        fill-opacity: 0.25;
    }
    circle.palette-circle:hover {
        fill-opacity: 1;
    }

    #metadata {
        font-family: monospace;
        position: fixed;
        bottom: 10px;
        right: 10px;
    }

    </style>

</head>
<body>

@yield("content")

<script src="http://d3js.org/d3.v4.min.js"></script>
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
<script src="{{ asset('js/bucket.js') }}"></script>

</body>
</html>
