<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Measurement;
use App\Data;
use Illuminate\Support\Facades\Storage;

class MeasurementsController extends Controller
{
    public function list()
    {
    	$all = Measurement::all();

    	return view('measurements.list', ['measurements' => $all]);
    }  

    private function composeData($results)
    {
    	$data = ['x' => [], 'y' => []];
    	foreach ($results as $result) {
    		$data['x'][] = date("H:i:s", strtotime($result->created_at));
    		$data['y'][] = $result->value;
    	}
    	$data['x'] = json_encode($data['x']);
    	$data['y'] = json_encode($data['y']);

    	return $data;
    }

    private function insertRandomData()
    {
        $new_data = new Data;
        $new_data->measurement_id = $id;
        $new_data->type = 'temp';
        $new_data->value = rand(5,25);
        date_default_timezone_set('Europe/Prague');
        $new_data->created_at = date('Y-m-d H:i:s');
        $new_data->updated_at = date('Y-m-d H:i:s');
        $new_data->save();

        $new_data = new Data;
        $new_data->measurement_id = $id;
        $new_data->type = 'volt';
        $new_data->value = rand(160,240);
        $new_data->created_at = date('Y-m-d H:i:s');
        $new_data->updated_at = date('Y-m-d H:i:s');
        $new_data->save();
    }

    public function ajaxUpdate($id)
    {
        $measurement = Measurement::find($id);
        
        // $this->insertRandomData();
        
        return ["dataTemp" => $this->composeData($measurement->temp),
                "dataVolt" => $this->composeData($measurement->volt)];   
    }

    public function last()
    {
        $measurement_id = Measurement::all()->last();
        if ($measurement_id) 
            $measurement_id = $measurement_id->id;
        else
            return redirect("/");
    }

    public function show($id)
    {
    	$measurement = Measurement::find($id);

    	return view('measurements.measurement', [
    		"dataTemp" => $this->composeData($measurement->temp),
    		"dataLayer" => $this->composeData($measurement->layer),
            "dataPress" => $this->composeData($measurement->press),
    		"dataVolt" => $this->composeData($measurement->volt),
    		"measurement" => $measurement]);
    }

    public function create()
    {
    	return view('measurements.create');
    }

    public function store()
    {
    	$measurement = new Measurement;

    	$measurement->title = request('title');	
    	$measurement->desc = request('desc');	
    	$measurement->save();

    	if (request()->file('import_file') !== null) {
    		$path = request()->file('import_file')->store("imports");
    		$lines = explode("\n",Storage::get($path));
    		$type = 0; 
    		foreach ($lines as $line) {
    			$parts = explode("\t", $line);
    			switch ($parts[0]) {
    				case '# Temperature':
    					$type = 1;
    					break;
    				case '# Layer':
    					$type = 2;
    					break;
    				case '# Pressure':
    					$type = 3;
    					break;
                    case '# Voltage':
                        $type = 4;
                        break;
    			}
    			if (count($parts) == 2) {
	    			$d = new Data;
	    			$d->created_at = date("Y-m-d H:i:s", strtotime($parts[0]));
	    			$d->value = $parts[1];
	    			$d->measurement_id = $measurement->id;
	    			switch ($type) {
	    				case 1:
	    					$d->type = "temp";
	    					break;
	    				case 2:
	    					$d->type = "layer";
	    					break;
	    				case 3:
	    					$d->type = "press";
	    					break;
                        case 3:
                            $d->type = "volt";
                            break;
	    			}
	    			$d->save();
	    		}
    		}
    	}
    	return redirect("/measurements");
    }

    public function edit($id)
    {
    	$measurement = Measurement::findOrFail($id);

    	return view('measurements.edit', ['measurement' => $measurement]);
    }

    public function update($id)
    {
    	$measurement = Measurement::findOrFail($id);
		$measurement->title = request('title');	
    	$measurement->desc = request('desc');	
    	$measurement->save();
    	return redirect("/measurement/".$id);
    }
    
    public function delete($id)
    {
    	Measurement::findOrFail($id)->delete();
        return redirect("/measurements");
    }

    public function export($id){
    	$measurement = Measurement::findOrFail($id);
		$data = "# Temperature\n";
		foreach ($measurement->temp as $result) {
			$data .= date("H:i:s", strtotime($result->created_at));
			$data .= "\t" . $result->value . "\n";
		}
		$data .= "\n\n# Layer\n";
		foreach ($measurement->layer as $result) {
			$data .= date("H:i:s", strtotime($result->created_at));
			$data .= "\t" . $result->value . "\n";
		}
		$data .= "\n\n# Pressure\n";
		foreach ($measurement->press as $result) {
			$data .= date("H:i:s", strtotime($result->created_at));
			$data .= "\t" . $result->value . "\n";
		}
        $data .= "\n\n# Voltage\n";
        foreach ($measurement->volt as $result) {
            $data .= date("H:i:s", strtotime($result->created_at));
            $data .= "\t" . $result->value . "\n";
        }
		$fileName = time() . '_data.txt';
		Storage::put('/public/exports/'.$fileName, $data);
		return Storage::download('/public/exports/'.$fileName);
	}
}
