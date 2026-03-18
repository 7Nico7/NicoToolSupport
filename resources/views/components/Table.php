<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Table extends Component
{
    public $items;
    public $columns;
    public $actions;
    public $title;
    public $createRoute;
    public $filters;
    public $searchPlaceholder;
    public $emptyMessage;
    public $filtersData;
    
    public function __construct(
        $items, 
        $columns = [], 
        $actions = [], 
        $title = null, 
        $createRoute = null,
        $filters = [],
        $searchPlaceholder = 'Buscar...',
        $emptyMessage = 'No hay registros para mostrar',
        $filtersData = []
    ) {
        $this->items = $items;
        $this->columns = $columns;
        $this->actions = $actions;
        $this->title = $title;
        $this->createRoute = $createRoute;
        $this->filters = $filters;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->emptyMessage = $emptyMessage;
        $this->filtersData = $filtersData;
    }
    
    public function render()
    {
        return view('components.table');
    }
}