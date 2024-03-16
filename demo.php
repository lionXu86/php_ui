<?php

use UI\Window;
use UI\Point;
use UI\Size;
use UI\Area;
use UI\Key;
use UI\Controls\Check;

use UI\Controls\Box;

use UI\Draw\Pen;
use UI\Draw\Brush;
use UI\Draw\Path;
use UI\Draw\Color;

use UI\Executor;

$win = new class("demo", new Size(400, 400), false) extends Window {
    public function addExecutor(Executor $executor) {
		$this->executors[] = $executor;
	}

	protected function onClosing() {
		foreach ($this->executors as $executor) {
			$executor->kill();
		}

		$this->destroy();

		UI\quit();
	}
};

$btn = new class('this is button') extends Check{};

$box = new Box(Box::Vertical);
$win->add($box);

$font = new UI\Draw\Text\Font(
	new UI\Draw\Text\Font\Descriptor("arial", 12)			
);

$area = new class($box, $frameIndex, $font) extends Area {

    protected function onKey(string $key, int $ext, int $flags) {
		if ($flags & Area::Down) {
			switch ($ext) {
				case Key::Up: 
                    
				break;

				case Key::Down: 
                    
				break;
			}
		}
	}

    public function __construct($box, $frameIndex = 0, $font) {
		$this->frameIndex = $frameIndex;
		$this->box = $box;
        $this->font = $font;

		$this->box->append($this, true);
	}

    protected function onDraw(UI\Draw\Pen $pen, UI\Size $size, UI\Point $clip, UI\Size $clipSize) {
        $screen_w      = 100;
        $screen_h      = 100;
        $half_screen_w = $screen_w / 2;
        $half_screen_h = $screen_h / 2;
        $r_skyblue = 163;
        $g_skyblue = 216;
        $b_skyblue = 239; // 天蓝色
        $r_orange  = 255;
        $g_orange  = 128;
        $b_orange  = 0;   // 橙色

        $this->frameIndex++;


        for ($i = 0; $i < $screen_w; ++$i) {
            $p = ($i + $this->frameIndex * 8) % $screen_w;

            for ($j = 0; $j < $screen_h; ++$j) {
                $t1 = abs((float) ($half_screen_w - $p) / $half_screen_w);
                $t2 = (float) 1 - $t1;

                $r  = (int)($r_skyblue * $t1 + $r_orange * $t2);
                $g  = (int)($g_skyblue * $t1 + $g_orange * $t2);
                $b  = (int)($b_skyblue * $t1 + $b_orange * $t2);

                $color = new Color( ($r << 16) | ($g << 8) | $b);

                $path = new Path();

                $path->addRectangle(new Point(150+$i, 150+$j), new Size(1, 1));

                $path->end();

                $pen->fill($path, $color);
            }
        }

        $this->writeRenderSpeed($pen, $size);
	}

    private function writeRenderSpeed(UI\Draw\Pen $pen, UI\Size $size) {
		$now = time();
		@$this->frames[$now]++;

		$layout = new UI\Draw\Text\Layout(sprintf(
			"%d fps",
			isset($this->frames[$now - 1]) ? 
				$this->frames[$now-1] : $this->frames[$now]
		), $this->font, $size->width);

		$layout->setColor(0x0000ff);
	
		$pen->write(new Point(20, 20), $layout);

		unset($this->frames[$now-2]);
	}
};

$animator = new class(1000000/24, $area) extends Executor {

	protected function onExecute() {
		$this->area->redraw();
	}

	public function __construct(int $microseconds, Area $area) {
		$this->area = $area;

		parent::__construct($microseconds);
	}
};

$win->addExecutor($animator);

$win->show();

UI\run();
?>
