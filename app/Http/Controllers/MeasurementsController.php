<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Measurement;
use App\Python;
use App\Data;
use Illuminate\Support\Facades\Storage;

class MeasurementsController extends Controller
{
    public function list()
    {
    	$all = Measurement::all();
        $server = Python::server_status();

        $status = $server['status'];
  
        switch ($status) {
            case true:
                $server = ['color' => 'success', 'message' => 'Running...'];
                break;
            case false:
                $server = ['color' => 'secondary', 'message' => 'Not Running!'];
                break;
        }

        $server['status'] = $status ? "true" : "false";

    	return view('measurements.list', ['measurements' => $all, 'server' => $server]);
    }  

    private function composeData($results, $data)
    {
    	$size = count($results);
        if (!empty($data))
            $size = count($data);

        for ($i = 0; $i < $size; $i++) {
            if (!isset($results[$i]))
                $data[$i][] = 0;
            else if (isset($data[$i])){
                if (doubleval($data[$i][0]) != doubleval(date("i:s", strtotime($results[$i]->created_at)))){

                   dd([$data[$i], date("i:s", strtotime($results[$i]->created_at))]);
                }
               
                $data[$i][] = $results[$i]->value;

            } else{

               $data[$i] = [ date("i:s", strtotime($results[$i]->created_at)),  $results[$i]->value];
            }
    	}
        
    	return $data;
    }

    private function insertRandomData($id)
    {
        $time = date('Y-m-d H:i:s');
        $new_data = new Data;
        $new_data->measurement_id = $id;
        $new_data->type = 'temp';
        $new_data->value = rand(24,30);
        date_default_timezone_set('Europe/Prague');
        $new_data->created_at = $time;
        $new_data->updated_at = $time;
        $new_data->save();

        $new_data = new Data;
        $new_data->measurement_id = $id;
        $new_data->type = 'volt';
        $new_data->value = 0;
        $new_data->created_at = $time;
        $new_data->updated_at = $time;
        $new_data->save();
    }

    public function status($id)
    {
        $measurement = Measurement::find($id);
        return response()->json($measurement);
    }

    public function ajaxUpdate($id)
    {
        $measurement = Measurement::find($id);
               
        //$this->insertRandomData($id);

        $data = $this->composeData($measurement->temp, $this->composeData($measurement->press, []));
        array_unshift($data, ['Time', 'Frequency', 'Temperature'] );
        
        return ["data" => json_encode($data)];
              
    }

    public function ajaxUpdateVolt($id)
    {
        $measurement = Measurement::find($id);
        if ($measurement->temp->count()) {
            $last = $measurement->temp->last();
            $time = $last->created_at;

            $new_data = new Data;
            $new_data->measurement_id = $id;
            $new_data->type = 'volt';
            $new_data->value = request('volt');
            $new_data->created_at = $time;
            $new_data->updated_at = $time;
            $new_data->save();
        }
        
        //$this->insertRandomData($id);
        
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
        return $this->show($measurement_id);
    }

    public function show($id)
    {
    	$measurement = Measurement::find($id);
        
        $status = $measurement['status'];
        switch ($status)
        {
            case 'running':
                $measurement['status'] = ['color' => 'success', 'message' => 'Running...', 'status'=>$status];
                break;
            default:
                $measurement['status'] = ['color' => 'secondary', 'message' => 'Not Running', 'status'=>$status];
                break;
        }

        $data = $this->composeData($measurement->temp, $this->composeData($measurement->press, []));
        array_unshift($data, ['Time', 'Frequency', 'Temperature'] );

    	return view('measurements.measurement', [
    		"data" => json_encode($data),
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
        $measurement->duration = request('duration');   
    	$measurement->status = 'new';	
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
        
    	return redirect("/measurement/" . $measurement->id);
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
