<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request\RequestStoreRequest;
use App\Http\Requests\Request\RequestUpdateRequest;
use App\Http\Resources\RequestResource;
use App\Models\Column;
use App\Models\Request;
use App\Models\Table;
use App\Models\Value;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($query)
    {
        $request = Request::where('query', $query)->firstOrFail();
        $column_ids = json_decode($request->filter, true);
        $rows = $request->table->rows()->get();

        foreach ($rows as $row) {
            $row->values = Value::whereIn('column_id', $column_ids)->where('row_id', $row->id)->get();
        }
        return $rows;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Table $table, RequestStoreRequest $request)
    {
        $_request = Request::where('filter', json_encode($request->get('filter')['ids']))->where('table_id', $table->id)->first();
        if ($_request) {
            return RequestResource::make($_request);
        }

        $query = uniqid();
        $_request = Request::create([
            'table_id' => $table->id,
            'query' => $query,
            'filter' => json_encode($_request->filter['ids']),
        ]);
        return RequestResource::make($_request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Request::class);
        $this->repository->findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}