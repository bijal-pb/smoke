<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlavourCategory;
use App\Models\Flavour;
use Illuminate\Support\Facades\Validator;

class FlavourController extends Controller
{
    public function index(Request $request)
    {
        $flavours = Flavour::select('flavours.*','flavour_categories.name as flavour_cat_name')
                            ->leftJoin('flavour_categories','flavours.flavour_category_id','flavour_categories.id');
        if($request->search != null)
        {
            $flavours = $flavours->where('flavours.name','LIKE','%'.$request->search.'%')
                                ->orWhere('flavour_categories.name','LIKE','%'.$request->search.'%');
        }
        if($request->sortby!= null && $request->sorttype)
        {
            $flavours = $flavours->orderBy($request->sortby,$request->sorttype);
        }else{
            $flavours = $flavours->orderBy('id','desc');
        }
        
        if($request->perPage != null){
            $flavours = $flavours->paginate($request->perPage);
        }else{
            $flavours = $flavours->paginate(10);
        }
        
        $flavourCats = FlavourCategory::select('id','name')->get();
        if($request->ajax())
        {
            return response()->json( view('admin.flavours.flavour_data', compact('flavours'))->render());
        }
        return view('admin.flavours.list' , compact(['flavours','flavourCats']));
    }

    public function flavour(Request $request)
    {
        $columns = array( 
            0 =>'id', 
            1 =>'name',
            2 =>'flavour_category_id',
           
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $flavour = Flavour::select('flavours.*','flavour_categories.name as flavour_cat_name')
                            ->leftJoin('flavour_categories','flavours.flavour_category_id','flavour_categories.id');
        if($request->search['value'] != null){
            $flavour = $flavour->where('flavours.name','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('flavour_categories.name','LIKE','%'.$request->search['value'].'%');
                                
         }
        if($request->length != '-1')
        {
            $flavour = $flavour->take($request->length);
        }else{
            $flavour = $flavour->take(Flavour::count());
        }
        $flavour = $flavour->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
       
        $data = array();
        if(!empty($flavour))
        {
            foreach ($flavour as $flavours)
            {
                $url = route('admin.flavour.get', ['cat_id' => $flavours->id]);
                $deleteUrl = route('admin.flavour.delete', ['cat_id' => $flavours->id]);
                $nestedData['id'] = $flavours->id;
                $nestedData['name'] = $flavours->name;
                $nestedData['flavour_category_id'] = $flavours->flavour_cat_name;
                $nestedData['action'] = "<td>
                                         <button class='edit-cat btn btn-outline-warning btn-sm btn-icon' data-toggle='modal' data-target='#default-example-modal' data-url=' $url '><i class='fal fa-pencil'></i></button>
                                         <button class='delete-cat btn btn-outline-danger btn-sm btn-icon'  data-url=' $deleteUrl '><i class='fal fa-trash'></i></button>
                                         </td>";
                $data[] = $nestedData;

            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => Flavour::count(),
            'recordsFiltered' => $request->search['value'] != null ? $flavour->count() : Flavour::count(),
        ]);
    }
    public function getFlavour(Request $request){
        $cat = Flavour::find($request->cat_id);
        return response()->json(['data'=>$cat]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'name' => 'required|max:255',
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }
        if($request->flavour_id != null)
        {
            $cat = Flavour::find($request->flavour_id);
        }else{
            $cat = new Flavour;
        }
        $cat->flavour_category_id = $request->category_id;
        $cat->name = $request->name;
        $cat->save();
        return response()->json(['status'=>'success']);
        
    }

    public function delete(Request $request)
    {
        $cat = Flavour::find($request->cat_id);
        $cat->delete();
        return response()->json(['status'=>'success']);
    }
}
