<?php 
require 'connection.php';
if (isset($_POST['state_id'])) {
$stid=$_POST['state_id'];
$sel=$con->prepare("select * from district where stid=? order by id DESC");
$exe=$sel->execute([$stid]);
while($resd=$sel->fetch())
{
echo '<option value='.$resd->id.'>'.$resd->name.'</option>';
}
}
elseif (isset($_POST['course_id'])){	 
$cauid=$_POST['course_id'];
$sel=$con->prepare("select * from subjects where pid=? and cid=? order by id DESC");
$exe=$sel->execute([$proid,$cauid]);
while($resb=$sel->fetch())
{
echo '<option value='.$resb->id.'>'.$resb->name.'</option>';
}
}
?>