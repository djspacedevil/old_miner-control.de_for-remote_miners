//Morris Chart
function renderChart() {	
	return Morris.Line({
			// ID of the element in which to draw the chart.
			element: 'Main_static_grid',
			// Chart data records -- each entry in this array corresponds to a point on
			// the chart.
			data: [
				{ year: '2004', value: 20 },
				{ year: '2009', value: 10 },
				{ year: '2010', value: 5 },
				{ year: '2011', value: 5 },
				{ year: '2012', value: 20 }
			],
			// The name of the data record attribute that contains x-values.
			xkey: 'year',
			// A list of names of data record attributes that contain y-values.
			ykeys: ['value'],
			// Labels for the ykeys -- will be displayed when you hover over the
			// chart.
			labels: ['Value']
	});
}		
//