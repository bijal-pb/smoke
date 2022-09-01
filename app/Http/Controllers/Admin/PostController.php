<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::select('posts.*','users.user_name as post_by')
                     ->leftJoin('users','posts.post_by','=','users.id');
        if($request->search != null){
            $posts = $posts->where('posts.category','LIKE','%'.$request->search.'%')
                        ->orWhere('users.user_name','LIKE','%'.$request->search.'%');
        }
        if($request->sortby!= null && $request->sorttype)
        {
            $posts = $posts->orderBy($request->sortby,$request->sorttype);
        }else{
            $posts= $posts->orderBy('id','desc');
        }
        
        if($request->perPage != null){
            $posts= $posts->paginate($request->perPage);
        }else{
            $posts= $posts->paginate(10);
        }
        
        if($request->ajax())
        {
            return response()->json( view('admin.post.post_data', compact('posts'))->render());
        }
        return view('admin.post.list' , compact('posts'));
    }

    public function post(Request $request)
    {
        $columns = array( 
            0 =>'id', 
            1 =>'user_name',
            2 =>'category',
            3 =>'comment',
            4 =>'rate',
           
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $post = Post::select('posts.*','users.user_name as post_by')
                    ->leftJoin('users','posts.post_by','=','users.id');
        if($request->search['value'] != null){
            $post = $post->where('posts.category','LIKE','%'.$request->search['value'].'%')
                         ->orWhere('users.user_name','LIKE','%'.$request->search['value'].'%');
                           
        }
        if($request->length != '-1')
        {
            $post = $post->take($request->length);
        }else{
            $post = $post->take(Post::count());
        }
        $post = $post->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
       
        $data = array();
        if(!empty($post))
        {
            foreach ($post as $posts)
            {
                $url = route('admin.post.get', ['cat_id' => $posts->id]);
                $nestedData['id'] = $posts->id;
                $nestedData['post_by'] =  $posts->post_by;
                $nestedData['category'] = $posts->category;
                $nestedData['comment'] =  $posts->comment;
                $nestedData['rate'] =  $posts->rate;
                $nestedData['action'] =  "<button class='detail-post btn btn-outline-warning btn-sm btn-icon' data-toggle='modal' data-target='#default-example-modal'  data-url=' $url '><i class='fal fa-list'></i></button>";
                $data[] = $nestedData;

            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => Post::count(),
            'recordsFiltered' => $request->search['value'] != null ? $post->count() : Post::count(),
        ]);
    }
   
    public function getPost(Request $request){
        $user_post = Post::select('posts.*','flavour_categories.name as flavour_category_name','flavours.name as flavour','posts.comment','posts.rate','users.user_name as post_by')
                            ->leftJoin('flavour_categories', 'posts.flavour_category_id','=','flavour_categories.id')
                            ->leftJoin('flavours', 'posts.flavour_id','=','flavours.id')
                            ->leftJoin('users','posts.post_by','=','users.id')
        ->where('posts.id',$request->cat_id)
        ->first();
        return response()->json(['data'=>$user_post]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
           
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg'
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }
        $filename = null;
		if($request->hasfile('image')) {
            $file = $request->file('image');
            $filename = time().$file->getClientOriginalName();
            $file->move(public_path().'/post/', $filename);  
		}
        if($request->post_id != null)
        {
            $post = Post::find($request->post_id);
        }else{
            $post = new Post;
        }
        $post->category = $request->category;
        if($request->hasfile('image'))
        {
            $post->image = $filename;
        }
        $post->flavour_category_id = $request->flavour_category_id;
        $post->flavour_id = $request->flavour_id;
        $post->comment = $request->comment;
        $post->rate = $request->rate;
        $post->post_by = $request->post_by;
        $post->save();
        return response()->json(['status'=>'success']);
        
    }
}
