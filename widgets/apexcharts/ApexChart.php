<?php

namespace app\widgets\apexcharts;

use app\services\DebugService;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\JsExpression;

class ApexChart extends Widget
{
    use ApexChartTrait;

    const SIMPLE_COLUMN = 'simple_column';
    const STACKED_COLUMN = 'stacked_column';
    const STACKED_100_COLUMN = 'stacked_100_column';
    const SIMPLE_PIE = 'simple_pie';
    const SIMPLE_DONUT = 'simple_donut';
    const SIMPLE_BAR = 'simple_bar';
    const STACKED_BAR = 'stacked_bar';
    const STACKED_100_BAR = 'stacked_100_bar';

    private $optionsInit = [
        'simple_column' => [
            'chart' => [
                'type' => 'bar',
            ],
        ],
        'stacked_column' => [
            'chart' => [
                'type' => 'bar',
                'stacked' => true,
            ],
        ],
        'stacked_100_column' => [
            'chart' => [
                'type' => 'bar',
                'stacked' => true,
                'stackType' => '100%',
            ],
        ],
        'simple_pie' => [
            'chart' => [
                'type' => 'pie'
            ],
        ],
        'simple_donut' => [
            'chart' => [
                'type' => 'donut'
            ],
        ],
        'simple_bar' => [
            'chart' => [
                'type' => 'bar'
            ],
            'plotOptions' => [
                'horizontal' => true,
            ],
        ],
        'stacked_bar' => [
            'chart' => [
                'type' => 'bar'
            ],
            'plotOptions' => [
                'horizontal' => true,
            ],
        ],
        'stacked_100_bar' => [
            'chart' => [
                'type' => 'bar'
            ],
            'plotOptions' => [
                'horizontal' => true,
            ],
        ]
    ];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $this->options['id'] = $this->id;
        echo "\n" . Html::tag('div', '', $this->options);
        $this->registerScript();
    }

    public function registerScript()
    {
//        DebugService::dumpdie(json_encode($this->series));
//        DebugService::dumpdie($this);

        $view = $this->getView();

        $options = $this->optionsInit[$this->type];

        $options['chart'] = array_merge($this->optionsInit[$this->type]['chart'], $this->chartOptions);

        $options['series'] = $this->series;

        if ($this->optionsInit[$this->type]['chart']['type'] == 'bar') {
            $options['xaxis'] = ['categories' => $this->label];

        }

        if ($this->optionsInit[$this->type]['chart']['type'] == 'pie' || $this->optionsInit[$this->type]['chart']['type'] == 'donut') {
            $options['labels'] = $this->label;
        }
        $options['responsive'] = [
            [
                'breakpoint' => 480,
                'options' => [
                    'legend' => [
                        'position' => 'bottom',
                        'offsetX' => '-10',
                        'offsetY' => '0',
                    ],
                ],
            ],
        ];

        $options['tooltip'] = [
            'y' => [
                'formatter' => '',
            ],
        ];
        $options['colors'] = $this->colors;

//        DebugService::dumpdie($options);
        $options = json_encode($options);
        $js[] = "var options$this->id = $options;";
        $js[] = "options$this->id.tooltip.y.formatter= function(val){return val + ' $this->unit';};";
        $js[] = "var $this->id = new ApexCharts(document.querySelector('#$this->id'), options$this->id);";
        $js[] = "$this->id.render();";

        $view->registerJs(implode("\n", $js));

    }
}