<html>
    <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta charset="UTF-8">
  <title>Diagram</title>
    </head>
<body>
<?php

session_start();
    $date_start=$_POST["date_start"];
    $date_end=$_POST["date_end"];
    $time_start=$_POST["time_start"];
    $time_end=$_POST["time_end"];
    $field=$_POST["field"];
    $field_width=$_POST["width"];
    $field_height=$_POST["height"];
    
   include ("./bd.php");
    $map_table="data";
    $mppt_table="mppt";
    $index=0;
    $_SESSION['Legend']=$field;

if (isset($_POST['multichart']))
    {
    $query = "SELECT MIN(number) FROM `data` WHERE date='".$date_start."' AND time LIKE '".$time_start.":0%'\n";
    $result=mysql_query($query) or die("Query failed date/time start:".mysql_error());
    $row=mysql_fetch_row($result);
    $number_low=$row[0]; if ($number_low==NULL) {echo "Нет соответствия по дате/времени начала для МАП"; 
    mysql_close($db);exit();}
    $_SESSION['number_start']=$number_low;
    mysql_free_result($result);    
    
    if (file_exists("/var/map/.mppt")) {

    $query = "SELECT MIN(number) FROM `mppt` WHERE date='".$date_start."' AND time LIKE '".$time_start.":0%'\n";
    $result=mysql_query($query) or die("Query failed date/time start:".mysql_error());
    $row=mysql_fetch_row($result);
    $number_low=$row[0]; if ($number_low==NULL) {echo "Нет соответствия по дате/времени начала для MPPT"; 
    mysql_close($db);exit();}
    $_SESSION['number_start_mppt']=$number_low;


    mysql_free_result($result);
    }
    mysql_close($db);
    header("Location:multichart_history.php");
    die;
    }

   switch ($field)
    {
     case "_Uacc":
     case "_Iacc":
     case "_PLoad":
     case "_UNET":
     case "_PNET":
     case "_UOUTmed":
     case "_Temp_Grad0":
     case "_INET_16_4":
     case "_IAcc_med_A_u16":
    $query = "SELECT MIN(number) FROM `data` WHERE date='".$date_start."' AND time LIKE '".$time_start.":0%'\n";
    $result=mysql_query($query) or die("Query failed date/time start:".mysql_error());
    $row=mysql_fetch_row($result);
    $number_low=$row[0]; if ($number_low==NULL) {echo "Нет соответствия по дате/времени начала"; exit();}
    $query = "SELECT MIN(number) FROM `data` WHERE date='".$date_end."' AND time LIKE '".$time_end.":0%'\n";
    $result=mysql_query($query) or die("Query failed date/time end:".mysql_error());
    $row=mysql_fetch_row($result);
    $number_high=$row[0];if ($number_high==NULL) {echo "Нет соответствия по дате/времени окончания"; exit();}

    $query = "SELECT date,time,".$field." FROM `data` WHERE number BETWEEN ".$number_low." AND ".$number_high."\n";
    
    $result= mysql_query($query) or die("Query failed final request:".mysql_error());	
	
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        foreach ($line as $col_value) {
        $temp_array[$index++]=$col_value;
        
            }
        }
        break;
    
    case "Vc_PV":
    case "Ic_PV":
    case "V_Bat":
    case "P_PV":
    case "P_curr":
    case "windspeed":
    $query = "SELECT MIN(number) FROM `mppt` WHERE date='".$date_start."' AND time LIKE '".$time_start.":0%'\n";
    $result=mysql_query($query) or die("Query failed:".mysql_error());
    $row=mysql_fetch_row($result);
    $number_low=$row[0];if ($number_low==NULL) {echo "Нет соответствия по дате/времени начала"; exit();}
    $query = "SELECT MIN(number) FROM `mppt` WHERE date='".$date_end."' AND time LIKE '".$time_end.":0%'\n";
    $result=mysql_query($query) or die("Query failed:".mysql_error());
    $row=mysql_fetch_row($result);
    $number_high=$row[0];if ($number_high==NULL) {echo "Нет соответствия по дате/времени окончания"; exit();}

    $query = "SELECT date,time,".$field." FROM `mppt` WHERE number BETWEEN ".$number_low." AND ".$number_high."\n";
    
    $result= mysql_query($query) or die("Query failed:".mysql_error());	
	
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        foreach ($line as $col_value) {
        $temp_array[$index++]=$col_value;
        
            }
        }
        break;

    case "Energy":
	$query="SELECT TO_DAYS('".$date_start."')\n";
	
        $result=mysql_query($query);
        $row=mysql_fetch_row($result);
	$number_start=$row[0];
	$query="SELECT TO_DAYS('".$date_end."')\n";
	$result=mysql_query($query);
        $row=mysql_fetch_row($result);
	$number_end=$row[0];
		
	

         $i=0; $e=0;
	  for  ($counter=$number_start; $counter<=$number_end; $counter++)
	    {
	     $query="SELECT FROM_DAYS(".$counter.")\n";
	     $result=mysql_query($query);
    	    $row=mysql_fetch_row($result);
	    $datax[$i]=$row[0];
	
	     $query="SELECT MAX(Pwr_kw) from `mppt` WHERE date='".$datax[$i]."'\n"; 
             $result= mysql_query($query) or die("Query failed:".mysql_error());
             if ($result!=NULL) 
		{
	        $row=mysql_fetch_row($result);
        	    $datay[$i]=$row[0];
		    $e+=$datay[$i++];
		    $_SESSION['Legend']=".Summary ".$e." kWh";
		}	
	    }
	break; 	
    default:
     mysql_free_result($result);
    mysql_close($db);
    exit(-1);
	
    }
   if ($field!="Energy") 
    {
    $i=0;$i1=0;
    do 
     {
      $datax[$i1]=substr($temp_array[$i],5,9)." ".$temp_array[$i+1];
      $datay[$i1++]=$temp_array[$i+2];
      $i+=3;
          
     } while ($i<=$index);
   }
	$_SESSION['datay1']=$datay;
;	$_SESSION['datax1']=$datax;
	
	$_SESSION['width']=$field_width;
	$_SESSION['height']=$field_height;


    mysql_free_result($result);
  //mysql_free_result($result_mpptd);
  mysql_close($db);

if ($field!="Energy")
echo "<img src='./diagram.php'/>";
else
echo "<img src='./diagram_bar.php'/>";


?>
</body>
</html>