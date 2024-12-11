<?php

namespace App\View\Components\User\Employee;

use Illuminate\View\Component;

class Agreement extends Component
{

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $case, public string $a)
    {
        // $this->case = $case;
        // $this->active = $active;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.user.employee.agreement');
    }
}
