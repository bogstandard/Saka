@extends('layouts.frame')

@section('content')
<svg width="960" height="500">
  <rect fill="#fff" width="100%" height="100%"></rect>
</svg>


<div id="metadata">
    <b>{{ $editable ? 'Editing' : ''}}</b> <span class="slug">/{{ $slug }}/</span> <b>{{ $editable ? 'As' : 'By'}}</b> 
        <span class="name">{{ $name }}</span><span class="trip">{{ $trip }}</span>
    <br>
    <b>Saved:</b> <span class="saved">{{ $drawing ? $drawing->updated_at->diffForHumans() : 'Never' }}</span>
</div>

@endsection
