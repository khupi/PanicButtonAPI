<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Panic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PanicResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PanicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        $panics = Panic::all(
            'id',
            'longitude',
            'latitude',
            'panic_type',
            'details',
            'created_at',
            /*'created_by':array(
                'id'=>'5',
                'name'=>'Commissioner Gordon',
                'email'=>'gordon@gothampd'
            )*/

        );
        return response(
            [ 
                'status' => 'success',
                'message' => 'Action completed successfully',
                'data'=>array
                (
                    'panics' => PanicResource::collection
                    (
                        $panics
                    ),
                    'id'=> '5',
                        'name'=> 'Commissioner Gordon',
                        'email'=> 'gordon@gothampd.com'
                    ) 
            ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $wayneid = 0; 
        $data = $request->all();

        $validator = Validator::make($data, [
            'longitude' => 'required|max:255',
            'latitude' => 'required|max:255',
            'panic_type' => 'nullable',
            'details' => 'nullable'
        ]);

        if($validator->fails())
        {
            return response(['error' => $validator->errors(), 'missing/incorrect variables'], 400);
        }

        $panic = Panic::create($data);

        $baseurl = 'https://wayne.fusebox-staging.co.za/api/v1/panic/create';
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMjNlYmIzMTk5NDU5MDQyMTRhZjM5YjZhMTVhODg1MmU4OTIxMDQ0M2FiOGE5N2ViZWQxNTcxYmRmYzk2MTM5ZDMwMGZmNzFhYzdkZWJkNTkiLCJpYXQiOjE2MzQyMTAzODAsIm5iZiI6MTYzNDIxMDM4MCwiZXhwIjoxNjY1NzQ2MzgwLCJzdWIiOiIxMSIsInNjb3BlcyI6W119.iTh-eaQQtZ2ueuthwrWVYT2vQ3-elpMxzUtbi_9vjZzvynTzYPF0JdyibBtKk7ERutpiNcbJ2-J_tRIewA5q5f_n3pym9g4AaJAv418Ev-Jh-DbPO73O6d8ZilFVSAmoVgNxyv8toz6sw12kPzDozLSSC8BHtAIHVtj3EPWkc850uuKloe_yhYgJP4AtACLO8sYogmgaFmhGK7av_uwEJxO9d0b7shJPGtTC1Y4ziVWafaN3656B6DTau-LDacB2tI6dI6Tu9xz3vU9pFZVZYIHcS0jYxFWWAYUyqnO8JuMKVihCqfiBzxP8sZXRhNbdkLijUrBSvmt3eTkuWHqdlrx1ttlEVmWJVhj5BktcnE1tDBThN4BD1-9VEwkfHaY_dzwRfingZsahTWXAwdhHKKpgfnGABF7Tktwpdz_Os9hD8dBmIbDfh5A7zpDR2LnQIj2hAVkhXzExa9l5-MSFVBSuvoeohS4p4CJmUfwULtpSJLEus9sh8d7sNpyd4cpWoUE3Rh_AKiVi8IJ8ja_QY7hL5eXR8_zcpJAptwgYF974i1hOhzwcyZI8nzxbeyI8Rm6sheaSA68nVtfzj45WL0QO10bMYL8phfKNhR260sKriWjCtaNh46gu-bksO8S-YjUr1UFqN1crKD1xK_IeW9p-9yLaSFPr8EIDDsuk-bQ';
        $postData = array(
            'longitude' => $panic->longitude,
            'latitude' => $panic->latitude,
            'panic_type' => $panic->panic_type,
            'details' => $panic->details,
            'reference_id'=>$panic->id,
            'user_name'=>'Khupi'
        );

        $headers = [
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        
        $response = Http::withHeaders($headers)->post($baseurl, $postData);
        error_log($response);
        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
        foreach($responseBody['data'] as $item => $value)
        {
            error_log($value);
            $wayneid = $value;
        }
                   
        $panic = Panic::where(['wayne_id' => $request->id])->update(['wayne_id' => $wayneid]);
 
        dd($responseBody);
        //$panicdata = $responseBody['Data'];
        /*foreach($item as $panicdata)
        {*/

            
        //}

        //$panic=panic::where('id',$panicid)->delete();

        /*$panicR = Panic::firstOrCreate([
            'shortcode',
            'thumbnail_src'
        ], $responseBody);*/

        

       // $this->sendToServer();
        /*return response(
            [
                'status' => 'success',
                'message' => 'Panic raised successfully',
                "data"=>array
                (
                    'panic_id' => $panic->id,
                )
            ], 200);*/
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Panic  $panic
     * @return \Illuminate\Http\Response
     */
    public function show(Panic $panic)
    {
        return response(['panic' => new PanicResource($panic), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Panic  $panic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Panic $panic)
    {
        $panic->update($request->all());

        return response(['panic' => new PanicResource($panic), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Panic  $panic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try
        {
            $panicid = $request->all();

        $validator = Validator::make($panicid, [
            'id' => 'required|max:255'
        ]);

        if($validator->fails())
        {
            return response(['error' => $validator->errors(), 'missing/incorrect variables'], 400);
        }


        ////// request
        $baseurl = 'https://wayne.fusebox-staging.co.za/api/v1/panic/cancel';
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMjNlYmIzMTk5NDU5MDQyMTRhZjM5YjZhMTVhODg1MmU4OTIxMDQ0M2FiOGE5N2ViZWQxNTcxYmRmYzk2MTM5ZDMwMGZmNzFhYzdkZWJkNTkiLCJpYXQiOjE2MzQyMTAzODAsIm5iZiI6MTYzNDIxMDM4MCwiZXhwIjoxNjY1NzQ2MzgwLCJzdWIiOiIxMSIsInNjb3BlcyI6W119.iTh-eaQQtZ2ueuthwrWVYT2vQ3-elpMxzUtbi_9vjZzvynTzYPF0JdyibBtKk7ERutpiNcbJ2-J_tRIewA5q5f_n3pym9g4AaJAv418Ev-Jh-DbPO73O6d8ZilFVSAmoVgNxyv8toz6sw12kPzDozLSSC8BHtAIHVtj3EPWkc850uuKloe_yhYgJP4AtACLO8sYogmgaFmhGK7av_uwEJxO9d0b7shJPGtTC1Y4ziVWafaN3656B6DTau-LDacB2tI6dI6Tu9xz3vU9pFZVZYIHcS0jYxFWWAYUyqnO8JuMKVihCqfiBzxP8sZXRhNbdkLijUrBSvmt3eTkuWHqdlrx1ttlEVmWJVhj5BktcnE1tDBThN4BD1-9VEwkfHaY_dzwRfingZsahTWXAwdhHKKpgfnGABF7Tktwpdz_Os9hD8dBmIbDfh5A7zpDR2LnQIj2hAVkhXzExa9l5-MSFVBSuvoeohS4p4CJmUfwULtpSJLEus9sh8d7sNpyd4cpWoUE3Rh_AKiVi8IJ8ja_QY7hL5eXR8_zcpJAptwgYF974i1hOhzwcyZI8nzxbeyI8Rm6sheaSA68nVtfzj45WL0QO10bMYL8phfKNhR260sKriWjCtaNh46gu-bksO8S-YjUr1UFqN1crKD1xK_IeW9p-9yLaSFPr8EIDDsuk-bQ';
        $postData = array(
            'panic_id' => $request->wayne_id
        );
        $headers = [
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        $response = Http::withHeaders($headers)->post($baseurl, $postData);
        $statusCode = $response->status();
        error_log('status: '.' '.$statusCode);
        if($statusCode == 200 || 201 || 202)
        {
            $responseBody = json_decode($response->getBody(), true);        
            //$panic = Panic::where(['id' => $request->id])->delete();
                try
                {
                    
                    if($panic=panic::where('panic_id',$panicid)->delete())
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
                }else
                {
                    return response(['message' => 'panic not found']);
                }
            }
                catch(exception $e)
                
            {
                error_log('Error:'+$e);
                $e->getMessage();
            }
        }
        else{
            return response(['message' => 'panic not found']);
            error_log('Error:panic not found');
        }
        
        }
            catch(exception $e)
        {
            echo $e->getMessage();
            error_log('Error:'+$e);
        }

    }
    
}
