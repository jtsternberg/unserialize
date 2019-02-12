<?php
/**
 * unserialize
 *
 * @version 1.0.0
 */
namespace Jtsternberg\Unserialize;

require_once __DIR__ . '/lib/autoloader.php';

$output = '';
$bodyClass = [];
if ( ! empty( $_POST['output'] ) && ! empty( $_POST['input'] ) ) {
	// $posted = $_POST;
	$unserializer = new Unserializer( $_POST['input'], $_POST['output'] );
	$output = $unserializer->getOutput();
	// die( '<xmp>'. print_r( get_defined_vars(), true ) .'</xmp>' );
	$bodyClass[] = 'submitted';
}
$bodyClass[] = empty( $output ) ? 'no-results' : 'has-results';

// echo '<xmp>'. __LINE__ .') $_POST[output]: '. print_r( $_POST['output'], true ) .'</xmp>';
function checked( $key, $default = false ) {
	echo ( isset( $_POST['output'] ) && $key === $_POST['output'] ) || ( $default && empty( $_POST['output'] ) )
		? ' checked="checked"'
		: '';
}

// echo '<xmp>'. __LINE__ .') $_POST[input]: '. print_r( $_POST['input'], true ) .'</xmp>';
$input = ! empty( $_POST['input'] )
	? htmlspecialchars( $_POST['input'], ENT_QUOTES, 'UTF-8' )
	: 'a:2:{i:0;s:12:"Sample array";i:1;a:2:{i:0;s:5:"Apple";i:1;s:6:"Orange";}}';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Unserialize - PHP, JSON, Base64</title>
	<link rel="shortcut icon" href="assets/favicon.png">
	<meta name="description" content="Unserialize PHP, JSON, or Base64 encoded data. Supports multiple output formats, including print_r, Krumo, and FirePHP.">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/style.css?v=<?php echo time(); ?>" media="screen">
</head>

<body class="<?php echo implode( ' ', $bodyClass ); ?>">
	<p class="center">
		<a href="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<img src="assets/logo.png" id="logo-img" alt="Unserialize" width="516" height="65">
		</a>
	</p>

	<div id="wrap">
		<div id="main">
			<div id="leftside">

				<form method="post" id="unserialize" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

					<div class="dataline">
						<label for="input" class="f-label">Input:</label>
						<textarea name="input" id="input"><?php echo $input; ?></textarea>
					</div>

					<div class="optionsline clear-left">
						<label class="f-label">Output:</label>
						<span class="types">
							<span class="option">
								 <label for="output-print_r" onmousedown="this.querySelector('input').click()">
							 		<input type="radio" name="output" value="print_r" id="output-print_r"<?php checked( 'print_r', true ); ?>>
								 	print_r
							 	</label>
							</span>
							<span class="option">
								 <label for="output-var_dump" onmousedown="this.querySelector('input').click()">
							 		<input type="radio" name="output" value="var_dump" id="output-var_dump"<?php checked( 'var_dump' ); ?>>
								 	var_dump
							 	</label>
							</span>
							<span class="option">
								 <label for="output-var_export" onmousedown="this.querySelector('input').click()">
							 		<input type="radio" name="output" value="var_export" id="output-var_export"<?php checked( 'var_export' ); ?>>
								 	var_export
							 	</label>
							</span>
							<span class="option">
								 <label for="output-JSON" onmousedown="this.querySelector('input').click()">
							 		<input type="radio" name="output" value="JSON" id="output-JSON"<?php checked( 'JSON' ); ?>>
								 	JSON
							 	</label>
							</span>
							<span class="option">
								 <label for="output-Krumo" onmousedown="this.querySelector('input').click()">
							 		<input type="radio" name="output" value="Krumo" id="output-Krumo"<?php checked( 'Krumo' ); ?>>
								 	Krumo
							 	</label>
							</span>
							<span class="option">
								 <label for="output-javascriptconsole" onmousedown="this.querySelector('input').click()">
							 		<input type="radio" name="output" value="javascriptconsole" id="output-javascriptconsole"<?php checked( 'javascriptconsole' ); ?>>
								 	Javascript Console
							 	</label>
							</span>
							<span class="option">
								 <label for="output-dBug" onmousedown="this.querySelector('input').click()">
							 		<input type="radio" name="output" value="dBug" id="output-dBug"<?php checked( 'dBug' ); ?>>
								 	dBug
							 	</label>
							</span>
						</span>
					</div>

					<div class="newline">
					</div>
					<div class="buttonline">
						<p>
							<input onmousedown="this.click()" type="submit" value="Unserialize" id="submit">
						</p>
					</div>
					<?php if ( $output && in_array( $unserializer->output, [ 'print_r', 'var_dump','var_export','json' ], true ) ) : ?>
						<div class="buttonline">
							<p>
								<button onmousedown="window.unserializer.download( document.getElementById('output-formatted').innerText, '<?php echo $unserializer->output; ?>' )" type="button">Download Output</button>&nbsp;
								<button onmousedown="window.unserializer.copy( document.getElementById('output-formatted').innerText )" type="button">Copy Output</button>
							</p>
						</div>
					<?php endif; ?>
				</form>
			</div>
		</div>
		<?php if ( $output ) : ?>
			<div id="output"><<?php echo $unserializer->wrapperEl(); ?> id="output-formatted"><?php echo $output; ?></<?php echo $unserializer->wrapperEl(); ?>></div>
		<?php else : ?>
			<br>
			<h2>PHP and JSON Unserializer</h2>
			<p>A common problem: you have a serialized PHP or JSON string, maybe even base64 encoded, but what you really want is an easy-to-read unserialized version. <em>Unserialize</em> is the solution. Simply paste in your serialized string, click "Unserialize", and we'll display your unserialized text in an easy-to-read format.</p>
		<?php endif; ?>
	</div>

	<script type="text/javascript" src="assets/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>