<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Traits\ApiTrait;
use Exception;
use Auth;

class NotificationController extends Controller
{
    use ApiTrait;
     /**
     *  @OA\Get(
     *     path="/api/notfication/list",
     *     tags={"Notification"},
     *     security={{"bearer_token":{}}},  
     *     summary="notifications",
     *     operationId="notification",
     * 
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function get_notification()
    {
        try{
            $notification = Notification::select('title','message','created_at')->where('user_id',Auth::id())->where('status',1)->orderBy('id','desc')->get();
            return $this->response($notification, 'Notifications list!'); 
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
    }

}
