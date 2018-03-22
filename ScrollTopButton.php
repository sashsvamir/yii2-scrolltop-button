<?php
namespace sashsvamir\scrollTopButton;

use yii\base\Widget;


/**
 * ScrollTopButton Widget
 */
class ScrollTopButton extends Widget
{
	/**
	 * Scroll length offset (from top) when button appear
	 * @var int
	 */
	public $offset = 700;

	/**
	 * @var int duration time of scroll
	 */
	public $duration = 500;

	/**
	 * Whether using native botton's styles or user css
	 * @var bool
	 */
	public $nativeStyle = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
    	$view = $this->getView();

    	if ($this->nativeStyle) {
    		ScrollTopAsset::register($view);
	    }

    	$js = // language=js
	        "
			var button = '<a id=\"scrollUp\" href=\"#\" title=\"Наверх\"></a>';
			document.getElementsByTagName('body')[0].insertAdjacentHTML('beforeend', button);


			// fade in
			function show(el, duration, display) {
				if (el.getAttribute('show-in-progress') === 'true' || el.style.opacity === '1') {
					return;
				}
				el.setAttribute('show-in-progress', 'true');
				el.setAttribute('hide-in-progress', 'false');
				// console.log('show');

				duration = duration || 500;
				el.style.display = display || 'block';
				var oldTimestamp = performance.now(); // текущее время
				var opacity = parseFloat(el.style.opacity) || 0;

				function fade(newTimestamp) {
					if (el.getAttribute('show-in-progress') === 'false') {
						return;
					}

					opacity += 1 / (duration / (newTimestamp - oldTimestamp)); // вычисляем шаг и добавляем его к прозрачности

					if (opacity >= 1) {
						el.style.opacity = 1;
						el.setAttribute('show-in-progress', 'false');
						return;
					}

					el.style.opacity = opacity;

					oldTimestamp = newTimestamp;
					requestAnimationFrame(fade);
				};
				requestAnimationFrame(fade);
			}


			// fade out
			function hide(el, duration) {
				if (el.getAttribute('hide-in-progress') === 'true' || el.style.opacity === '0' || el.style.display === '') {
					return;
				}
				el.setAttribute('hide-in-progress', 'true');
				el.setAttribute('show-in-progress', 'false');
				// console.log('hide');

				duration = duration || 500;
				var oldTimestamp = performance.now(); // текущее время
				var opacity = parseFloat(el.style.opacity) || 1;

				function fade(newTimestamp) {
					if (el.getAttribute('hide-in-progress') === 'false') {
						return;
					}

					opacity -= 1 / (duration / (newTimestamp - oldTimestamp)); // вычисляем шаг и уменьшаем на него прозрачность

					if (opacity <= 0) {
						el.style.opacity = 0;
						el.style.display = 'none';
						el.setAttribute('hide-in-progress', 'false');
						return;
					}

					el.style.opacity = opacity;

					oldTimestamp = newTimestamp;
					requestAnimationFrame(fade);
				};
				requestAnimationFrame(fade);
			}

			// show/hide button if scroll
			window.addEventListener('scroll', function() {
				var el = document.getElementById('scrollUp');
				if (getScrollTop() > {$this->offset}) {
					show(el, 400);
				} else {
					hide(el, 200);
				}
			});


			// get scroll Y offset
			var getScrollTop = function() {
				// https://stackoverflow.com/questions/3464876/javascript-get-window-x-y-position-for-scroll
				var doc = document.documentElement;
				var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
				return top;
			}

			/**
			 * Provides requestAnimationFrame in a cross browser way, see: http://jsfiddle.net/paul/XQpzU/
			 */
			/*if (!window.requestAnimationFrame) {
				window.requestAnimationFrame = (function() {
					return window.webkitRequestAnimationFrame ||
					window.mozRequestAnimationFrame ||
					window.oRequestAnimationFrame ||
					window.msRequestAnimationFrame;
					// return function(/!* function FrameRequestCallback *!/callback, /!* DOMElement Element *!/element ) {
					// 	window.setTimeout(callback, 1000/60);
					// };
				})();
			}*/

			// scroll window to top, see: https://stackoverflow.com/questions/21474678/scrolltop-animation-without-jquery
			function scrollToTop(scrollDuration) {
				var radius = getScrollTop(), // вся высота скрола
					scrollCount = 0, // значение радиан
					oldTimestamp = performance.now(); // текущее время

				function step(newTimestamp) {

					var fps = newTimestamp - oldTimestamp; // вычислим длительность кадра
					var rad = Math.PI / (scrollDuration / fps); // вычисляем шаг (колич. радиан) за время данного кадра
					scrollCount += rad; // установим текущее положение на длине окружности (в радианах)

					// проходим по 1/2 окружности (pi) и останавливаемся
					if (scrollCount < Math.PI) {
						// вычисляем положение скрола на оси Y:
						// cos() даст нам плавное изменение координаты в пределах от 1 до -1,
						// если умножить координату на radius (длину скрола), получим изменения в пределах от radius до -radius, 
						// поэтому установим диаметр = пол радиуса, и сместим на пол радиуса по оси x, получаем диапазон от radius до 0
						var posY = Math.cos(scrollCount) * radius/2 + radius/2;
						window.scrollTo(0, posY);
						oldTimestamp = newTimestamp;
						window.requestAnimationFrame(step);
					} else {
						window.scrollTo(0, 0);
					}
				}

				window.requestAnimationFrame(step);
			}

            document.getElementById('scrollUp').addEventListener('click', function(event) {
				event.preventDefault();
				scrollToTop({$this->duration});
            });

		";
	    $view->registerJs($js, $view::POS_READY);
    }
}