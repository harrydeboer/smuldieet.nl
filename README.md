<h1>Smuldieet</h1>

<h3>Install</h3>
<ol>
<li>Copy .env.local.example to .env.local and fill in the fields.
The DB_HOST has to be host.docker.internal or localhost when not using docker.</li>
<li>Copy docker/docker-compose.override_dev.yml to main folder and remove _dev from file name.</li>
<li>Build the docker image and run the image when using docker.</li>
<li>Create the database and the 4 test databases named ${DB_DATABASE}_test{1-4}.</li>
<li>Chown the public/uploads folder to www-data.</li>
<li>Run composer install.</li>
<li>Run the migrations.</li>
<li>Register the user in the url /registreren.</li>
<li>Run `php bin/console make:admin 1` to make the first user admin.</li>
<li>Run `php bin/console sync:nutrients` to sync the nutrients with the foodstuff.</li>
<li>In the user table the field is_verified must be set to 1 
since mailing is disabled in development.</li>
<li>Run npm install (with a global node and npm). When you are using PhpStorm 
link the UglifyJS and SCSS File Watchers to the node_modules/.bin binaries. 
The files in public/scss must compile to minimized files in public/css.</li>
<li>Run run-tests.sh to test.</li>
</ol>
