    <!-- General script -->
    <script type="text/javascript">
	    var _baseUrl = '<?php echo site_url() ?>';
	</script>

	<?php if ($this->config->item("ENV") !== "production") : ?>
		<?php
		$medias = json_decode(file_get_contents(base_url('assets/media.json')));
		foreach ($medias->js as $js) : 
		?>
			<script src="<?php echo base_url($js) ?>?time=<?php echo $now ?>" type="text/javascript"></script>
		<?php endforeach; ?>
	<?php else : ?>
		<script src="<?php echo base_url('assets/js/application.min.js') ?>?time=<?php echo $now ?>" type="text/javascript"></script>
	<?php endif; ?>

</body>
</html>
