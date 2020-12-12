<?php
class WPJAM_Cron{
	private static $crons	= [];
	private static $jobs	= [];

	public static function register(...$args){
		if(is_callable($args[0])){
			$callback	= $args[0];
			
			$args	= $args[1] ?? [];
			$args	= is_numeric($args) ? ['weight'=>$args] : $args;
			
			self::$jobs[]	= wp_parse_args($args, [
				'callback'	=> $callback,
				'weight'	=> 1,
				'day'		=> -1
			]);
		}else{
			if(empty($args[1])){
				return;
			}

			$hook	= $args[0];
			$args	= wp_parse_args($args[1], ['callback'=>'', 'jobs'=>'', 'recurrence'=>'', 'time'=>time(), 'args'=>[]]);

			if($args['jobs']){
				self::$crons[$hook]	= $args;

				add_action($hook, ['WPJAM_Cron', 'scheduled']);

				if(!self::is_scheduled($hook) && $args['recurrence']){
					wp_schedule_event($args['time'], $args['recurrence'], $hook);
				}
			}elseif($args['callback'] && is_callable($args['callback'])){
				add_action($hook, $args['callback']);

				if(!self::is_scheduled($hook)){
					if($args['recurrence']){
						wp_schedule_event($args['time'], $args['recurrence'], $hook, $args['args']);
					}else{
						wp_schedule_single_event($args['time'], $hook, $args['args']);
					}
				}
			}
		}	
	}

	public static function get_jobs(){
		if(empty(self::$jobs)){
			return [];
		}
		
		$jobs	= array_filter(self::$jobs, function($job){
			if($job['day'] == -1){
				return true;
			}else{
				$day	= (current_time('H') > 2 && current_time('H') < 6) ? 0 : 1;
				return $job['day']	== $day;
			}
		});

		return self::$jobs;
	}

	public static function get_callbacks($jobs, $weight=true){
		if(empty($jobs)){
			return [];
		}

		if($weight){
			$callbacks	= [];

			foreach ($jobs as $i=> &$job) {
				if($job['weight']){
					$callbacks[]	= $job['callback'];

					if($job['weight'] <= 1){
						unset($jobs[$i]);
					}else{
						$job['weight'] --;
					}
				}
			}

			if($jobs){
				$callbacks	= array_merge($callbacks, self::get_callbacks($jobs)); 
			}

			return $callbacks;
		}else{
			return wp_list_pluck($jobs, 'callback');
		}
	}

	public static function scheduled(){
		if(get_site_transient('wpjam_corns_lock')){
			return;
		}

		set_site_transient('wpjam_corns_lock', 1, 5);

		$hook	= current_action();

		if(empty(self::$crons[$hook])){
			return;
		}

		$cron	= self::$crons[$hook];

		$weight	= $cron['weight'] ?? false;
		$jobs	= $cron['jobs'] ?? false;

		if($jobs && is_callable($jobs)){
			$jobs	= call_user_func($jobs);
		}
		
		$callbacks	= self::get_callbacks($jobs, $weight);

		if(empty($callbacks)){
			return;
		}

		$total		= count($callbacks);
		$index		= get_transient($hook.'_index') ?: 0;

		$callback	= $callbacks[$index] ?? '';
		$index		= $index >= $total ? 0 : ($index + 1);

		$today		= date('Y-m-d', current_time('timestamp'));
		$counter	= get_transient($hook.'_counter:'.$today) ?: 0;	

		set_transient($hook.'_index', $index, DAY_IN_SECONDS);
		set_transient($hook.'_counter:'.$today, ($counter+1), DAY_IN_SECONDS);

		if($callback){
			if(is_callable($callback)){
				return call_user_func($callback);
			}else{
				trigger_error('invalid_job_callback'.var_export($callback, true));
			}
		}

		return true;
	}

