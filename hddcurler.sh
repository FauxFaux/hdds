#!/bin/bash
curl -s http://faux.uwcs.co.uk/hdds.php > /home/faux/hdds/grab/grab-$(date +'%Y-%m-%d').html
cd /home/faux/hdds/grab/
for f in *.html; do
	if grep -m1 'td class="hi"' $f > /dev/null; then
		printf "%s %s %s\n" \
			"$(basename $f | sed 's/grab-//;s/\..*//')" \
			"$(grep -m1 'td class="hi"' $f | cut -d\< -f 11 | cut -d\> -f2)" \
			"$(fgrep 'That is, ssds are' $f | cut -d\  -f 5)"
	fi
done > sample.data

gnuplot history.gnuplot > plot.svg

