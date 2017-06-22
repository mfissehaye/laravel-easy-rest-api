<?php

namespace Mfissehaye\Larapi;

use Illuminate\Support\Facades\Route;

class RouteHelper {

    const PROTECT_GET_ALL = "protect_get_all";
    const PROTECT_GET_ONE = 'protect_get_one';

    private $path;
    private $options = [
        RouteHelper::PROTECT_GET_ALL => false,
        RouteHelper::PROTECT_GET_ONE => false
    ];
    private $controller;
    private $middleware = "auth:api";
    private $idPlaceholder = 'id';

    function __construct($path, $options = [])
    {
        $this->path = $path;
        $this->controller = $this->getControllerName();

        $this->options = array_merge($this->options, $options);
        Route::group(['prefix' => '/' . $path], function() {
            $this->index();
            $this->sindex();
            $this->create();
            $this->get();
            $this->update();
            $this->delete();
        });
    }

    public function index() {
        if($this->options[RouteHelper::PROTECT_GET_ALL]) {
            Route::get('', $this->controller . '@index')->middleware($this->middleware);
        } else {
            Route::get('', $this->controller . '@index');
        }
    }

    // secure index - list all items created by current user
    public function sindex() {
        Route::get('mine', $this->controller . '@sindex')->middleware($this->middleware);
    }

    public function get() {
        if(RouteHelper::PROTECT_GET_ONE) {
            Route::get('/{' . $this->idPlaceholder . '}', $this->controller . '@get')->middleware($this->middleware)->where([$this->idPlaceholder => '[0-9]+']);
        } else {
            Route::get('/{' . $this->idPlaceholder . '}', $this->controller . '@get')->where([$this->idPlaceholder => '[0-9]+']);
        }
    }

    public function create() {
        Route::post('', $this->controller . '@create')->middleware($this->middleware);
    }

    public function update() {
        Route::put('/{' . $this->idPlaceholder . '}', $this->controller . '@update')->where([$this->idPlaceholder => '[0-9]+'])->middleware($this->middleware);
    }

    public function delete() {
        Route::delete('/{' . $this->idPlaceholder . '}', $this->controller . '@delete')->where([$this->idPlaceholder => '[0-9]+'])->middleware($this->middleware);
    }

    public function getControllerName() {
        // capitalize the controller name
        $controllerName = str_singular($this->path);
        $controllerName[0] = strtoupper($controllerName[0]);
        return $controllerName . 'Controller';
    }
}