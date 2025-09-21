<?php

declare(strict_types = 1);

namespace app\widgets;

use yii\base\Widget;

class RatingWidget extends Widget
{
    public int $maxRating = 5;
    public int $minRating = 0;
    public ?int $rating;
    public string $wrapperClass = 'small';

    public function init(): void
    {
        parent::init();
        if ($this->rating === null) {
            $this->rating = 0;
        }
    }

    public function run(): string
    {
        $result = "<div class='stars-rating {$this->wrapperClass}'>";

        for ($i = $this->minRating + 1; $i <= $this->maxRating; $i++) {
            $result .= "<span ";
            $result .= $this->rating >= $i ? 'class="fill-star"' : '';
            $result .= ">&nbsp;</span>";
        }

        $result .= '</div>';
        return $result;
    }
}