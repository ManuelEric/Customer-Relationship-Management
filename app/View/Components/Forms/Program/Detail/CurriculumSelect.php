<?php

namespace App\View\Components\Forms\Program\Detail;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CurriculumSelect extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $programType,
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.program.detail.curriculum');
    }
}
