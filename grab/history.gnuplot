set terminal svg size 500,1000
set ydata time
set key off
set timefmt "%Y-%m-%d"
set format y "%Y-%m"
set xlabel "pence per GB"
set grid
plot "sample.data" u ($2*100):1 with lines

