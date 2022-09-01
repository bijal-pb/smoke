<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Follower;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use Hash;
use DB;

class HomeController extends Controller
{
    use ApiTrait;
    /**
     *  @OA\Get(
     *     path="/api/home",
     *     tags={"Home"},
     *     security={{"bearer_token":{}}},  
     *     summary="Home",
     *     operationId="home",
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
    public function home(Request $request)
    {
        try{
            $category = Category::select('id','name','image','description')->get();
            $recommend = Post::with(['postBy','flavour_category','flavour','reviews','likes'])->withCount('likes')->where('post_by','!=',Auth::id())->latest()->limit(5)->get();
            $data['categoris'] = $category;
            $data['recommendations'] = $recommend;
            return $this->response($data, 'Home!'); 
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
    }
    /**
     *  @OA\Get(
     *     path="/api/recommendations",
     *     tags={"Recommendations"},
     *     security={{"bearer_token":{}}},  
     *     summary="recommendations",
     *     operationId="recommendations",
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
    public function recommendations()
    {
        try{
            $recommend = Post::with(['postBy','flavour_category','flavour','reviews','likes'])->withCount('likes')->where('post_by','!=',Auth::id())->latest()->get();
            foreach($recommend as $p)
                {
                    $post_like = PostLike::where('post_id', $p->id)->where('user_id',Auth::id())->where('like',1)->first();
                    if(isset($post_like)){
                        $p->is_like = 1;
                    }else{
                        $p->is_like = 0;
                    }
                    $follow = Follower::where('follow_by',Auth::id())->where('follow_to',$p->postBy->id)->first();
                    if(isset($follow)){
                        $p->is_follow = 1;
                    }else{
                        $p->is_follow = 0;
                    }
                    if ($p->user_photo != null) {
                        $p->user_photo = asset('/user/' . $p->user_photo);
                    }
                }
            return $this->response($recommend, 'Recommendation Lists'); 
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
    }
    /**
     *  @OA\Get(
     *     path="/api/posts/search",
     *     tags={"Search Posts"},
     *     security={{"bearer_token":{}}},  
     *     summary="saarch posts",
     *     operationId="search",
     * 
     * 
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
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
    public function get_posts(Request $request)
    {
        if($request->search != null)
        {
            try{
                $posts = Post::select('posts.*','flavours.name as flavour_name','flavour_categories.name as flavour_category_name','users.id as user_id','users.first_name as user_first_name','users.last_name as user_last_name','users.user_name as user_name','users.photo as user_photo')
                        ->withCount('likes')
                        ->leftJoin('users','posts.post_by','=','users.id')
                        ->leftJoin('flavours','posts.flavour_id','=','flavours.id')
                        ->leftJoin('flavour_categories','posts.flavour_category_id','=','flavour_categories.id')
                        ->where('flavours.name','LIKE','%'.$request->search.'%')
                        ->orWhere('flavour_categories.name','LIKE','%'.$request->search.'%')
                        ->orWhere('users.first_name','LIKE','%'.$request->search.'%')
                        ->orWhere('users.last_name','LIKE','%'.$request->search.'%')
                        ->orWhere('users.user_name','LIKE','%'.$request->search.'%')
                        ->orWhere('users.first_name','LIKE','%'.$request->search.'%')
                        ->orWhere('users.last_name','LIKE','%'.$request->search.'%')
                        ->get();
                foreach($posts as $p)
                {
                    $post_like = PostLike::where('post_id', $p->id)->where('user_id',Auth::id())->where('like',1)->first();
                    if(isset($post_like)){
                        $p->is_like = 1;
                    }else{
                        $p->is_like = 0;
                    }
                    $follow = Follower::where('follow_by',Auth::id())->where('follow_to',$p->user_id)->first();
                    if(isset($follow)){
                        $p->is_follow = 1;
                    }else{
                        $p->is_follow = 0;
                    }
                    if ($p->user_photo != null) {
                        $p->user_photo = asset('/user/' . $p->user_photo);
                    }
                }
                return $this->response($posts, 'Post List'); 
            }catch(Exception $e){
                return $this->response([], $e->getMessage(), false);
            }
        }
        return $this->response([], 'Posts List');
    }
}
