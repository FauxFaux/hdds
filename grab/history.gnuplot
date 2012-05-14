set terminal svg size 500,1000
set ydata time
set key off
set timefmt "%Y-%m-%d"
set format y "%Y-%m"
set style line 1 lt rgb "#006600"
set style line 2 lt rgb "#3300cc"

set x2label "pence per GB" textcolor ls 1
set xlabel "relative ssd / hdd price" textcolor ls 2
set x2tics autofreq
set xtics 10
set grid x2tics ytics
plot "sample.data" u ($2*100):1 axes x2y1 with lines ls 1, "sample.data" u ($3):1 with lines ls 2

