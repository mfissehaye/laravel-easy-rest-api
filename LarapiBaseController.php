<?php

namespace Mfissehaye\LaravelEasyRestAPI;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

abstract class LarapiBaseController extends Controller
{

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var \stdClass $modelClass
     */
    protected $modelClass;


    /**
     * Display a listing of the resource.
     *
     * @param LarapiModel|Collection $modelObjects
     *
     * @return \Illuminate\Http\Response
     */
    private function _index(Collection $modelObjects)
    {
        /** @var LarapiModel $modelObject */
        foreach ($modelObjects as $modelObject) {
            $modelObject->extraAttributes();
        }
        return response()->json($modelObjects);
    }

    protected function index()
    {
        $class = $this->modelClass;
        $modelObjects = $class::all();
        return $this->_index($modelObjects);
    }

    protected function sindex()
    {
        $class = $this->modelClass;
        $extraAttributes = $this->getExtraMineIdentifiers();
        $query = $class::orWhere('created_by', Auth::id());
        foreach($extraAttributes as $key => $attribute) {
            $query->orWhere($key, $attribute);
        }
        $modelObjects = $query->get();
        return $this->_index($modelObjects);
    }

    public function getExtraMineIdentifiers() {
        return [];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function create(Request $request)
    {
        $class = $this->modelClass;
        $modelObject = new $class($request->all());
        $validator = \Validator::make($request->toArray(), $class::rules);
        if ($validator->passes()) {
            if ($modelObject->save()) {
                return response()->json($modelObject);
            }
            return LarapiError::apiDatabaseError();
        }
        return response()->json([
            'errors' => $validator->errors()
        ], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @internal param ArtGenre|Model $artGenre
     */
    protected function get($id)
    {
        $class = $this->modelClass;
        /** @var LarapiModel $modelObject */
        $modelObject = $class::find($id);
        $modelObject->extraAttributes();
        return response()->json($modelObject);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @internal param ArtGenre|Model $artGenre
     */
    protected function update(Request $request, $id)
    {
        $class = $this->modelClass;
        $modelObject = $class::find($id);
        $modelObject->update($request->all());
        if ($modelObject->save()) {
            return response()->json($modelObject);
        }
        return LarapiError::apiDatabaseError();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @internal param ArtGenre|Model $artGenre
     */
    protected function destroy($id)
    {
        $class = $this->modelClass;
        $modelObject = $class::find($id);
        if ($modelObject->delete()) {
            return response()->json($modelObject);
        }
        return LarapiError::apiDatabaseError();
    }
}
