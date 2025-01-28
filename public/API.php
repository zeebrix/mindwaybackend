<?php
header('Content-Type: application/json');
$key="30JH&^%9JiKi(YT&*";

$SERVER_URL="localhost";
$DB_USERNAME="mindw172_mind";
$DB_PASSWORD="gznyo(s-f6Si";
$DB_NAME="mindw172_mindway";

$con = mysqli_connect($SERVER_URL,$DB_USERNAME,$DB_PASSWORD,$DB_NAME);


if(isset($_POST['check'])){
if (mysqli_connect_errno()){
    $set['success']="FAILED";
    $set["message"]="ENTER API KEY";
}else{
    $set['success']="SUCCESS";
    $set["message"]="DATABASE CONNECTION SUCCESS";
}

echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));


}


if(isset($_POST['update_play'])){

    $userKey="";
    $id="";
    $addPlay="";
    $tableName="";

    
    if(!isset($_POST['key'])){
        $set['success']="FAILED";
        $set["message"]="ENTER API KEY";
        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();

    }else{
        $userKey=$_POST['key'];
    }
    
    if(!isset($_POST['table_name'])){
        $set['success']="FAILED";
        $set["message"]="ENTER TABLE NAME";
        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();

    }else{
        $tableName=$_POST['table_name'];
    }





    if(!isset($_POST['id'])){
        $set['success']="FAILED";
        $set["message"]="ENTER AUDIO POST ID";
        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }else{
        $id=$_POST['id'];
    }
    if(!isset($_POST['play'])){
        $set['success']="FAILED";
        $set["message"]="ENTER PLAY COUNT";
        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }else{
        $addPlay=$_POST['play'];
    }

    if($userKey==$key){
        $set['success']="OK";
        $set["message"]="VALID KEY";
        
    }else{
        $set['success']="FAILED";
        $set["message"]="NOT VALID KEY";
        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }


    $sql="SELECT * FROM `$tableName` WHERE `id`='$id'";
    $result=mysqli_query($con,$sql);
    if(mysqli_num_rows($result)==0){
        $set['success']="FAILED";
        $set["message"]="NO AUDIO FOUND";
        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }else{
        $audioDetails=mysqli_fetch_assoc($result);
        $currentPlay=(int)$audioDetails['total_play'];
        $totalPlay=$currentPlay+$addPlay;

        $sql="UPDATE `$tableName` SET `total_play`='$totalPlay' WHERE `id`='$id'";

        if(mysqli_query($con,$sql)){
            $set['success']="OK";
            $set["message"]="PLAY COUNT UPDATED TO $totalPlay";
        }else{
            $set['success']="FAILED";
            $set["message"]="PLAY COUNT UPDATE FAILED";
        }


        $audioDetails['total_play']=$totalPlay;
        $set['audio_details']=$audioDetails;
    }

    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));



}



?>