<?php
		function removeExtension($strName) {
			$ext = explode('.',$strName);
			$ext = array_reverse($ext);
			$ext = $ext[0];
					$strName = substr($strName, 0, -strlen($ext) -1);

			return $strName;
		}
		function getExtension($strName) {
			$ext = explode('.',$strName);
			$ext = array_reverse($ext);
			$ext = str_replace('.','',$ext[0]);

			return $ext;
		}
		function allowedExtensions($filename) {
			$filetype = file($filename, FILE_IGNORE_NEW_LINES);
			return $filetype;
		}
		function in_array_recursive($needle, $haystack, $strict = false) {
    		foreach ($haystack as $item) {
        		if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_recursive($needle, $item, $strict))) {
            		return true;
        		}
    		}
		    return false;
		}
?>