<?php
if ($fh = fopen('englishWorldCupLaLigaCapped.txt', 'r')) {
    include('xssPrevent.php');
    if(isset($_POST['retrieveImage'])){
        if(isset($_POST['pName'])){
            $sentName = escape($_POST['pName']);
            //create teams array from all team listed for each player 
            while (!feof($fh)) {
                $line = fgets($fh);
                if(strlen($line) > 1){
                    $lineArray = json_decode($line, true);
                    if($lineArray['name'] == $sentName){
                        echo '<img src="'.$lineArray['image'].'" alt="'.$sentName.'" width="100" height="100"><br/>';
                        break;
                    }
                }
            }
            fclose($fh);
        }else{
                 echo 'no player name submitted';
        }
    }
    
    if(isset($_POST['retrieveURL'])){
        if(isset($_POST['pName'])){
            $sentName = escape($_POST['pName']);
            //create teams array from all team listed for each player 
            while (!feof($fh)) {
                $line = fgets($fh);
                if(strlen($line) > 1){
                    $lineArray = json_decode($line, true);
                    if($lineArray['name'] == $sentName){
                        echo '<a href="'.$lineArray['url'].'">'.$sentName.'</a>';
                        break;
                    }
                }
            }
            fclose($fh);
        }else{
                 echo 'no player name submitted';
        }
    }
        
}else{
    echo 'could not search players.  Please try your request again';
}
?>