<?php
header("Access-Control-Allow-Origin: *");

if(is_file("../../core/admin/index.php"))
    require("../../core/admin/index.php");
elseif(is_file("../../../core/admin/index.php"))
    require("../../../core/admin/index.php");
?>
