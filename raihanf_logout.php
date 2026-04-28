<?php
session_start();
session_destroy();
header("Location: raihanf_index.php");
?>