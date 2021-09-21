<?php
/**
 * unserialize
 *
 * @version 1.0.0
 */
namespace Jtsternberg\Unserialize;

ini_set( 'error_log', __DIR__ . '/debug.log' );

require_once __DIR__ . '/lib/autoloader.php';

$data = new Data( $_POST );
$unserializer = new Unserializer(
	Formatter::parse( $data->get( 'input' ), $data->get( 'input-type' ) ),
	$data->get( 'method' )
);

function checked( $key, $val, $default = false ) {
	echo ( isset( $val ) && $key === $val ) || ( $default && empty( $val ) )
	? ' checked="checked"'
	: '';
}

function actionButtons( $unserializer ) {
	if ( in_array( $unserializer->method, [ 'print_r','var_dump','var_export','json','csv','base64' ], true ) ) : ?>
		<div class="buttonline">
			<p>
				<button onmouseup="window.unserializer.download()" type="button">Download Output</button>&nbsp;
				<button onmouseup="window.unserializer.copy( document.getElementById('output-formatted').innerText )" type="button">Copy Output</button>
			</p>
		</div>
	<?php endif;
}

$input = 'a:2:{i:0;s:12:"Sample array";i:1;a:2:{i:0;s:5:"Apple";i:1;s:6:"Orange";}}';
$input = $data->get( 'input', $input, true );
$input = htmlspecialchars( $input, ENT_QUOTES, 'UTF-8' );

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

<body class="<?php echo $unserializer->hasOutput() ? 'has-results' : 'no-results'; ?>">
	<p class="center">
		<a href="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<img src="assets/logo.png" id="logo-img" alt="Unserialize" width="516" height="65">
		</a>
	</p>
	<p class="center">
		<small>Thank you to the authors of <a href="https://www.unserialize.com">www.unserialize.com</a> for the inspiration.</small>
	</p>

	<p class="center">
		<small><em>Go to <a href="/phppad/">PHPPad</a> instead.</em></small>
	</p>

	<div id="wrap">
		<div id="main">
			<div id="leftside">

				<form method="post" id="unserialize" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

					<div class="dataline">
						<label for="input" class="i-label">Input:</label>&nbsp;
							<small>
								<span class="types">
									<span class="option">
										<label for="input-serialized">
											<input type="radio" name="input-type" value="serialized" id="input-serialized"<?php checked( 'serialized', $data->get( 'input-type' ), true ); ?>>
											Serialized/Base64
										</label>
									</span>
									<span class="option">
										<label for="input-JSON">
											<input type="radio" name="input-type" value="JSON" id="input-JSON"<?php checked( 'JSON', $data->get( 'input-type' ) ); ?>>
											JSON
										</label>
									</span>
									<span class="option">
										<label for="input-CSV">
											<input type="radio" name="input-type" value="CSV" id="input-CSV"<?php checked( 'CSV', $data->get( 'input-type' ) ); ?>>
											CSV
										</label>
									</span>
									<span class="option">
										<label for="input-yaml">
											<input type="radio" name="input-type" value="yaml" id="input-yaml"<?php checked( 'yaml', $data->get( 'input-type' ) ); ?>>
											Yaml
										</label>
									</span>
								</span>
							</small>
						<textarea onfocus="this.select()" name="input" id="input"><?php echo $input; ?></textarea>
					</div>
					<div class="optionsline clear-left">
						<label class="i-label">Output:</label>&nbsp;
						<small>
							<span class="types">
								<span class="option">
									<label for="method-print_r" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="print_r" id="method-print_r"<?php checked( 'print_r', $data->get( 'method' ), true ); ?>>
										print_r
									</label>
								</span>
								<span class="option">
									<label for="method-var_dump" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="var_dump" id="method-var_dump"<?php checked( 'var_dump', $data->get( 'method' ) ); ?>>
										var_dump
									</label>
								</span>
								<span class="option">
									<label for="method-var_export" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="var_export" id="method-var_export"<?php checked( 'var_export', $data->get( 'method' ) ); ?>>
										var_export
									</label>
								</span>
								<span class="option">
									<label for="method-JSON" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="JSON" id="method-JSON"<?php checked( 'JSON', $data->get( 'method' ) ); ?>>
										JSON
									</label>
								</span>
								<span class="option">
									<label for="method-CSV" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="CSV" id="method-CSV"<?php checked( 'CSV', $data->get( 'method' ) ); ?>>
										CSV
									</label>
								</span>
								<span class="option">
									<label for="method-base64" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="base64" id="method-base64"<?php checked( 'base64', $data->get( 'method' ) ); ?>>
										base64
									</label>
								</span>
								<span class="option">
									<label for="method-yaml" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="yaml" id="method-yaml"<?php checked( 'yaml', $data->get( 'method' ) ); ?>>
										Yaml
									</label>
								</span>
								<span class="option">
									<label for="method-Krumo" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="Krumo" id="method-Krumo"<?php checked( 'Krumo', $data->get( 'method' ) ); ?>>
										Krumo
									</label>
								</span>
								<span class="option">
									<label for="method-javascriptconsole" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="javascriptconsole" id="method-javascriptconsole"<?php checked( 'javascriptconsole', $data->get( 'method' ) ); ?>>
										Javascript Console
									</label>
								</span>
								<span class="option">
									<label for="method-dBug" onmouseup="this.querySelector('input').click()">
										<input type="radio" name="method" value="dBug" id="method-dBug"<?php checked( 'dBug', $data->get( 'method' ) ); ?>>
										dBug
									</label>
								</span>
							</span>
						</small>
					</div>

					<div class="newline">
					</div>
					<div class="buttonline">
						<p>
							<button onmouseup="this.click()" type="submit" id="submit">Unserialize</button>
						</p>
					</div>
					<?php actionButtons( $unserializer ); ?>
				</form>
			</div>
		</div>
		<?php if ( $unserializer->hasOutput() ) : ?>
			<div id="output"><<?php echo $unserializer->wrapperEl(); ?> id="output-formatted"><?php echo $unserializer->getOutput(); ?></<?php echo $unserializer->wrapperEl(); ?>></div>
			<?php actionButtons( $unserializer ); ?>
			<?php else : ?>
				<br>
				<h2>PHP/JSON/YAML Unserializer</h2>
				<p>A common problem: you have a serialized PHP or JSON string, maybe even base64 encoded, but what you really want is an easy-to-read unserialized version. <em>Unserialize</em> is the solution. Simply paste in your serialized string, click "Unserialize", and we'll display your unserialized text in an easy-to-read format.</p>
			<?php endif; ?>
		</div>

		<div id="footer">
			<a href="https://github.com/jtsternberg/unserialize">Find me on github</a>
		</div>
		<script>
			window.unserializer = {
				method: <?php echo json_encode( $unserializer->method ); ?>,
			};
		</script>
		<script type="text/javascript" src="assets/script.js?v=<?php echo time(); ?>"></script>
	</body>
	</html>