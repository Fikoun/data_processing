@extends('main')

@section('content')
<div class="container">

	<ul class="navbar-nav ml-auto">
		 <li class="nav-item dropdown">
              	<button id="status-button" type="button" class="btn btn-{{ $server['color'] }} nav-link dropdown-toggle px-3" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				   Status:  <span class="badge badge-light p-1"> <span id="status-value">{{ $server['status'] }}</span> 

							<div id="loading" class="spinner-border spinner-border-sm" role="status" style="display: none">
							  <span class="sr-only">Loading...</span>
							</div>
				   </span>
				</button>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                  <button class="dropdown-item text-success" onclick="serverCommand('start')">
                  	Start
                  </button>
                  <button class="dropdown-item text-danger" onclick="serverCommand('stop')">
                  	Stop
                  </button>
                  <button class="dropdown-item text-info" onclick="serverCommand('restart')">
                  	Restart
                  </button>
              </div>
          </li>
	</ul>
	
    <div class="row justify-content-around">
	        @foreach($measurements as $measurement)
		        <div class="col-sm-6 col-md-6 col-lg-4 py-4 px-4">
					<div class="card text-center">
					  <div class="card-body">
					    <h5 class="card-title">{{ $measurement->title }}</h5>
					    <p class="card-text">{{ $measurement->desc }}</p>
					    <a  href="{{ route('measurement', $measurement->id) }}" class="btn btn-primary">Open measurement</a>
					  </div>
					  <div class="card-footer text-muted">
					    Last data: 1 min
					  </div>
					</div>
				</div>		 
	        @endforeach
		        <div class="col-sm-6 col-md-6 col-lg-4 py-4 px-4">
		        <div class="card" style="top: 15%">
				  <div class="card-body text-center">
				    <h5 class="card-title">New Measurement</h5>
	    			<a href="{{ route('create_measurement') }}" class="btn btn-success">Create</a>
				  </div>
				</div>
			</div>   
    </div>
</div>

<script type="text/javascript">
var url_base = "http://data.processing/"

function changeStatus($status) {
	$('#status-button').removeClass('btn-success');
	$('#status-button').removeClass('btn-secondary');
	/// others...

	switch($status){
		case true:
			$('#status-button').addClass('btn-success');
		break;

		case false:
			$('#status-button').addClass('btn-secondary');
		break;
	}
	$('#status-value').html($status ? 'Running...' : 'Not Running!');
	
	$('#loading').hide();
}

function updateStatus(delay) {
	setTimeout(function() {
		$.ajax({ url: url_base + "server/status" }).done(function( data ) {
			console.log(data);
			  changeStatus(data['status'])
		});
	}, 2000);
}
	

function serverCommand(command) {
	$('#loading').show();
	switch(command) {
		case 'start':
			$.ajax({ url: url_base + "server/start" })
		break;
		case 'stop':
			$.ajax({ url: url_base + "server/stop" })
		break;
		case 'restart':
			
		break;
	}
	updateStatus();
}


</script>
@endsection
