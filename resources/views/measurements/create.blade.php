@extends('main')

@section('content')
<div class="container">
	<h1>Create Measurement</h1>
    <div class="row justify-content-center">
	    <div class="col">
	       <form action="{{ route('store_measurement') }}" method="post" enctype="multipart/form-data">
	       		{{ csrf_field() }}
	       		
				<div class="form-group">
				    <label for="title">Title</label>
				    <input name="title" type="text" class="form-control" placeholder="Measurement_1">
				</div>

				<div class="form-group">
				    <label for="title">Presets</label>
				    <select class="form-control" name="preset">
				    	<option>-</option>
				      <option> Temperature & Voltage </option>
				      <option>MOTES preasure chamber</option>
				      <option>X-Band epr</option>
				    </select>
				 </div>

				 <div class="form-group">
				    <label for="duration">Duration</label>
				    <input name="duration" type="number" class="form-control" placeholder="seconds">
				</div>


				<div class="form-group">
				    <label for="description">Description</label>
				    <textarea name="desc" class="form-control" placeholder="Description..."></textarea>
				</div>

				<div class="form-group">
				    <label for="description">Import data</label> <br>
				    <input type="file" name="import_file">
				</div>
				   
				  <button type="submit" class="btn btn-primary">Create</button>       		
	       </form>
	    </div>
    </div>
</div>
@endsection
