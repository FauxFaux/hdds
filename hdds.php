<html><head>
  <title>Faux' hdds</title>
  <style type="text/css">
   td,th { border: 1px solid black; padding: .4em }
   td { text-align: right }
   tr.hi td, td.hi, span.hi { background-color: #cfc }
   .mid { background-color: #ffc }
   .low { background-color: #fcc }
   span { padding: 1ex }
  </style>
 </head>
<body>
<?
$vhi = 1;
$vmid = 5;
$vlow = 25;
?>
<div style="float:left">
<p>Open <a href="http://git.goeswhere.com/?p=hdds.git;a=summary">source</a>.  Data sourced from <a href="http://www.overclockers.co.uk/">Overclockers</a>.</p>
<p>Values within <span class="hi"><?=$vhi?>%</span>, <span class="mid"><?=$vmid?>%</span> and <span class="low"><?=$vlow?>%</span> of the best are marked.  Hover links for names.</p>
<?
$mins = array();
foreach (array( 'rotary' =>
	array(
		1952, // sata 6gbps
		1663, // sata 250-320
		1664, // sata 450-800
		1665, // sata 1000-1500
		1954, // sata 2000-
	), 'ssd' => array (
		910, // ssd 30-100
		1427, // ssd 120-1000
	)
) as $label => $table) {
	echo "<h2>$label</h2>";
	$str = '';
	foreach ($table as $sub) 
		$str.=file_get_contents("http://www.overclockers.co.uk/productlist.php?groupid=701&catid=1660&subid={$sub}");

	$mins[] = table($str);
}

echo "<p>That is, ssds are " . $mins[1]/$mins[0] . " times the price of rotary media.</p>";

function table($str) {
	global $vlow,$vmid,$vhi;
	?>
	<table><tr><th>Size (GB)</th><th>Cheapest</th><th>Average</th><th>Cheapest per GB</th><th>Average per GB</th><th>Sample size</th></tr>
	<?
	preg_match_all('/<a href="(showproduct[^"]+?)" title="(?:View more details for )?([^"]*?)".*?([0-9.]+)((?:T|G))B.*?<span class="incVat">\(&pound;(\d+\.\d+)/s', $str, $regs);

	$bits = array();
	$url = array();
	$name = array();
	foreach ($regs[3] as $ind => $numpart) {
		if (stristr($regs[2][$ind], 'hybrid'))
			continue;
		$key = ($regs[4][$ind] == 'T' ? 1000 : 1) * $numpart;
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

	$min = 9000;
	$max = 0;
	foreach ($bits as $size => $drives) {
		$p = min($drives)/$size;
		if ($p < $min)
			$min = $p;
		if ($p > $max)
			$max = $p;
	}

	foreach ($bits as $size => $drives) {
		$cheap = min($drives);
		$rat = ($cheap/$size) / $min;
		echo "<tr" . ($size == $bestsiz ? ' class="hi"' : '') . ">" .
			"<td>$size</td>" .
			"<td><a " .
				"href=\"http://www.overclockers.co.uk/{$url[$size][$cheap][0]}\" " .
				"title=\"{$name[$size][$cheap][0]}\">" .
				price($cheap) . "</a></td>" .
			"<td>" . price(average($drives)) . "</td>" .
			"<td class=\"" .
				($rat < (1 + $vhi/100.0) ? 'hi' : 
					($rat < (1 + $vmid/100.0) ? 'mid' : 
						($rat < (1 + $vlow/100.0) ? 'low' : '')))  . "\">" .
				price($cheap/$size, 3) . "</td>" .
			"<td>" . price(average($drives)/$size,3) . "</td>" .
			"<td>" . count($drives) . "</td></tr>\n";
	}

	echo "</table>\n";
	return $min;
}

function average(array $arr) {
	return array_sum($arr) / count($arr);
}

function price($num, $level = 2) {
	return number_format($num, $level);
}

?>
</div>
<img src="http://faux.uwcs.co.uk/hdd-grabs/plot.svg"/>
</body>
</html>
