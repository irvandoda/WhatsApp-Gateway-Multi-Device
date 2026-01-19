<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\View\Components;

use Illuminate\View\Component;

class NoData extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    protected $text;
    protected $colspan;
    public function __construct($text = 'No Data', $colspan = 1)
    {
        $this->text = $text;
        $this->colspan = $colspan;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.no-data', [
            'text' => $this->text,
            'colspan' => $this->colspan
        ]);
       
    }
}
