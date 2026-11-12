<?php
error_reporting(E_ALL);
set_error_handler(function($n,$s){throw new Exception($s);});
$_SESSION=['role'=>'user','userID'=>4,'venueID'=>1]; $_SERVER['SCRIPT_NAME']='/index.php';
$profile=['name'=>'Juan Dela Cruz','email'=>'juan@example.com'];
$venue=['venueID'=>1,'venueName'=>'Pasay Sports Complex','address'=>'Pasay City','description'=>'A sports venue for your next game.','sportsType'=>'Basketball','openHour'=>'08:00:00','closeHour'=>'22:00:00','hourlyRate'=>200,'imageFilePath'=>'/public/media/venues/pasay-sports-complex.jpg'];
$venues=[$venue]; $users=[['userID'=>4,'name'=>'Juan Dela Cruz','email'=>'juan@example.com','userType'=>'user'],['userID'=>2,'name'=>'Alex Owner','email'=>'owner@example.com','userType'=>'court_owner']];
$booking=['bookingID'=>1,'venueName'=>$venue['venueName'],'courtName'=>'Court 1','bookingDate'=>date('Y-m-d',strtotime('+1 day')),'bookingTime'=>'14:00:00','amount'=>200,'paymentStatus'=>'paid','status'=>'upcoming','userName'=>'Juan Dela Cruz','userEmail'=>'juan@example.com','imageFilePath'=>$venue['imageFilePath']];
$upcomingBookings=[$booking];$bookingHistory=[];$recentBookings=[$booking];$bookings=[$booking];$staffList=[['name'=>'Sam Staff','email'=>'staff@example.com','venueName'=>$venue['venueName']]];
$messages=[['senderID'=>4,'body'=>'Hello!','createdAt'=>'2026-11-12 10:00:00']];$paymentsByBooking=[1=>['status'=>'paid']];
$search='';$sport='all';$error='';$success=false;$venueID=1;$senderID=4;$receiverID=2;$cancelToken='test';
$targetDate=$booking['bookingDate'];$courts=[['courtID'=>1,'courtName'=>'Court 1'],['courtID'=>2,'courtName'=>'Court 2']];$images=[['imageFilePath'=>$venue['imageFilePath']]];$grid=['09:00:00'=>[1=>true]];
$stats=['totalBookings'=>10,'totalRevenue'=>2000,'totalStaff'=>1];$year=2026;$month=0;$years=[2025,2026];$monthlyRevenue=[];$monthlyBookings=[];for($i=1;$i<=12;$i++){ $monthlyRevenue[]=['month'=>$i,'revenue'=>$i*100];$monthlyBookings[]=['month'=>$i,'bookings'=>$i]; } $courtUsage=[['courtName'=>'Court 1','completedBookings'=>6],['courtName'=>'Court 2','completedBookings'=>4]];
if(!empty($_GET['empty'])){$venues=$users=$upcomingBookings=$bookingHistory=$recentBookings=$bookings=$staffList=$messages=$paymentsByBooking=$courts=$images=$grid=$courtUsage=[];}