	public static function get_counter($hook){
		$today	= date('Y-m-d', current_time('timestamp'));

		return get_transient($hook.'_counter:'.$today) ?: 0;
	}

	public static function is_scheduled($hook){
		$crons = _get_cron_array();

		if(empty($crons)){
			return false;
		}
		
		foreach ($crons as $timestamp => $cron) {
			if(isset($cron[$hook])){
				return true;
			}
		}

		return false;
	}

	public static function filter_cron_schedules($schedules){
		return array_merge($schedules, [
			'five_minutes'		=> ['interval'=>300,	'display'=>'每5分钟一次'],
			'fifteen_minutes'	=> ['interval'=>900,	'display'=>'每15分钟一次'],
		]);
	}


	private static $tab	= 'crons';

	public static function set_tab($tab){
		self::$tab	= $tab;
	}

	public static function get_primary_key(){
		return self::$tab == 'crons' ? 'cron_id' : 'id';
	}

	public static function get($id){
		list($timestamp, $hook, $key)	= explode('--', $id);

		$wp_crons = _get_cron_array();

		if(isset($wp_crons[$timestamp][$hook][$key])){
			$data	= $wp_crons[$timestamp][$hook][$key];

			$data['hook']		= $hook;	
			$data['timestamp']	= $timestamp;
			$data['time']		= get_date_from_gmt(date('Y-m-d H:i:s', $timestamp));
			$data['cron_id']	= $id;
			$data['interval']	= $data['interval'] ?? 0;

			return $data;
		}else{
			return new WP_Error('cron_not_exist', '该定时作业不存在');
		}
	}

	public static function insert($data){
		if(!has_filter($data['hook'])){
			return new WP_Error('invalid_hook', '非法 hook');
		}

		$timestamp	= strtotime(get_gmt_from_date($data['time']));

		if($data['interval']){
			wp_schedule_event($timestamp, $data['interval'], $data['hook'], $data['_args']);
		}else{
			wp_schedule_single_event($timestamp, $data['hook'], $data['_args']);
		}

		return true;
	}

	public static function do($id){
		$data = self::get($id);

		if(is_wp_error($data)){
			return $data;
		}

		$result	= do_action_ref_array($data['hook'], $data['args']);

		if(is_wp_error($result)){
			return $result;
		}else{
			return true;
		}
	}

	public static function delete($id){
		$data = self::get($id);
		
		if(is_wp_error($data)){
			return $data;
		}

		return wp_unschedule_event($data['timestamp'], $data['hook'], $data['args']);
	}
	
	public static function query_items($limit, $offset){
		$items	= [];

		if(self::$tab == 'crons'){
			foreach (_get_cron_array() as $timestamp => $wp_cron) {
				foreach ($wp_cron as $hook => $dings) {
					foreach($dings as $key=>$data) {
						if(!has_filter($hook)){
							wp_unschedule_event($timestamp, $hook, $data['args']);	// 系统不存在的定时作业，自动清理
							continue;
						}

						$schedule	= $schedules[$data['schedule']] ?? $data['interval']??'';
						// $args	= $data['args'] ? '('.implode(',', $data['args']).')' : '';
						
						$items[] = [
							'cron_id'	=> $timestamp.'--'.$hook.'--'.$key,
							'time'		=> get_date_from_gmt( date('Y-m-d H:i:s', $timestamp) ),
							// 'hook'		=> $hook.$args,
							'hook'		=> $hook,
							'interval'	=> $data['interval'] ?? 0
						];
					}
				}
			}
		}else{
			foreach(self::$jobs as $id => $job){
				if(is_array($job['callback'])){
					if(is_object($job['callback'][0])){
						$job['function']	= '<p>'.get_class($job['callback'][0]).'->'.(string)$job['callback'][1].'</p>';	
					}else{
						$job['function']	= '<p>'.$job['callback'][0].'->'.(string)$job['callback'][1].'</p>';
					}
				}elseif(is_object($job['callback'])){
					$job['function']	= '<pre>'.print_r($job['callback'], true).'</pre>';
				}else{
					$job['function']	= wpautop($job['callback']);
				}

				$job['id']	= $id;
				$items[]	= $job;
			}
		}

		$total	= count($items);

		return compact('items', 'total');
	}

