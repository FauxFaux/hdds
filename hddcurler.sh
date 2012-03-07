#!/bin/bash
curl -s http://faux.uwcs.co.uk/hdds.php > /home/faux/hdds/grab/grab-$(date +'%Y-%m-%d').html
cd /home/faux/hdds/grab/
grep -m1 'td class="hi"' * | cut -d\< -f 1,11 | sed 's/grab-//;s/.html:.td.class....../ /' > sample.data && gnuplot history.gnuplot > plot.svg

