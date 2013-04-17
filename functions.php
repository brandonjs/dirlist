<?php

function format_size($size) {
      $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
      if ($size == 0) { return('n/a'); } else {
      return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); }
}

function getFileList($dir, $recurse=false) {
   // array to hold return value
   $retval = array();

   // add trailing slash if missing
   if(substr($dir, -1) != "/") $dir .= "/";

   // open pointer to directory and read list of files
   $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
   while(false !== ($entry = $d->read())) {
      // skip hidden files
      if($entry[0] == ".") continue;
      if(preg_match("/include|index\.|\.htaccess|\.info|^\.\.?$|downloads/", $entry)) continue;
      if(is_dir("$dir$entry")) {
         $retval[] = array(
            "name" => basename(trim("$dir$entry/", "./ ")),
            "path" => "$dir$entry",
#           "type" => filetype("$dir$entry"),
            "type" => "dir",
            "size" => "-",
            "realsize" => "0",
            "lastmod" => filemtime("$dir$entry"),
         );
         if($recurse && is_readable("$dir$entry/")) {
            $retval = array_merge($retval, getFileList("$dir$entry/", true));
         }
      } elseif(is_readable("$dir$entry")) {
         $retval[] = array(
            "name" => basename(trim("$dir$entry", "./ ")),
            "path" => "$dir$entry",
#           "type" => mime_content_type("$dir$entry"),
            "type" => "file",
            "size" => format_size(filesize("$dir$entry")),
            "realsize" => filesize("$dir$entry"),
            "lastmod" => filemtime("$dir$entry"),
         );
      }
   }
   $d->close();

   return $retval;
}

function arraySort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
    return $new_array;
}

function aComp($a, $b) {
   global $oSort;
   global $order;
   global $sort;

   $aType = $a['type'];
   $aSort = $a[$sort];
   $bType = $b['type'];
   $bSort = $b[$sort];

   return ($aType == "dir") ? ($bType == "dir" ?  ($order == "A" ? strnatcmp ($aSort, $bSort) : strnatcmp ($bSort, $aSort)) : -1) : ($bType == "dir" ? 1 : ($order == "A" ? strnatcmp ($aSort, $bSort) : strnatcmp ($bSort, $aSort)));

#   if ($aType == "dir" ) {
#      if ($bType == "dir") {
#         if ($order == "A" ) {
#            return strnatcmp ($aSort, $bSort);
#         } else {
#            return strnatcmp ($bSort, $aSort);
#         }
#      } else {
#        return -1;
#      }
#   } else {
#      if ($bType == "dir" ) {
#         return 1;
#      } else {
#         if ($order == "A" ) {
#               return strnatcmp ($aSort, $bSort);
#         } else {
#               return strnatcmp ($bSort, $aSort);
#            }
#         }
#      }
#   }
}

?>

