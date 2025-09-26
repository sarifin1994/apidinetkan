<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BarChart extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public array $legends,
        public string $route,
        public array $colors,
        public bool $stacked = true,
        public int $height = 316,
        public string $columnWidth = '80%',
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
            'legends' => $this->legends,
            'route' => $this->route,
            'colors' => $this->colors,
            'stacked' => $this->stacked,
            'height' => $this->height,
            'columnWidth' => $this->columnWidth,
        ];
        $chartOptions = str_replace('"', "'", json_encode($chartOptions));

        return view('components.bar-chart', compact('chartOptions'));
    }
}
