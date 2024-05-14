<?php

namespace App\View\Components\Invoice\Program;

use Illuminate\View\Component;

class Nav extends Component
{
    public bool $needed = false;
    public bool $list = false;
    public bool $reminder = false;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $activeMenu
    )
    {
        $this->{$activeMenu} = true;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.invoice.program.nav');
    }
}
