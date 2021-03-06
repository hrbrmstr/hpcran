<?php
define("PROJ_ROOT", "/hpcran/backend/");

include PROJ_ROOT . "php/read_file.php";
define('OAUTH2_CLIENT_ID', read_file(PROJ_ROOT . "api_keys/id.txt"));
define('OAUTH2_CLIENT_SECRET', read_file(PROJ_ROOT . "api_keys/secret.txt"));
define('APP_NAME', 'hpcran_uploader');
define('APP_URL', 'https://hpcran.org/upload.php');

include PROJ_ROOT . "php/gh.php";
include PROJ_ROOT . "php/db.php";



if (session('access_token'))
{
  $response = apiRequest($apiURLBase. '/user');
  $ghun = $response->login;
  echo '<h4>Logged in as ' . $ghun . '</h4>';
  
  $db = new SQLite3(PROJ_ROOT . "uploaders.db");
  $authorized = authorized_users_lookup($db, $ghun);
  if (!$authorized)
    echo "<font color='red'>ERROR:</font> account is not able to publish packages on hpcran";
  else
  {
    $ghemails = apiRequest("https://api.github.com/user/emails");
    $_SESSION['ghemails'] = $ghemails;
    
    echo '
    <form enctype="multipart/form-data" action="submit.php" method="POST">
      Upload Package (max size 10MB)
      <br>
      <input name="package" type="file">
      <br>
      <input type="submit" value="Upload">
      <br>
      <input type="checkbox" required name="checkbox" value="check" id="agree">
      I agree to abide by
      <a href="rules.html" target="_blank">the HPCRAN rules</a>.
    </form>';

  }
}
else
{
  echo '<h4>Not logged in</h4>';
  echo '<p><a href="?action=login">Log In with GitHub</a></p>';
  echo '<p>Please review <a href="rules.html" target="_blank">the HPCRAN rules</a> before proceeding.</p>';
}
