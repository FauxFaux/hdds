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
<table><tr><th>Size (GB)</th><th>Cheapest</th><th>Average</th><th>Cheapest per GB</th><th>Average per GB</th><th>Sample size</th></tr>
<?
$str = '';
foreach (array(
	167, // sata 74-160
	768, // sata 250-320
	940, // sata 450-800
	1279, // sata 1tb and over
	910, // ssd 30-100
	1427, // ssd 120-256
	) as $sub)
	$str.=file_get_contents("http://www.overclockers.co.uk/productlist.php?groupid=701&catid=14&subid={$sub}");

preg_match_all('/<a href="showproduct.*?([0-9.]+)((?:T|G))B.*?<span class="incVat">\(&pound;(\d+\.\d+)/s', $str, $regs);

$bits = array();
foreach ($regs[1] as $ind => $numpart)
	$bits[($regs[2][$ind] == 'T' ? 1024 : 1) * $numpart][] = $regs[3][$ind];

ksort($bits);

function average(array $arr) {
	return array_sum($arr) / count($arr);
}

function price($num, $level = 2) {
	return number_format($num, $level);
}

$bestavg = $bestsiz = 1337;

foreach ($bits as $size => $drives)
	if (min($drives)/$size < $bestavg) {
		$bestavg = min($drives)/$size;
		$bestsiz = $size; 
	}


foreach ($bits as $size => $drives)
	echo "<tr" . ($size == $bestsiz ? ' class="hi"' : '') . ">" .
		"<td>$size</td><td>" . price(min($drives)) . "</td>" .
		"<td>" . price(average($drives)) . "</td>" .
		"<td>" . price(min($drives)/$size, 3) . "</td>" .
		"<td>" . price(average($drives)/$size,3) . "</td>" .
		"<td>" . count($drives) . "</td></tr>\n";
?>
</table>
</body>
</html>
