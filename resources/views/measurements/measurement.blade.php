@extends('main')

@section('content')
<div class="container-fluid">
	<ul class="nav nav-pills mb-3">
	  <li class="nav-item">
	    <a class="nav-link active" data-toggle="pill" href="#pills-graph" role="tab" aria-controls="pills-graph" aria-selected="true">Graph</a>
	  </li>
	  <li class="nav-item">
	    <a class="nav-link" data-toggle="pill" href="#pills-settings" role="tab" aria-controls="pills-settings" aria-selected="false">Settings</a>
	  </li>
	  <li class="nav-item">
	    <a class="nav-link" data-toggle="pill" href="#pills-export" role="tab" aria-controls="pills-export" aria-selected="false">Export</a>
	  </li>
	</ul>
	<div class="tab-content" style="margin-top: 50px">
    <div class="tab-pane fade show active" id="pills-graph" role="tabpanel" aria-labelledby="pills-graph-tab">
        @include('measurements.plot')
        <div class="slidecontainer px-5">
          <input type="range" min="0" max="10" value="5" class="slider" id="voltage">
        </div>
        <h2 class="text-center py-4">
          Voltage <span class="text-danger" id="voltage-display"></span>
        </h2>
    </div>
    <div class="tab-pane fade" id="pills-settings" role="tabpanel" aria-labelledby="pills-settings-tab">
    		@include('measurements.edit')
    </div>
	  <div class="tab-pane fade" id="pills-export" role="tabpanel" aria-labelledby="pills-export-tab">
	  		@include('measurements.export')
	  </div>
	</div>	
</div>

<script>

var temperature = {
  x: {!! $dataTemp['x'] !!},
  y: {!! $dataTemp['y'] !!},
  name: 'Temperature',
  type: 'scatter'
};
var layer = {
  x: {!! $dataLayer['x'] !!},
  y: {!! $dataLayer['y'] !!},
  name: 'Layer',
  yaxis: 'y2',
  type: 'scatter'
};
var pressure = {
  x: {!! $dataPress['x'] !!},
  y: {!! $dataPress['y'] !!},
  name: 'Pressure',
  yaxis: 'y3',
  type: 'scatter',
  line: {
      color: '#d62728',
    }
};
var data = [temperature, layer, pressure];

var layout = {
  title: "{{ $measurement->title }}",
  showlegend: false,
  xaxis: {domain: [0.1, 1]},
  yaxis: {
    title: 'Temperature',
    titlefont: {color: '#1f77b4'},
    tickfont: {color: '#1f77b4'}
  },
  yaxis2: {
    title: 'Layer',
    titlefont: {color: '#ff7f0e'},
    tickfont: {color: '#ff7f0e'},
    anchor: 'free',
    overlaying: 'y',
    side: 'left',
    position: 0
  },
  yaxis3: {
    title: 'Pressure',
    titlefont: {color: '#d62728'},
    tickfont: {color: '#d62728'},
    anchor: 'x',
    overlaying: 'y',
    side: 'right',
  }
};
Plotly.newPlot('plot', data, layout, { scrollZoom: true, responsive: true });


var slider = document.getElementById("voltage");
var output = document.getElementById("voltage-display");
output.innerHTML = slider.value; // Display the default slider value

// Update the current slider value (each time you drag the slider handle)
slider.oninput = function() {
  output.innerHTML = this.value;
  $.ajax({
     type:'POST',
     url:'/set/voltage',
     data: {
        _token : '<?php echo csrf_token() ?>',
        value : this.value
     },
     success:function() {}
  });
} 


</script>
@endsection