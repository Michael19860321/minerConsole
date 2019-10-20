    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="right-side ">                	
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo lang('app.mining') ?>
                <small><?php echo lang('app.settings') ?></small>
            </h1>
            <ul class="mini-save-toolbox">
				<li>
					<button type="submit" class="btn btn-sm btn-primary save-minera-settings" name="save" value="1"><i class="fa fa-floppy-o"></i> <?php echo lang('app.save') ?></button>
				</li>
				<li>
					<button type="submit" class="btn btn-sm btn-danger save-minera-settings-restart" name="save_restart" value="1"><i class="fa fa-repeat"></i> <?php echo lang('app.save_and_restart') ?></button>
				</li>
	    	</ul>
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url("app/dashboard") ?>"><i class="fa fa-dashboard"></i> <?php echo lang('app.dashboard') ?></a></li>
            </ol>
        </section>

		<!-- Save toolbox -->
    	<div class="save-toolbox">
	    	<ul>
		    	<li><a href="#" class="toggle-save-toolbox"><i class="fa fa-close"></i></a></li>
				<li>
					<button type="submit" class="btn btn-lg btn-primary save-minera-settings" name="save" value="1"><i class="fa fa-floppy-o"></i> <?php echo lang('app.save') ?></button>
				</li>
				<li>
					<button type="submit" class="btn btn-lg btn-danger save-minera-settings-restart" name="save_restart" value="1"><i class="fa fa-repeat"></i> <?php echo lang('app.save_and_restart') ?></button>
				</li>
	    	</ul>
		</div>

        <!-- Main content -->
        <section class="content">

			<div class="row">

                <?php if ($message) : ?>
                    <section class="col-md-12">
                    	<div class="alert alert-<?php echo $message_type ?> alert-dismissable">
							<i class="fa fa-check"></i>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<?php echo $message ?>.
						</div>
                    </section>
                <?php endif; ?>                        
                <?php if ($this->session->flashdata('message')) : ?>
                    <section class="col-md-12">
                    	<div class="alert alert-<?php echo $this->session->flashdata('message_type') ?> alert-dismissable">
							<i class="fa fa-check"></i>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<?php echo $this->session->flashdata('message') ?>.
						</div>
                    </section>
                <?php endif; ?>                        
                
                <!-- Top section -->
                <section class="col-md-12">
						
					<form action="#" method="post" role="form" id="minersettings" enctype="multipart/form-data">
												
						<input type="hidden" name="save_settings" value="1" />
						                            	                          
						<!-- Pools box -->
                        <div class="box box-primary" id="pools-box">
							<div class="box-header">
								<!-- tools box -->
                                <div class="pull-right box-tools">
                                    <button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                                </div><!-- /. tools -->
                                <i class="fa fa-cloud"></i>
                                
                                <h3 class="box-title"><?php echo lang('app.pools_settings') ?></h3>
                            </div>

							<div class="box-body">
								<p>Pools are taken in the order you put them, the first one is the main pool, all the others ones are failovers.</p>

								<div class="form-group">
                                    <div class="row">
										<div class="col-xs-4">
											<strong><?php echo lang('app.pool_url') ?></strong>
										</div>
										<div class="col-xs-2">
											<strong><?php echo lang('app.pool_username') ?></strong>
										</div>
										<div class="col-xs-2">
											<strong><?php echo lang('app.pool_password') ?></strong>
										</div>
                                    </div>
								</div>
								<!-- Main Pool -->
								<div class="poolSortable ui-sortable">
									<?php $savedPools = json_decode($minerdPools); $donationPool = false; ?>
									<?php $s = (count($savedPools) == 0) ? 2 : count($savedPools); ?>
									<?php $donationHelp = '<h6><strong>Minera pool</strong></h6>
														<p><small>You can always remove the Minera donation pool clicking the button below, but if you hadn\'t issue with it and you like Minera, you should think to keep it as failover pool because your support is really needed to continue developing Minera. So please, before clicking the button below, consider keeping the donation pool as at least your latest failover. Thanks for your support. (If you have enabled time donation, this pool is automatically added.)</small></p>
														<p><button class="btn btn-danger btn-xs del-pool-row" name="del-row" value="1"><i class="fa fa-times"></i> Remove donation pool </button></p>'; ?>
									<?php for ($i=0;$i<=$s;$i++) : ?>
										<?php if ( isset($savedPools[$i]->url) &&
													($savedPools[$i]->url == $this->config->item('minera_pool_url') || $savedPools[$i]->url == $this->config->item('minera_pool_url_sha256')) &&
													isset($savedPools[$i]->username) &&
													$savedPools[$i]->username == $this->util_model->getMineraPoolUser() &&
													isset($savedPools[$i]->password) &&
													$savedPools[$i]->password == $this->config->item('minera_pool_password') ) : $donationPool = true; ?>
										<!-- row pool for Minera -->
										<div class="form-group">
										    <div class="row sort-attach">
										    	<div class="col-xs-4">
										    		<div class="input-group">
										    			<span class="input-group-addon"><i class="fa fa-gift"></i></span>
										    			<input type="text" class="form-control" name="pool_url[]" data-ismain="0" value="<?php echo $savedPools[$i]->url ?>" readonly />
										    		</div>
										    	</div>
										    	<div class="col-xs-2">
										    		<div class="input-group">
										    			<span class="input-group-addon"><i class="fa fa-user"></i></span>
										    			<input type="text" class="form-control" name="pool_username[]" data-ismain="0" value="<?php echo $this->util_model->getMineraPoolUser() ?>" readonly />
										    		</div>
										    	</div>
										    	<div class="col-xs-2">
										    		<div class="input-group">
										    			<span class="input-group-addon"><i class="fa fa-lock"></i></span>
										    			<input type="text" class="form-control" name="pool_password[]" data-ismain="0" value="x" readonly />
										    		</div>
										    	</div>
										    	<div class="col-xs-1">
										    		<button style="margin-top:5px;" class="btn btn-primary btn-xs help-pool-row" name="help-row" value="1"><i class="fa fa-question"></i></button>
										    	</div>
										    </div>
										    <div class="row minera-pool-help" style="display:none;">
										    	<div class="col-xs-11" style="margin-top:10px">
											    	<div class="callout callout-info">
														<?php echo $donationHelp ?>
													</div>
										    	</div>
										    	<div class="col-xs-1">&nbsp;</div>
										    </div>
										</div>
										<?php else : ?>
										<div class="form-group pool-group">
										    <div class="row sort-attach pool-row">
										    	<div class="col-xs-4">
										    		<div class="input-group">
										    			<span class="input-group-addon"><i class="fa fa-cloud-<?php echo ($i == 0) ? "upload" : "download"; ?>"></i></span>
										    			<input type="text" class="form-control pool_url" placeholder="<?php echo ($i == 0) ? "Main" : "Failover"; ?> url" name="pool_url[]" data-ismain="<?php echo ($i == 0) ? "1" : "0"; ?>" value="<?php echo (isset($savedPools[$i]->url)) ? $savedPools[$i]->url : ''; ?>" />
										    		</div>
										    	</div>
										    	<div class="col-xs-2">
										    		<div class="input-group">
										    			<span class="input-group-addon"><i class="fa fa-user"></i></span>
										    			<input type="text" class="form-control pool_username" placeholder="username" name="pool_username[]" data-ismain="<?php echo ($i == 0) ? "1" : "0"; ?>" value="<?php echo (isset($savedPools[$i]->username)) ? $savedPools[$i]->username : ''; ?>"  />
										    		</div>
										    	</div>
										    	<div class="col-xs-2">
										    		<div class="input-group">
										    			<span class="input-group-addon"><i class="fa fa-lock"></i></span>
										    			<input type="text" class="form-control pool_password" placeholder="password" name="pool_password[]" data-ismain="<?php echo ($i == 0) ? "1" : "0"; ?>" value="<?php echo (isset($savedPools[$i]->password)) ? $savedPools[$i]->password : ''; ?>"  />
										    		</div>
										    	</div>
										    </div>
										</div>
										<?php endif; ?>
									<?php endfor; ?>
									<!-- fake donation row pool for Minera -->
									<div class="form-group pool-donation-group" style="display:none;">
									    <div class="row sort-attach">
										    <?php if ($algo === "Scrypt") : ?>
										    	<div class="col-xs-4">
										    		<div class="input-group">
										    			<span class="input-group-addon"><i class="fa fa-gift"></i></span>
										    			<input type="text" class="form-control form-donation" name="pool_url[]" data-ismain="0" value="<?php echo $this->config->item('minera_pool_url') ?>" readonly />
										    		</div>
										    	</div>
										    <?php else: ?>
										    	<div class="col-xs-4">
										    		<div class="input-group">
										    			<span class="input-group-addon"><i class="fa fa-gift"></i></span>
										    			<input type="text" class="form-control form-donation" name="pool_url[]" data-ismain="0" value="<?php echo $this->config->item('minera_pool_url_sha256') ?>" readonly />
										    		</div>
										    	</div>
										    <?php endif; ?>
									    	<div class="col-xs-2">
									    		<div class="input-group">
									    			<span class="input-group-addon"><i class="fa fa-user"></i></span>
									    			<input type="text" class="form-control form-donation" name="pool_username[]" data-ismain="0" value="<?php echo $this->util_model->getMineraPoolUser() ?>" readonly />
									    		</div>
									    	</div>
									    	<div class="col-xs-2">
									    		<div class="input-group">
									    			<span class="input-group-addon"><i class="fa fa-lock"></i></span>
									    			<input type="text" class="form-control form-donation" name="pool_password[]" data-ismain="0" value="x" readonly />
									    		</div>
									    	</div>
									    	<div class="col-xs-3">
									    		<div class="input-group">
									    			<span class="input-group-addon"><i class="fa fa-certificate"></i></span>
													<input type="text" class="form-control pool_proxy" placeholder="socks5|http://proxy:port" name="pool_proxy[]" readonly />
									    		</div>
									    	</div>
									    	<div class="col-xs-1">
									    		<button style="margin-top:5px;" class="btn btn-primary btn-xs help-pool-row" name="help-row" value="1"><i class="fa fa-question"></i></button>
									    	</div>
									    </div>
									    <div class="row minera-pool-help" style="display:none;">
									    	<div class="col-xs-11" style="margin-top:10px">
										    	<div class="callout callout-info">
													<?php echo $donationHelp ?>
												</div>
									    	</div>
									    	<div class="col-xs-1">&nbsp;</div>
									    </div>
									</div>
									<!-- fake row to be cloned -->
									<div class="form-group pool-group pool-group-master" style="display:none;">
									    <div class="row sort-attach pool-row">
									    	<div class="col-xs-4">
									    		<div class="input-group">
									    			<span class="input-group-addon"><i class="fa fa-cloud-download"></i></span>
									    			<input type="text" class="form-control pool_url" placeholder="Failover url" name="pool_url[]" data-ismain="0" value="" />
									    		</div>
									    	</div>
									    	<div class="col-xs-2">
									    		<div class="input-group">
									    			<span class="input-group-addon"><i class="fa fa-user"></i></span>
									    			<input type="text" class="form-control pool_username" placeholder="username" name="pool_username[]" data-ismain="0" value=""  />
									    		</div>
									    	</div>
									    	<div class="col-xs-2">
									    		<div class="input-group">
									    			<span class="input-group-addon"><i class="fa fa-lock"></i></span>
									    			<input type="text" class="form-control pool_password" placeholder="password" name="pool_password[]" data-ismain="0" value=""  />
									    		</div>
									    	</div>
									    	<div class="col-xs-3">
									    		<div class="input-group">
									    			<span class="input-group-addon"><i class="fa fa-certificate"></i></span>
													<input type="text" class="form-control pool_proxy" placeholder="socks5|http://proxy:port" name="pool_proxy[]" data-ismain="0" value=""  />
									    		</div>
									    	</div>
									    </div>
									</div>
									
								</div><!-- sortable -->
                            </div>
                        </div>
					</form>

					<!-- User box -->
					<div class="box box-primary" id="user-box">
						<div class="box-header">
							<!-- tools box -->
                            <div class="pull-right box-tools">
                                <button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                            </div><!-- /. tools -->
                            <i class="fa fa-user"></i>
                            
                            <h3 class="box-title"><?php echo lang('app.user') ?></h3>
                        </div>
						
						<form action="<?php echo site_url("app/settings") ?>" method="post" role="form" id="minerapassword">
							<input type="hidden" name="save_password" value="1" />
                            <div class="box-body">
								<p>Change the Minera lock screen password</p>
                               	<label for="password1"><?php echo lang('app.password') ?></label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock"></i></span>
									<input type="password" class="form-control" name="password" placeholder="<?php echo lang('app.lock_screen_password') ?>">
								</div>
								<div class="input-group mt10">
									<span class="input-group-addon"><i class="fa fa-lock"></i></span>
									<input type="password" class="form-control" name="password2" placeholder="<?php echo lang('app.repeat_lock_screen_password') ?>Repeat the lock screen password">
								</div>
                            </div>
							<div class="box-footer">
								<button type="submit" class="btn btn-primary save-minera-password"><?php echo lang('app.save_password') ?></button>
							</div>
						
						</form>
                    </div>
                    
					<!-- Reset box -->
					<div class="box box-primary" id="resets-box">
						<div class="box-header">
							<!-- tools box -->
                            <div class="pull-right box-tools">
                                <button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                            </div><!-- /. tools -->
                            <i class="fa fa-warning"></i>
                            
                            <h3 class="box-title"><?php echo lang('app.resets') ?></h3>
                        </div>
						
                        <div class="box-body">
	                        <div class="row">
	                        <div class="col-md-10">
								<p>If you are in trouble or you wanna start over, you can resets some of the stored data or reset everything to factory default.</p>
								<div class="form-group">
	                            	<button type="submit" class="btn btn-warning reset-action" data-reset-action="charts"><i class="fa fa-eraser"></i> Reset Charts data</button>
	                            	<h6>This resets all the stored stats needed by the charts, so charts will start from zero.</h6>
								</div>
								<div class="form-group">
	                            	<button type="submit" class="btn btn-primary reset-action" data-reset-action="options"><i class="fa fa-eraser"></i> Reset Guided/manual settings</button>
									<h6>If you have problem choosing between guided/manual options above you can reset them here.</h6>
								</div>
								<div class="form-group">
	                            	<button type="submit" class="btn btn-warning reset-action" data-reset-action="logs"><i class="fa fa-eraser"></i> Clear the Minera logs</button>
									<h6>This will delete everything inside application/logs. This includes all Minera application logs and also all the Miner logs.</h6>
								</div>
								<div class="form-group">
	                            	<button type="submit" class="btn btn-danger reset-factory-action"><i class="fa fa-recycle"></i> Reset to factory default</button>
									<h6>This will reset your Minera to the factory default settings (it doesn't change anything at system level, only the web interface with all the relative data will be reset, this includes: lock password, stats, charts, miner settings, saved miner configs, pools, etc...)</h6>
								</div>
	                        </div>
	                        </div>
                        </div>
						<div class="box-footer">
							<h6><strong>Clicking the reset buttons resets data immediately, there isn't any confirmation to do. Reset actions aren't recoverable, data will be lost.</strong></h6>
						</div>
                    </div>
                
                </section><!-- /.left col -->
                
			</div><!-- /.row -->

        </section><!-- /.content -->
    </aside><!-- /.right-side -->
</div><!-- ./wrapper -->
