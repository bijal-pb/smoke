<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReview;
use App\Models\PostLike;
use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;

class PostController extends Controller
{
    use ApiTrait;
    /**
     *  @OA\Post(
     *     path="/api/post/add",
     *     tags={"Post"},
     *     summary="Add new Post",
     *     security={{"bearer_token":{}}},
     *     operationId="add-post",
     * 
     *     @OA\Parameter(
     *         name="category",
     *         required=true,
     *         description="1 - Cigars | 2 - Pipe Tobacco",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     
     *     @OA\Parameter(
     *         name="flavour_category_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="flavour_id",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="comment",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="rate",
     *         required=true,
     *         description="between 0 to 5", 
     *         in="query",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\RequestBody(
     *        @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *                @OA\Property(
     *                    property="image",
     *                    description="Post image",
     *                    type="array",
     *                    @OA\Items(type="file", format="binary")
     *                 ),
     *		        ),
     *          ),
     *     ),
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
    public function create_post(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category' => 'required|in:1,2',
            'flavour_category_id' => 'required|exists:flavour_categories,id',
            'flavour_id' => 'nullable|exists:flavours,id',
            'image' => 'required|image|mimes:svg,jpeg,jpg,gif,png'
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        try{
            $filename = null;
            if($request->hasFile('image'))
            {
                $file = $request->file('image');
                $filename = time().$file->getClientOriginalName();
                $file->move(public_path().'/post/', $filename);  
            }
            $post = new Post;
            $post->category = $request->category == 1 ? 'Cigars' : 'Pipe Tobacco';
            $post->image = $filename;
            $post->flavour_category_id = $request->flavour_category_id;
            $post->flavour_id = $request->flavour_id;
            $post->comment = $request->comment;
            $post->rate = $request->rate;
            $post->post_by = Auth::id();
            $post->save();
            $post = Post::find($post->id);
            $user = User::find($post->post_by);
            sendPushNotification($user->device_token,$user->device_type,'Post Created','Post created successfully',1,$user->id);
            return $this->response($post,'Post is added successfully.');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
    }
    /**
     *  @OA\Post(
     *     path="/api/post/review",
     *     tags={"Post"},
     *     summary="Post review",
     *     security={{"bearer_token":{}}},
     *     operationId="Post-review",
     * 
     *     @OA\Parameter(
     *         name="post_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="review",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="rate",
     *         required=true,
     *         description="between 0 to 5", 
     *         in="query",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
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
    public function post_review(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'post_id' => 'required|exists:posts,id',
            'rate' => 'required'
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        try{
            $post_review = new PostReview;
            $post_review->post_id = $request->post_id;
            $post_review->user_id = Auth::id();
            $post_review->review = $request->review;
            $post_review->rate = $request->rate;
            $post_review->save();
            $post = Post::find($request->post_id);
            $user = User::find($post->post_by);
            sendPushNotification($user->device_token,$user->device_type,'Post Reviewd','Post reviewed by '.Auth::user()->user_name,1,$user->id);
            return $this->response('','Review is added successfully.');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
    }
    /**
     *  @OA\Post(
     *     path="/api/post/like",
     *     tags={"Post"},
     *     summary="Post Like",
     *     security={{"bearer_token":{}}},
     *     operationId="Post-like",
     * 
     *     @OA\Parameter(
     *         name="post_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="like",
     *         required=true,
     *         description="1 - like | 2 -dislike", 
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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
   public function post_like(Request $request)
   {
       $validator = Validator::make($request->all(),[
           'post_id' => 'required|exists:posts,id',
           'like' => 'required|in:1,2',
       ]);

       if($validator->fails())
       {
           return $this->response([], $validator->errors()->first(), false);
       }

       try{
           $post_like = PostLike::where('post_id',$request->post_id)->where('user_id', Auth::id())->first();
           if($post_like)
           {
               if($request->like == 1){ 
                    $post_like->like = $request->like;
                    $post_like->save();
                    $post = Post::find($request->post_id);
                    $user = User::find($post->post_by);
                    sendPushNotification($user->device_token,$user->device_type,'Post Like','Post Liked by '.Auth::user()->user_name,1,$user->id);
                    return $this->response('','Post liked successfully.');
               }else{
                    $post_like->like = $request->like;
                    $post_like->save();
                    $post = Post::find($request->post_id);
                    $user = User::find($post->post_by);
                    sendPushNotification($user->device_token,$user->device_type,'Post Dislike','Post disliked by '.Auth::user()->user_name,1,$user->id);
                    return $this->response('','Post disliked successfully.');
               }
           }
           $post_like = new PostLike;
           $post_like->post_id = $request->post_id;
           $post_like->user_id = Auth::id();
           $post_like->like = $request->like;
           $post_like->save();
           $post = Post::find($request->post_id);
           $user = User::find($post->post_by);
           sendPushNotification($user->device_token,$user->device_type,'Post Like','Post Liked by '.Auth::user()->user_name,1,$user->id);
           return $this->response('','Post like Successully!');
       }catch(Exception $e){
           return $this->response([], $e->getMessage(), false);
       }
   }
   /**
     *  @OA\Get(
     *     path="/api/collections",
     *     tags={"My Collection"},
     *     summary="my collection",
     *     security={{"bearer_token":{}}},
     *     operationId="collection-list",
     * 
     *     @OA\Parameter(
     *         name="category",
     *         required=true,
     *         description=" 1 - Cigars | 2 - Pipe Tobacco",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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
   public function get_collections(Request $request)
   {
        $validator = Validator::make($request->all(),[
            'category' => 'required|in:1,2',
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        try{
            if($request->category == 1)
            {
                $cat = 'Cigars';
            }else{
                $cat = 'Pipe Tobacco';
            }
            $posts = Post::select('posts.id','posts.category','posts.image','flavour_categories.name as flavour_category_name','flavours.name as flavour','posts.comment','posts.rate')
                    ->leftJoin('flavour_categories', 'posts.flavour_category_id','=','flavour_categories.id')
                    ->leftJoin('flavours', 'posts.flavour_id','=','flavours.id')
                    ->where('category',$cat)
                    ->where('post_by', Auth::id())
                    ->get();
            return $this->response($posts,'Your Collections!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
   }
   /**
     *  @OA\Get(
     *     path="/api/post",
     *     tags={"Post"},
     *     summary="Get specific post",
     *     security={{"bearer_token":{}}},
     *     operationId="specific-post",
     * 
     *     @OA\Parameter(
     *         name="post_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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
   public function get_post(Request $request)
   {
        $validator = Validator::make($request->all(),[
            'post_id' => 'required|exists:posts,id',
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        try{
            $post = Post::with(['postBy','flavour_category','flavour','likes','reviews'])
                    ->withCount(['likes','reviews'])
                    ->find($request->post_id);
            $post_like = PostLike::where('post_id', $request->post_id)->where('user_id',Auth::id())->where('like',1)->first();
            if(isset($post_like)){
                 $post->is_like = 1;
            }else{
                 $post->is_like = 0;
            }
            $follow = Follower::where('follow_by',Auth::id())->where('follow_to', $post->postBy->id)->first();
            if(isset($follow)){
                 $post->is_follow = 1;
            }else{
                 $post->is_follow = 0;
            }
                    
            return $this->response($post,'Get particular post!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
   }
}
