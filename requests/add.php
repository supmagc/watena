<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('DatabaseManager');
require_plugin('ToeVla');

$oConnection = DatabaseManager::getConnection('toevladmin');
$oTable = $oConnection->getTable('festival');

function GetNameByIndex($nIndex) {
	if($nIndex == '1') return 'City';
	if($nIndex == '2') return 'Beach';
	if($nIndex == '4') return 'Field';
	if($nIndex == '5') return 'Park';
	if($nIndex == '6') return 'Indoor';
}

if(isset($_POST['name'])) {
	$aData = array();
	$aData['name'] = $_POST['name'];
	$aData['genreId'] = (int)$_POST['genre'];
	$aData['locationTypeId'] = (int)$_POST['location'];
	$aData['hash'] = md5('TOEVLA-156+464' . $_POST['name']);
	$aData['data'] = GetNameByIndex($_POST['location']) . '@255$0$0;0$231$243;255$240$159;0$021;7$102;6$102;@Tree1$-0.05$0$-7.57$270$323.97$0$;Tree3$-3.9$0$-7.19$270$0$0$;Stand2$7.04$0$9.22$270$301.72$0$;Stand4$5.63$0$-8.72$270$62.99$0$;Stand6$1.08$0$10.47$270$278.97$0$;Speaker5$-6.99$0$2.9$270$212.49$0$;Speaker5$-7.02$0$-4.14$270$148.74$0$;Flag1$-1.8$0$-3.9$270$0$0$;Flag2$-11.03$0$-1.53$270$0$0$;Flag1$-4.1$0$-0.38$270$0$0$;Flag1$-2.16$0$3.27$270$0$0$;Flag2$-10.92$0$1.13$270$0$0$;Tree3$-8$0$-7.13$270$0$0$;Tree3$0.3$0$5.4$270$280.98$0$;Tree3$-3.59$0$5.37$270$344.73$0$;Tree3$-8.02$0$5.19$270$8.74$0$;Stand5$-3.96$0$-11.8$270$112.74$0$;Stand7$-3.18$0$10.39$270$86.24$0$;Stand6$11.24$0$-12.13$270$41.49$0$;Stand3$4.03$0$-13.94$270$75.49$0$;Stand1$-10.4$0$7.26$270$45.25$0$;Stand1$-11.6$0$-7.62$270$320.24$0$;Speaker4$2.8$0$3.05$270$336.48$0$;Speaker4$3.53$0$-6.2$270$35.99$0$;@';
	$aData['artists'] = '§;§;§;§;§;§;§;§;§;§;';
	$aData['quiz'] = 'How many festivals are features in this game?§73§75§78;How many different styles of music do we feature?§12§10§8;How many stars are there in the pop Area§8§6§7;How many cups must you collect before you can recycle?§10§15§20;There\'s a statue of what in the city?§Your character§a Vinyl LP§a Bird;§§§;§§§;§§§;§§§;§§§;';
	
	$oTable->insert($aData);
	echo "Succes when no error !!<br />";
}

?>
<form action="add.php" method="post">
	<input type="text" name="name" />
	<select name="genre">
		<option value="1">Blues</option>
		<option value="2">Dance</option>
		<option value="3">Various</option>
		<option value="4">Folk</option>
		<option value="5">Jazz</option>
		<option value="6">Classical</option>
		<option value="7">Metal</option>
		<option value="8">Pop</option>
		<option value="9">Reggae</option>
		<option value="10">Rock</option>
		<option value="11">Theater</option>
		<option value="12">World</option>
	</select>
	<select name="location">
		<option value="1">City</option>
		<option value="2">Beach</option>
		<option value="4">Field</option>
		<option value="5">Park</option>
		<option value="6">Indoor</option>
	</select>
	<input type="submit" value="CREATE" />
</form>