<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LineChart extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $route,
        public array $colors,
        public int $height = 316,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $chartOptions = [
            'title' => $this->title,
            'route' => $this->route,
            'colors' => $this->colors,
            'height' => $this->height,
        ];
        $chartOptions = str_replace('"', "'", json_encode($chartOptions));

        return view('components.line-chart', compact('chartOptions'));
    }
}
