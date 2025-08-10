<?php 
//for admin
$admin_password="admin1";
$hashed=password_hash($admin_password,PASSWORD_DEFAULT);
echo $hashed;
echo "<br><br>";

//for staff
$staff_password="seeker1";
$hashed=password_hash($staff_password,PASSWORD_DEFAULT);
echo $hashed;
echo "<br><br>";

//for passenger
$member_password="donor1";
$hashed=password_hash($member_password,PASSWORD_DEFAULT);
echo $hashed;
echo "<br><br>";

//for passenger
$member_password="satff1";
$hashed=password_hash($member_password,PASSWORD_DEFAULT);
echo $hashed;
echo "<br><br>";


?>
<!-- 
seeker
username:ram
password:ram1

donors
username:hari
password:hari12

staff
username:sabin1
password:sabin1


admin
username:ashok1
password:ashok1 -->
