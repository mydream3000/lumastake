<?php

namespace App\View\Components\Cabinet;

use Illuminate\View\Component;

class Rightbar extends Component
{
    public $name;
    public $show;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $show = false)
    {
        $this->name = $name;
        $this->show = $show;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.cabinet.rightbar');
    }
}
