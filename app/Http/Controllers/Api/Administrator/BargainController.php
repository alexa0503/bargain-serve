<?php

namespace App\Http\Controllers\Api\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bargain;
use App\Http\Resources\Administrator\Bargain as BargainResource;


class BargainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $model = Bargain::orderBy('created_at', 'DESC');
        if( $admin->shop_id ){
            $model->where('shop_id', $admin->shop_id);
        }
        if( $request->keyword ){
            $model->whereHas('items', function ($query) use($request) {
                $query->where('name', 'like', '%'.$request->keyword.'%');
            });
        }
        $bargains = $model->paginate(20);
        return BargainResource::collection($bargains);
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
        //
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
