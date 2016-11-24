<?php

echo '<div class="container">
		<h2>Allowed filetypes</h2>
		<div class="content">
			<h3>All allowed filetypes (hover for corresponding mimetypes)</h3>
			<ul id="filetypelist" class="alternate">';
			$filetypes = array_chunk(allowedMimeAndExtensions('extension','mime'),5,true);
			for ($c = 0; $c < count($filetypes); $c++) {
				echo '<li>';
				foreach ($filetypes[$c] as $key => $value) {
					echo '<span class="helper" title="'.$value.'">'.$key.'</span>';
				}
				echo '</li>';
			}
echo '</ul></div></div>';
?>