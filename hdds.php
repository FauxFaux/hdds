<html><head>
  <title>Faux' hdds</title>
  <style type="text/css">
   td,th { border: 1px solid black; padding: .4em }
   td { text-align: right }
   tr.hi td { background-color: #cfc }
  </style>
 </head>
<body>
<p>Data sourced from <a href="http://www.overclockers.co.uk/">Overclockers</a>.</p>
<?
foreach (array( 'rotary' =>
	array(
		167, // sata 74-160
		768, // sata 250-320
		940, // sata 450-800
		1279, // sata 1tb and over
	), 'ssd' => array (
		910, // ssd 30-100
		1427, // ssd 120-256
	)
) as $label => $table) {
	echo "<h2>$label</h2>";
	$str = '';
	foreach ($table as $sub) 
		$str.=file_get_contents("http://www.overclockers.co.uk/productlist.php?groupid=701&catid=14&subid={$sub}");

	table($str);
}

function table($str) {
	?>
	<table><tr><th>Size (GB)</th><th>Cheapest</th><th>Average</th><th>Cheapest per GB</th><th>Average per GB</th><th>Sample size</th></tr>
	<?
	preg_match_all('/<a href="(showproduct[^"]+?)" title="(?:View more details for )?([^"]*?)".*?([0-9.]+)((?:T|G))B.*?<span class="incVat">\(&pound;(\d+\.\d+)/s', $str, $regs);

	$bits = array();
	$url = array();
	$name = array();
	foreach ($regs[3] as $ind => $numpart) {
		$key = ($regs[4][$ind] == 'T' ? 1024 : 1) * $numpart;
		$price = $regs[5][$ind] * 1.2;
		$bits[$key][] = $price;
		$url[$key][$price][] = $regs[1][$ind];
		$name[$key][$price][] = $regs[2][$ind];
	}

	ksort($bits);
	$bestavg = $bestsiz = 1337;

	foreach ($bits as $size => $drives)
		if (min($drives)/$size < $bestavg) {
			$bestavg = min($drives)/$size;
			$bestsiz = $size; 
		}

	foreach ($bits as $size => $drives) {
		$cheap = min($drives);
		echo "<tr" . ($size == $bestsiz ? ' class="hi"' : '') . ">" .
			"<td>$size</td>" .
			"<td><a " .
				"href=\"http://www.overclockers.co.uk/{$url[$size][$cheap][0]}\" " .
				"title=\"{$name[$size][$cheap][0]}\">" .
				price($cheap) . "</a></td>" .
			"<td>" . price(average($drives)) . "</td>" .
			"<td>" . price($cheap/$size, 3) . "</td>" .
			"<td>" . price(average($drives)/$size,3) . "</td>" .
			"<td>" . count($drives) . "</td></tr>\n";
	}

	echo "</table>";
}

function average(array $arr) {
	return array_sum($arr) / count($arr);
}

function price($num, $level = 2) {
	return number_format($num, $level);
}

?>
</table>
</body>
</html>
