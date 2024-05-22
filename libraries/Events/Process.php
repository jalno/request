<?php

namespace packages\request\Events;

use packages\base\Event;
use packages\base\View;
use packages\request\Events\Process\Box;
use packages\request\Process as Request;

abstract class Process extends Event
{
    public $request;
    public static $boxs = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    abstract public function runProcess();

    abstract public function buildFrontend(View $view);

    public function cancel(): bool
    {
        return false;
    }

    public static function addBox(Box $box)
    {
        self::$boxs[] = $box;
    }

    public function getBoxs()
    {
        return self::$boxs;
    }

    public function generateRows()
    {
        $rows = [];
        $lastrow = 0;
        foreach (self::$boxs as $box) {
            $rows[$lastrow][] = $box;
            $size = 0;
            foreach ($rows[$lastrow] as $rowbox) {
                $size += $rowbox->size;
            }
            if ($size >= 12) {
                ++$lastrow;
            }
        }
        $html = '';
        foreach ($rows as $row) {
            $html .= '<div class="row">';
            foreach ($row as $box) {
                $html .= "<div class=\"col-sm-{$box->size}\">".$box->getHTML().'</div>';
            }
            $html .= '</div>';
        }

        return $html;
    }
}
