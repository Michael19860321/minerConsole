    <body>
    	<div class="app_data"
			data-browser-mining="<?php echo $browserMining ?>"
			data-browser-mining-threads="<?php echo $browserMiningThreads ?>"
			data-minera-id="<?php echo $minera_system_id ?>"
			data-page="login"
		></div>
   		<header class="header noheader" data-this-section="<?php echo $sectionPage ?>"></header>
        <!-- Automatic element centering using js -->
        <div class="center">            
	        <div class="lockscreen-cover"></div>
            <div class="toptime headline text-center" id="time"></div>

            <!-- User name -->
            <div class="lockscreen-name"><?php echo lang("app.welcome_miner") ?></div>

            <!-- START LOCK SCREEN ITEM -->
            <div class="lockscreen-item">
                <!-- lockscreen image -->
                <div class="lockscreen-image">
                    <img src="<?php echo base_url("assets/img/avatar.png") ?>" alt="user image"/>
                </div>
                <!-- /.lockscreen-image -->
				<form action="<?php echo site_url("app/login") ?>" method="post">
                <!-- lockscreen credentials (contains the form) -->
	                <div class="lockscreen-credentials">   
	
	                    <div class="input-group">
	                        <input type="password" name="password" class="pass-form form-control" placeholder="<?php echo lang("app.password") ?>" />
	                        <div class="input-group-btn">
	                            <button class="btn btn-flat"><i class="fa fa-arrow-right text-muted"></i></button>
	                        </div>
	
	                    </div>
	                </div><!-- /.lockscreen credentials -->

				</form>
            </div><!-- /.lockscreen-item -->
			
			<div class="lockscreen-link">
				<div class="mt20">
			        <p class="terminal-font"><?php echo lang("app.system") ?>: <?php echo gethostname() ?></p>
					<p class="terminal-font"><?php echo lang("app.ip_address") ?>: <?php echo $_SERVER['SERVER_ADDR'] ?></p>
				</div>
			</div> 

			<div class="lockscreen-link">
				<?php if ($isOnline) : ?><i class="fa fa-circle text-success"></i> <?php echo lang("app.online") ?><?php else: ?><i class="fa fa-circle text-muted"></i> <?php echo lang("app.offline") ?><?php endif; ?>
			</div>
            <div class="lockscreen-link">
                <img width="100" height="100" src="<?php echo base_url("assets/img/wechat-offical-account.jpg") ?>" alt="wechat qr code"/>
            </div>
            <div class="lockscreen-link">
                <div><?php echo lang("app.language") ?>:<a href="<?php echo site_url("app/switchLanguage") . '?lang=cn'?>"><?php echo lang("app.chinese") ?> </a> 
													  | <a href="<?php echo site_url("app/switchLanguage") . '?lang=en'?>"><?php echo lang("app.english") ?></a></div>
            </div>
						
        </div><!-- /.center -->