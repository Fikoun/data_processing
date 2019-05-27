<div class="container">
	<h2>Measurement Settings</h2>
    <div class="row justify-content-center">
    	
	    <div class="col">
	       <form action="{{ route('update_measurement', $measurement->id) }}" method="post">
	       		{{ method_field('PATCH') }}
	       		{{ csrf_field() }}
	       		
				<div class="form-group">
				    <label for="title">Title</label>
				    <input name="title" type="text" class="form-control" placeholder="Measurement_1" value="{{ $measurement->title }}">
				  </div>
				  <div class="form-group">
				    <label for="description">Description</label>
				    <textarea name="desc" class="form-control" placeholder="Description..." >{{ $measurement->desc }}</textarea>
				  </div>
				  <button type="submit" class="btn btn-primary">Save</button>       		
				  <a href="{{ route('delete_measurement', $measurement->id) }}" class="btn btn-danger">Delete</a>       		
	       </form>
	    </div>
    </div>
</div>