<html basePath='<?=requireCore::$config['path']?>'>
	<head>
		<?=partial::includeCore("/meta")?>
		<?=javascript::includeCore()?>
		<?=stylesheet::includeCore()?>
	</head>
	<title><?=$title?></title>
	<body>
		<?=$content?>
    </body>
</html>