<?php
session_start();
session_unset();
session_destroy();
echo "<script> location.href='https://artemnails.online\index.html'; </script>";
exit();
?> 
