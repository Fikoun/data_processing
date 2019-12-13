@extends('main')

@section('content')
<div class="container-fluid">
  <div>
  	<ul class="nav nav-pills mr-auto">
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

    <div class="float-right">
       <ul class="navbar-nav d-inline-block">
           <li class="nav-item dropdown">
                      <button id="status-button" type="button" class="btn btn-{{ $measurement['status']['color'] }} nav-link dropdown-toggle px-3" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 Measurement:  <span class="badge badge-light p-1"> <span id="status-value">{{ $measurement['status']['message'] }}</span> 

                    <div id="loading" class="spinner-border spinner-border-sm" role="status" style="display: none">
                      <span class="sr-only">Loading...</span>
                    </div>
                 </span>
              </button>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <button class="dropdown-item text-success" onclick="serverCommand('start')">
                          Start
                        </button>
                    </div>
                </li>
        </ul>


        <button class="btn nohover btn-outline-primary">Temperature: <span id="live_temp">  </span> °C
        </button>
        <button class="btn nohover btn-outline-danger">Voltage: <span id="live_volt">  </span> V</button>
    </div>
  </div>

   
	<div class="tab-content" style="margin-top: 50px">
    <div class="tab-pane fade show active" id="pills-graph" role="tabpanel" aria-labelledby="pills-graph-tab">
        @include('measurements.plot')
        @guest
        @else
          <div class="slidecontainer px-5">
            <input type="range" min="0" max="5" value="0" class="slider" id="voltage">
          </div>
        
        <h2 class="text-center py-4">
          Voltage <span class="text-danger" id="voltage-display"></span>
        </h2>
        @endguest
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
  xaxis: {domain: [0, 1]},
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
var live_temp = document.getElementById("live_temp");
var live_volt = document.getElementById("live_volt");
output.innerHTML = slider.value;

// SLIDER handler
var setting = true;
slider.oninput = function() {
  if (setting) {
      setting = false;
      if (this.value > 1) {
        var val = 5;
      }else{
        var val = 0;
      }
      output.innerHTML = this.value;
      $.ajax({
         type:'POST',
         url:'/set/voltage',
         data: {
            _token : '<?php echo csrf_token() ?>',
            value : val
         },
         success: function() {
            setting = true;
         }
      });
  }
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
    output.innerHTML = slider.value;
    live_temp.innerText = temperature.y[temperature.y.length - 1];
    live_volt.innerText = voltage.y[voltage.y.length - 1];
    
    updateStatus(100);
    
    if (loading)
      setTimeout(updatePlot, 1000);


    @guest
    @else
      updateVolt(slider.value);
    @endguest

  });
}
setTimeout(updatePlot, 1000);

function updateVolt(volt) {
  $.get("{{ route('ajax_update_volt', $measurement->id) }}", {volt: volt},
  function(data){
    console.log(data);
  });
}

var loading = true;
function stop() {
  if (loading) {
    loading = false;
    document.getElementById('stop').innerText = "Start"
  }else{
    loading = true;
    document.getElementById('stop').innerText = "Stop"
    setTimeout(updatePlot, 10);
  }
  
}

live_temp.innerText = temperature.y[temperature.y.length - 1];
live_volt.innerText = voltage.y[voltage.y.length - 1];
</script>




<script type="text/javascript">

var server_status = {{ $measurement['status']['status']=="running" ? "true" : "false" }};
var restart = false;

function changeStatus($status) {
  $('#status-button').removeClass('btn-success');
  $('#status-button').removeClass('btn-secondary');
  /// others...

  switch($status){
    case true:
      $('#status-button').addClass('btn-success');

      if (!server_status) 
        server_status = true;
      //else
        //updateStatus(500)
    break;

    case false:
      $('#status-button').addClass('btn-secondary');
      
      if (server_status) 
        server_status = false;
      //else
        //updateStatus(500)
    break;
  }
  $('#status-value').html($status ? 'Running...' : 'Not Running!');
  
  $('#loading').hide();
}

function updateStatus(delay) {
  setTimeout(function() {
    $.ajax({ url:  "/status/{{ $measurement['id'] }}" }).done(function( data ) {
      console.log(data['status']);

      switch(data['status']){
          case "running":
            changeStatus(true)
          break;
          default:
            changeStatus(false)
      }
      
    });
  }, delay);
}
  

function serverCommand(command) {
  $('#loading').show();
  switch(command) {
    case 'start':
      $.ajax({ url: "/start/{{ $measurement['id'] }}/3" })
    break;
    case 'stop':
      $.ajax({ url: "/stop" })
    break;
    case 'restart':
      $.ajax({ url: "/stop" })

    break;
  }
  updateStatus(1000);
}


</script>
@endsection