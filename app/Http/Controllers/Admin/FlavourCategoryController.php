<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlavourCategory;
use Illuminate\Support\Facades\Validator;



class FlavourCategoryController extends Controller
{
    public function index(Request $request)
    {
        $flavourCats = FlavourCategory::query();
        if($request->search != null)
        {
            $flavourCats = $flavourCats->where('name','LIKE','%'.$request->search.'%');
        }
        if($request->sortby!= null && $request->sorttype)
        {
            $flavourCats = $flavourCats->orderBy($request->sortby,$request->sorttype);
        }else{
            $flavourCats = $flavourCats->orderBy('id','desc');
        }
        if($request->perPage != null){
            $flavourCats = $flavourCats->paginate($request->perPage);
        }else{
            $flavourCats = $flavourCats->paginate(10);
        }
        if($request->ajax())
        {
            return response()->json( view('admin.flavourCategories.flavour_cat_data', compact('flavourCats'))->render());
        }
        return view('admin.flavourCategories.list' , compact(['flavourCats']));
    }

    public function flavourcategories(Request $request)
    {
        $columns = array( 
            0 =>'id', 
            1 =>'name',
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $flavourcategories = FlavourCategory::query();
        if($request->search['value'] != null){
            $flavourcategories = $flavourcategories->where('name','LIKE','%'.$request->search['value'].'%');
                           
        }
        if($request->length != '-1')
        {
            $flavourcategories = $flavourcategories->take($request->length);
        }else{
            $flavourcategories = $flavourcategories->take(FlavourCategory::count());
        }
        $flavourcategories = $flavourcategories->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
       
        $data = array();
        if(!empty($flavourcategories))
        {
            foreach ($flavourcategories as $category)
            {
                $url = route('admin.flavour.category.get', ['cat_id' => $category->id]);
                $deleteUrl = route('admin.flavour.category.delete', ['cat_id' => $category->id]);
                $nestedData['id'] = $category->id;
                $nestedData['name'] = $category->name;
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
            'recordsTotal' => FlavourCategory::count(),
            'recordsFiltered' => $request->search['value'] != null ? $flavourcategories->count() : FlavourCategory::count(),
        ]);
    }

    public function getFlavourCategory(Request $request){
        $cat = FlavourCategory::find($request->cat_id);
        return response()->json(['data'=>$cat]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255',
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }
        if($request->cat_id != null)
        {
            $cat = FlavourCategory::find($request->cat_id);
        }else{
            $cat = new FlavourCategory;
        }
        $cat->name = $request->name;
        $cat->save();
        return response()->json(['status'=>'success']);
        
    }

    public function delete(Request $request)
    {
        $cat = FlavourCategory::find($request->cat_id);
        $cat->delete();
        return response()->json(['status'=>'success']);
    }

}
