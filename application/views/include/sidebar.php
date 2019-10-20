    <body class="skin-<?php echo $dashboardSkin ?>" onload="getStats(false);">
		<div class="app_data"
			data-refresh-time="<?php echo ($dashboard_refresh_time) ? $dashboard_refresh_time : 60; ?>"
			data-records-per-page="<?php echo ($dashboardTableRecords) ? $dashboardTableRecords : 5; ?>"
			data-minerd-log="<?php echo base_url($this->config->item("minerd_log_url")); ?>"
			data-device-tree="<?php echo $dashboardDevicetree ?>"
			data-dashboard-temp="<?php echo ($this->redis->get("dashboard_temp")) ? $this->redis->get("dashboard_temp") : "c"; ?>"
			data-miner-status="<?php echo ($this->redis->get("minerd_status")) ? 1 : 0; ?>"
			data-minera-pool-username="<?php echo $this->util_model->getMineraPoolUser(); ?>"
			data-minera-pool-url-scrypt="<?php echo $this->config->item('minera_pool_url') ?>"
			data-minera-pool-url-sha256="<?php echo $this->config->item('minera_pool_url_sha256') ?>"
			data-browser-mining="<?php echo $browserMining ?>"
			data-browser-mining-threads="<?php echo $browserMiningThreads ?>"
			data-minera-id="<?php echo $mineraSystemId ?>"
		></div>

		<!-- Modal -->
		<div id="modal-saving" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SavingData" aria-hidden="true" data-backdrop="static" data-keyboard="false" >
			<div class="modal-dialog modal-dialog-center modal-md">
				<div class="modal-content">
					<div class="modal-header bg-red">
						<h4 class="modal-title" id="modal-saving-label"></h4>
					</div>
					<div class="modal-body" style="text-align:center;">
						<img src="<?php echo base_url("assets/img/ajax-loader1.gif") ?>" alt="<?php echo lang('app.loading') ?>" />
					</div>
					<div class="modal-footer modal-footer-center">
						<h6>Page will automatically reload as soon as the process terminate.</h6>
					</div>
				</div>
			</div>
		</div>

		<div id="modal-log" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Logs" aria-hidden="true" data-backdrop="static" data-keyboard="false" >
			<div class="modal-dialog modal-dialog-center modal-md">
				<div class="modal-content">
					<div class="modal-header bg-red">
						<h4 class="modal-title" id="modal-log-label"></h4>
					</div>
					<div class="modal-body">
						<p>Please take care of the lines below, here you could find the problem why your miner is not running:</p>
						<blockquote class="modal-log-lines"></blockquote>
					</div>
					<div class="modal-footer">
						<h6 class="pull-left">if you are still in trouble please check also <a href="<?php echo base_url($this->config->item("minerd_log_url")); ?>" target="_blank"><i class="fa fa-briefcase"></i> the full log here</a></h6>
						<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		<div id="modal-sharing" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SharingData" aria-hidden="true" data-backdrop="static" data-keyboard="false" >
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header bg-blue">
						<h4 class="modal-title" id="modal-sharing-label"><i class="fa fa-share-square-o"></i> Share your config</h4>
					</div>
					<div class="modal-body">
						<p>Before you can share your config with the Minera community you need to add a description, please add helpful infos like devices used and notes for users.<br />Only miner software and miner settings along with this description will be shared, no pools info.</p>
						<form method="post" id="formsharingconfig">
							<div class="form-group">
								<label>Config description</label>
								<textarea name="config_description" class="form-control" rows="5" placeholder="Example: Used with Gridseed Blade and Zeus Blizzard, adjust clock and chips for your needs" class="config-description"></textarea>
								<input type="hidden" name="config_id" value="" />
							</div>
						</form>
						<h6>Each config will be moderated before being available in the public repository. (Available soon on <a href="http://getminera.com">Getminera.com</a>)</h6>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-primary share-config-action" data-config-id="">Share config</button>
					</div>
				</div>
			</div>
		</div>
		
		<div id="modal-terminal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-dialog-center modal-terminal">
				<div class="modal-content">
					<div class="modal-header bg-blue">
						<button type="button" class="close modal-hide"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="modal-saving-label"><i class="fa fa-terminal"></i> Minera terminal window</h4>
					</div>
					<div class="modal-body bg-black" style="text-align:center;">
						<iframe id="terminal-iframe" src="" style="" width="100%" height="450" frameborder="0"></iframe>
					</div>
					<div class="modal-footer modal-footer-center">
						<h6>This is a full terminal window running on your Minera system, use any user you want to login, but remember Minera runs as user "minera" and you should use this for each operation you wanna do.</h6>
					</div>
				</div>
			</div>
		</div>
		
        <header class="header" data-this-section="<?php echo $sectionPage ?>">

            <a href="<?php echo site_url('app/dashboard') ?>" class="logo"><?php echo lang('app.title') ?></a>

            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                
				<div class="navbar-right">
					<ul class="nav navbar-nav">
						<!-- Cron status -->
						<li class="messages-menu cron-status" style="display:none;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-spin fa-cog"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">Cron is running</li>
                                <li>
                                	<ul class="menu">
	                                	<li class="p10">
		                                	Cron process runs every minute and should take not more than 30 seconds to terminate.<br />
		                                	If this icon is permanent, click the button below to unlock the cron.
		                                	<div class="text-center mt10">
			                                	<button class="btn btn-default cron-unlock">Unlock cron</button>
		                                	</div>
	                                	</li>
                                	</ul>
                                </li>
                            </ul>
                        </li>
						<!-- Clock -->
						<li class="messages-menu">
                            <a href="#" class="clock">
                                <i class="fa fa-clock-o"></i> <span class="toptime"></span>
                            </a>
                        </li>

                        <!-- Browser mining -->
                        <?php if ($browserMining) : ?>
						<li class="messages-menu messages-bm">
                            <a href="#" class="bmWarm">
                                <i class="fa fa-globe"></i> <span>Warming up...</span>
                            </a>
                            <a href="#" class="dropdown-toggle bmHash" data-toggle="dropdown" style="display:none;">
                                <i class="fa fa-globe"></i> <span class="bmHashText"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-mining">
                                <li class="header">Browser mining</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu avg-stats" style="overflow: hidden; width: 100%; height: 200px;">
	                                   	<li>
											<a href="#">
												<div class="pull-left" style="padding-left:15px;">
													<i class="fa fa-trophy"></i>
												</div>
												<h4><span class="bmAccepted"></span><small><i class="fa fa-globe"></i> Accepted hashes</small></h4>
												<p>Browser mining hash</p>
											</a>
										</li>
										<li>
											<div class="text-center">
												<h1><a href="#" data-toggle="tooltip" title="Increase CPU threads" class="increase-mining-threads"><i class="fa fa-plus"></i></a> <span class="bm-threads ml10 mr10 fs72"></span> <a href="#" data-toggle="tooltip" title="Decrease CPU threads" class="decrease-mining-threads"><i class="fa fa-minus"></i></a></h1>
											</div>
										</li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="<?php echo site_url("app/settings") ?>">Go to settings</a></li>
                            </ul>
						</li>
						<?php endif; ?>

                        <!-- Averages -->
						<li class="messages-menu messages-avg">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-dashboard"></i> <span>Calculating...</span>
                            </a>
							<!-- BEGIN: Underscore Template Definition. -->
							<script type="text/template" class="avg-stats-template">
	                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
	                                <i class="fa fa-dashboard"></i> <span><%- rc.avgonemin %> <i class="fa <%= rc.arrow %>" style="color:<%= rc.color %>;"></i></span>
	                            </a>
	                            <ul class="dropdown-menu">
	                                <li class="header">Average stats</li>
	                                <li>
	                                    <!-- inner menu: contains the actual data -->
	                                    <ul class="menu avg-stats" style="overflow: hidden; width: 100%; height: 200px;">
		                                    <%	_.each( rc.avgs, function( avg ) { %>
												<li>
													<a href="#">
														<div class="pull-left" style="padding-left:15px;">
															<i class="fa <%= avg.arrow %>" style="color:<%= avg.color %>;"></i>
														</div>
														<h4><%- avg.hrCurrentText %><small><i class="fa fa-dashboard"></i> Pool Hashrate</small></h4>
														<p><%- avg.key %></p>
													</a>
												</li>
											<% }); %>
	                                    </ul>
	                                </li>
	                                <li class="footer"><a href="<?php echo site_url("app/charts") ?>">Go to Charts</a></li>
	                            </ul>
	                        </script>
							<!-- END: Underscore Template Definition. -->
						</li>

						<!-- BTC/USD rates -->
						<li class="messages-menu messages-btc-rates">
							<!-- BEGIN: Underscore Template Definition. -->
							<script type="text/template" class="btc-rates-template">
	                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
	                                <i class="fa fa-btc"></i> price: <%- rc.btc_rates.last %> <i class="fa fa-dollar"></i> <span class="small">(<%- rc.btc_rates.last_eur %> <i class="fa fa-eur"></i>)</span>
	                            </a>
	                            <ul class="dropdown-menu">
	                                <li class="header">Data from Bitstamp</li>
	                                <li>
	                                    <!-- inner menu: contains the actual data -->
	                                    <ul class="menu" style="overflow: hidden; width: 100%;">
	                                        <li>
	                                            <a href="#">
	                                            	<div class="pull-left" style="padding-left:15px;">
	                                                    <i class="fa fa-archive"></i>
	                                                </div>
	                                                <h4>
	                                                    <%- rc.btc_rates.volume %>
	                                                    <small><i class="fa fa-clock-o"></i> <%- moment(rc.btc_rates.timestamp, 'X').format('hh:mm:ss a') %></small>
	                                                </h4>
	                                                <p>Volume</p>
	                                            </a>
	                                        </li>
	                                        <li>
	                                            <a href="#">
	                                            	<div class="pull-left" style="padding-left:15px;">
	                                                    <i class="fa fa-arrow-circle-up"></i>
	                                                </div>
	                                                <h4>
	                                                    <%- rc.btc_rates.high %> <i class="fa fa-dollar"></i> <span class="small">(<%- rc.btc_rates.high_eur %> <i class="fa fa-eur"></i>)</span>
	                                                    <small><i class="fa fa-clock-o"></i> <%- moment(rc.btc_rates.timestamp, 'X').format('hh:mm:ss a') %></small>
	                                                </h4>
	                                                <p>High</p>
	                                            </a>
	                                        </li>
	                                        <li>
	                                            <a href="#">
	                                            	<div class="pull-left" style="padding-left:15px;">
	                                                    <i class="fa fa-arrow-circle-down"></i>
	                                                </div>
	                                                <h4>
	                                                    <%- rc.btc_rates.low %> <i class="fa fa-dollar"></i> <span class="small">(<%- rc.btc_rates.low_eur %> <i class="fa fa-eur"></i>)</span>
	                                                    <small><i class="fa fa-clock-o"></i> <%- moment(rc.btc_rates.timestamp, 'X').format('hh:mm:ss a') %></small>
	                                                </h4>
	                                                <p>Low</p>
	                                            </a>
	                                        </li>
	                                        <li>
	                                            <a href="#">
	                                            	<div class="pull-left" style="padding-left:15px;">
	                                                    <i class="fa fa-exchange"></i>
	                                                </div>
	                                                <h4>
	                                                    1 <i class="fa fa-eur"></i> / <%- rc.btc_rates.eur_usd %> <i class="fa fa-dollar"></i>
	                                                    <small><i class="fa fa-clock-o"></i> <%- moment(rc.btc_rates.timestamp, 'X').format('hh:mm:ss a') %></small>
	                                                </h4>
	                                                <p>Eur/Usd Rate</p>
	                                            </a>
	                                        </li>
	                                    </ul>                                </li>
	                                
	                            </ul>
	                        </script>
							<!-- END: Underscore Template Definition. -->
						</li>

					    <!-- Donate/Help dropdown -->
					    <li class="dropdown user user-menu">
					        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
					            <i class="glyphicon glyphicon-user"></i>
					            <span><i class="caret"></i></span>
					        </a>
					        <ul class="dropdown-menu">
					            <!-- User image -->
					            <li class="user-header bg-dark-grey">
                                    <a href="<?php echo site_url("app/logout") ?>" style="color: #ffffff;" class="btn btn-danger btn-flat"><?php echo lang('app.logout') ?></a>
					            </li>
					        </ul>
					    </li>
					</ul>
				</div>
			</nav>
        </header>
        
        <!-- Main content -->
        <div class="wrapper row-offcanvas row-offcanvas-left">

			<!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar main panel -->
                    <div class="user-panel">
                        <div class="pull-left info">
							<?php if ($isOnline) : ?>
	                            <a href="<?php echo site_url("app/dashboard") ?>"><i class="fa fa-circle text-success"></i> <?php echo lang('app.online') ?> </a>
	                        <?php else: ?>
	                            <a href="<?php echo site_url("app/settings") ?>" data-toggle="tooltip" title="" data-original-title="<?php echo lang('app.go_to_settings') ?>"><i class="fa fa-circle text-muted"></i> <?php echo lang('app.offline') ?> <?php if ($minerdSoftware) : ?><small class="pull-right badge bg-muted"><?php echo $minerdSoftware ?></small><?php endif; ?></a>
							<?php endif; ?>
                        </div>
                    </div>

                    <!-- sidebar menu -->
                    <ul class="sidebar-menu">
	                    <?php if ($sectionPage === "dashboard" && (($isOnline && $appScript) || (is_array($netMiners) && count($netMiners) > 0))) : ?>
                        	<li data-toggle="tooltip" title="" data-original-title="<?php echo lang('app.refresh_dashboard') ?>">
                            	<a href="#" class="refresh-btn">
                                	<i class="fa fa-refresh refresh-icon mr5" style="width: inherit;"></i> <span><?php echo lang('app.refresh') ?></span><span class="badge bg-muted pull-right auto-refresh-time">auto in</span>
								</a>
							</li>
						<?php endif; ?>
                        <li class="treeview" data-toggle="tooltip" title="" data-original-title="<?php echo lang('app.go_to_dashboard') ?>">
                        	<a href="<?php echo site_url("app/dashboard") ?>">
                        		<i class="fa fa-dashboard"></i> 
                        		<span><?php echo lang('app.dashboard') ?></span>
                                <i class="treeview-menu-dashboard-icon fa pull-right fa-angle-left"></i>
                        	</a>
                        	<ul class="treeview-menu treeview-menu-dashboard" style="display: none;">
                                <li>
                                	<a href="<?php echo site_url("app/dashboard#box-widgets") ?>" class="menu-top-widgets-box ml10">
                                		<i class="fa fa-th"></i> <?php echo lang('app.widgets') ?>
                                	</a>
                                </li>
                                <li>
                                	<a href="<?php echo site_url("app/dashboard#box-local-miner") ?>" class="menu-local-miner-box ml10">
                                		<i class="fa fa-desktop"></i> <?php echo lang('app.local_miner') ?>
                                	</a>
                                </li>
                                <li>
                                	<a href="<?php echo site_url("app/dashboard#box-local-pools") ?>" class="menu-local-pools-box ml10">
                                		<i class="fa fa-cloud"></i> <?php echo lang('app.local_pools') ?>
                                	</a>
                                </li>
                                <li>
                                	<a href="<?php echo site_url("app/dashboard#box-charts") ?>" class="menu-charts-box ml10">
                                		<i class="fa fa-bar-chart"></i> <?php echo lang('app.charts') ?>
                                	</a>
                                </li>
                                <li>
                                	<a href="<?php echo site_url("app/dashboard#box-log") ?>" class="menu-log-box ml10">
                                		<i class="fa fa-file"></i> <?php echo lang('app.log') ?>
                                	</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-charts" data-toggle="tooltip" title="" data-original-title="<?php echo lang('app.go_to_charts') ?>">
                            <a href="<?php echo site_url("app/charts") ?>">
                                <i class="fa fa-bar-chart-o"></i> <span><?php echo lang('app.charts') ?></span>
                            </a>
                        </li>
                        <li class="treeview">
                        	<a href="#">
                        		<i class="fa fa-gear"></i> 
                        		<span><?php echo lang('app.settings') ?></span>
                                <i class="treeview-menu-settings-icon fa pull-right fa-angle-left"></i>
                        	</a>
                        	<ul class="treeview-menu treeview-menu-settings" style="display: none;">

                                <li>
                                	<a href="<?php echo site_url("app/settings#pools-box") ?>" class="menu-pools-box ml10">
                                		<i class="fa fa-cloud"></i> <?php echo lang('app.pools') ?>
                                	</a>
                                </li>

                                <li>
                                	<a href="<?php echo site_url("app/settings#user-box") ?>" class="menu-user-box ml10">
                                		<i class="fa fa-user"></i> <?php echo lang('app.user') ?>
                                	</a>
                                </li>
                                <li>
                                	<a href="<?php echo site_url("app/settings#resets-box") ?>" class="menu-resets-box ml10">
                                		<i class="fa fa-warning"></i> <?php echo lang('app.resets') ?>
                                	</a>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-desktop"></i>
                                <span><?php echo lang('app.miner') ?></span>
                                <i class="fa pull-right fa-angle-left"></i>
                            </a>
                            <ul class="treeview-menu" style="display: none;">
                                <li data-toggle="tooltip" title="" data-original-title="<?php echo lang('app.restart_miner_title') ?>">
                                	<a href="#" class="miner-action" data-miner-action="restart" style="margin-left: 10px;">
                                		<i class="fa fa-repeat"></i> <?php echo lang('app.restart_miner') ?>
                                	</a>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-rocket"></i>
                                <span><?php echo lang('app.system') ?></span>
                                <i class="fa pull-right fa-angle-left"></i>
                            </a>
                            <ul class="treeview-menu" style="display: none;">
                                <li data-toggle="tooltip" title="" data-original-title="<?php echo lang('app.reboot_title') ?>">
                                	<a href="<?php echo site_url("app/reboot") ?>" style="margin-left: 10px;">
                                		<i class="fa fa-power-off"></i> <?php echo lang('app.reboot') ?>
                                	</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </section>
                
                <!-- /.sidebar -->
            </aside>
            