	public static function get_actions(){
		if(self::$tab == 'crons'){
			return [
				'add'		=> ['title'=>'新建',		'response'=>'list'],
				'do'		=> ['title'=>'立即执行',	'direct'=>true,	'response'=>'list'],
				'delete'	=> ['title'=>'删除',		'direct'=>true,	'response'=>'list']
			];
		}else{
			return [];
		}
	}

	public static function get_fields($action_key='', $id=0){
		if(self::$tab == 'crons'){
			$schedule_options	= [0=>'只执行一次']+wp_list_pluck(wp_get_schedules(), 'display', 'interval');
			
			return [
				'hook'		=> ['title'=>'Hook',	'type'=>'text',		'show_admin_column'=>true],
				// '_args'		=> ['title'=>'参数',		'type'=>'mu-text',	'show_admin_column'=>true],
				'time'		=> ['title'=>'运行时间',	'type'=>'text',		'show_admin_column'=>true,	'value'=>current_time('mysql')],
				'interval'	=> ['title'=>'频率',		'type'=>'select',	'show_admin_column'=>true,	'options'=>$schedule_options],
			];
		}else{
			return [
				'function'	=> ['title'=>'回调函数',	'type'=>'view',	'show_admin_column'=>true],
				'weight'	=> ['title'=>'作业权重',	'type'=>'view',	'show_admin_column'=>true],
				'day'		=> ['title'=>'运行时间',	'type'=>'view',	'show_admin_column'=>true,	'options'=>['-1'=>'全天','1'=>'白天','0'=>'晚上']],
			];
		}
	}
}

function wpjam_register_cron($callback, $args=[]){
	WPJAM_Cron::register($callback, $args);
}

function wpjam_is_scheduled_event($hook) {	// 不用判断参数
	return WPJAM_Cron::is_scheduled($hook);
}

add_action('init', function(){
	add_filter('cron_schedules',	['WPJAM_Cron', 'filter_cron_schedules']);

	wpjam_register_cron('wpjam_scheduled', [
		'recurrence'	=> wp_using_ext_object_cache() ? 'five_minutes' : 'fifteen_minutes',
		'jobs'			=> ['WPJAM_Cron', 'get_jobs'],
		'weight'		=> true
	]);
});

if(is_admin()){
	wpjam_add_basic_sub_page('wpjam-crons', [
		'menu_title'	=> '定时作业',		
		'function'		=> 'tab',
		'tabs'			=> ['crons'=>['title'=>'定时作业',	'function'=>'list']],
		'summary'		=> '定时作业让你可以可视化管理 WordPress 的定时作业，详细介绍请点击：<a href="https://blog.wpjam.com/m/wpjam-basic-cron-jobs/" target="_blank">定时作业</a>。',
	]);

	add_action('wpjam_plugin_page_load', function($plugin_page, $current_tab){
		if($plugin_page != 'wpjam-crons' || !in_array($current_tab, ['crons', 'jobs'])){
			return;
		}

		$summary	= $current_tab == 'jobs' ? '今天已经运行 <strong>'.WPJAM_Cron::get_counter('wpjam_scheduled').'</strong> 次' : null;

		WPJAM_Cron::set_tab($current_tab);

		wpjam_register_list_table('wpjam-crons', [
			'title'		=> wpjam_get_current_tab_setting('title'),
			'plural'	=> 'crons',
			'singular'	=> 'cron',
			'model'		=> 'WPJAM_Cron',
			'summary'	=> $summary,
			'fixed'		=> false,
			'ajax'		=> true
		]);
	}, 10, 2);
}


