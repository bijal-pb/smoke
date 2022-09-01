<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlavourCategory;
use App\Models\Flavour;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;

class FlavourController extends Controller
{
    use ApiTrait;
    /**
     *  @OA\Get(
     *     path="/api/flavour/categories",
     *     tags={"Flavour"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get Flavour categories list",
     *     security={{"bearer_token":{}}},
     *     operationId="Flavour category",
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
   public function get_flavour_categories()
   {
       try{
           $flavour_cats = FlavourCategory::select('id','name')->get();
           return $this->response($flavour_cats,'Flavour categories list');
       }catch(Exception $e){
           return $this->response([], $e->getMessage(), false);
       }
   }
   /**
     *  @OA\Get(
     *     path="/api/flavours",
     *     tags={"Flavour"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get Flavours list",
     *     security={{"bearer_token":{}}},
     *     operationId="Flavours",
     * 
     *     @OA\Parameter(
     *         name="flavour_category_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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
   public function get_flavours(Request $request)
   {
       try{
           $flavour_cats = Flavour::select('id','name','flavour_category_id')->where('flavour_category_id',$request->flavour_category_id)->get();
           return $this->response($flavour_cats,'Flavour categories list');
       }catch(Exception $e){
           return $this->response([], $e->getMessage(), false);
       }
   }
}
