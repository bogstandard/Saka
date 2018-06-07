@extends('layouts.frame')

@section('content')


<svg preserveAspectRatio="xMinYMin"></svg>


<div id="metadata">
    <b>{{ $editable ? 'Editing' : ''}}</b> <span class="slug">/{{ $slug }}/</span> <b>{{ $editable ? 'as' : 'by'}}</b> 
        <span class="name">{{ $name }}</span><span class="trip">{{ $trip }}</span>
    <br>
    <b>Saved:</b> <span class="saved">{{ $drawing ? $drawing->updated_at->diffForHumans() : 'Never' }}</span>
</div>

<div id="links">

    <a href="#share" title="Share your doodle" onclick="toggleHidden('#share')">Share Doodle</a>
    <a href="/" title="Start a new doodle">New</a>
    <a href="#about" title="Learn about Saka" onclick="toggleHidden('#about')">About</a>
    <a href="#help" title="Get Help!" onclick="toggleHidden('#help')">Help</a>

</div>

<div class="modal hidden" id="help">

    <a class="btn" style="float: right;" 
        href="#close" title="Close this message!"
        onclick="toggleHidden('#help')">Close</a>

    <b>Get doodling</b>
    <p>
        Click <b>new</b> or go to <a href="//saka.ericdaddio.co.uk">saka.ericdaddio.co.uk</a> to get a blank canvas. 
        <br> 
        Use your mouse or touchscreen to drag anywhere on the page to draw. 
        Change colours using the controls in the top left. 
    </p>
    <br>
    <b>Keyboard Controls</b>
    <p>
        &#x2B06; bigger brush 
        &#x2B07; smaller brush <br>
        &#x2B05; more see through 
        &#x27A1; less see through
    </p>

    <br>

    <b>Share your doodle or keep it private</b>
    <p>
        Doodles can only be seen by others if you link them! <br> 
        people you share the link with cannot edit your doodle but can 
        watch you make the art live or come back later to see your progress!
    </p>
    <p>
        <i>Be quick doodles are deleted after 7 days!</i>
    </p>
</div>


<div class="modal hidden" id="about">

<a class="btn" style="float: right;" 
    href="#close" title="Close this message!"
    onclick="toggleHidden('#about')">Close</a>


<img src="/img/logo.png" title="" style="max-width: 20%; margin: 30px; float: left;"/>
<b>Doodles and Doodles of fun!</b>
<p>
    Doodle and share privately live to friends anonymously.<br><br>
    Doodles get deleted after 7 days &amp; remain private until you 
    share the link.
</p>
<p>
    <i>Created by <a href="http://ericdaddio.co.uk">Eric D'Addio</a> for fun</i>
</p>
</div>

<div class="modal hidden" id="share">

<a class="btn" style="float: right;" 
    href="#close" title="Close this message!"
    onclick="toggleHidden('#share')">Close</a>

<b>Sharable Link to Your Doodle</b>
<p>
    Share this with friends &amp; they can even watch live
</p>
<div class="gutter" style="text-align: center;">
    <input type="text" 
    style="font-size: 2em; width: 100%; text-align: center;"
    value="http://saka.ericdaddio.co.uk/{{ $slug }}"/>
</div>

</div>

@endsection
