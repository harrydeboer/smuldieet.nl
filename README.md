<h1>Smuldieet</h1>

<h3>Install Development</h3>
<ol>
<li>Copy .env.local.example to .env.local and fill in the fields.
The DB_HOST has to be host.docker.internal</li>
<li>Build the docker-compose image and up -d the image.</li>
<li>Create the database and the 4 test databases named ${DB_DATABASE}_test{1-4}.</li>
<li>Run composer install.</li>
<li>Run the migrations.</li>
<li>Register the user in the route /registreren.</li>
<li>Run `php bin/console make:admin 1` to make the first user admin.</li>
<li>Run npm install (with a global node and npm). 
Then link the UglifyJS and SCSS File Watchers to the node_modules/.bin binaries. 
The SCSS Arguments in PhpStorm is 
'--style compressed $FileParentDir$/scss/$FileName$:$FileParentDir$/css/$FileNameWithoutExtension$.css'.</li>
</ol>

<h3>Testing</h3>

<ol>
<li>Run runTests.sh to test.</li>
</ol>
