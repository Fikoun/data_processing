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


        <button class="btn nohover btn-outline-primary" id="toggleTemp">Temperature <span id="live_temp">  </span>
        </button>
        <button class="btn nohover btn-outline-danger" id="toggleCurr">Frequency <span id="live_volt">  </span> </button>
    </div>
  </div>

   
	<div class="tab-content" style="margin-top: 50px">
    <div class="tab-pane fade show active" id="pills-graph" role="tabpanel" aria-labelledby="pills-graph-tab">
        @include('measurements.plot')
        @guest
        @else

        <h2 class="mt-5">Control Panel</h2>

        <div class="row">
          
          
          <fieldset class="col-md-6 border p-4 text-center m-5">
             <legend  class="w-auto">Current</legend>

             <input class="form-control w-auto d-inline-block" type="number" id="voltage-num" value="0">  <input class="btn btn-success  d-inline-block" type="button" id="voltage-num" value="SET">

              <div class="slidecontainer px-5 my-4">
                <input type="range" min="0" max="5" value="0" class="slider" id="voltage">
             </div>
          </fieldset>
        </div>
       
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
 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>


var loading = true;
var data = {!! $data !!};

if (data.length == 1)
    data.push([0,0,0])

console.log(data)

var options = {
  title: '{{ $measurement->title }}',
  curveType: 'line',
  legend: { position: 'bottom' },
  colors: ["#2e57d1"]       
};
var chart = null;

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
  data = google.visualization.arrayToDataTable( data );

  view = new google.visualization.DataView(data);
  if (isCurr)
      view.hideColumns([2]);
  else
      view.hideColumns([1]);

  chart = new google.visualization.LineChart(document.getElementById('plot'));

  chart.draw(view, options);
}
var isTemp = true, isCurr = true;

var toggleCurr = document.getElementById("toggleCurr");
var toggleTemp = document.getElementById("toggleTemp");

toggleCurr.onclick = function()
{
  if (isTemp) {
    view = new google.visualization.DataView(data);
    view.hideColumns([2]);

    toggleCurr.classList.remove("btn-outline-danger");
    toggleCurr.classList.add("btn-danger");

    options.colors = ["#eb4034"];
    
    isTemp = false

    ////

    toggleTemp.classList.remove("btn-primary");
    toggleTemp.classList.add("btn-outline-primary");

    isCurr = true
    
    chart.draw(view, options);
  }
}


toggleTemp.onclick = function()
{
  if (isCurr) {
    view = new google.visualization.DataView(data);
    view.hideColumns([1]); 

    toggleTemp.classList.remove("btn-outline-primary");
    toggleTemp.classList.add("btn-primary");

    options.colors = ["#2e57d1"];

    ////

    toggleCurr.classList.remove("btn-danger");
    toggleCurr.classList.add("btn-outline-danger");

    isTemp = true

    isCurr = false
    chart.draw(view, options);
  }
  
}


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
// Plotly.newPlot('plot', data, layout, { scrollZoom: true, responsive: true });

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
    data = JSON.parse(data.data)

    data = google.visualization.arrayToDataTable( data );

    view = new google.visualization.DataView(data);
    if (isCurr)
      view.hideColumns([2]);
    else
      view.hideColumns([1]);

    chart.draw(view, options);
    
    updateStatus(100);

    console.log(data)
    
    if (loading)
      setTimeout(updatePlot, 1000);


    @guest
    @else
      updateVolt(slider.value);
    @endguest

  });
}


function updateVolt(volt) {
  $.get("{{ route('ajax_update_volt', $measurement->id) }}", {volt: volt},
  function(data){
    console.log(data);
  });
}

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
      $.ajax({ url: "/start/{{ $measurement['id'] }}/{{ $measurement['duration'] }}" })
    break;
    case 'stop':
      $.ajax({ url: "/stop" })
    break;
    case 'restart':
      $.ajax({ url: "/stop" })

    break;
  }
  updateStatus(1000);
  updatePlot();
}

drawChart();


</script>
@endsection