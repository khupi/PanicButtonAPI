<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Panic;
use Config;

class CallAPIController extends Controller
{
    public function apicallFunc(Request $request, $endpoint, $postData)
    {

        $baseurl = Config::get('constants.constants.base_url');
        error_log($baseurl);
        $token = Config::get('constants.constants.access_token');
        error_log($token);

        try
        {

            ////// request
            $headers = ['Accept' => 'application/json', 'Content-type' => 'application/json', 'Authorization' => 'Bearer ' . $token];
            $response = Http::withHeaders($headers)->post($baseurl . '' . $endpoint, $postData);
            $statusCode = $response->status();
            error_log('status: ' . ' ' . $statusCode);

            if ($statusCode == 200 || 201 || 202)
            {
                if($endpoint == 'create')
                {
                    $responseBody = json_decode($response->getBody() , true);
                    foreach($responseBody['data'] as $item => $value)
                    {
                        error_log($value);
                        $wayneid = $value;
                    }
                            
                    $panic = Panic::where(['wayne_id' => $request->id])->update(['wayne_id' => $wayneid]);

                    return response()->json(
                        [
                            'status' => 'success',
                            'message' => 'Panic cancelled successfully – I hope you have a good excuse for this',
                            "data"=>array
                            (
                                'status'=>'status'
                            )
                        ], 200);
                        error_log('status: '.' '.$statusCode);
                }
                    else
                {
                    
                    return response(
                        [
                            'status' => 'success',
                            'message' => 'Panic cancelled successfully – I hope you have a good excuse for this',
                            "data"=>array
                            (
                                
                            )
                        ], 200);
                        error_log('status: '.' '.$statusCode);
                }
                
                //$panic = Panic::where(['id' => $request->id])->delete();
                
            }
            else
            {
                return response()->json(['message' => 'panic not found']);
                error_log('Error:panic not found');
            }

        }
        catch(exception $e)
        {
            echo $e->getMessage();
            error_log('Error:' + $e);
        }

    }
}

