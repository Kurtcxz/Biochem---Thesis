<?php
session_start();
session_destroy();
header('Location: patient_login.php');
exit();