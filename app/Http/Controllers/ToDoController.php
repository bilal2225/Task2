<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Todo;
use Validator;
use App\Http\Resources\ToDoResource;
use Illuminate\Support\Facades\Gate; // Import Gate facade for authorization

class ToDoController extends BaseController
{
    public function index()
    {
        $products = Todo::paginate(10);
        return response()->json(['products' => $products, 'message' => 'Products Displayed Successfully'], 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required'
        ]);
   
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $product = Todo::create(array_merge($input, ['user_id' =>  auth()->id()]));

        return $this->sendResponse(['product'=>$product], 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Todo::where('user_id',$id)->get();

        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        $this->authorize('view', $product); 

        return $this->sendResponse(['product'=>$product], 'Product retrieved successfully.');
    }
    
    public function update(Request $request,$id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $this->authorize('update', $todo); 
        $todo =Todo::find($id);
        $todo->update([
            'name' => $input['name'],
            'description' => $input['description']
        ]);

        return $this->sendResponse(['todo'=>$todo], 'Product updated successfully.');
    }
   
    public function destroy($id)
    {
        $this->authorize('delete', $todo); 
        $todo =Todo::find($id);
        $todo->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
