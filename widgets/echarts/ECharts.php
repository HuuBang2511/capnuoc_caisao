<?php

namespace app\widgets\echarts;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

class ECharts extends Widget
{
    use EChartsTrait;

    public $title = '';
    public $show = false;
    public $orient = 'vertical';
    public $type;
    public $legend = true;
    public $labels;
    public $showLabel = true;
    public $data;
    public $unit = '';
    public $radius = '70%';
    public $options;
    public $divOptions;
    public $isStacked = false;
    public $height = '400px';
    private $xAxis;
    private $yAxis;

    const PIE = 'pie';
    const DONUT = 'pie';
    const BAR = 'bar';
    const STACKED_BAR = 'total';
    const LINE = 'line';

    public function init()
    {
        parent::init();
        if (empty($this->type)) {
            throw new InvalidConfigException(
                "'type' attribute cannot be empty."
            );
        }
    }

    public function run()
    {
        $this->divOptions['id'] = $this->id;
        echo "\n" . Html::tag('div', '', $this->divOptions);
        $this->registerScript();
    }

    public function registerScript()
    {
        $view = $this->getView();

        $options['title'] = [
            'show' => $this->show,
            'text' => $this->title,
            'left' => 'center',
        ];

        $options['color'] = $this->color;

        $options['tooltip'] = [
            'trigger' => 'item',
            'textStyle' => [
                'fontFamily' => 'Arial'
            ],
            'formatter' => '<b>{b}:</b> {c} ' . $this->unit . ' ({d}%)'
        ];

        $options['legend'] = [
            'orient' => $this->orient,
            'left' => 'right',
            'textStyle' => [
                'fontFamily' => 'Arial'
            ],
            'formatter' => new JsExpression("function (name, value) {
                return 'Legend ' + name + value;
            }")
        ];

        $options['series'] = [
            [
                'type' => $this->type,
                'radius' => $this->radius,
                'left' => 0,
                'right' => '50%',
                'data' => $this->data,
                'label' => [
                    'show' => $this->showLabel,
                    'textStyle' => [
                        'fontFamily' => 'Arial'
                    ],
                ],
                'labelLine' => ['show' => $this->showLabel],
            ]
        ];

        //đặt màu cho chart
        if (isset($this->data[0]['color'])) {
            $color = ArrayHelper::getColumn($this->data, 'color');
            if ($color !== null) {
                $options['color'] = $color;
            }
        }


        $options['emphasis'] = [
            'itemStyle' => [
                'shadowBlur' => 10,
                'shadowOffsetX' => 10,
                'shadowColor' => 'rgba(0, 0, 0, 0.5)'
            ]
        ];

        $options = json_encode($options);
        if ($this->type == 'bar') {
            $options = self::renderBarChart($this->isStacked);
        }
        $js[] = "var $this->id = echarts.init(document.getElementById('$this->id'), null, {renderer: 'canvas', useDirtyRect: false});";
        $js[] = "var option$this->id = $options";
        $js[] = "if (option$this->id && typeof option$this->id === \"object\") {
  $this->id.setOption(option$this->id);
}";
        $js[] = "window.addEventListener('resize', $this->id.resize);";

        $view->registerJs(implode("\n", $js));
    }

    private function initPieOptions()
    {
        $option = [];

        return $option;
    }

    private function renderPieChart()
    {
        $option = [];

        return $option;
    }

    private function renderBarChart($isStacked = false)
    {
//        foreach ($this->labels as $i => $label) {
//            $explode = explode(' ', trim($label));
//            $numOfWord[$i] = sizeof($explode);
//            if ($numOfWord[$i] > 7) {
//                $labelCut = array_slice($explode, 0, 7);
//                for ($k = 1; $k <= floor($numOfWord[$i] / 7); $k++) {
//                    $labelCut = array_merge($labelCut, ['\n']);
//                    $arr = array_slice($explode, 7 * $k, 7);
//                    $labelCut = array_merge($labelCut, $arr);
//                }
//                $this->labels[$i] = implode(' ', ($labelCut));
//                $labelCut = null;
//            } else {
//                $this->labels[$i] = $label;
//            }
//        }
        $options['yAxis'] = [
            'type' => 'category',
            'data' => $this->labels,
            'axisLabel' => [
                'fontFamily' => $this->fontFamily,
                'formatter' => '{value}',
                'margin' => 20,
                'width' => "300",
                'overflow' => "truncate",
            ],
        ];

        $options['xAxis'] = [
            'type' => 'value',
        ];

        $options['series'] = [
            [
                'data' => $this->data,
                'type' => 'bar',
                'showBackground' => true,
            ]
        ];

        $options['grid'] = [
            'left' => '3%',
            'right' => '4%',
            'bottom' => '3%',
            'containLabel' => true
        ];

        $options['tooltip'] = [
            'trigger' => 'axis',
            'formatter' => '<b>{b}:</b> {c} ' . $this->unit,
            'axisPointer' => [
                'type' => 'shadow'
            ],
            'textStyle' => [
                'fontFamily' => $this->fontFamily
            ],
        ];


        return json_encode($options);
    }
}
