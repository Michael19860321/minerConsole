<?php if (!defined('BASEPATH')) die();

class App extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		self::__initLanguage();

		// Set the general timezone
		$timezone = ($this->redis->get("minera_timezone")) ? $this->redis->get("minera_timezone") : 'Asia/Shanghai';
		date_default_timezone_set($timezone);

	}

	private function __initLanguage(){
		$lang_support = config_item('support_language');
		$lang_url    = $this->uri->segment(1);
		$lang_default = config_item('language');
		//$lang_current = ( FALSE !== in_array($lang_url, $lang_support) ) ? $lang_url : $lang_default;
		$lang_current = empty($this->session->userdata("language")) ?  $lang_default : $this->session->userdata("language");

		$this->config->set_item('language', $lang_current);
		$this->lang->load('app', $lang_current);
		$this->load->helper('language');
		
		return;
	}

	/*
	 * switch lanuage
	 */
    public function switchLanguage()
    {
		$lang_default = config_item('language');

        $lang_switch  = empty($this->input->get('lang')) ? $lang_default : $this->input->get('lang');
		$lang_current = empty($this->session->userdata("language")) ?  $lang_default : $this->session->userdata("language");
		
		$this->session->set_userdata("language", $lang_switch);
		$this->lang->load('app', $lang_switch);

        redirect('app/index'); // redirect to index
    }
	
	/*
	// Index/lock screen controller
	*/
	public function index()
	{

		// Always try to assign the mineraId if not present
		$mineraSystemId = $this->util_model->generateMineraId();
		$this->redis->del("minera_update");
		
		//$this->util_model->checkUpdate();
				
		if (!$this->redis->command("EXISTS dashboard_devicetree")) $this->redis->set("dashboard_devicetree", 1);
		if (!$this->redis->command("EXISTS dashboard_box_profit")) $this->redis->set("dashboard_box_profit", 1);
		if (!$this->redis->command("EXISTS dashboard_box_local_miner")) $this->redis->set("dashboard_box_local_miner", 1);
		if (!$this->redis->command("EXISTS dashboard_box_local_pools")) $this->redis->set("dashboard_box_local_pools", 1);
		if (!$this->redis->command("EXISTS dashboard_box_network_details")) $this->redis->set("dashboard_box_network_details", 1);
		if (!$this->redis->command("EXISTS dashboard_box_network_pools_details")) $this->redis->set("dashboard_box_network_pools_details", 1);
		if (!$this->redis->command("EXISTS dashboard_box_chart_shares")) $this->redis->set("dashboard_box_chart_shares", 1);
		if (!$this->redis->command("EXISTS dashboard_box_chart_system_load")) $this->redis->set("dashboard_box_chart_system_load", 1);
		if (!$this->redis->command("EXISTS dashboard_box_chart_hashrates")) $this->redis->set("dashboard_box_chart_hashrates", 1);
		if (!$this->redis->command("EXISTS dashboard_box_scrypt_earnings")) $this->redis->set("dashboard_box_scrypt_earnings", 1);
		if (!$this->redis->command("EXISTS dashboard_box_log")) $this->redis->set("dashboard_box_log", 1);

		$miner_pools = $this->redis->get("minerd_pools");
		$pools = $this->util_model->getPools();
		
		$data['now'] = time();
		$data['minera_system_id'] = $mineraSystemId;
		$data['minera_version'] = $this->util_model->currentVersion(true);
		$data['browserMining'] = $this->redis->get('browser_mining');
		$data['browserMiningThreads'] = $this->redis->get('browser_mining_threads');
		$data['env'] = $this->config->item('ENV');
		$data['sectionPage'] = 'lockscreen';
		$data['htmlTag'] = "lockscreen";
		$data['isOnline'] = $this->util_model->isOnline();

		$this->load->view('include/header', $data);
		$this->load->view('lockscreen');
		$this->load->view('include/footer', $data);
	}
	
	/*
	// Login controller
	*/
	public function login()
	{	
		$storedp = $this->redis->get('minera_password');
		if (preg_match('/^[0-9a-f]{40}$/', $storedp)) {
			$storedp = $storedp;
		} elseif (preg_match('/^[a-f0-9]{32}$/', $storedp)) {
			if ($this->input->post('password', true) && md5($this->input->post('password')) == $storedp) {
				$storedp = sha1($this->input->post('password', true));
				$this->redis->set('minera_password', $storedp);
			}
		} else {
			$storedp = sha1($this->config->item('init_console_password'));
			
			$this->redis->set('minera_password', $storedp);
		}

		if ($this->input->post('password', true) && sha1($this->input->post('password')) == $storedp) {
			$this->session->set_userdata("loggedin", $storedp);
			redirect('app/dashboard');
		}
		else
			redirect('app/index');
	}

	/*
	// Logout controller
	*/
	public function logout()
	{	
		$this->session->set_userdata("loggedin", null);
		redirect('app/index'); // redirect to index
	}
	
	/*
	// Dashboard controller
	*/
	public function dashboard()
	{

		$this->util_model->isLoggedIn();
		
		//var_export($this->redis->command("HGETALL box_status"));
		$boxStatuses = json_decode($this->redis->get("box_status"), true);

		$data['boxStatuses'] = array();
		if (isset($boxStatuses)) {
			$data['boxStatuses'] = $boxStatuses;
		}

		$data['now'] = time();
		$data['sectionPage'] = 'dashboard';
		$data['minerdPools'] = json_decode($this->util_model->getPools());
		$data['isOnline'] = $this->util_model->isOnline();
		$data['minerdLog'] = $this->redis->get('minerd_log');
		$data['savedFrequencies'] = $this->redis->get('current_frequencies');
		$data['htmlTag'] = "dashboard";
		$data['appScript'] = true;
		$data['settingsScript'] = false;
		$data['mineraUpdate'] = $this->util_model->checkUpdate();
		$data['dashboard_refresh_time'] = $this->redis->get("dashboard_refresh_time");
		$data['dashboardTableRecords'] = $this->redis->get("dashboard_table_records");
		$data['dashboardDevicetree'] = ($this->redis->get("dashboard_devicetree")) ? $this->redis->get("dashboard_devicetree") : false;
		$data['dashboardBoxProfit'] = ($this->redis->get("dashboard_box_profit")) ? $this->redis->get("dashboard_box_profit") : false;
		$data['dashboardBoxLocalMiner'] = ($this->redis->get("dashboard_box_local_miner")) ? $this->redis->get("dashboard_box_local_miner") : false;
		$data['dashboardBoxLocalPools'] = ($this->redis->get("dashboard_box_local_pools")) ? $this->redis->get("dashboard_box_local_pools") : false;
		$data['dashboardBoxNetworkDetails'] = ($this->redis->get("dashboard_box_network_details")) ? $this->redis->get("dashboard_box_network_details") : false;
		$data['dashboardBoxNetworkPoolsDetails'] = ($this->redis->get("dashboard_box_network_pools_details")) ? $this->redis->get("dashboard_box_network_pools_details") : false;
		$data['dashboardBoxChartShares'] = ($this->redis->get("dashboard_box_chart_shares")) ? $this->redis->get("dashboard_box_chart_shares") : false;
		$data['dashboardBoxChartSystemLoad'] = ($this->redis->get("dashboard_box_chart_system_load")) ? $this->redis->get("dashboard_box_chart_system_load") : false;
		$data['dashboardBoxChartHashrates'] = ($this->redis->get("dashboard_box_chart_hashrates")) ? $this->redis->get("dashboard_box_chart_hashrates") : false;
		$data['dashboardBoxScryptEarnings'] = ($this->redis->get("dashboard_box_scrypt_earnings")) ? $this->redis->get("dashboard_box_scrypt_earnings") : false;
		$data['dashboardBoxLog'] = ($this->redis->get("dashboard_box_log")) ? $this->redis->get("dashboard_box_log") : false;
		$data['dashboardSkin'] = ($this->redis->get("dashboard_skin")) ? $this->redis->get("dashboard_skin") : "black";
		$data['minerdRunning'] = $this->redis->get("minerd_running_software");
		$data['minerdRunningUser'] = $this->redis->get("minerd_running_user");
		$data['minerdSoftware'] = $this->redis->get("minerd_software");
		$data['netMiners'] = $this->util_model->getNetworkMiners();
		$data['localAlgo'] = "SHA-256";
		$data['browserMining'] = $this->redis->get('browser_mining');
		$data['browserMiningThreads'] = $this->redis->get('browser_mining_threads');
		$data['env'] = $this->config->item('ENV');
		$data['mineraSystemId'] = $this->redis->get("minera_system_id");
		
		$this->load->view('include/header', $data);
		$this->load->view('include/sidebar', $data);
		$this->load->view('frontpage', $data);
		$this->load->view('include/footer', $data);
	}
	
	/*
	// Charts controller
	*/
	public function charts()
	{
		$this->util_model->isLoggedIn();
		
		$data['now'] = time();
		$data['sectionPage'] = 'charts';
		$data['isOnline'] = $this->util_model->isOnline();
		$data['htmlTag'] = "charts";
		$data['chartsScript'] = true;
		$data['appScript'] = false;
		$data['settingsScript'] = false;
		$data['mineraUpdate'] = $this->util_model->checkUpdate();
		$data['dashboard_refresh_time'] = $this->redis->get("dashboard_refresh_time");
		$data['dashboardTableRecords'] = $this->redis->get("dashboard_table_records");
		$data['minerdLog'] = $this->redis->get('minerd_log');
		$data['dashboardSkin'] = ($this->redis->get("dashboard_skin")) ? $this->redis->get("dashboard_skin") : "black";
		$data['dashboardDevicetree'] = ($this->redis->get("dashboard_devicetree")) ? $this->redis->get("dashboard_devicetree") : false;
		$data['minerdRunning'] = $this->redis->get("minerd_running_software");
		$data['minerdRunningUser'] = $this->redis->get("minerd_running_user");		
		$data['minerdSoftware'] = $this->redis->get("minerd_software");
		$data['netMiners'] = $this->util_model->getNetworkMiners();
		$data['browserMining'] = $this->redis->get('browser_mining');
		$data['browserMiningThreads'] = $this->redis->get('browser_mining_threads');
		$data['env'] = $this->config->item('ENV');
		$data['mineraSystemId'] = $this->redis->get("minera_system_id");
		
		$this->load->view('include/header', $data);
		$this->load->view('include/sidebar', $data);
		$this->load->view('charts', $data);
		$this->load->view('include/footer');
	}

	/*
	// Settings controller
	*/
	public function settings()
	{
		$this->util_model->isLoggedIn();
		
		$data['now'] = time();
		$data['sectionPage'] = 'settings';

		$data['message'] = false;
		$data['message_type'] = false;

		if ($this->input->post('save_password'))
		{
			$password = trim($this->input->post('password'));
			$password2 = trim($this->input->post('password2'));
			if (empty($password) && empty($password2))
			{
				$data['message'] = "<b>Warning!</b> Password can't be empty";
				$data['message_type'] = "warning";
			}
			elseif ($password != $password2)
			{
				$data['message'] = "<b>Warning!</b> Password mismatch";
				$data['message_type'] = "warning";				
			}
			else
			{
				$this->redis->set("minera_password", sha1($password));
				$data['message'] = '<b>Success!</b> Password saved!';
				$data['message_type'] = "success";
			}
		}

		if ($this->input->post('save_miner_pools'))
		{
			$poolUrls = $this->input->post('pool_url');
			$poolUsernames = $this->input->post('pool_username');
			$poolPasswords = $this->input->post('pool_password');

			$pools = array();
			foreach ($poolUrls as $key => $poolUrl)
			{
				if ($poolUrl)
				{
					if (isset($poolUsernames[$key]) && isset($poolPasswords[$key]))
					{
						$pools[] = array("url" => $poolUrl, "user" => $poolUsernames[$key], "pass" => $poolPasswords[$key]);
					}
				}
			}
			$confArray = array();			
			$confArray['pools'] = $pools;
			$jsonPoolsConfRedis = json_encode($pools);
			$this->redis->set('minerd_pools', $jsonPoolsConfRedis);

			// Prepare JSON conf
			$jsonConfFile = json_encode($confArray, 192); // JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES = 128 + 64

			// Save the JSON conf file
			file_put_contents($this->config->item("minerd_conf_temp_file"), $jsonConfFile);
			sleep(3);
			exec("sudo mv " . $this->config->item('minerd_conf_temp_file') . " " . $this->config->item('minerd_conf_file'));
			sleep(2);

			$this->util_model->restartCgminer();
			$data['message'] = '<b>Success!</b> pools are saved!';
			$data['message_type'] = "success";

		}

		if ($this->input->post('firmware_upgrade')) 
		{
			$fileInfo = $_FILES["upFile"];
			
			$fileInfoName = $fileInfo["name"];//文件名
			$fileInfoPath = $fileInfo["tmp_name"];//文件当前路径文件夹
			if (!move_uploaded_file($fileInfoPath,"/tmp/".$fileInfoName))
			{
				$data['message'] = "<b>Warning!</b> An error has occurred moving the uploaded file.<BR>Please ensure that if safe_mode is on that the " . "UID PHP is using matches the file.";
				$data['message_type'] = "warning";
			} else
			{
				sleep(5);
				exec("sudo chmod 755 /tmp/".$fileInfoName);
				exec("sudo system_update online /tmp/".$fileInfoName." >/tmp/upgrade.log &");
				$data['message'] = '<b>Success!</b> The upgrade will take couple of minutes, please wait!';
				$data['message_type'] = "success";
			}

		}

		if ($this->input->post('save_network')) 
		{
			$network_type = $this->input->post('network-type');

			$content = '';

			if ($network_type == 'static')
			{
				$ip_address = $this->input->post('ip_address');
				$net_mask = $this->input->post('net_mask');
				$gateway = $this->input->post('gateway');
				$dns = $this->input->post('dns');
				$content = 'auto lo'.PHP_EOL.'iface lo inet loopback'.PHP_EOL.'auto eth0'.PHP_EOL.'iface eth0 inet static'.PHP_EOL.'address '.$ip_address.PHP_EOL.'netmask '.$net_mask.PHP_EOL.'gateway '.$gateway.PHP_EOL.'dns-nameservers 8.8.8.8 114.114.114.114 '.$dns;
				$dns_content = 'nameserver 8.8.8.8'.PHP_EOL.'nameserver 114.114.114.114'.PHP_EOL ;
				shell_exec('sudo rm /etc/resolv.conf');
				shell_exec('sudo chmod 766 /tmp/resolv.conf');
				file_put_contents('/tmp/resolv.conf', $dns_content);
				shell_exec('sudo cp /tmp/resolv.conf /etc/resolv.conf');
			}
			else
			{
				$content = 'auto lo'.PHP_EOL.'iface lo inet loopback'.PHP_EOL.'auto eth0'.PHP_EOL.'iface eth0 inet dhcp'.PHP_EOL.' pre-up /etc/network/nfs_check'.PHP_EOL.'	wait-delay 15'.PHP_EOL.' hostname $(hostname)';
			}

			file_put_contents('/tmp/interfaces', $content);
			sleep(1);
			shell_exec('sudo cp /tmp/interfaces /etc/network');
			//shell_exec('rm /tmp/interfaces');
			shell_exec('sudo ip addr flush dev eth0');
			shell_exec('sudo sh /etc/init.d/S40network restart');
			$data['message'] = 'Success! IP saved!';
			$data['message_type'] = "success";
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($data));
			return;
		}
		
		// Load miner settings
		$data['builtInMinersConf'] = json_decode($this->util_model->refreshMinerConf());
		$data['minerdCommand'] = $this->config->item("minerd_command");
		$data['minerdAutorestart'] = $this->redis->get('minerd_autorestart');
		$data['minerdAutorestartDevices'] = $this->redis->get('minerd_autorestart_devices');
		$data['minerdAutorestartTime'] = $this->redis->get('minerd_autorestart_time');
		$data['minerdAutorecover'] = $this->redis->get('minerd_autorecover');
		$data['minerdUseRoot'] = $this->redis->get('minerd_use_root');
		$data['minerdScrypt'] = $this->redis->get('minerd_scrypt');
		$data['minerdAutodetect'] = $this->redis->get('minerd_autodetect');
		$data['minerdAutotune'] = $this->redis->get('minerd_autotune');
		$data['minerdStartfreq'] = $this->redis->get('minerd_startfreq');
		$data['minerdExtraoptions'] = $this->redis->get('minerd_extraoptions');
		$data['minerdSoftware'] = $this->redis->get('minerd_software');
		$data['minerdLog'] = $this->redis->get('minerd_log');
		$data['minerdDebug'] = $this->redis->get('minerd_debug');
		$data['minerdAppendConf'] = $this->redis->get('minerd_append_conf');
		$data['minerdManualSettings'] = $this->redis->get('minerd_manual_settings');
		$data['minerdSettings'] = $this->util_model->getCommandline();
		$data['minerdJsonSettings'] = $this->redis->get("minerd_json_settings");
		$data['minerdPools'] = $this->util_model->getPools();
		$data['minerdNetwork'] = $this->util_model->getIfconfig();
		$data['minerdGuidedOptions'] = $this->redis->get("guided_options");
		$data['minerdManualOptions'] = $this->redis->get("manual_options");
		$data['minerdDelaytime'] = $this->redis->get("minerd_delaytime");
		$data['minerApiAllowExtra'] = $this->redis->get("minerd_api_allow_extra");
		$data['globalPoolProxy'] = $this->redis->get("pool_global_proxy");
		$data['browserMining'] = $this->redis->get('browser_mining');
		$data['browserMiningThreads'] = $this->redis->get('browser_mining_threads');

		$data['networkMiners'] = json_decode($this->redis->get('network_miners'));
		$data['netMiners'] = $this->util_model->getNetworkMiners();
		
		// Load Dashboard settings
		$data['mineraStoredDonations'] = $this->util_model->getStoredDonations();
		$data['mineraDonationTime'] = $this->redis->get("minera_donation_time");
		$data['dashboard_refresh_time'] = $this->redis->get("dashboard_refresh_time");
		$dashboard_coin_rates = $this->redis->get("dashboard_coin_rates");
		$data['dashboard_coin_rates'] = (is_array(json_decode($dashboard_coin_rates))) ? json_decode($dashboard_coin_rates) : array();
		$data['cryptsy_data'] = $this->redis->get("cryptsy_data");
		$data['dashboardTemp'] = ($this->redis->get("dashboard_temp")) ? $this->redis->get("dashboard_temp") : "c";
		$data['dashboardSkin'] = ($this->redis->get("dashboard_skin")) ? $this->redis->get("dashboard_skin") : "black";
		$data['dashboardDevicetree'] = ($this->redis->get("dashboard_devicetree")) ? $this->redis->get("dashboard_devicetree") : false;
		$data['dashboardBoxProfit'] = ($this->redis->get("dashboard_box_profit")) ? $this->redis->get("dashboard_box_profit") : false;
		$data['dashboardBoxLocalMiner'] = ($this->redis->get("dashboard_box_local_miner")) ? $this->redis->get("dashboard_box_local_miner") : false;
		$data['dashboardBoxLocalPools'] = ($this->redis->get("dashboard_box_local_pools")) ? $this->redis->get("dashboard_box_local_pools") : false;
		$data['dashboardBoxNetworkDetails'] = ($this->redis->get("dashboard_box_network_details")) ? $this->redis->get("dashboard_box_network_details") : false;
		$data['dashboardBoxNetworkPoolsDetails'] = ($this->redis->get("dashboard_box_network_pools_details")) ? $this->redis->get("dashboard_box_network_pools_details") : false;
		$data['dashboardBoxChartShares'] = ($this->redis->get("dashboard_box_chart_shares")) ? $this->redis->get("dashboard_box_chart_shares") : false;
		$data['dashboardBoxChartSystemLoad'] = ($this->redis->get("dashboard_box_chart_system_load")) ? $this->redis->get("dashboard_box_chart_system_load") : false;
		$data['dashboardBoxChartHashrates'] = ($this->redis->get("dashboard_box_chart_hashrates")) ? $this->redis->get("dashboard_box_chart_hashrates") : false;
		$data['dashboardBoxScryptEarnings'] = ($this->redis->get("dashboard_box_scrypt_earnings")) ? $this->redis->get("dashboard_box_scrypt_earnings") : false;
		$data['dashboardBoxLog'] = ($this->redis->get("dashboard_box_log")) ? $this->redis->get("dashboard_box_log") : false;
		
		$data['dashboardTableRecords'] = ($this->redis->get("dashboard_table_records")) ? $this->redis->get("dashboard_table_records") : 5;
		$data['algo'] = "SHA-256";

		// Everything else
		$data['savedFrequencies'] = $this->redis->get('current_frequencies');
		$data['isOnline'] = $this->util_model->isOnline();
		$data['mineraUpdate'] = $this->util_model->checkUpdate();
		$data['htmlTag'] = "settings";
		$data['appScript'] = false;
		$data['settingsScript'] = true;
		$data['minerdRunning'] = $this->redis->get("minerd_running_software");
		$data['minerdRunningUser'] = $this->redis->get("minerd_running_user");
		$data['minerdSoftware'] = $this->redis->get("minerd_software");
		$data['donationProfitability'] = ($prof = $this->util_model->getAvgProfitability()) ? $prof : "0.00020";
		
		// Saved Configs
		$data['savedConfigs'] = $this->redis->command("HVALS saved_miner_configs");

		$this->load->view('include/header', $data);
		$this->load->view('include/sidebar', $data);
		$this->load->view('settings', $data);
		$this->load->view('include/footer');
	}
	
	/*
	// Save Settings controller
	*/
	public function save_settings()
	{
		$this->util_model->isLoggedIn();
		
		$extramessages = false;
		$dataObj = new stdClass();
		$mineraSystemId = $this->util_model->generateMineraId();
		
		if ($this->input->post('save_settings'))
		{
			/*$minerSoftware = $this->input->post('minerd_software');
			$this->redis->set("minerd_software", $minerSoftware);
			$this->util_model->switchMinerSoftware($minerSoftware);
			$dataObj->minerd_software = $minerSoftware;*/
			
			//$dashSettings = $this->input->post('dashboard_refresh_time');
			//$mineraDonationTime = $this->input->post('minera_donation_time');

			/*$coinRates = $this->input->post('dashboard_coin_rates');
			$this->redis->set("altcoins_update", (time()-3600));
			$dashboardTemp = $this->input->post('dashboard_temp');
			$dashboardSkin = $this->input->post('dashboard_skin');
			$dashboardTableRecords = $this->input->post('dashboard_table_records');
			$dashboardDevicetree = $this->input->post('dashboard_devicetree');
			$dashboardBoxProfit = $this->input->post('dashboard_box_profit');
			$dashboardBoxLocalMiner = $this->input->post('dashboard_box_local_miner');
			$dashboardBoxLocalPools = $this->input->post("dashboard_box_local_pools");
			$dashboardBoxNetworkDetails = $this->input->post("dashboard_box_network_details");
			$dashboardBoxNetworkPoolsDetails = $this->input->post("dashboard_box_network_pools_details");
			$dashboardBoxChartShares = $this->input->post("dashboard_box_chart_shares");
			$dashboardBoxChartSystemLoad = $this->input->post("dashboard_box_chart_system_load");
			$dashboardBoxChartHashrates = $this->input->post("dashboard_box_chart_hashrates");
			$dashboardBoxScryptEarnings = $this->input->post("dashboard_box_scrypt_earnings");
			$dashboardBoxLog = $this->input->post("dashboard_box_log");*/

			// Network miners
			/*$netMinersNames = $this->input->post('net_miner_name');
			$netMinersIps = $this->input->post('net_miner_ip');
			$netMinersPorts = $this->input->post('net_miner_port');
			$netMinersAlgos = $this->input->post('net_miner_algo');
			$netMinersTypes = $this->input->post('net_miner_type');*/

			// Network miners pools
			/*$netGroupPoolActives = $this->input->post('net_pool_active');
			$netGroupPoolUrls = $this->input->post('net_pool_url');
			$netGroupPoolUsernames = $this->input->post('net_pool_username');
			$netGroupPoolPasswords = $this->input->post('net_pool_password');*/

			/*$netMiners = array();
			foreach ($netMinersNames as $keyM => $netMinerName)
			{
				if (!empty($netMinerName))
				{
					if (isset($netMinersIps[$keyM]) && isset($netMinersPorts[$keyM]))
					{
						// Network Miners
						$netMiners[] = array("name" => $netMinerName, "ip" => $netMinersIps[$keyM], "port" => $netMinersPorts[$keyM], "algo" => $netMinersAlgos[$keyM], "type" => $netMinersTypes[$keyM], "pools" => array());
					}
				}
			}

			$this->redis->set('network_miners', json_encode($netMiners));
			$dataObj->network_miners = json_encode($netMiners);*/
			
			// Save Custom miners
			// $dataObj->custom_miners = $this->input->post('active_custom_miners');
			// $this->redis->set('active_custom_miners', json_encode($this->input->post('active_custom_miners')));
		
			// Start creating command options string
			$settings = null;
			$confArray = array();

			if ($minerSoftware != "cpuminer")
			{
				$confArray["api-listen"] = true;
				$confArray["api-allow"] = "W:127.0.0.1";
			}
			
			// Save manual/guided selection
			/*$this->redis->set('manual_options', $this->input->post('manual_options'));
			$this->redis->set('guided_options', $this->input->post('guided_options'));
			$dataObj->manual_options = $this->input->post('manual_options');
			$dataObj->guided_options = $this->input->post('guided_options');*/
						
			// Append JSON conf
			$this->redis->set('minerd_append_conf', $this->input->post('minerd_append_conf'));
			$this->minerd_append_conf = $this->input->post('minerd_append_conf');				

			// Add the pools to the command
			$poolUrls = $this->input->post('pool_url');
			$poolUsernames = $this->input->post('pool_username');
			$poolPasswords = $this->input->post('pool_password');

			$pools = array();
			foreach ($poolUrls as $key => $poolUrl)
			{
				if ($poolUrl)
				{
					if (isset($poolUsernames[$key]) && isset($poolPasswords[$key]))
					{
						$pools[] = array("url" => $poolUrl, "username" => $poolUsernames[$key], "password" => $poolPasswords[$key]);
					}
				}
			}
			$poolsArray = array();
								
			$poolsArray = $this->util_model->parsePools($minerSoftware, $pools);
			
			$confArray['pools'] = $poolsArray;
			
			// Prepare JSON conf
			$jsonConfRedis = json_encode($confArray);
			$jsonConfFile = json_encode($confArray, JSON_PRETTY_PRINT);
			
			// Add JSON conf to miner command
			/*$exportConfigSettings = $settings;
			if ($this->redis->get('minerd_append_conf')) {
				$settings .= " -c ".$this->config->item("minerd_conf_file");	
			}*/
			
			// Save the JSON conf file
			// file_put_contents($this->config->item("minerd_conf_file"), $jsonConfFile);

			// End command options string			

			$this->util_model->setPools($pools);
			
			/*$this->util_model->setCommandline($settings);
			$this->redis->set("minerd_json_settings", $jsonConfRedis);
			$dataObj->minerd_json_settings = $jsonConfRedis;
			$this->redis->set("minerd_autorecover", $this->input->post('minerd_autorecover'));
			$dataObj->minerd_autorecover = $this->input->post('minerd_autorecover');
			$this->redis->set("minerd_use_root", $this->input->post('minerd_use_root'));
			$dataObj->minerd_use_root = $this->input->post('minerd_use_root');
			$this->redis->set("minerd_autorestart", $this->input->post('minerd_autorestart'));
			$dataObj->minerd_autorestart = $this->input->post('minerd_autorestart');
			$this->redis->set("minerd_autorestart_devices", $this->input->post('minerd_autorestart_devices'));
			$dataObj->minerd_autorestart_devices = $this->input->post('minerd_autorestart_devices');
			($this->input->post('minerd_autorestart_time') > 0) ? $this->redis->set("minerd_autorestart_time", $this->input->post('minerd_autorestart_time')) : 600;
			$dataObj->minerd_autorestart_time = $this->input->post('minerd_autorestart_time');
			$this->redis->set("minera_donation_time", $mineraDonationTime);
			$dataObj->minera_donation_time = $mineraDonationTime;
			$this->redis->set("dashboard_refresh_time", $dashSettings);
			$dataObj->dashboard_refresh_time = $dashSettings;
			$this->redis->set("dashboard_temp", $dashboardTemp);
			$dataObj->dashboard_temp = $dashboardTemp;
			$this->redis->set("dashboard_skin", $dashboardSkin);
			$dataObj->dashboard_skin = $dashboardSkin;
			$this->redis->set("dashboard_table_records", $dashboardTableRecords);
			$dataObj->dashboard_table_records = $dashboardTableRecords;*/
			
			//$this->redis->set("dashboard_devicetree", $dashboardDevicetree);
			//$dataObj->dashboard_devicetree = $dashboardDevicetree;
			//$this->redis->set("dashboard_box_profit", $dashboardBoxProfit);
			//$dataObj->dashboard_box_profit = $dashboardBoxProfit;
			//$this->redis->set("dashboard_box_local_miner", $dashboardBoxLocalMiner);
			//$dataObj->dashboard_box_local_miner = $dashboardBoxLocalMiner;
			//$this->redis->set("dashboard_box_local_pools", $dashboardBoxLocalPools);
			//$dataObj->dashboard_box_local_pools = $dashboardBoxLocalPools;
			//$this->redis->set("dashboard_box_network_details", $dashboardBoxNetworkDetails);
			//$dataObj->dashboard_box_network_details = $dashboardBoxNetworkDetails;
			//$this->redis->set("dashboard_box_network_pools_details", $dashboardBoxNetworkPoolsDetails);
			//$dataObj->dashboard_box_network_pools_details = $dashboardBoxNetworkPoolsDetails;
			//$this->redis->set("dashboard_box_chart_shares", $dashboardBoxChartShares);
			//$dataObj->dashboard_box_chart_shares = $dashboardBoxChartShares;
			//$this->redis->set("dashboard_box_chart_system_load", $dashboardBoxChartSystemLoad);
			//$dataObj->dashboard_box_chart_system_load = $dashboardBoxChartSystemLoad;
			//$this->redis->set("dashboard_box_chart_hashrates", $dashboardBoxChartHashrates);
			//$dataObj->dashboard_box_chart_hashrates = $dashboardBoxChartHashrates;
			//$this->redis->set("dashboard_box_scrypt_earnings", $dashboardBoxScryptEarnings);
			//$dataObj->dashboard_box_scrypt_earnings = $dashboardBoxScryptEarnings;
			//$this->redis->set("dashboard_box_log", $dashboardBoxLog);
			//$dataObj->dashboard_box_log = $dashboardBoxLog;
			
			/*if ($this->redis->get("dashboard_coin_rates") !== json_encode($coinRates)) {
				$this->redis->set("dashboard_coin_rates", json_encode($coinRates));
				$dataObj->dashboard_coin_rates = json_encode($coinRates);
				$this->util_model->updateAltcoinsRates(true);
			}
			
			if ($mineraDonationTime)
			{
				$this->util_model->autoAddMineraPool();
			}
			
			$dataObj->minerd_pools = $this->util_model->getPools();*/
			
			// System settings
			
			// System hostname
			/*if ($this->input->post('system_hostname')) 
			{
				$this->util_model->setSystemHostname($this->input->post('system_hostname'));
			}*/
			
			// Minera user password
			/*if ($this->input->post('system_password') && $this->input->post('system_password2')) 
			{
				$this->util_model->setSystemUserPassword($this->input->post('system_password'));
			}*/
			
			// Set the System Timezone
			/*$timezone = $this->input->post('minera_timezone');
			$currentTimezone = $this->redis->get("minera_timezone");
			if ($currentTimezone != $timezone)
			{
				date_default_timezone_set($timezone);
				$this->util_model->setTimezone($timezone);
			}
			$dataObj->minera_timezone = $timezone;*/
			
			// Delay time
			/*$delay = 5;
			if ($this->input->post('minerd_delaytime'))
			{
				$delay = $this->input->post('minerd_delaytime');
				$this->redis->set("minerd_delaytime", $delay);
			}
			$dataObj->minerd_delaytime = $delay;*/

			// On boot extra commands
			/*$extracommands = false;
			if ($this->input->post('system_extracommands'))
			{
				$extracommands = $this->input->post('system_extracommands');
			}
			$this->redis->set("system_extracommands", $extracommands);
			$dataObj->system_extracommands = $extracommands;*/
			
			// Scheduled event
			/*$scheduledEventTime = false; $scheduledEventAction = false; $scheduledEventStartTime = false;
			if ($this->input->post('scheduled_event_time'))
			{
				$scheduledEventStartTime = time();
				$scheduledEventTime = $this->input->post('scheduled_event_time');
				$scheduledEventAction = $this->input->post('scheduled_event_action');
			}
			if ($this->redis->get("scheduled_event_time") != $scheduledEventTime)
			{
				$this->redis->set("scheduled_event_start_time", $scheduledEventStartTime);
			}
			$this->redis->set("scheduled_event_time", $scheduledEventTime);
			$dataObj->scheduled_event_time = $scheduledEventTime;
			$this->redis->set("scheduled_event_action", $scheduledEventAction);
			$dataObj->scheduled_event_action = $scheduledEventAction;*/
			
			// Anonymous stats
			/*$anonymousStats = false;
			if ($this->input->post('anonymous_stats'))
			{
				$anonymousStats = $this->input->post('anonymous_stats');
			}
			$this->redis->set("anonymous_stats", $anonymousStats);
			$dataObj->anonymous_stats = $anonymousStats;*/
						
			// Startup script rc.local
			// $this->util_model->saveStartupScript($minerSoftware, $delay, $extracommands);
			
			// Mobileminer
			// Enabled
			/*$mobileminerEnabled = false;
			if ($this->input->post('mobileminer_enabled'))
			{
				$mobileminerEnabled = $this->input->post('mobileminer_enabled');
			}
			$this->redis->set("mobileminer_enabled", $mobileminerEnabled);
			$dataObj->mobileminer_enabled = $mobileminerEnabled;
			
			// Sys name
			$mobileminerSysName = false;
			if ($this->input->post('mobileminer_system_name'))
			{
				$mobileminerSysName = $this->input->post('mobileminer_system_name');
			}
			$this->redis->set("mobileminer_system_name", $mobileminerSysName);
			$dataObj->mobileminer_system_name = $mobileminerSysName;
			
			// email
			$mobileminerEmail = false;
			if ($this->input->post('mobileminer_email'))
			{
				$mobileminerEmail = $this->input->post('mobileminer_email');
			}
			$this->redis->set("mobileminer_email", $mobileminerEmail);
			$dataObj->mobileminer_email = $mobileminerEmail;
			
			// Application key
			$mobileminerAppkey = false;
			if ($this->input->post('mobileminer_appkey'))
			{
				$mobileminerAppkey = $this->input->post('mobileminer_appkey');
			}
			$this->redis->set("mobileminer_appkey", $mobileminerAppkey);
			$dataObj->mobileminer_appkey = $mobileminerAppkey;*/

			$data['message'] = '<b>Success!</b> Settings saved!';
			$data['message_type'] = "success";
						
			if ($this->input->post('save_restart'))
			{
				$this->util_model->minerRestart();
				
				$this->session->set_flashdata('message', '<b>Success!</b> Settings saved and miner restarted!');
				$this->session->set_flashdata('message_type', 'success');
			}
			else
			{
				$this->session->set_flashdata('message', '<b>Success!</b> Settings saved!');
				$this->session->set_flashdata('message_type', 'success');
			}

		}
		
		/*if (is_array($extramessages))
		{
			$this->session->set_flashdata('message', '<b>Warning!</b> '.implode(" ", $extramessages));
			$this->session->set_flashdata('message_type', 'warning');
		}*/
		
		// Save export
		// $this->redis->set("export_settings", json_encode($dataObj));

		// Publish stats to Redis
		//$dataObj->minera_id = $mineraSystemId;
		//$this->redis->publish("minera-channel", json_encode($dataObj));
		
		// Save current miner settings
		/*if ($this->input->get("save_config"))
		{
			unset($confArray['pools']);
			$lineConf = false;
			foreach ($confArray as $keyConf => $valueConf)
			{
				if ($valueConf != "1")
					$lineConf .= " --".$keyConf."=".$valueConf;
				else
					$lineConf .= " --".$keyConf;
			}
			$exportConfigSettings .= $lineConf;
			$dataObj = array("timestamp" => time(), "software" => $minerSoftware, "settings" => $exportConfigSettings, "pools" => $pools, "description" => false);
			$this->redis->command("HSET saved_miner_configs ".time()." ".base64_encode(json_encode($dataObj)));
		}*/
			
		//$this->redis->command("BGSAVE");
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($dataObj));
	}

	/*
	// Enable disable browser mining
	*/
	public function manage_browser_mining()
	{
		$this->util_model->isLoggedIn();
		$result = new stdClass();
		$error = new stdClass();
		
		if (!$this->input->post('action')) {
			$error->err = 'Action is required';
			echo json_encode($error);
			return false;
		}

		$action = $this->input->post('action');
		$threads = $this->input->post('threads');
		$threads = ($threads) ? $threads : 2;
		$enable = ($action === 'enable') ? true : false;

		$this->redis->set('browser_mining', $enable);
		$this->redis->set('browser_mining_threads', $threads);

		// log_message("error", $action);
		$result->action = $action;
		$result->threads = $threads;
		echo json_encode($result);
	}
	
	/*
	// Export the settings forcing download of JSON file
	*/
	public function export()
	{
		$this->util_model->isLoggedIn();
		
		$o = $this->redis->get("export_settings");
		if ($this->util_model->isJson($o))
		{
			$this->output
				->set_content_type('application/json')
				->set_header('Content-disposition: attachment; filename=minera-export.json')
				->set_output($o);
		}
		else
			return false;
	}
	
	/*
	// Shutdown controller (this should be in a different "system" controller file)
	*/
	public function shutdown()
	{	
		$this->util_model->isLoggedIn();
		
		if ($this->input->get('confirm'))
		{
			$data['message'] = "Please wait to unplug me.";
			$data['timer'] = true;
			$this->util_model->shutdown();
		}
		else
		{
			$data['title'] = "Are you sure?";
			$data['message'] = '<a href="'.site_url("app/shutdown").'?confirm=1" class="btn btn-danger btn-lg"><i class="fa fa-check"></i> Yes, shutdown now</a>&nbsp;&nbsp;&nbsp;<a href="'.site_url("app/dashboard").'" class="btn btn-primary btn-lg"><i class="fa fa-times"></i> No, thanks</a>';
			$data['timer'] = false;
		}
		
		$data['now'] = time();
		$data['sectionPage'] = 'lockscreen';
		$data['onloadFunction'] = false;
		$data['messageEnd'] = "you can unplug me now.";
		$data['htmlTag'] = "lockscreen";
		$data['seconds'] = 30;
		$data['refreshUrl'] = false;
		$data['env'] = $this->config->item('ENV');

		$this->load->view('include/header', $data);
		$this->load->view('sysop', $data);
		$this->load->view('include/footer', $data);
	}

	/*
	// Reboot controller (this should be in a different "system" controller file)
	*/
	public function reboot()
	{
		$this->util_model->isLoggedIn();
			
		if ($this->input->get('confirm'))
		{
			$data['refreshUrl'] = site_url("app/index");
			$data['message'] = lang('app.wait_while_rebooting');;
			$data['timer'] = true;
			$this->util_model->reboot();
		}
		else
		{
			$data['title'] = lang('app.are_you_sure');
			$data['message'] = '<a href="'.site_url("app/reboot").'?confirm=1" class="btn btn-danger btn-lg"><i class="fa fa-check"></i>' . lang('app.yes_reboot') . '</a>&nbsp;&nbsp;&nbsp;<a href="'.site_url("app/dashboard").'" class="btn btn-primary btn-lg"><i class="fa fa-times"></i>' . lang('app.no_thanks') . '</a>';
			$data['timer'] = false;
		}
		
		$data['now'] = time();
		$data['sectionPage'] = 'lockscreen';
		$data['onloadFunction'] = false;
		$data['messageEnd'] = lang('app.miner_has_rebooted');
		$data['htmlTag'] = "lockscreen";
		$data['seconds'] = 30;
		$data['env'] = $this->config->item('ENV');
		
		$this->load->view('include/header', $data);
		$this->load->view('sysop', $data);
		$this->load->view('include/footer', $data);
	}

	/*
	// Start miner controller (this should be in a different "system" controller file)
	*/
	public function start_miner()
	{
		$this->util_model->isLoggedIn();
		
		if (!$this->util_model->isOnline())
			$this->util_model->minerStart();
		else
		{
			$this->session->set_flashdata('message', "<b>Warning!</b> Your miner is currently mining, before you can start it you need to stop it before, or try the restart link.");
		}	
		
		redirect('app/dashboard');
	}

	/*
	// Stop miner controller (this should be in a different "system" controller file)
	*/
	public function stop_miner()
	{
		$this->util_model->isLoggedIn();
		
		$this->util_model->minerStop();
		
		redirect('app/dashboard');
	}
	
	/*
	// Restart miner controller (this should be in a different "system" controller file)
	*/
	public function restart_miner()
	{
		$this->util_model->isLoggedIn();
		
		$this->util_model->minerRestart();
		
		redirect('app/dashboard');
	}
	
	/*
	// Update controller (this should be in a different "system" controller file)
	*/
	public function update()
	{
		$this->util_model->isLoggedIn();
		
		if ($this->util_model->checkUpdate())
		{
			if ($this->input->get('confirm'))
			{
				$data['message'] = "Please wait while I'm upgrading the system...";
				$data['timer'] = true;
				$data['onloadFunction'] = "callUpdate()";
				$data['refreshUrl'] = site_url("app/index");
			}
			else
			{
				$data['title'] = "System update detected";
				$data['message'] = '<a href="'.site_url("app/update").'?confirm=1"><button class="btn btn-danger btn-lg"><i class="fa fa-check"></i> Let me install the updates</button></a>&nbsp;&nbsp;&nbsp;<a href="'.site_url("app/dashboard").'"><button class="btn btn-primary btn-lg"><i class="fa fa-times"></i> No, thanks</button></a><p><br /><small>Your local miner will be stopped during the update process. Minera will try to restart it after the update is complete.</small></p>';
				$data['timer'] = false;
				$data['onloadFunction'] = false;
				$data['refreshUrl'] = false;
			}
			
			$data['now'] = time();
			$data['sectionPage'] = 'lockscreen';
			$data['messageEnd'] = "System updated!";
			$data['htmlTag'] = "lockscreen";
			$data['seconds'] = 200;
			$data['env'] = $this->config->item('ENV');
		
			$this->load->view('include/header', $data);
			$this->load->view('sysop', $data);
			$this->load->view('include/footer', $data);
		}
		else
		{
			redirect("app/dashboard");
		}
	}
	
	/*
	// API controller
	*/
	public function api($command = false)
	{		
		$cmd = ($command) ? $command : $this->input->get('command');
		
		$o = '{}';
		
		switch($cmd)
		{
			case "save_current_freq":
				$o = $this->util_model->saveCurrentFreq();
			break;
			case "update_minera":
				$o = $this->util_model->update();
			break;
			case "cron_unlock":
				$o = $this->redis->del("cron_lock");
			break;
			case "stats":
				$o = $this->util_model->getStats();
			break;
			case "miner_stats":
				$o = json_encode($this->util_model->getMinerStats());
			break;
			case "history_stats":
				$o = $this->util_model->getHistoryStats($this->input->get('type'));
			break;
			case "reset_action":
				$o = $this->util_model->reset($this->input->get('action'));
				$this->session->set_flashdata('message', '<b>Success!</b> Data has been reset.');
				$this->session->set_flashdata('message_type', 'success');
			break;
			case "factory_reset_action":
				$o = $this->util_model->factoryReset();
				$this->cron();
				$this->session->set_flashdata('message', '<b>Success!</b> Data has been reset.');
				$this->session->set_flashdata('message_type', 'success');
			break;
			case "upgrade_exec":
				$file_name = $this->input->post('file_name');
				// execute the upgrade command
				exec("sudo system_update online ".$fileInfoName." >/tmp/upgrade.log &");
				$o = json_encode(array("message" => 'success','code' => '200'));
			break;
			case "import_file":
				$o = json_encode($this->util_model->importFile($this->input->post()));
			break;
			case "reboot":
				$o = $this->util_model->reboot();
			case "save_network":
				$o = $this->util_model->saveNetwork($this->input->get('type'),$this->input->get('ip'),$this->input->get('mask'),$this->input->get('gw'),$this->input->get('dns'));
			break;
			case "scan_network":
				$o = json_encode($this->util_model->discoveryNetworkDevices($this->input->get('network')));
			break;
			case "tail_log":
				$o = json_encode($this->util_model->tailFile($this->input->get('file'), ($this->input->get('lines')) ? $this->input->get('lines') : 5));
			break;
			case "miner_info":
				$o = $this->util_model->minerInfo();
			break;
			case "save_pools":
				$poolUrls = $this->input->post('pool_url');
				$poolUsernames = $this->input->post('pool_username');
				$poolPasswords = $this->input->post('pool_password');

				$pools = array();
				foreach ($poolUrls as $key => $poolUrl)
				{
					if ($poolUrl)
					{
						if (isset($poolUsernames[$key]) && isset($poolPasswords[$key]))
						{
							$pools[] = array("url" => $poolUrl, "user" => $poolUsernames[$key], "pass" => $poolPasswords[$key]);
						}
					}
				}
				$confArray = array();			
				$confArray['pools'] = $pools;
				$jsonPoolsConfRedis = json_encode($pools);
				$this->redis->set('minerd_pools', $jsonPoolsConfRedis);

				// Prepare JSON conf
				$jsonConfFile = json_encode($confArray, 192); // JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES = 128 + 64

				// Save the JSON conf file
				file_put_contents($this->config->item("minerd_conf_temp_file"), $jsonConfFile);
				sleep(3);
				exec("sudo mv " . $this->config->item('minerd_conf_temp_file') . " " . $this->config->item('minerd_conf_file'));
				sleep(2);

				$this->util_model->restartCgminer();
				$o = json_encode(array("message" => 'success','code' => '200'));
			break;
			case "miner_action":
				$action = ($this->input->get('action')) ? $this->input->get('action') : false;
				switch($action)
				{					
					case "start":
						$o = $this->util_model->minerStart();
					break;
					case "stop":
						$o = $this->util_model->minerStop();
					break;
					case "restart":
						$o = $this->util_model->minerRestart();
					break;
					default:
						$o = json_encode(array("err" => true));
				}
			break;
		}

		$this->output
			->set_content_type('application/json')
			->set_output($o);
	}
	
	/*
	// Stats controller get the live stats
	*/
	public function stats()
	{
	    $stats = $this->util_model->getStats();

		$this->output
			->set_content_type('application/json')
			->set_output($stats);
	}

    public function varLog()
    {
		
		$str = file_get_contents('/var/log/messages');

		$this->output
			->set_content_type('application/json')
			->set_output($str);
      
    }
	
	/*
	// Store controller Get the store stats from Redis
	*/
	public function stored_stats()
	{
		$this->util_model->isLoggedIn();
		
		$storedStats = $this->util_model->getStoredStats(3600);
		
		$this->output
			->set_content_type('application/json')
			->set_output("[".implode(",", $storedStats)."]");
	}	

	/*
	// Cron controller to be used to run scheduled tasks
	*/
	public function cron()
	{
		if ($this->redis->get("cron_lock"))
		{
			log_message('error', "CRON locked waiting previous process to terminate...");			
			return true;
		}
		
		if ( ($this->util_model->getSysUptime() + $this->redis->get('minerd_delaytime') ) <= 60 )
		{
			log_message('error', "System just started, warming up...");			
			return true;			
		}
	
		$time_start = microtime(true); 
			
		log_message('info', "--- START CRON TASKS ---");
			
		$this->redis->set("cron_lock", true);
			
		// Check and restart the minerd if it's dead
		if ($this->redis->get('minerd_autorecover'))
		{
			$this->util_model->checkMinerIsUp();	
		}
		
		$now = time();
		$currentHour = date("H", $now);
		$currentMinute = date("i", $now);
		
		// Refresh Cryptsydata if needed
		//$this->util_model->refreshcryptsyData();
		//$this->util_model->updateAltcoinsRates();
						
		// Store the live stats
		$stats = $this->util_model->storeStats();
		
		// Publish stats to Redis
		$this->util_model->getStats();

		/*
		// Store the avg stats
		*/
		// Store 5min avg
		if ( ($currentMinute%5) == 0)
		{
			$this->util_model->storeAvgStats(300);
		}
		// Store 1hour avg
		if ( $currentMinute == "00")
		{
			$this->util_model->storeAvgStats(3600);
		}
		// Store 1day avg
		if ( $currentHour == "04" && $currentMinute == "00")
		{
			$this->util_model->storeAvgStats(86400);
		}
		
		// Store coins profitability
		//if ($profit = $this->util_model->getProfitability()) {
		//	$this->redis->set("coins_profitability", $profit);
		//}
		
		// Activate/Deactivate time donation pool if enable
		if ($this->util_model->isOnline() && isset($stats->pool_donation_id))
		{		
			$donationTime = $this->redis->get("minera_donation_time");
			if ($donationTime > 0)
			{
				$currentHr = (isset($stats->pool->hashrate)) ? $stats->pool->hashrate : 0;
				$poolDonationId = $stats->pool_donation_id;
				$donationTimeStarted = ($this->redis->get("donation_time_started")) ? $this->redis->get("donation_time_started") : false;

				$donationTimeDoneToday = ($this->redis->get("donation_time_done_today")) ? $this->redis->get("donation_time_done_today") : false;

				$donationStartHour = "04";
				$donationStartMinute = "10";
				$donationStopHour = date("H", ($donationTimeStarted + $donationTime*60));
				$donationStopMinute = date("i", ($donationTimeStarted + $donationTime*60));
				
				// Delete the donation-done flag after 24h
				if ($now >= ($donationTimeDoneToday+86400))
				{
					$this->redis->del("donation_time_started");
					$this->redis->del("donation_time_done_today");	
					$donationTimeStarted = false;
					$donationTimeDoneToday = false;
				}
				
				// Stop time donation
				if ($donationTimeStarted > 0 && (int)$currentHour >= (int)$donationStopHour && (int)$currentMinute >= (int)$donationStopMinute)
				{
					$this->redis->del("donation_time_started");
					$donationTimeStarted = false;
					$this->util_model->selectPool(0);
					log_message("error", "[Donation-time] Terminated... Switching back to main pool ID [0]");
				}

				if ($donationTimeStarted > 0)
				{
					// Time donation in progress
					$remain = round(((($donationTime*60) - ($now - $donationTimeStarted))/60));
					$this->redis->set("donation_time_remain", $remain);
					log_message("error", "[Donation time] In progress..." . $remain . " minutes remaing..." );
				}

				// Start time donation
				if ($donationTimeDoneToday === false && ((int)$currentHour >= (int)$donationStartHour && (int)$currentMinute >= (int)$donationStartMinute))
				{
					// Starting time donation
					$this->util_model->selectPool($poolDonationId);
					$this->redis->set("donation_time_started", $now);
					
					// This prevent any re-activation for the current day
					$this->redis->set("donation_time_done_today", $now);
					
					$this->redis->command("LPUSH saved_donations ".$now.":".$donationTime.":".$currentHr);
					
					log_message("error", "[Donation time] Started... (for ".$donationTime." minutes) - Switching to donation pool ID [".$poolDonationId."]");
				}
			}
		}

		// Scheduled event
		$scheduledEventStartTime = $this->redis->get("scheduled_event_start_time");
		$scheduledEventTime = $this->redis->get("scheduled_event_time");
		$scheduledEventAction = $this->redis->get("scheduled_event_action");
		if ($scheduledEventTime > 0)
		{
			log_message("error", "TIME: ".time()." - SCHEDULED START TIME: ".$scheduledEventStartTime);
			
			$timeToRunEvent = (($scheduledEventTime*3600) + $scheduledEventStartTime);
			if (time() >= $timeToRunEvent)
			{				
				
				log_message("error", "Running scheduled event ($timeToRunEvent) -> ".strtoupper($scheduledEventAction));
				
				$this->redis->set("scheduled_event_start_time", time());

				$this->redis->command("BGSAVE");
								
				log_message("error", "TIME: ".time()." - AFTER SCHEDULED START TIME: ".$this->redis->get("scheduled_event_start_time"));
				
				if ($scheduledEventAction == "restart")
				{
					$this->util_model->minerRestart();
				}
				else
				{
					sleep(10);
					$this->util_model->reboot();
				}
			}
		}
		
		// Send anonymous stats
		$anonynousStatsEnabled = $this->redis->get("anonymous_stats");
		$mineraSystemId = $this->util_model->generateMineraId();

		if ($mineraSystemId)
		{
			if ($this->util_model->isOnline()) {
				$totalDevices = 0; $totalHashrate = 0; 
				if (isset($stats->totals->hashrate))
					$totalHashrate = $stats->totals->hashrate;
					
				if (isset($stats->devices))
				{
					$devs = (array)$stats->devices;
					$totalDevices = count($devs);
				}
	
				$minerdRunning = $this->redis->get("minerd_running_software");

				$anonStats = array("id" => $mineraSystemId, "algo" => "SHA-256", "hashrate" => $totalHashrate, "devices" => $totalDevices, "miner" => $minerdRunning, "version" => $this->util_model->currentVersion(true), "timestamp" => time());
			}
			
			if ( $currentMinute == "00")
			{
				if ($this->util_model->isOnline()) $this->util_model->sendAnonymousStats($mineraSystemId, $anonStats);
			}
		}
				
		// Use the live stats to check if autorestart is needed
		// (devices possible dead)
		$autorestartenable = $this->redis->get("minerd_autorestart");
		$autorestartdevices = $this->redis->get("minerd_autorestart_devices");
		$autorestarttime = $this->redis->get("minerd_autorestart_time");

		if ($autorestartenable && $autorestartdevices)
		{
			log_message('error', "Checking miner for possible dead devices...");
		
			// Use only if miner is online
			if ($this->util_model->isOnline())
			{
				// Check if there is stats error
				if (isset($stats->error))
					return false;
				
				// Get the max last_share time per device
				$lastshares = false;
				
				if (isset($stats->devices))
				{
					foreach ($stats->devices as $deviceName => $device)
					{
						$lastshares[$deviceName] = $device->last_share;
					}
				}
				
				// Check if there is any device with last_share time > 10minutes (possible dead device)
				if (is_array($lastshares))
				{
					$i = 0;
					foreach ($lastshares as $deviceName => $lastshare)
					{
						if ( (time() - $lastshare) > $autorestarttime )
						{
							log_message('error', "WARNING: Found device: ".$deviceName." possible dead");
							$i++;
						}
					}
					
					// Check if dead devices are equal or more than the ones set
					if ($i >= $autorestartdevices)
					{
						// Restart miner
						log_message('error', "ATTENTION: Restarting miner due to possible dead devices found - Threshold: ".$autorestartdevices." Found: ".$i);
						$this->util_model->minerRestart();
					}
				}
			}
		}

		$this->redis->del("cron_lock");

		$time_end = microtime(true);
		$execution_time = ($time_end - $time_start);
		
		log_message('error', "--- END CRON TASKS (".round($execution_time, 2)." secs) ---");
	}
	
	/*
	// Controllers for retro compatibility
	*/
	public function cron_stats()
	{
		redirect('app/cron');
	}

}

