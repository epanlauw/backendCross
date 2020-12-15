<?php

namespace App\Http\Controllers;

use App\Recipe;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RecipesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // show all recipe
        $recipes = Recipe::all();
        
        foreach($recipes as $recipe) {
            $file = Storage::disk('s3')->get('recipe_images/' . $recipe['image_url']);
            $type = pathinfo($recipe['image_url'], PATHINFO_EXTENSION);
            $recipe['image_url'] = 'data:image/' . $type . ';base64,' . base64_encode($file);
        }

        $success['recipes'] = $recipes;

        return $this->sendResponse($success, "Show all recipes");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // create recipe
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'difficulty' => 'in:"Super Simple","Fairly Easy", "Average", "Hard", "Very Difficult"',
            'image_url' => 'required|string',
            'steps' => 'required|string',
            'ingredient' => 'required|string'
        ]);

        if($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors(), 422);
        }

        $image_parts = explode(";base64,",$request['image_url']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $file = base64_decode($image_parts[1]);
        $safeName = rand(10000,120371). '.' . $image_type;
        $path = "recipe_images/" . $safeName;
        Storage::disk('s3')->put($path, $file);
        $request['image_url'] = $safeName;

        $recipe = Recipe::create($request->toArray());

        $success['recipe'] = $recipe;
        return $this->sendResponse($success, "Recipe created successfully.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recipe = Recipe::find($id);

        $file = Storage::disk('s3')->get('recipe_images/' . $recipe['image_url']);
        $type = pathinfo($recipe['image_url'], PATHINFO_EXTENSION);
        $recipe['image_url'] = 'data:image/' . $type . ';base64,' . base64_encode($file);

        $success['recipe'] = $recipe;

        return $this->sendResponse($success, "Show a recipe");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        // edit recipe
        $recipe = Recipe::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'difficulty' => 'in:"Super Simple","Fairly Easy", "Average", "Hard", "Very Difficult"',
            'image_url' => 'required|string',
            'steps' => 'required|string',
            'ingredient' => 'required|string'
        ]);

        if($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors(), 422);
        }

        $recipe['name'] = $request->name;
        $recipe['difficulty'] = $request->difficulty;
        $recipe['steps'] = $request->steps;
        $recipe['ingredient'] = $request->ingredient;
        
        
        if (Storage::disk('s3')->exists('recipe_images/' . $recipe['image_url'])) {
            Storage::disk('s3')->delete('recipe_images/' . $recipe['image_url']);
        }

        $image_parts = explode(";base64,",$request['image_url']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $file = base64_decode($image_parts[1]);
        $safeName = rand(10000,120371). '.' . $image_type;
        $path = "recipe_images/" . $safeName;
        Storage::disk('s3')->put($path, $file);
        $recipe['image_url'] = $safeName;

        $recipe->save();
        $success['recipe'] = $recipe;
        return $this->sendResponse($success, "Recipe successfully update.");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // delete recipe
        $recipe = Recipe::find($id);
        if (Storage::disk('s3')->exists('recipe_images/' . $recipe['image_url'])) {
            Storage::disk('s3')->delete('recipe_images/' . $recipe['image_url']);
        }
        $recipe->delete();
        $success['recipe'] = $recipe;
        return $this->sendResponse($success, "Recipe successfully delete.");
    }
}
