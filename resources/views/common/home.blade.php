@extends('main')

@section('content')
<div class="container">
    <h1>About</h1>
   	<p>
   		The MOTeS Data Processing utility is being developed as a part of a project between CEITEC BUT and a talented high-shool student, namely Filip Janko. 
   		The aim is to develop a user-friendly online utility to monitor output physical quantities
   		such as temperature, evaporation rate, film thickness, and pressure from MOTeS Evaporation Chamber in real-time. 
   	</p>
	<h1>Application</h1>

	<div class="row justify-content-center align-items-center py-5">
    	<div class="col my-3" >
			<div class="card text-center bg-light card ">
			  <h5 class="card-header">Voltage-Temperature</h5>
			  <div class="card-body">
			    <img src="{{{ asset('temp.png') }}}" height="200px"> <br>
			    	<a href="{{ route('measurement_last') }}" class="btn btn-success my-3">Open measurement</a>
			  </div>
			</div>
		</div>
		<div class="col my-3" >
			<div class="card text-center bg-light card ">
			  <h5 class="card-header">Evaporation Chamber</h5>
			  <div class="card-body">
			    <img src="{{{ asset('chamber.jpg') }}}" height="200px"> <br>
			    <a href="#" class="btn btn-secondary my-3">comming soon ..</a>
			  </div>
			</div>
		</div>
	</div>

    {{-- <div class="row justify-content-center align-items-center py-5">
    	<div class="col my-3" >
			<div class="card text-center bg-light card ">
			  <h5 class="card-header">Measurements</h5>
			  <div class="card-body">
			    <h5 class="card-title">Login and open list of measurements</h5>
			    <p class="card-text">Monitor, edit, manage and create new measurements</p>
			    @guest
			    	<a href="{{ route('login') }}" class="btn btn-primary">Login</a>
			    @else
					<a href="{{ route('measurements') }}" class="btn btn-success">Open measurements</a>
			    @endguest
			  </div>
			</div>
		</div>
		<div class="col my-3" >
			<div class="card text-center bg-light card ">
			  <h5 class="card-header">Register</h5>
			  <div class="card-body">
			    <h5 class="card-title">Create new user</h5>
			    <p class="card-text">Monitor, edit, manage and create new measurements</p>
			    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
			  </div>
			</div>
		</div>
	</div> --}}


    <h1>Team</h1>
    <div class="row justify-content-center align-items-center">
    	<div class="col my-3" >
			<div class="card mx-auto" style="width: 300px">
			  <img src="http://spectroscopy.ceitec.cz/files/273/129.jpg" class="card-img-top" alt="..." width="200px">
			  <div class="card-body">
			  	<h3>Jakub Hrubý</h3>
			    <p class="card-text">Researcher and project leader, Ph.D. Student</p>
			  </div>
			</div>
		</div>
		<div class="col my-3" >
			<div class="card mx-auto" style="width: 320px">
			  <img src="http://spectroscopy.ceitec.cz/files/273/192.jpg" class="card-img-top" alt="..." width="200px">
			  <div class="card-body">
			    <h3>Filip Janko</h3>
			    <p class="card-text">Developer, High School Student</p>
			  </div>
			</div>
		</div>
	</div>    
</div>

@endsection
