<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'EduALL CRM');
set('repository', 'https://github.com/Edu-ALL/crm.git');

set('git_tty', true);
set('git_ssh_command', 'ssh -o StrictHostKeyChecking=no');

set('keep_releases', 5);


add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', [
    "bootstrap/cache",
    "storage",
    "storage/app",
    "storage/framework",
    "storage/logs",
]);

// Hosts

host('CRMStagingServer') // Nama remote host server ssh anda | contoh host('NAMA_REMOTE_HOST')
->setHostname('16.78.219.83') // Hostname atau IP address server anda | contoh  ->setHostname('10.10.10.1') 
->set('remote_user', 'ec2-user') // SSH user server anda | contoh ->set('remote_user', 'u1234567')
->set('port', 22) // SSH port server anda, untuk kasus ini server yang saya gunakan menggunakan port custom | contoh ->set('remote_user', 65002)
->set('branch', 'staging-aws') // Git branch anda
->set('deploy_path', '~/home/ec2-user/production/staging-crm/src'); // Lokasi untuk menyimpan projek laravel pada server | contoh ->set('deploy_path', '~/public_html/api-deploy');


// Tasks

task('deploy:secrets', function () {
    file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV'));
    upload('.env', get('deploy_path') . '/shared');
});

desc('Build assets');
task('deploy:build', [
    'npm:install',
]);

task('deploy', [
    'deploy:prepare',
    'deploy:secrets',       
    'deploy:vendors',
    'deploy:shared',
    'artisan:storage:link',
    'artisan:queue:restart',
    'deploy:publish',
    'deploy:unlock',
]);

// Hooks

after('deploy:failed', 'deploy:unlock');
