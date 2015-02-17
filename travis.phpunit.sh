php -r "putenv('APP_ENV=testing');"
./vendor/bin/phpunit -c module/Options/test/phpunit.xml --log-junit 'logs/tests-report.options.xml' --coverage-clover 'logs/phpunit.clover.options.xml' --coverage-html 'logs/coverage/options' > logs/phpunit.options.log
./vendor/bin/phpunit -c module/Pages/test/phpunit.xml --log-junit 'logs/tests-report.pages.xml' --coverage-clover 'logs/phpunit.clover.pages.xml' --coverage-html 'logs/coverage/pages' > logs/phpunit.pages.log
./vendor/bin/phpunit -c module/Categories/test/phpunit.xml --log-junit 'logs/tests-report.categories.xml' --coverage-clover 'logs/phpunit.clover.categories.xml' --coverage-html 'logs/coverage/categories' > logs/phpunit.categories.log
./vendor/bin/phpunit -c module/Media/test/phpunit.xml --log-junit 'logs/tests-report.media.xml' --coverage-clover 'logs/phpunit.clover.media.xml' --coverage-html 'logs/coverage/media' > logs/phpunit.media.log
./vendor/bin/phpunit -c module/User/test/phpunit.xml --log-junit 'logs/tests-report.user.xml' --coverage-clover 'logs/phpunit.clover.user.xml' --coverage-html 'logs/coverage/user' > logs/phpunit.user.log
./vendor/bin/phpunit -c module/Install/test/phpunit.xml --log-junit 'logs/tests-report.install.xml' --coverage-clover 'logs/phpunit.clover.install.xml' --coverage-html 'logs/coverage/install' > logs/phpunit.install.log
./vendor/bin/phpunit -c module/Comment/test/phpunit.xml --log-junit 'logs/tests-report.comment.xml' --coverage-clover 'logs/phpunit.clover.comment.xml' --coverage-html 'logs/coverage/comment' > logs/phpunit.comment.log
