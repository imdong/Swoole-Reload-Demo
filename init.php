<?php
/**
 * Swoole 热更新功能 简单示例
 *
 * @author      青石 <www@qs5.org>
 * @copyright   Swoole Reload Demo 2017-4-30 09:47:10
 */

//**** 启动时配置 ****//

    // pid保存文件名
    define('RUN_PID_FILE', '/var/run/swoole_reload_demo.pid');

    // 定义根目录
    define('APP_ROOT', dirname(__FILE__) . '/');

    // 日志文件目录
    define('LOG_PATH', APP_ROOT . 'logs/');

    // 设置调试模式 先定义等下写静态
    $is_debug = true;

//**** 运行前判断 单例模式 ****//

    // 判断是否 cli 运行
    if(php_sapi_name() != 'cli') die('Please use cli Mode to Start!');

    // 判断是否已经运行
    if(file_exists(RUN_PID_FILE)){
        // 判断进程是否存在
        $run_pid = file_get_contents(RUN_PID_FILE);
        if(file_exists("/proc/{$run_pid}/")){
            die("is Runing, pid: {$run_pid}\n");
        }
    }

    // 保存当前进程pid 感觉用不到
    $run_pid = posix_getpid();
    file_put_contents(RUN_PID_FILE, $run_pid) || die("save pid File Error.\n");
    printf("Run Pid: %s\n", $run_pid);

//**** 运行前初始化 ****//

    // 判断是否传递后台运行命令
    $isRun = !empty($argv['1']) && $argv['1'] == 'start';

    // 判断是否传入日志文件名
    if(empty($argv['2'])){
        // 判断日志文件夹是否存在
        file_exists(LOG_PATH) || mkdir(LOG_PATH);
        $log_file = sprintf(LOG_PATH . '/%s_%s.log', date('Ymd'), $run_pid);
    } else{
        $log_file = $argv['2'];
    }

    // 根据运行情况设置调试模式
    define('IS_DEBUG', !$isRun && $is_debug );

//**** 启动进程 ****//

    // 引入进程文件
    require 'demoServer.class.php';

    // 启动服务器
    $server = new Demo_Server($isRun, $log_file);

//**** 结束前处理 ****//

    // 删除pid文件
    unlink(RUN_PID_FILE);

    // 输出结果
    echo "Process Exit";
