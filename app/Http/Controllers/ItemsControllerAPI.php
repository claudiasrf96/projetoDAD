<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ItemsResource;
use App\Item;

class ItemsControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return ItemsResource::collection(Item::paginate(8));
       //return Item::all();
    }

    /**
     * Show the form for creating a new resource.       
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $item = Item::findOrFail($id);
       $item->update($request->all());

       return new ItemsResource($item);
    }
    public function updatePic(Request $request, $id)
    {
       $item = Item::findOrFail($id);
       $item->update($request->all());
       
       $filename = basename($request->file('file')->store('public/items'));
       $item->photo_url = $filename;
       $item->update($request->all());

       return new ItemsResource($item);
    }

    

    public function getDish()
    {
        return ItemsResource::collection(Item::where('items.type', 'dish')->paginate(10));
    }


    public function getDrink()
    {
        return ItemsResource::collection(Item::where('items.type', 'drink')->paginate(10));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
