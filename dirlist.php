<html>
   <head>

<?php
require_once 'functions.php';

$styleSheet = dirname($_SERVER['PHP_SELF']) . '/style.css';
echo "<link rel=stylesheet href=$styleSheet>\n";

$patchDir = ".";
if(isset($_GET['path'])){
   $patchDir = $_GET['path'];
}

if(preg_match("/redmine/", $patchDir)) {
   header('HTTP/1.0 404 Not Found');
   echo "<h1>404 Not Found</h1>";
   echo "The page that you have requested could not be found.";
   exit();
}

$basePath = preg_replace('/\/patches-new\//', "", $_SERVER['REQUEST_URI']);
$basePath = preg_replace('/(\w+)\/(\w+)?\/.*/', '/$1/$2/', $basePath);
$basePath = preg_replace('/\/$/', "", $basePath);
$basePath = preg_replace('/^\//', "", $basePath);

if (empty($basePath)) {
   $infoFile = "desc/info";
} else {
   $infoFile = "desc/${basePath}_info";
}

if (is_file($infoFile))
{
   require_once($infoFile);
} else {
   require_once('info.php');
}

$S = "N";
if(isset($_GET['S'])){
   $S = $_GET['S'];
}

$order = "";
if(isset($_GET['O'])){
   $order = $_GET['O'];
}

if ($S == "N" ) {
   $sort = "name";
} elseif ($S == 'D' ) {
   $sort = "lastmod";
} elseif ($S == 'S' ) {
   $sort = "realsize";
}

// Make the default order sorting
$oSort = SORT_ASC;
if ($order == "") {
   $order = "A";
   $oSort = SORT_ASC;
} elseif ($order == "A") {
   $order = "D";
   $oSort = SORT_DESC;
} elseif ($order == "D") {
   $order = "A";
   $oSort = SORT_ASC;
}

$dirlist = getFileList("$patchDir", false);
#$dirlist = arraySort($dirlist, $sort, $oSort);
usort ($dirlist, "aComp");
#$dirlist = aComp($dirlist, $sort, $oSort);

print ("
   <table>
   <thead>
      <tr>
         <th id='left'><a href=?S=N&O=$order title=Name>Name</a></th>
         <th id='center'><a href=?S=D&O=$order>Last Modified</a></th>
         <th id='right'><a href=?S=S&O=$order>Size</a></th>
      </tr>
      <tr >
         <th colspan=3><hr /></th>
      </tr>
   </thead>");


foreach($dirlist as $file) {
   if(preg_match("/index\./", $file['name'])) continue;
   if(preg_match("/redmine/", $file['path'])) continue;
   if(is_dir($file['path'])) {
      $class="dir";
   } else {
      $class="file";
   }
   $fName = $file["name"];
   $fDate = date("M j Y g:i A", $file['lastmod']);
   print("
      <tr class='$class'>
         <td id='left'><a alt=$fName title=$fName href=$fName>$fName</a></td>
         <td id='center'>$fDate</td>
         <td id='right'>{$file['size']}</td>
      </tr>");
}
?>
         </table>
      </div>
   </body>
</html>
