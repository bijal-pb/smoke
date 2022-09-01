<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Follower;
use App\Models\PostLike;
use App\Models\Country;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use Hash;
use DB;
use Mail;


/**
* @OA\Info(
*      description="",
*     version="1.0.0",
*      title="Smoke Cellar",
* )
**/
 
/**
*  @OA\SecurityScheme(
*     securityScheme="bearer_token",
*         type="http",
*         scheme="bearer",
*     ),
**/
class UserController extends Controller
{

    use ApiTrait;
    /**
     *  @OA\Post(
     *     path="/api/register",
     *     tags={"User"},
     *     summary="Create Account",
     *     security={{"bearer_token":{}}},
     *     operationId="create account",
     * 
     *     @OA\Parameter(
     *         name="user_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="gender",
     *         description="1 - Male | 2 - female", 
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="dob",
     *         description="yyyy-mm-dd", 
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="country_id",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ), 
     *      
     *     @OA\Parameter(
     *         name="phone",
     *         required=true,
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
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_name' => 'required|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'first_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
            'gender' => 'nullable|in:1,2',
            'password' => 'required|min:8',
            'country_id' => 'nullable|exists:countries,id',
            'dob' => 'nullable',
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        try{
            $user = new User;
            $user->user_name = $request->user_name;
            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->gender = $request->gender;
            $user->password = bcrypt($request->password);
            $user->country_id = $request->country_id;
            $user->dob = $request->dob;
            $user->phone = $request->phone;
            $user->save();
            return $this->response('','Registered Successully!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }

    }
    /**
     *  @OA\Post(
     *     path="/api/login",
     *     tags={"User"},
     *     summary="Login",
     *     security={{"bearer_token":{}}},
     *     operationId="login",
     * 
     *     @OA\Parameter(
     *         name="user_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="device_type",
     *         description="android | ios",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="device_token",
     *         description="device token for push notification",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_name' => 'required|exists:users',
            'password' => 'required',
            'device_type' => 'nullable|in:android,ios'
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        try{
            $user = User::where('user_name',$request->user_name)->first();
            $users = User::where('device_token', $request->device_token)->get();
            if(isset($users) && count($users) > 0)
            {
                foreach($users as $u){
                    $u->device_type = null;
                    $u->device_token = null;
                    $u->save();
                }
            }
            if($user){
                if(Hash::check($request->password,$user->password)){
                    $user->device_type = $request->device_type;
                    $user->device_token = $request->device_token;
                    $user->save();
                    $user->tokens()->delete();
                    $token = $user->createToken('API')->accessToken;
                    $user['token'] = $token;
                    return $this->response($user,'Login Successully!');
                }else{
                    return $this->response([], 'Enter valid password!', false); 
                }   
            }
            return $this->response([], 'Enter Valid user name', false); 

        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }

    }
    /**
     *  @OA\Get(
     *     path="/api/profile",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get User Profile",
     *     operationId="profile",
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
    public function me()
    {
        try{
            $user = User::with(['country','posts'])->withCount(['following','follower'])->find(Auth::id());
            return $this->response($user, 'Profile!'); 
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }

    }
    /**
     *  @OA\Post(
     *     path="/api/user/profile",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get Other User Profile",
     *     operationId="user profile",
     * 
     *     @OA\Parameter(
     *         name="user_id",
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
    public function user_profile(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id',
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }
        try{
            $user = User::with(['country','posts'])->withCount(['following','follower'])->find($request->user_id);
            $follow = Follower::where('follow_by',Auth::id())->where('follow_to',$user->id)->first();
            if(isset($follow)){
                $user->is_follow = 1;
            }else{
                $user->is_follow = 0;
            }
            foreach($user->posts as $up){
                $post_like = PostLike::where('post_id', $up->id)->where('user_id',Auth::id())->where('like',1)->first();
                    if(isset($post_like)){
                        $up->is_like = 1;
                    }else{
                        $up->is_like = 0;
                    }
            }
            return $this->response($user, 'Profile!'); 
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
    }
    /**
     *  @OA\Post(
     *     path="/api/profile/edit",
     *     tags={"User"},
     *     summary="Edit Profile",
     *     security={{"bearer_token":{}}},
     *     operationId="edit-profile",
     * 
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="gender",
     *         description="1 - Male | 2 - female", 
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="dob",
     *         description="yyyy-mm-dd", 
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="country_id",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ), 
     *      
     *     @OA\Parameter(
     *         name="phone",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *        @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *                @OA\Property(
     *                    property="photo",
     *                    description="User Profile photo",
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
    public function edit_profile(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'first_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
            'gender' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
            'photo' => 'nullable|image|mimes:svg,jpeg,jpg,gif',
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        try{
            $filename = null;
            if($request->hasFile('photo'))
            {
                $file = $request->file('photo');
                $filename = time().$file->getClientOriginalName();
                $file->move(public_path().'/user/', $filename);  
            }
            $user = User::find(Auth::id());
            if($user){
                $user->email = $request->email;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->gender = $request->gender;
                $user->dob = $request->dob;
                $user->country_id = $request->country_id;
                $user->phone = $request->phone;
                if($request->hasFile('photo'))
                {
                    $user->photo = $filename;
                }
                $user->save();
                return $this->response($user, 'User updated successfully!');
            }
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
    }
    /**
     *  @OA\Post(
     *     path="/api/username/check",
     *     tags={"User"},
     *     summary="Username Check available or not register time",
     *     operationId="Username-Check",
     * 
     *     @OA\Parameter(
     *         name="user_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
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
    public function check_username(Request $request){
        $validator = Validator::make($request->all(),[
            'user_name' => 'required|max:255',
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        $user = User::where('user_name', $request->user_name)->first();
        if($user){
            return $this->response([], 'Already taken this username!', false);
        }
        return $this->response('','Username Available!');
    }
    /**
     *  @OA\Get(
     *     path="/api/logout",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Logout",
     *     security={{"bearer_token":{}}},
     *     operationId="Logout",
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
    public function logout()
    {
        try{
            $user = User::find(Auth::id());
            $user->tokens()->delete();
            $user->device_type = null;
            $user->device_token = null;
            $user->save();
            return $this->response('','Logout Successfully!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
       
    }
    /**
     *  @OA\Get(
     *     path="/api/countries",
     *     tags={"Country"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get Country List",
     *     security={{"bearer_token":{}}},
     *     operationId="Country",
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
    public function get_countries()
    {
        try{
            $countries = Country::select('id','name','code')->get();
            return $this->response($countries,'Country List');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
    }
    /**
     *  @OA\Post(
     *     path="/api/following",
     *     tags={"Followers"},
     *      security={{"bearer_token":{}}},  
     *     summary="following specific user",
     *     operationId="Username-Check",
     * 
     *     @OA\Parameter(
     *         name="follow_to",
     *         required=true,
     *         description="pass user id",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         required=true,
     *         description="1 - follow | 2 - unfollow",
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
    public function following(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'follow_to' => 'required|exists:users,id',
            'status' => 'required|in:1,2'
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false);
        }

        try{
            if($request->status == 1){
                $follow = Follower::where('follow_by',Auth::id())->where('follow_to',$request->follow_to)->first();
                if($follow)
                {
                    return $this->response('','Already following!');
                }
                $follow = new Follower;
                $follow->follow_by = Auth::id();
                $follow->follow_to = $request->follow_to;
                $follow->save();
                $user = User::find($request->follow_to);
                sendPushNotification($user->device_token,$user->device_type,'Following',Auth::user()->user_name.' has started following you ',1,$user->id);
                return $this->response('','Following successfully!');
            }
            if($request->status == 2){
                $follow = Follower::where('follow_by',Auth::id())->where('follow_to',$request->follow_to)->first();
                if($follow)
                {
                    $follow->delete();
                    return $this->response('','Unfollow successfully!');
                }
                return $this->response('','Unfollow successfully!');
            }
            
            
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
        
    }
    /**
     *  @OA\Get(
     *     path="/api/following/list",
     *     tags={"Followers"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get following List",
     *     security={{"bearer_token":{}}},
     *     operationId="Following-list",
     *      
     *     @OA\Parameter(
     *         name="search",
     *         description="search by first name, last name, and user_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
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
    public function get_following(Request $request)
    {
        try{
            $query = Follower::query()
                            ->leftJoin('users','followers.follow_to','=','users.id')
                            ->leftJoin('posts','followers.follow_to','=','posts.post_by')
                            ->select('users.id as user_id','users.user_name','users.photo','users.first_name','users.last_name',DB::raw('COUNT(posts.id) as posts'))
                            ->groupBy('followers.follow_to');

            if($request->search != null)
            {
                $query = $query->where('users.first_name','Like','%'.$request->search.'%')
                            ->orWhere('users.last_name','Like','%'.$request->search.'%')
                            ->orWhere('users.user_name','Like','%'.$request->search.'%');
            }
            $query = $query->where('follow_by',Auth::id());

            $followings = $query->paginate(10);
            foreach($followings as $f)
            {
                if($f->photo != null)
                {
                    $f->photo = asset('/user/' . $f->photo);
                }
            }
            return $this->response($followings,'Following users!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }
        
    }
    /**
     *  @OA\Get(
     *     path="/api/follower/list",
     *     tags={"Followers"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get following List",
     *     security={{"bearer_token":{}}},
     *     operationId="Follower-list",
     * 
     *     @OA\Parameter(
     *         name="search",
     *         description="search by first name, last name, and user_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
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
   public function get_followers(Request $request)
   {
       try{
           $query = Follower::query()
                           ->leftJoin('users','followers.follow_by','=','users.id')
                           ->leftJoin('posts','followers.follow_by','=','posts.post_by')
                           ->select('users.id as user_id','users.user_name','users.photo','users.first_name','users.last_name', DB::raw('COUNT(posts.id) as posts'))
                           ->groupBy('followers.follow_by');
            if($request->search != null)
            {
                $query = $query->where('users.first_name','Like','%'.$request->search.'%')
                            ->orWhere('users.last_name','Like','%'.$request->search.'%')
                            ->orWhere('users.user_name','Like','%'.$request->search.'%');
            }

            $query = $query->where('follow_to',Auth::id());

            $followings = $query->paginate(10);
           foreach($followings as $f)
           {
               if($f->photo != null)
               {
                   $f->photo = asset('/user/' . $f->photo);
               }
           }
           return $this->response($followings,'Follower users!');
       }catch(Exception $e){
           return $this->response([], $e->getMessage(), false);
       }
       
   }
   /**
     *  @OA\Post(
     *     path="/api/notification/enable",
     *     tags={"Notification"},
     *     security={{"bearer_token":{}}},  
     *     summary="Notification enable disable",
     *     security={{"bearer_token":{}}},
     *     operationId="notification-enable-disable",
     * 
     *     @OA\Parameter(
     *         name="status",
     *         description="1 - enable | 2 - disbale",
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
   public function notification_enable(Request $request)
   {
    $validator = Validator::make($request->all(),[
        'status' => 'required|in:1,2',
    ]);

    if($validator->fails())
    {
        return $this->response([], $validator->errors()->first(), false);
    }
       try{
            $user = User::find(Auth::id());
            $user->is_notification = $request->status;
            $user->save();
            if($request->status == 1){
                $msg = 'Notification Enabled successfully!';
            }else{
                $msg = 'Notification Disabled successfully!';
            }
            return $this->response('',$msg);
       }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
       }
   }
   /**
     *  @OA\Post(
     *     path="/api/change/password",
     *     tags={"User"},
     *     summary="Change Password",
     *     security={{"bearer_token":{}}},
     *     operationId="change-password",
     * 
     *     @OA\Parameter(
     *         name="current_password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
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
   public function change_password(Request $request)
   {
    $validator = Validator::make($request->all(),[
        'current_password' => 'required',
        'password' => 'required|min:8',
    ]);

    if($validator->fails())
    {
        return $this->response([], $validator->errors()->first(), false);
    }

    try{
        $user = User::find(Auth::id());
        if($user){
            if(Hash::check($request->current_password,$user->password)){
                $user->password =  bcrypt($request->password);
                $user->save();
                return $this->response('','Password changed Successully!');
            }else{
                return $this->response([], 'Not matched current password!', false); 
            }   
        }
        return $this->response([], 'Enter Valid user name', false); 

    }catch(Exception $e){
        return $this->response([], $e->getMessage(), false);
    }
   }
   /**
	 *  @OA\Post(
	 *     path="/api/forgot/password",
	 *     tags={"User"},
	 *     summary="Forgot password",
	 *     operationId="forgot-password",
	 * 
	 *     @OA\Parameter(
	 *         name="email",
	 *         in="query",
	 *         required=true,
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
	 * )
	**/
	public function forgot_password(Request $request)
	{
		
		$validator = Validator::make($request->all(),[
			'email' => 'required|email|exists:users,email',
		]);

		if($validator->fails())
		{
			return $this->response([], $validator->errors()->first(), false);
		}
		$user = User::where('email',$request->email)->first();
        if(empty($user))
        {
            return $this->response([], 'This email not registered', false);
        }

        try{
            $newPass = substr(md5(time()), 0, 10);
            $user->password = bcrypt($newPass);
            $user->save();
            $data = [
                'username' => $user->user_name,
                'password' => $newPass
            ];
            $email = $user->email;
            Mail::send('mail.forgot', $data, function($message) use ($email) {
                $message->to($email, 'test')->subject
                   ('Forgot Password');
            });
            return $this->response('','Email sent succesfully!');

        } catch (Exception $e)
        {
            return $this->response([], $e->getMessage(), false);
        }      
		
    }
    
    

}
