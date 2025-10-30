<?php echo $Main->outFavicon(); ?>

<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
<?php echo $settings["header_meta"]; ?>
<?php echo $Main->assets($config["meta"]); ?>
<?php echo $Main->assets($config["css_styles"]); ?>
<?php echo $Main->schemaColor($route_name); ?>