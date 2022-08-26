<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class Limit extends AbstractDisplayer
{
    protected function addScript()
    {
        $script = <<<'JS'
$('.limit-more').click(function () {
    $(this).parent('.limit-text').toggleClass('d-none').siblings().toggleClass('d-none');
});
JS;

        Admin::script($script);
    }

    public function display($limit = 100, $end = '...', $row = false)
    {
        $this->value = Helper::htmlEntityEncode($this->value);

        // 数组
        if ($this->value !== null && ! is_scalar($this->value)) {
            $value = Helper::array($this->value);

            if (count($value) <= $limit) {
                return $value;
            }

            $value = array_slice($value, 0, $limit);

            array_push($value, $end);

            return $value;
        }

        // 行或字符串
        $this->addScript();

        $reached = false;
        $original = '';

        if ($row) {
            // 行
            $value = Helper::rowLimit($this->value, $limit, $end, $reached);
            $value = nl2br($value);

            if (!$reached) {
                return $value;
            }

            $original = $this->column->getOriginal();
            $original = nl2br($original);

        } else {
            // 字符串
            $value = Helper::strLimit($this->value, $limit, $end, $reached);

            if (!$reached) {
                return $value;
            }

            $original = $this->column->getOriginal();
        }

        return <<<HTML
<div class="limit-text">
    <span class="text">{$value}</span>
    <a href="javascript:void(0);" class="limit-more"><i class="fa fa-angle-down"></i></a>
</div>
<div class="limit-text d-none">
    <span class="text">{$original}</span>
    <a href="javascript:void(0);" class="limit-more"><i class="fa fa-angle-up"></i></a>
</div>
HTML;
    }
}
