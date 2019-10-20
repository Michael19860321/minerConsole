<!DOCTYPE html>
<html lang="en" class="<?php echo $htmlTag ?>">
<head>
	<?php if (isset($refreshUrl) && $refreshUrl) : ?>
		<meta http-equiv="refresh" content="<?php echo $seconds+2 ?>;URL='<?php echo $refreshUrl ?>'" />  
	<?php endif; ?>
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<title><?php echo lang("app.title") ?></title>
	<link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic|Kaushan+Script|VT323" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url('favicon.ico') ?>" rel="icon">
	<?php if ($this->config->item("ENV") !== "production") : ?>
		<?php
		$medias = json_decode(file_get_contents(base_url('assets/media.json')));
		foreach ($medias->css as $css) : 
		?>
			<link href="<?php echo base_url($css) ?>?time=<?php echo $now ?>" rel="stylesheet" type="text/css" />
		<?php endforeach; ?>
	<?php else : ?>
		<link href="<?php echo base_url('assets/css/application.min.css') ?>" rel="stylesheet" type="text/css" />
	<?php endif; ?>
</head>
