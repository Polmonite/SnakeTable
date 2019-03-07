<?php
	$w = isset($_GET['w']) ? $_GET['w'] : mt_rand(16, 32);
	$h = isset($_GET['h']) ? $_GET['h'] : mt_rand(16, 28);
	$w = min($w, 32);
	$h = min($h, 28);
	$speed = isset($_GET['speed']) ? $_GET['speed'] : 200;
	$threedimode = isset($_GET['threedimode']) ? (bool)$_GET['threedimode'] : false;
?>
<!doctype HTML>
<html>
	<head>
		<title>Table-Snake</title>
		<script
			src="http://code.jquery.com/jquery-3.2.1.min.js"
			integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
			crossorigin="anonymous"></script>
		<style>
			body {
				background: rgb(123, 163, 82);
				font-family: Arial!important;
				cursor: default;
			}
			table#grid thead, table#grid thead th {
				height: 32px!important;
				text-align: left;
				position: relative;
			}
			#game-container {
				display: block;
				position: relative;
				text-align: center;
			}
			#points {
				text-align: left;
				padding-left: 24px;
				font-style: italic;
				font-size: 1.3em;
				width: 100%;
			}
			#points:before {
				content: 'SCORE: ';
			}
			#info {
				text-align: right;
				padding-right: 24px;
			}
			table#grid, #grid tr, #grid td, #grid th {
				border-collapse: collapse;
				border-spacing: 0;
				border-color: transparent;
				border: none;
				margin: 0;
				padding: 0;
			}
			table#grid {
				margin: auto!important;
			}
			#grid td {
				width: 24px;
				height: 24px;
				position: relative;
			}
			#grid tr {
				height: 24px;
			}
			#grid td div:after, #grid td div:before {
				content: '';
				background: transparent;
				z-index: 0;
			}
			#grid td div, #grid td div:after, #grid td div:before {
				position: absolute;
				display: inline-block;
				cursor: default;
			}
			/* cell */
			#grid .cell {
				background: rgb(123, 163, 82);
			}
				#grid .cell > div, #grid .cell > div:after {
					background: rgb(125, 170, 80);
					height: 6px;
					width: 6px;
				}
				#grid .cell > div.a {
					top: 3px;
					left: 3px;
				}
				#grid .cell > div.a:after {
					top: 12px;
					left: 0;
				}
				#grid .cell > div.b {
					top: 3px;
					right: 3px;
				}
				#grid .cell > div.b:after {
					top: 12px;
					right: 0;
				}
			/* wall */
			#grid .wall {
				background: rgb(123, 163, 82);
			}
			#grid .wall > div.a {
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				border-radius: 1px;
				width: 22px;
				height: 22px;
				background: rgb(30, 30, 30);
				border: 1px solid rgba(40, 40, 40, 0.6);
			}
				#grid .wall > div.b, #grid .wall > div:after, #grid .wall > div:before {
					background: transparent;
					height: 0;
					width: 0;
					opacity: 0;
				}
			/* snake */
			#grid .snake {}
				#grid .snake > div, #grid .snake > div:after {
					background: rgb(30, 30, 30);
				}
				#grid .snake > div.a {
					top: 1px;
					left: 1px;
					width: 10px;
					height: 10px;
					border-radius: 2px;
				}
					#grid .snake > div.a:after {
						top: 0;
						left: 12px;
						width: 10px;
						height: 10px;
						border-radius: 2px;
					}
				#grid .snake > div.b {
					top: 13px;
					left: 1px;
					width: 10px;
					height: 10px;
					border-radius: 2px;
				}
					#grid .snake > div.b:after {
						top: 0;
						left: 12px;
						width: 10px;
						height: 10px;
						border-radius: 2px;
					}
				#grid .snake > div:before {
					background: transparent;
					height: 0;
					width: 0;
					opacity: 0;
				}
			/* bug */
			#grid .bug {}
				#grid .bug > div, #grid .bug > div:after {
					background: rgb(30, 30, 30);
					width: 8px;
					height: 8px;
				}
				#grid .bug > div:before {
					background: transparent;
					height: 0;
					width: 0;
					opacity: 0;
				}
				#grid .bug > div.a {
					top: 0;
					left: 8px;
					border-radius: 2px;
				}
					#grid .bug > div.a:after {
						top: 16px;
						left: 0;
						border-radius: 2px;
					}
				#grid .bug > div.b {
					top: 8px;
					left: 0;
					border-radius: 2px;
				}
					#grid .bug > div.b:after {
						top: 0;
						left: 16px;
						border-radius: 2px;
					}
			<?php
				if ($threedimode) {
			?>
				#game-container {
					perspective: 400px;
				}
				#grid thead {
					transform: translateY(80px) rotateX(-35deg);
				}
				#grid tbody {
					transform: rotateX(35deg);
				}
			<?php
				}
			?>
		</style>
	</head>
	<body>
		<div id="game-container">
			<table id="grid" data-w="<?= $w ?>" data-h="<?= $h ?>" data-speed="<?= $speed ?>">
				<thead>
					<tr>
						<th colspan="<?= ($w + 2) / 2 ?>">
							<div id="points">0</div>
						</th>
						<th colspan="<?= ($w + 2) / 2 ?>">
							<div id="info">
								<h3 id="game-over">&nbsp;</h3>
							</div>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
					<?php for ($j = 0; $j < $w + 2; ++$j) { ?>
						<td class="wall"><div class="a">&nbsp;</div><div class="b">&nbsp;</div></td>
					<?php } ?>
					</tr>
				<?php for ($i = 0; $i < $h; ++$i) { ?>
					<tr>
						<td class="wall"><div class="a">&nbsp;</div><div class="b">&nbsp;</div></td>
						<?php for ($j = 0; $j < $w; ++$j) { ?>
						<td class="cell" id="cell-<?= $j ?>-<?= $i ?>" data-x="<?= $j ?>" data-y="<?= $i ?>"><div class="a">&nbsp;</div><div class="b">&nbsp;</div></td>
						<?php } ?>
						<td class="wall"><div class="a">&nbsp;</div><div class="b">&nbsp;</div></td>
					</tr>
				<?php } ?>
					<tr>
					<?php for ($j = 0; $j < $w + 2; ++$j) { ?>
						<td class="wall"><div class="a">&nbsp;</div><div class="b">&nbsp;</div></td>
					<?php } ?>
					</tr>
				</tbody>
			</table>
		</div>
		<script>
			var gameProc = true;
			var w = null;
			var h = null;
			var grid = null;
			var info = jQuery('#info');
			var gameOverEl = info.find('#game-over');
			var speed = null;
			var maxSpeed = 100;

			var pointsContainer = jQuery('#points');
			var points = 0;

			var snakeDir = null; // 0: up, 1: right, 2: down, 3: left

			var snake = [];

			var eating = {};

			var rand = function(min, max) {
				return Math.floor(Math.random() * max) + min;
			};
			var randFreeCell = function() {
				var freeCells = grid.find('.cell:not(.snake)');
				return jQuery(freeCells[rand(0, freeCells.length - 1)]);
			};
			var gameOver = function() {
				gameProc = false;
				gameOverEl.text('GAME OVER');
			};
			var updatePoints = function() {
				pointsContainer.text(points);
			};
			var move = function() {
				var head = snake[0];
				var x = (snakeDir === 1)
					? head.x + 1
					: (snakeDir === 3)
						? head.x - 1
						: head.x;
				var y = (snakeDir === 0)
					? head.y - 1
					: (snakeDir === 2)
						? head.y + 1
						: head.y;
				var cellType = getCellType(x, y);
				if (cellType !== 'empty' && cellType !== 'bug') {
					gameOver();
					return;
				}
				snake.unshift({
					x: x,
					y: y
				});
				draw(head.x, head.y, 'snake');
				// if head is on bug, we don't remove the tail
				if (cellType !== 'bug') {
					var tail = snake.pop();
					draw(tail.x, tail.y);
				} else {
					if (speed > maxSpeed) {
						speed -= 5;
					}
					points += 1;
					updatePoints();
					getBug();
				}
				draw(snake[0].x, snake[0].y, 'snake');
			};
			var draw = function(x, y, el) {
				var el = el || '';
				var cell = grid.find('#cell-' + x + '-' + y);
				cell.removeClass('snake').removeClass('bug');
				if (el) {
					cell.addClass(el);
				}
			};
			var getBug = function() {
				var bug = grid.find('.cell.bug');
				if (bug.length == 0) {
					bug = randFreeCell();
					bug.addClass('bug');
				}
				return bug;
			};
			var getBugXY = function() {
				var bug = getBug();
				return {
					x: bug.attr('data-x')|0,
					y: bug.attr('data-y')|0
				};
			};
			var retrieveDir = function(cell1, cell2) {
				if (cell1.dir) {
					return cell1.dir;
				}
				if (cell2 == null) {
					return null;
				}
				if (cell1.y - cell2.y === -1) {
					return 0; // up
				} else if (cell1.x - cell2.x === 1) {
					return 1; // right
				} else if (cell1.y - cell2.y === 1) {
					return 2; // down
				}
				return 3; // left
			};
			var getCellType = function(x, y) {
				if (x < 0 || x >= w || y < 0 || y >= h) {
					return 'wall';
				}
				var bugXY = getBugXY();
				if (x == bugXY.x && y == bugXY.y) {
					return 'bug';
				}
				for (var i = 0; i < snake.length; ++i) {
					if (snake[i].x == x && snake[i].y == y) {
						return 'snake';
					}
				}
				return 'empty';
			};

			var step = function() {
				move();
			};

			// controller
			jQuery(window).on('keydown', function(e) {
				if (e.keyCode == 40) {
					if (snakeDir == 0) {
						gameOver();
						return;
					}
					snakeDir = 2; // down
				} else if (e.keyCode == 39) {
					if (snakeDir == 3) {
						gameOver();
						return;
					}
					snakeDir = 1; // right
				} else if (e.keyCode == 38) {
					if (snakeDir == 2) {
						gameOver();
						return;
					}
					snakeDir = 0; // up
				} else if (e.keyCode == 37) {
					if (snakeDir == 1) {
						gameOver();
						return;
					}
					snakeDir = 3; // left
				}
			});

			$(function() {
				grid = $('#grid');
				h = grid.attr('data-h')|0;
				w = grid.attr('data-w')|0;
				speed = grid.attr('data-speed')|0;
				var head = {
					x: rand(1, w - 2),
					y: rand(1, h - 2)
				};
				var tailDir = rand(0, 4); // 0: up, 1: right, 2: down, 3: left
				var tail = {
					x: head.x + (tailDir == 1 ? 1 : (tailDir == 3 ? -1 : 0)),
					y: head.y + (tailDir == 0 ? -1 : (tailDir == 2 ? 1 : 0))
				};
				snake.push(head);
				snake.push(tail);
				draw(head.x, head.y, 'snake');
				draw(tail.x, tail.y, 'snake');


				snakeDir = retrieveDir(head, tail);
				getBug();

				// game start
				var gameProcFunc = function() {
					if (gameProc) {
						step();
						setTimeout(function() {
							gameProcFunc();
						}, speed);
					}
				};
				gameProcFunc();
			});
		</script>
	</body>
</html>