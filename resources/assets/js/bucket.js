const palette = [
    [255,171,171],
    [255,218,171],
    [221,255,171],
    [171,228,255],
    [217,171,255],
    [27,27,27],
    [255,255,255],
    [248,236,201],
    [175,220,200],
    [240,135,129]
];

var strokeWidth = 4;
var strokeRgb = palette[5];
var strokeAlpha = 0.75;

var increment = 0;
var active;

var line = d3.line()
    .curve(d3.curveBasis);

var svg = d3.select('svg')
    .attr('viewBox' , `0 0 ${window.innerWidth} ${window.innerHeight}`)
    .attr('initwidth' , window.innerWidth)
    .attr('initheight' , window.innerHeight);

var canvas = svg.append('g');

affixLines();

if(!bucketdata.editable)
    liveFetch();

if(bucketdata.editable) {

    var nib = svg.append('circle')
                            .attr('class', 'nib')
                            .attr('cy', 0)
                            .attr('cx', 0)
                            .attr('r', strokeWidth)
                            .attr('fill', `rgba(${strokeRgb[0]}, ${strokeRgb[1]}, ${strokeRgb[2]}, ${strokeAlpha-0.2})`)
                            .attr('stroke', `rgb(${strokeRgb[0]}, ${strokeRgb[1]}, ${strokeRgb[2]})`);

    var pci = 0;
    var paletteCircles = svg.append('g').selectAll('circle')
                                    .data(palette).enter().append('circle')
                                    .attr('class', 'palette-circle')
                                    .attr('cy', function(){ return ++pci%2==0 ? 20 : 30;})
                                    .attr('cx', function(){ return pci--*20;})
                                    .attr('r', 10)
                                    .attr('stroke', 'black')
                                    .attr('fill', function(d) { return `rgb(${d[0]}, ${d[1]}, ${d[2]})`})
                                    .on('click', function(d) {
                                    strokeRgb = d; recolourNib();
                                    });

    svg.call(d3.drag()
    .container(function() { return this; })
    .subject(function() { var p = [d3.event.x, d3.event.y, strokeWidth, strokeRgb, strokeAlpha]; return [p, p]; })
    .on('start', dragstarted)
    .on('end', function(){active = null; makeDrawing(); }))
    .on('mousemove', alignMouseNib);

    document.onkeydown = checkKey;

}

function checkKey(e) {
    e = e || window.event;
    if (e.keyCode == '38') {
        // up arrow
        strokeWidth+=5;
        resizeNib();
    }
    else if (e.keyCode == '40') {
        // down arrow
        strokeWidth-=5;
        resizeNib();
    }
    else if (e.keyCode == '37') {
       // left arrow
       strokeAlpha-=0.05;
       if(strokeAlpha<0.1) strokeAlpha = 0.1;
    }
    else if (e.keyCode == '39') {
       // right arrow
       strokeAlpha+=0.05;
       if(strokeAlpha>1) strokeAlpha = 1;
    }
    else if (e.keyCode == '27') {
        // escape key
        canvas.select(`#line_${increment--}`).remove();
        makeDrawing();
    }
}


function alignMouseNib(){
  var coordinates = d3.mouse(this);
  nib
    .attr('cx', coordinates[0])
    .attr('cy', coordinates[1]);
}

function resizeNib(){
    if(strokeWidth < 0) strokeWidth=1;
    nib.transition().duration(500).attr('r', strokeWidth);
    if(active)
        active.transition().duration(500).attr('stroke-width', strokeWidth);
}

function recolourNib(){
    nib.attr('fill', `rgba(${strokeRgb[0]}, ${strokeRgb[1]}, ${strokeRgb[2]}, ${strokeAlpha}`);
    nib.attr('stroke', `rgba(${strokeRgb[0]}, ${strokeRgb[1]}, ${strokeRgb[2]}, ${strokeAlpha}`);
}

function dragstarted() {

    
  var d = d3.event.subject,
      x0 = d3.event.x,
      y0 = d3.event.y;
    
active = canvas.append('path').datum(d);

active.attr('id', `line_${++increment}`);

  d3.event.on('drag', function() {
    var x1 = d3.event.x,
        y1 = d3.event.y,
        dx = x1 - x0,
        dy = y1 - y0;

    nib
        .attr('cx', d3.event.x)
        .attr('cy', d3.event.y);

    if (dx * dx + dy * dy > 10) d.push([x0 = x1, y0 = y1, strokeWidth, strokeRgb, strokeAlpha]);
    else d[d.length - 1] = [x1, y1, strokeWidth, strokeRgb, strokeAlpha];

    active.attr('d', line)
            .attr('stroke-width', strokeWidth*2)
            .attr('stroke', `rgba(${strokeRgb[0]}, ${strokeRgb[1]}, ${strokeRgb[2]}, ${strokeAlpha}`);
  });
}

function affixLines() {
    bucketdata.lines.forEach(d => {

        l = canvas.append('path').datum(d);
        dt = d[0];

        l.attr('d', line)
        .attr('stroke-width', dt[2]*2)
        .attr('stroke', `rgba(${dt[3][0]}, ${dt[3][1]}, ${dt[3][2]}, ${dt[4]}`);
    });
}

function makeDrawing() {

    bucketdata.lines = svg.selectAll('path').data();
    bucketdata.width = window.innerWidth;
    bucketdata.height = window.innerHeight;

    bucketdata.lines = bucketdata.lines.map( l => {

        return l.map( (d, i) => {
            if(i===0) return d;
            else return [d[0], d[1]];
        });

    });

    //console.log(bucketdata);

    fetch('/!make-drawing', {
            method: 'POST',
            body: JSON.stringify(bucketdata),
            credentials: 'same-origin',
            headers: new Headers({
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': bucketdata._token
            })
        }).then(res => res.json())
        .catch(error => console.error('Error:', error))
        .then(response => {

            if(response.error){
                console.log('Error:', response.error);
                return;
            }
            
            bucketdata = response;
            document.querySelector('#metadata .saved').textContent = (Date(response.saved)).toLocaleString();

        });
}

window.toggleHidden = function(target) {

    document.querySelectorAll('.modal')
    .forEach(function(el){ 
        if(('#'+el.id) != target)
            el.classList.add('hidden')
    });

    target  = document.querySelector(target);
    target.classList.toggle('hidden');
}

function liveFetch() {
    setTimeout(function () {

        fetch('/!fetch-drawing', {
            method: 'POST',
            body: JSON.stringify(bucketdata),
            credentials: 'same-origin',
            headers: new Headers({
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': bucketdata._token
            })
        }).then(res => res.json())
        .catch(error => console.error('Error:', error))
        .then(response => {

            if(response.error){
                console.log('Error:', response.error);
                return;
            }
            
            let newLines = JSON.parse(response.lines);
            if(bucketdata.lines != newLines)
                bucketdata.lines = newLines;
            
            affixLines();
            liveFetch();

        });

    }, 10000);
}