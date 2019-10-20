			<!-- Right side column. Contains the navbar and content of the page -->
			<aside class="right-side">				   
				<!-- Content Header (Page header) -->
				<section class="content-header" data-toggle="dropdown">
					<h1><?php echo lang('app.mining') ?> <small><?php echo lang('app.dashboard') ?></small></h1>
					<ol class="breadcrumb">
						<li><button class="btn btn-default btn-xs view-raw-stats"><i class="fa fa-list"></i> <?php echo lang('app.raw_stats') ?></button></li>
					</ol>
				</section>

				<!-- Main content -->
				<section class="content">

					<div class="row" id="box-widgets">
						
						<?php if (is_array($netMiners) && count($netMiners) > 0) : ?>
						<section class="col-md-12 local-miners-title" style="display:none;">
							<h4><?php echo lang('app.local') ?> <small><?php echo lang('app.miner') ?></small></h4>
						</section>
						<?php endif; ?>

						<section class="col-md-12 section-raw-stats">
							<div class="alert alert-info alert-dismissable">
								<i class="fa fa-list"></i>
								<button type="button" class="close close-stats" aria-hidden="true">×</button>
								<p style="margin:20px 0;"><?php echo lang('app.raw_stats_tips') ?> <a href="<?php echo site_url("app/stats") ?>" target="_blank"><?php echo lang('app.here') ?></a>.</p>
								<span></span>
							</div>
						</section>
							 						
						<?php if (isset($message)) : ?>
							 <section class="col-md-12 pop-message">
							 	<div class="alert alert-<?php echo $message_type ?> alert-dismissable">
									<i class="fa fa-check"></i>
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<?php echo $message ?>.
								</div>
							 </section>
						<?php endif; ?>
						<?php if ($this->session->flashdata('message')) : ?>
							 <section class="col-md-12 pop-message">
							 	<div class="alert alert-warning alert-dismissable">
									<i class="fa fa-check"></i>
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<?php echo $this->session->flashdata('message'); ?>.
								</div>
							 </section>
						<?php endif; ?>

						<!-- Warning section -->
						<section class="col-md-12 connectedSortable ui-sortable warning-section">
						
							<!-- Miner error -->
							<div class="box box-solid bg-red">
								<div class="box-header" style="cursor: move;">
									<!-- tools box -->
									<div class="pull-right box-tools">
										<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
									</div><!-- /. tools -->
									<i class="fa fa-warning"></i>

									<h3 class="box-title"><?php echo lang('app.warning') ?></h3>
								</div>
								<div class="box-body warning-message"></div>
								<div class="box-footer text-center">
									<a href="<?php site_url("app/dashboard") ?>"><h6><?php echo lang('app.refresh_tips') ?></h6></a>
								</div>
							</div><!-- /.miner box -->	
						
						</section>
						
						<!-- widgets section -->
						<section class="col-md-12 widgets-section">
							<div class="row disable-if-not-running">
							 	<!-- total hashrate widget -->
								<div class="col-lg-4 col-sm-4 col-xs-12">
									<!-- small box -->
									<div class="small-box bg-red">
										<div class="inner">
											<h3 class="widget-total-hashrate"><i class="ion spin ion-load-c"></i></h3>
											<p><?php echo lang('app.pool_hashrate') ?></p>
										</div>
										<div class="icon"><i class="ion ion-ios-speedometer-outline"></i></div>
										<a href="#hashrate-history" class="small-box-footer"><?php echo lang('app.history') ?> <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								
								<!-- hw/re widget -->
								<div class="col-lg-4 col-sm-4 col-xs-12">
									<!-- small box -->
									<div class="small-box bg-light">
										<div class="inner">
											<h3 class="widget-hwre-rates"><i class="ion spin ion-load-c"></i></h3>
											<p><?php echo lang('app.error_rates') ?></p>
										</div>
										<div class="icon"><i class="ion ion-alert-circled"></i></div>
										<a href="#error-history" class="small-box-footer"><?php echo lang('app.details') ?> <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								
								<!-- last share widget -->
								<div class="col-lg-4 col-sm-4 col-xs-12">
									<!-- small box -->
									<div class="small-box bg-light-blue">
										<div class="inner">
											<h3 class="widget-last-share"><i class="ion spin ion-load-c"></i></h3>
											<p>Last Share</p>
										</div>
										<div class="icon"><i class="ion ion-ios-stopwatch-outline"></i></div>
										<a href="#miner-details" class="small-box-footer"><?php echo lang('app.miner_details') ?> <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
							</div>							
							
							<div class="row">

								<!-- Warning  widget -->
								<div class="col-lg-4 col-sm-4 col-xs-12 local-widget disable-if-stopped" style="display: none;">
									<!-- small box -->
									<div class="small-box bg-red">
										<div class="inner">
											<h3 class="widget-warning"><i class="ion spin ion-load-c"></i></h3>
											<p><?php echo lang('app.local_miner') ?></p>
										</div>
										<div class="icon"><i class="ion ion-alert"></i></div>
										<a href="" class="small-box-footer warning-message" data-toggle="tooltip" title="" data-original-title="<?php echo lang('app.warning_message') ?>">...<i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								
								<!-- Stopped  widget -->
								<?php if (!$this->redis->get("minerd_status")) : ?>
								<div class="col-lg-4 col-sm-4 col-xs-12 enable-if-not-running local-widget" style="display: none;">
									<!-- small box -->
									<div class="small-box bg-gray">
										<div class="inner">
											<h3 class="widget-warning"><?php echo lang('app.offline') ?></h3>
											<p><?php echo lang('app.local_miner') ?></p>
										</div>
										<div class="icon"><i class="ion ion-power"></i></div>
										<a href="#" data-miner-action="start" class="miner-action small-box-footer warning-message" data-toggle="tooltip" title="" data-original-title="<?php echo lang('app.try_to_start_tips') ?>"><?php echo lang('app.try_to_start') ?> <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								<?php endif; ?>

								<!-- sys temp widget -->
								<div class="col-lg-4 col-sm-4 col-xs-12 local-widget">
									<!-- small box -->
									<div class="small-box sys-temp-box bg-blue">
										<div class="inner">
											<h3 class="widget-sys-temp"><i class="ion spin ion-load-c"></i></h3>
											<p><?php echo lang('app.system_temperature') ?></p>
										</div>
										<div class="icon"><i class="ion ion-thermometer"></i></div>
										<a href="#sysload" class="small-box-footer sys-temp-footer">...<i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								
								<!-- main pool -->
								<div class="col-lg-4 col-sm-4 col-xs-12 disable-if-not-running">
									<!-- small box -->
									<div class="small-box bg-dark">
										<div class="inner">
											<h3 class="widget-main-pool"><i class="ion spin ion-load-c"></i></h3>
											<p><?php echo lang('app.checking') ?></p>
										</div>
										<div class="icon"><i class="ion ion-ios-cloud-upload-outline"></i></div>
										<a href="#pools-details" class="small-box-footer"><?php echo lang('app.pools_details') ?><i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								
								<!-- uptime widget -->
								<div class="col-lg-4 col-sm-4 col-xs-12 disable-if-not-running">
									<!-- small box -->
									<div class="small-box bg-aqua">
										<div class="inner">
											<h3 class="widget-uptime"><i class="ion spin ion-load-c"></i></h3>
											<p><?php echo lang('app.miner_uptime') ?></p>
										</div>
										<div class="icon"><i class="ion ion-ios-timer-outline"></i></div>
										<a href="#miner-details" class="small-box-footer uptime-footer">... <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								
							</div>
							
						</section>

						<!-- Top section -->
						<section class="hidden-xs col-md-12 connectedSortable ui-sortable top-section">

							<?php if ($dashboardBoxLocalMiner) : ?>
							<!-- Local Miner box -->
							<div class="box box-light <?php if (isset($boxStatuses['box-local-miner']) && !$boxStatuses['box-local-miner']) :?>collapsed-box<?php endif; ?>" id="box-local-miner">
							   	<div class="overlay"></div>
							   	<div class="loading-img"></div>
								<div class="box-header" style="cursor: move;">
									<!-- tools box -->
									<div class="pull-right box-tools">
										<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
									</div><!-- /. tools -->
									<i class="fa fa-desktop"></i>

									<h3 class="box-title" id="miner-details"><?php echo lang('app.local_miner_details') ?></h3>
								</div>
								<div class="box-body">
									<div class="row">
										<div class="col-sm-12">
											<div class="">
												  <table id="miner-table-details" class="responsive-datatable-minera table table-striped datatable">
													  <thead>
													  <tr>
														  <th>DEV</th>
														  <th>Temp</th>
														  <th>Frequency</th>
														  <th>Dev HR</th>
														  <th>Shares</th>
														  <th>AC</th>
														  <th>% AC</th>
														  <th>RE</th>
														  <th>% RE</th>
														  <th>HW</th>
														  <th>% HW</th>
														  <th>Last share</th>
														  <th>Last share time</th>
													  </tr>
													  </thead>
													  <tbody class="devs_table">
													</tbody>
													  <tfoot class="devs_table_foot">
													</tfoot>
												</table><!-- /.table -->
											  </div>
										</div>
									</div><!-- /.row - inside box -->
								</div><!-- /.box-body -->
								<div class="box-footer">
								 	<div class="freq-box" style="display:none; margin-top:10px;">
									  	<h6>You can find this on the <a href="<?php echo site_url("app/settings") ?>">settings page</a> too.</h6>
										<pre id="miner-freq" style="font-size:10px; margin-top:10px;">--gc3355-freq=<?php echo $savedFrequencies ?></pre>
								 	</div>
								</div>
							</div><!-- /.miner box -->
							<?php endif; ?>
							
							<?php if ($dashboardBoxLocalPools) : ?>
							<!-- Local Pools box -->
							<div class="box box-light <?php if (isset($boxStatuses['box-local-pools']) && !$boxStatuses['box-local-pools']) :?>collapsed-box<?php endif; ?>" id="box-local-pools">
							   	<div class="overlay"></div>
							   	<div class="loading-img"></div>
								<div class="box-header" style="cursor: move;">
									<!-- tools box -->
									<div class="pull-right box-tools">
										<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
									</div><!-- /. tools -->
									<i class="fa fa-cloud"></i>

									<h3 class="box-title" id="pools-details"><?php echo lang('app.local_pools_details') ?></h3>
								</div>
								<div class="box-body">
									<div class="row">
										<div class="col-sm-12">
											<div class="">
												  <!-- .table - Uses sparkline charts-->
												  <table id="pools-table-details" class="responsive-datatable-minera table table-striped datatable">
													  <thead>
													  <tr>
														  <th>&nbsp;</th>
														  <th>Pool</th>
														  <th>Url</th>
														  <th>Type</th>
														  <th>Status</th>
														  <th>Pool HR</th>
														  <th>CS</th>
														  <th>PS</th>
														  <th>CA</th>
														  <th>PA</th>
														  <th>CR</th>
														  <th>PR</th>
														  <th>Username</th>
													  </tr>
													  </thead>
													  <tbody class="pools_table">
													</tbody>
												</table><!-- /.table -->
												<p class="pool-alert"></p>
											  </div>
										</div>
									</div><!-- /.row - inside box -->
								</div><!-- /.box-body -->
								<div class="box-footer">
									<h6>Legend: <strong>CS</strong> = Current Shares, <strong>PS</strong> = Previous shares, <strong>CA</strong> = Current Accepted, <strong>PA</strong> = Previous Accepted, <strong>CR</strong> = Current Rejected, <strong>PR</strong> = Previous Rejected</h6>
									<h6><strong>Current</strong> is the current or last session, <strong>Previous</strong> is the total of all previous sessions. Pool HashRate is based on shares over the time per session.</h6>
								</div>
							</div><!-- /.local pools box -->
							<?php endif; ?>
							
							<?php if ($dashboardBoxNetworkDetails) : ?>
								<!-- Network Miners box -->
								<?php if (isset($netMiners) && count($netMiners) > 0) : ?>
								<div id="box-network-details" class="box box-light network-miner-details <?php if (isset($boxStatuses['box-network-details']) && !$boxStatuses['box-network-details']) :?>collapsed-box<?php endif; ?>" style="display:none;">
								   	<div class="overlay"></div>
								   	<div class="loading-img"></div>
									<div class="box-header" style="cursor: move;">
										<!-- tools box -->
										<div class="pull-right box-tools">
											<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
										</div><!-- /. tools -->
										<i class="fa fa-server"></i>
	
										<h3 class="box-title" id="miner-details">Network Miners details</h3>
									</div>
									<div class="box-body">
										<div class="row">
											<div class="col-sm-12">
												<div class="">
													  <table id="network-miner-table-details" class="responsive-datatable-minera table table-striped datatable">
														  <thead>
														  <tr>
															  <th>DEV</th>
															  <th>Temp</th>
															  <th>Frequency</th>
															  <th>Dev HR</th>
															  <th>Shares</th>
															  <th>AC</th>
															  <th>% AC</th>
															  <th>RE</th>
															  <th>% RE</th>
															  <th>HW</th>
															  <th>% HW</th>
															  <th>Last share</th>
															  <th>Last share time</th>
														  </tr>
														  </thead>
														  <tbody class="network_devs_table">
														</tbody>
														  <tfoot class="network_devs_table_foot">
														</tfoot>
													</table><!-- /.table -->
												  </div>
											</div>
										</div><!-- /.row - inside box -->
									</div><!-- /.box-body -->
                                        </div>
									</div>
								</div><!-- /.network miner box -->
								<?php endif; ?>
							<?php endif; ?>	
							<?php if ($dashboardBoxNetworkPoolsDetails) : ?>
								<!-- Network pools box -->
								<?php if (isset($netMiners) && count($netMiners) > 0) : ?>
								<div id="box-network-pools-details" class="box box-light <?php if (isset($boxStatuses['box-network-pools-details']) && !$boxStatuses['box-network-pools-details']) :?>collapsed-box<?php endif; ?>">
								   	<div class="overlay"></div>
								   	<div class="loading-img"></div>
									<div class="box-header" style="cursor: move;">
										<!-- tools box -->
										<div class="pull-right box-tools">
											<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
										</div><!-- /. tools -->
										<i class="fa fa-cloud"></i>
	
										<h3 class="box-title" id="pools-details" >Network Pools details</h3>
									</div>
									<div class="box-body">
										<div class="row">
											<div class="col-sm-12">
												<?php $npi = 1; $netCounts = count($netMiners); ?>
													<?php foreach ($netMiners as $netMiner) : ?>
													<hr />
													<div id="net-<?php echo md5($netMiner->name) ?>">
														<div class="mb20 net-pools-label-<?php echo md5($netMiner->name) ?>"></div>
														<div class="">
															  <!-- .table - Uses sparkline charts-->
															  <table id="net-pools-table-details-<?php echo md5($netMiner->name) ?>" class="responsive-datatable-minera net-pools-table table table-striped datatable">
																  <thead>
																  <tr>
																	  <th>&nbsp;</th>
																	  <th>Pool</th>
																	  <th>Url</th>
																	  <th>Type</th>
																	  <th>Status</th>
																	  <th>Pool HR</th>
																	  <th>CS</th>
																	  <th>PS</th>
																	  <th>CA</th>
																	  <th>PA</th>
																	  <th>CR</th>
																	  <th>PR</th>
																	  <th>Username</th>
																  </tr>
																  </thead>
																  <tbody class="net_pools_table">
																</tbody>
															</table><!-- /.table -->
															<p class="net-pool-alert-<?php echo md5($netMiner->name) ?>"></p>
														</div>
														<div class="net-pools-addbox-<?php echo md5($netMiner->name) ?>">
															<button class="btn btn-xs btn-primary toggle-add-net-pool" data-open="0"><i class="fa fa-plus"></i> Add pool</button> <button class="btn btn-xs btn-danger add-net-donation-pool" data-netminer="<?php echo md5($netMiner->name) ?>" data-network="<?php echo $netMiner->ip.':'.$netMiner->port ?>" data-netcoin="<?php echo $netMiner->algo ?>"><i class="fa fa-gift"></i> Add donation pool</button>
															<div class="form-group mt10" style="display:none;">
																<div class="row sort-attach">
															    	<div class="col-xs-5">
															    		<div class="input-group">
															    			<span class="input-group-addon"><i class="fa fa-cloud-download"></i></span>
															    			<input type="text" class="form-control pool_url_<?php echo md5($netMiner->name) ?>" placeholder="Pool url" name="pool_url" value="" />
															    		</div>
															    	</div>
															    	<div class="col-xs-3">
															    		<div class="input-group">
															    			<span class="input-group-addon"><i class="fa fa-user"></i></span>
															    			<input type="text" class="form-control pool_username_<?php echo md5($netMiner->name) ?>" placeholder="username" name="pool_username" value=""  />
															    		</div>
															    	</div>
															    	<div class="col-xs-3">
															    		<div class="input-group">
															    			<span class="input-group-addon"><i class="fa fa-lock"></i></span>
															    			<input type="text" class="form-control pool_password_<?php echo md5($netMiner->name) ?>" placeholder="password" name="pool_password" value=""  />
															    		</div>
															    	</div>
															    	<div class="col-xs-1">
															    		<div class="input-group">
															    			<button class="btn btn-sm btn-success add-net-pool" data-netminer="<?php echo md5($netMiner->name) ?>" data-network="<?php echo $netMiner->ip.':'.$netMiner->port ?>"><i class="fa fa-plus"></i> Add</button>
															    		</div>
															    	</div>
															    </div>
															</div>
														</div>
														<div class="net-pool-error-<?php echo md5($netMiner->name) ?> mt10 text-red"></div>
													</div>
												<?php $npi++; endforeach; ?>
											</div>
										</div><!-- /.row - inside box -->
									</div><!-- /.box-body -->
									<div class="box-footer">
										<h6>Every changes here will be lost if you stop/restart your network miner</h6>
										<h6>Legend: <strong>CS</strong> = Current Shares, <strong>PS</strong> = Previous shares, <strong>CA</strong> = Current Accepted, <strong>PA</strong> = Previous Accepted, <strong>CR</strong> = Current Rejected, <strong>PR</strong> = Previous Rejected</h6>
										<h6><strong>Current</strong> is the current or last session, <strong>Previous</strong> is the total of all previous sessions. Pool HashRate is based on shares over the time per session.</h6>
									</div>
								</div><!-- /.network pools box -->
								<?php endif; ?>
							<?php endif; ?>
						</section>
						
						<!-- Right col -->
						<section class="col-md-6 col-xs-12 connectedSortable ui-sortable right-section" id="box-charts">
							<?php if ($dashboardBoxChartShares) : ?>
							<!-- A/R/H chart -->
							<div class="box box-primary <?php if (isset($boxStatuses['box-chart-shares']) && !$boxStatuses['box-chart-shares']) :?>collapsed-box<?php endif; ?>" id="box-chart-shares">
							   	<div class="overlay"></div>
							   	<div class="loading-img"></div>
								<div class="box-header">
									<!-- tools box -->
									<div class="pull-right box-tools">
										<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
									</div><!-- /. tools -->
									<i class="fa fa-bullseye"></i>
									
									<h3 class="box-title" id="error-history"><?php echo lang('app.local_accepted_rejected_errors') ?></h3>
								</div>
								<div class="box-body chart-responsive">
									<div class="chart" id="rehw-chart" style="height:160px;"></div>
								</div>
							</div><!-- /.A/R/H chart -->
							<?php endif; ?>
							
							<?php if ($dashboardBoxChartSystemLoad) : ?>
							<!-- System box -->
							<div class="box box-light <?php if (isset($boxStatuses['box-chart-system-load']) && !$boxStatuses['box-chart-system-load']) :?>collapsed-box<?php endif; ?>" id="box-chart-system-load">
							   	<div class="overlay"></div>
							   	<div class="loading-img"></div>
								<div class="box-header" style="cursor: move;">
									<!-- tools box -->
									<div class="pull-right box-tools">
										<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
									</div><!-- /. tools -->
									<i class="fa fa-tasks"></i>

									<h3 class="box-title" id="sysload"><?php echo lang('app.system_load') ?></h3>
								</div><!-- /.box-header -->
								<div class="box-body" style="display: block;">
									<div class="row padding-vert sysload" ></div>
								</div><!-- /.box-body -->
								<div class="box-footer">
									<h6 class="sysuptime"></h6>
							   </div>
							</div><!-- /.system box -->
							<?php endif; ?>
												 
						</section><!-- Right col -->
						
						<!-- Left col -->
						<section class="col-md-6 col-xs-12 connectedSortable ui-sortable left-section">
							
							<?php if ($dashboardBoxChartHashrates) : ?>
							<!-- Hashrate box chart -->
							<div class="box box-primary <?php if (isset($boxStatuses['box-chart-hashrates']) && !$boxStatuses['box-chart-hashrates']) :?>collapsed-box<?php endif; ?>" id="box-chart-hashrates">
							   	<div class="overlay"></div>
							   	<div class="loading-img"></div>
								<div class="box-header">
									<!-- tools box -->
									<div class="pull-right box-tools">
										<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
									</div><!-- /. tools -->
									<i class="fa fa-bar-chart-o"></i>
									
									<h3 class="box-title" id="hashrate-history"><?php echo lang('app.local_hashrate_history') ?></h3>
								</div>
								<div class="box-body chart-responsive">
									<div class="chart" id="hashrate-chart" style="height:160px;"></div>
								</div>
							</div><!-- /.hashrate box -->
							<?php endif; ?>
						
						</section><!-- /.left col -->
						
					</div><!-- /.row -->
					
					<div class="row">
					
						<!-- Bottom section -->
						<section class="col-md-12 connectedSortable ui-sortable bottom-section">

							<?php if ($dashboardBoxLog) : ?>
							<!-- Real time log box -->
							<div class="box box-light <?php if (isset($boxStatuses['box-log']) && !$boxStatuses['box-log']) :?>collapsed-box<?php endif; ?>" id="box-log">
								<div class="box-header" style="cursor: move;">
									<!-- tools box -->
									<div class="pull-right box-tools">
										<a href="<?php echo base_url($this->config->item("minerd_log_url")); ?>" target="_blank" style="padding-right: 20px;"><button class="btn btn-default btn-xs"><i class="fa fa-briefcase"></i> <?php echo lang('app.view_raw_log') ?></button></a>
										<button class="btn btn-default btn-xs pause-log" data-widget="pause" data-toggle="tooltip" title="" data-original-title="Pause Log"><i class="fa fa-pause"></i></button>
										<button class="btn btn-default btn-xs" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
									</div><!-- /. tools -->
									<i class="fa fa-file-o"></i>

									<h3 class="box-title" id="pools-details"><?php echo lang('app.miner_real_time_log') ?></h3>
								</div>
								<div class="box-body">
									<?php if ($minerdLog) :?>
										<pre class="log-box" id="real-time-log-data"><?php echo lang('app.log_pause_tips') ?></pre>
									<?php else: ?>
										<pre><?php echo lang('app.enable_logging_tips') ?></pre>
									<?php endif; ?>
								</div><!-- /.box-body -->
								<div class="box-footer">
									<h6><?php echo lang('app.download_log_tips_1') ?> <a href="<?php echo base_url($this->config->item("minerd_log_url")); ?>" target="_blank"><?php echo lang('app.download_log_tips_2') ?></a>.</h6>
								</div>
							</div><!-- /.miner box -->
							<?php endif; ?>
						</section>
					</div>

				</section><!-- /.content -->
			</aside><!-- /.right-side -->
		</div><!-- ./wrapper -->
