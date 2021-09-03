<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'pechakucha');

// Project repository
set('repository', 'git@github.com:JunichiKanno/pechakucha.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('13.230.36.59')
    ->stage('staging')
    ->user('deployer')
    ->forwardAgent(true)
    ->identityFile('~/.ssh/pechakucha_deployer.pem')
    ->set('deploy_path', '/{{application}}');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// デプロイ先のブランチを確認する
task('deploy:set_branch', function(){
    $branch = ask('What branch to deploy? (default: master)');
    if (empty($branch)) {
        $branch = 'master';
    }
    set('branch', $branch);
})->local()->setPrivate();
before('deploy', 'deploy:set_branch');

// 本番環境へのリリースを確認する
task('deploy:confirm_production', function(){
    $branch = get('branch', 'master');
    $result = askConfirmation("Are you sure you release '{$branch}' to production servers?", false);
    if (!$result) {
        exit();
    }
})->onStage('production')->local()->setPrivate();
after('deploy:set_branch', 'deploy:confirm_production');

desc('Set environment file');
task('deploy:environment', function(){
    $shared_path = '{{deploy_path}}/shared';
    $env = '{{release_path}}/.env.{{stage}}';

    run("if [ -e $(echo ${shared_path}/.env) ]; then cp ${env} ${shared_path}/.env; fi");
});
before('deploy:shared', 'deploy:environment');

// 各種リソースのコンパイル
// リソースのコンパイルが必要であれば、この設定を外す
desc('Run laravel-mix');
task('deploy:assets', function(){
    run('cd {{release_path}} && npm install');
    run('cd {{release_path}} && npm run prod');
    run('rm -rf {{release_path}}/node_modules');
});
// before('deploy:vendors', 'deploy:assets');

// デプロイが失敗した場合に、自動的にロックを解除する
// 解除したくない場合は、この設定を外す
after('deploy:failed', 'deploy:unlock');

// デプロイ時に自動的にマイグレーションを行う
// 行いたくない場合は、この設定を外す
// before('deploy:symlink', 'artisan:migrate');

// パブリックフォルダを使いたい場合はこの設定を外す
// before('deploy:symlink', 'deploy:public_disk');
