<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Panic;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Utility\APICall;
use App\Http\Controllers\CallAPIController;
use Response;

class PanicController extends Controller
{

    
    public function index()
    {

        $panic = auth()->user()->panic;
        $user = auth()->user()->user;
        $user1 = auth()->user(); 
        $panic = Panic::get(['id', 'longitude','latitude','panic_type','details','created_at'])->toArray();

        return response()->json([
            'status' => 'success',
            'message' => 'Action completed successfully',
            'data'=>[ 'panics' => $panic],
            'created_by'=>['created_by'=>$user]
        ]);
    }
 
    public function show($id)
    {
        $panic = auth()->user()->panic()->find($id);
 
        if (!$panic) {
            return response()->json([
                'success' => false,
                'message' => 'Panic is not available! '
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $panic->toArray()
        ], 400);
    }
 
    public function store(Request $request)
    {
        $user = auth()->user();  
        error_log($user->name);

        $this->validate($request, [
            'longitude' => 'required',
            'latitude' => 'required',
            'panic_type' => 'nullable',
            'details' => 'nullable'
        ]);
 
        $panic = new Panic();
        $panic->longitude = $request->longitude;
        $panic->latitude = $request->latitude;
        $panic->panic_type = $request->panic_type;
        $panic->details = $request->details;

        if (auth()->user()->panic()->save($panic))
            {
                
                $postData = array(
                    'longitude' => $panic->longitude,
                    'latitude' => $panic->latitude,
                    'panic_type' => $panic->panic_type,
                    'details' => $panic->details,
                    'reference_id'=>$panic->id,
                    'user_name'=>$user->name
                );
                //$wayneid = Panic::find($panic->id)->value('created_at');
                
                $response = (new CallAPIController)->apicallFunc($request, 'create',  $postData);
                $wayneid = Panic::where(['id' => $panic->id])->value('wayne_id');
                error_log($response);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Panic raised successfully – Batma is on the way',
                    'data' => ['panic_id' => $wayneid,          
                ]
                    
                ], 200);           
            }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Panic could not be raised!'
            ], 500);
        }
            
    }

 
    public function update(Request $request, $id)
    {
        $panic = auth()->user()->panic()->find($id);
 
        if (!$panic) {
            return response()->json([
                'success' => false,
                'message' => 'Panic could not be found!'
            ], 400);
        }
 
        $updated = $panic->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Panic could not be updated!'
            ], 500);
    }
 
    public function destroy(Request $request)
    {
 
            $panicid = $request->all();
            
            
            
            error_log($request->wayne_id);
            $validator = Validator::make($panicid, [
                'id' => 'required|max:255'
            ]);

            if($validator->fails())
            {
                return response(['error' => $validator->errors(), 'missing/incorrect variables'], 400);
            }
            
            $postData = array(
                'panic_id' => $request->wayne_id
            );

 
            if (Panic::where('id', $request->id )->exists()) 
            {
                $deletestatus = Panic::find($request->id)->value('deleted');
                error_log($deletestatus);
                
                if($deletestatus == 0)
                {
                try
                {
                    if($panic = Panic::where(['id' => $request->id])->update(['deleted' => 1]))
                    {
                        $response = (new CallAPIController)->apicallFunc($request, 'cancel',  $postData);
                        error_log('Response:::::::'.''.$response);
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
                        return response([
                            'status' => 'failed',
                            'message' => 'Something went wrong.... please try again'
                        ], 400);
                    }

                }
                
                catch(exception $e)    
                {
                    error_log('Error:'+$e);
                    $e->getMessage();
                }
                }
                else
                {
                    return response(['message' => 'panic not found']);
                }
            }else
            {
                return response(['message' => 'panic not found']);
            }

            
            

            // ($panic = Panic::where(['id' => $request->id])->update(['deleted' => 1])

            
            /*if($response)
            {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Panic cancelled successfully',
                    'data'=>[]
                ]);
            }else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Panic could not be deleted!'
                ], 500);
            }*/
            
            /*try
                {
                    
                    if($panic=panic::where('id',$panicid)->delete())
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
            }*/

            

        /*//$panic = auth()->user()->panic()->find(9);
        $panic = $request->
        return response()->json([
            'status' => 'success',
            'message' => 'Panic cancelled successfully',
            'data'=>[]
        ]);

        if (!$panic) {
            return response()->json([
                'success' => 'missing/incorrect variables',
                'message' => 'Panic could not be found!'
            ], 400);
        }
 
        if ($panic->delete()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Panic cancelled successfully',
                'data'=>$panic
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Panic could not be deleted!'
            ], 500);
        }*/
    }
}