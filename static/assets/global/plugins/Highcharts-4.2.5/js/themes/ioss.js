//自定义的 HighCharts 样式
Highcharts.theme = {
	colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee","#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
	chart: {
		plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: null,
		backgroundColor: null,
		style: {
			fontFamily: "Open Sans,Microsoft Yahei, Tahoma, Helvetica, Arial, sans-serif"
		}
	},
	title: {
		text: null,
		style: {
			fontSize: '16px',
			fontWeight: 'bold',
			textTransform: 'uppercase'
		}
	},
	tooltip: {
		borderWidth: 0,
		backgroundColor: 'rgba(219,219,216,0.8)',
		shadow: false
	},
	legend: {
		itemStyle: {
			fontWeight: 'bold',
			fontSize: '13px'
		}
	},
	xAxis: {
		gridLineWidth: 1,
		labels: {
			style: {
				fontSize: '12px'
			}
		}
	},
	yAxis: {
		minorTickInterval: 'auto',
		title: {
			style: {
				textTransform: 'uppercase'
			}
		},
		labels: {
			style: {
				fontSize: '12px'
			}
		}
	},
	plotOptions: {
		candlestick: {
			lineColor: '#404048'
		},
		pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			dataLabels: {
				enabled: true,
				format: '{point.name}',
				style:'font-weight:normal'
			}
		}
		
	},
	// General
	background2: '#F0F0EA',
	credits:{ enabled:false }
};
// Apply the theme
Highcharts.setOptions(Highcharts.theme);