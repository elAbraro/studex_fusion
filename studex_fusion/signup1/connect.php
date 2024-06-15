<?php
$HOSTNAME='localhost';
$USERNAME='root';
$PASSWORD='';
$DATABaSE='studex_fusion';

$con=mysqli_connect($HOSTNAME,$USERNAME, $PASSWORD,
$DATABaSE);

if(!$con){
    die(mysqli_error($con));
}
?>