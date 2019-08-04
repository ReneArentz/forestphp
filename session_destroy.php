<?php
session_set_cookie_params(2*24*60*60,'/','');
session_name('forestPHPSession');
session_start();
session_regenerate_id(false);
unset($_SESSION);
session_destroy();
?>
Logout erfolgreich<br />
<a href="./">Weiter...</a>