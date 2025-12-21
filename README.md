<h1>Smuldieet</h1>

<h3>Install</h3>
<ol>
<li>Copy .env.local.example to .env.local and .env.test.local and fill in the fields.</li>
<li>Copy .env.dev to .env.dev.local and fill in the fields.</li>
<li>Copy docker/compose.override_$ENV.yml to the main folder and remove _$ENV from the file name.</li>
<li>Build the docker image and run the image when using docker.</li>
<li>Create the database and the test database with suffix _test.</li>
<li>Chown the public/uploads folder to www-data.</li>
<li>Run composer install.</li>
<li>Run the migrations.</li>
<li>Register the user in the url /registreren.</li>
<li>Run `php bin/console make:admin 1` to make the first user admin.</li>
<li>Run `php bin/console sync:nutrients` to sync the nutrients with the foodstuff.</li>
<li>In the user table the field verified must be set to 1 
since mailing is disabled in development.</li>
<li>Run npm ci (with a global node and npm). When you are using PhpStorm 
link the UglifyJS and SCSS File Watchers to the node_modules/.bin binaries. 
The files in public/scss must compile to minimized files in public/css 
and the scope of SCSS must be the scss folder. 
The files in public/js must compile to minimized files in public/dist 
and the scope of UglifyJS must be the js folder.</li>
<li>Run run-tests.sh to test.</li>
</ol>
