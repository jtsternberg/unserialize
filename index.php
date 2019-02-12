<?php
$output = false;
if ( ! empty( $_POST ) ) {

	// 3) $_POST: Array
	// (
	//     [input] => a:2:{i:0;s:12:"Sample array";i:1;a:2:{i:0;s:5:"Apple";i:1;s:6:"Orange";}}
	//     [output] => print_r
	// )
	switch ( $_POST['output'] ) {
		case 'print_r':
			$output = print_r( $_POST['input'], true );
			$output = '<xmp>'. print_r( $output, true ) .'</xmp>';
			break;
		case 'var_dump':
			ob_start();
			var_dump( $_POST['input'] )
			$output = ob_get_clean();
			break;
	}
	$output = call_user_func( $_POST['output'], $_POST['input'] );
	die( '<xmp>'. __LINE__ .') $_POST: '. print_r( $_POST, true ) .'</xmp>' );
}
?>
<!DOCTYPE html>
<!-- saved from url=(0028)https://www.unserialize.com/ -->
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Unserialize - PHP, JSON, Base64</title>
	<meta name="description" content="Unserialize PHP, JSON, or Base64 encoded data. Supports multiple output formats, including print_r, Krumo, and FirePHP.">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/style.css" media="screen">
</head>

<body>
	<p class="center">
		<a href="https://www.unserialize.com/">
			<img src="assets/logo.png" id="logo-img" alt="Unserialize" width="516" height="65">
		</a>
	</p>

	<div id="wrap">
		<div id="main">
			<div id="leftside">

				<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

					<div class="dataline">
						<label for="input" class="f-label">Input:</label>
						<textarea name="input" id="input">a:2:{i:0;s:12:"Sample array";i:1;a:2:{i:0;s:5:"Apple";i:1;s:6:"Orange";}}</textarea>
					</div>

					<div class="optionsline clear-left">
						<label class="f-label">Output:</label>
						<span class="types">
							<span class="option">
								<input type="radio" name="output" value="print_r" id="output-print_r" checked="checked"> <label for="output-print_r">print_r</label>
							</span>
							<span class="option">
								<input type="radio" name="output" value="var_dump" id="output-var_dump"> <label for="output-var_dump">var_dump</label>
							</span>
							<span class="option">
								<input type="radio" name="output" value="var_export" id="output-var_export"> <label for="output-var_export">var_export</label>
							</span>
							<span class="option">
								<input type="radio" name="output" value="Krumo" id="output-Krumo"> <label for="output-Krumo">Krumo</label>
							</span>
							<span class="option">
								<input type="radio" name="output" value="FirePHP" id="output-FirePHP"> <label for="output-FirePHP">FirePHP</label>
							</span>
							<span class="option">
								<input type="radio" name="output" value="dBug" id="output-dBug"> <label for="output-dBug">dBug</label>
							</span>
						</span>
					</div>

					<div class="newline">
					</div>
					<div class="buttonline">
						<span class="space">&nbsp;</span>

						<input type="submit" value="Unserialize" id="submit">

					</div>

				</form>
			</div>
		</div>
		<?php if ( $output ) : ?>
<?php echo $output; ?>
		<?php else : ?>
			<br>
			<h2>PHP and JSON Unserializer</h2>
			<p>A common problem: you have a serialized PHP or JSON string, maybe even base64 encoded, but what you really want is an easy-to-read unserialized version. <em>Unserialize</em> is the solution. Simply paste in your serialized string, click "Unserialize", and we'll display your unserialized text in an easy-to-read format.</p>
		<?php endif; ?>
	</div>
</body>
</html>