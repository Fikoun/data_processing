@extends('main')

@section('content')
<div class="container">
	
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
@endsection
