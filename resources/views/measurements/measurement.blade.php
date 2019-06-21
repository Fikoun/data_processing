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
  name: 'Temperature (°C)',
  type: 'scatter'
};
var voltage = {
  x: {!! $dataVolt['x'] !!},
  y: {!! $dataVolt['y'] !!},
  name: 'Voltage (V)',
  yaxis: 'y2',
  type: 'scatter',
  line: {
    color: '#d62728',
  }
};

var data = [temperature, voltage];
console.log(data);
var layout = {
  title: "{{ $measurement->title }}",
  showlegend: false,
  xaxis: {domain: [0.1, 1]},
  yaxis: {
    title: 'Temperature (°C)',
    titlefont: {color: '#1f77b4'},
    tickfont: {color: '#1f77b4'}
  },
  yaxis2: {
    title: 'Voltage (V)',
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
output.innerHTML = slider.value;

// SLIDER handler
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

// PLOT update handler (AJAX)
function updatePlot() {
  $.get("{{ route('ajax_update', $measurement->id) }}",
  function(data){
    temperature.x = JSON.parse(data.dataTemp.x)
    temperature.y = JSON.parse(data.dataTemp.y)
  
    voltage.x = JSON.parse(data.dataVolt.x)
    voltage.y = JSON.parse(data.dataVolt.y)
  
    data = [temperature, voltage];
    console.log(data);
    Plotly.react('plot', data, layout, { scrollZoom: true, responsive: true });
    setTimeout(updatePlot, 1000);
  });
}
setTimeout(updatePlot, 1000);
</script>
@endsection