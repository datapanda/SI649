<!DOCTYPE html>
<html>
	<head>
		<title>SI649: Information Visualization</title>

		<script src="http://d3js.org/d3.v2.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
		<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js" type="text/javascript"></script>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css">
    	<style>#slider { margin: 10px; }  </style>
		<script src="./square-crossfilter/crossfilter.min.js" type="text/javascript"></script>
		<script src="./js/tooltipsy.min.js" type="text/javascript"></script>

		<style type="text/css">

		#day-selector ul li{

			display: inline-block;
			margin-right: 20px;
			margin-left: 20px;
		}
		div.chart {

			font-family: sans-serif;
			font-size: 0.7em;
		}
		div.bar {

			display: inline-block;
			color: white;
			width: 20px;
			height: 75px;
			background-color: teal;
			margin-right: 2px;
		}
		.chart {

			margin-top: 50px;
			margin-right: 50px;
			margin-bottom: 50px;
			margin-left: 50px;
		}
		.chart rect {

			stroke: white;
			fill: steelblue;

		}
		.yaxis path, 
		.yaxis line {
    		
    		fill: none;
    		stroke: black;
    		shape-rendering: crispEdges;
		}
		.yaxis text {

			font-family: sans-serif;
			font-size: 11px;
		}
		.xaxis path,
		.xaxis line {

			fill: none;
    		stroke: black;
    		shape-rendering: crispEdges;
		}

		.xaxis text {
			
			font-style: normal;	
			font-family: sans-serif;
			font-size: 12px;
			text-transform: "rotate(-90)";

		}
		</style>
		
		<script>		
		//Call jQuery so the code doesn't run until the document is ready
		var date;
		d3.json("http://localhost:8888/SI649/data.php/", function(json) {



			date = json
	 		console.log(date)
	 		
	 		drawBars(date)
	
	 	});

		//Function to draw the bar graphs
		function drawBars(data){

			max =  Math.max.apply(Math,data.map(function(d){return d.convoCount;}))

			//Setup SVG Canvas Constants
			var barWidth = 40;
			var width = data.length * barWidth + 20;
			var height = (max + 20);
			var margin = { top: 20, right: 20, bottom: 200, left: 40 };

			//Setup the SVG Canvas
			var chart = 	d3.select("body")
								.append("svg")
									.attr("class","chart")
									.attr("width",width)
									.attr("height",height + margin.bottom);

			//Setup the Scales for the Data
				var x0 = d3.scale.ordinal()
   						.rangeRoundBands([0, width], .1);

				var x1 = d3.scale.ordinal();

			var yScale = 		d3.scale.linear()
									.domain([max, -20]) //Changed to -20 in order to help the user show the 1 or the 2
									.range([10, height]);
			
			//yScale.domain([0, d3.max(data, function(d) { return d3.max(d.ages, function(d) { return d.value; }); })]);
									
			//Setup and Draw Axis
			var xAxis = d3.svg.axis()
							.scale(x0)
							.orient("bottom");

			var yAxis = d3.svg.axis()
							.scale(yScale)
							.orient("left");
			
			//Override the x0 and x1 Scales to set their domains to the data
			x0.domain(data.map(function(d) { return d.username; }));
				x1.domain(data).rangeRoundBands([0, x0.rangeBand()]);

			//Draw X-Axis
			chart.append("g")
				.attr("class","xaxis")
				.attr("width",width)
				.attr("height",height)
				.attr("transform", "translate(50," + max + ")")
				.call(xAxis);

			//Transform the X-Axis Labels
			d3.select(".xaxis").selectAll("text")
				.attr("transform", "rotate(-90)")
				.attr("x", -100)
				.attr("y", 0);

			//Draw Y-Axis
			chart.append("g")
				.attr("class","yaxis")
				.attr("width",10)
				.attr("height",height)
				.attr("transform", "translate(50,-20)")
				.call(yAxis)
				.append("text")
     				.attr("transform", "rotate(-90)")
     				.attr("x", -50)
     				.attr("y", -50)
     				.attr("dy", ".71em")
     				.style("text-anchor", "end")
     				.text("Conversation Count");
			
			//Draw the Bars
			var bars = chart.selectAll("rect").data(data);
				
				bars.enter()
					.append("rect")
						.attr("title",function(d) { return d.convoCount + " Conversations" })
						.attr("class","rect")
						.attr("width", barWidth)//x1.rangeBands())//barWidth)//x1.rangeBand())
						.attr("x", function(d) { return x0(d.username) + 50 + "px"})
						.attr("y", function(d) { return yScale(d.convoCount) - 20 + "px" })
						.attr("height", function(d) { return height - yScale(d.convoCount)});

				bars.exit().remove();
		
			//Begin Transiton Code
			d3.select("sort")
				.on("change", change);

			var sortTimeout = setTimeout(function() {
			
				d3.select("input").property("checked", true).each(change);
				
			}, 2000);
	  		
	  		function change() {

	  			clearTimeout(sortTimeout);

			    // Copy-on-write since tweens are evaluated after a delay.
			    var x02 = x0.domain(data.sort(this.checked

			    	? function(a, b) { return b.convoCount - a.convoCount; }
			        : function(a, b) { return d3.ascending(a.convoCount, b.convoCount);

			    })
			        	
			        .map(function(d) { return d.username; }))
			        .copy();

			    var transition = chart.transition().duration(750),
			        delay = function(d, i) { return i * 50; };

			    transition.selectAll(".rect")
			        .delay(delay)
			        .attr("x", function(d) { return x0(d.username) + 50 + "px"; });

			    transition.select(".xaxis")
							.call(xAxis)
							.delay(delay)
							.selectAll("text")
								.attr("transform", "rotate(-90)")
								.attr("x", -100)
								.attr("y", 0)
							.delay(delay);	
			}

			//ToolTipsy Mousover
			$('rect').tooltipsy({

				offset: [10, 0],
				alignTo: 'cursor',
				css: {
				        'padding': '10px',
				        'max-width': '120px',
				        'color': '#FFFFFF',
				        'background-color': '#000000',
				        'border': 'none',
				        '-moz-box-shadow': '0 0 10px rgba(0, 0, 0, .5)',
				        '-webkit-box-shadow': '0 0 10px rgba(0, 0, 0, .5)',
				        'box-shadow': '0 0 10px rgba(0, 0, 0, .5)',
				        'text-shadow': 'none'
				    }
			}); // End ToolTipsy
		} // End function drawBars
		var dataReturned;
		// //Begin jQuery AJAX Call to Update JSON Data
		$(document).ready(function(){

			$("#month").on("change",function(){

				$.ajax({

					type: "POST",
					url: "data.php",
					dataType: "JSON",
					data: {

						month: $(this).val()

					},
					async: true,

					success: function(Res)	{
						//Mess with code here


						dataReturned = Res;
						// **** Working ****
						d3.select("body").selectAll("svg").remove();
						drawBars(dataReturned); // This works but doesn't do the enter/exit correct.y
						//*** End Working ****

					},
					error: function() {
						console.log("Failure");
					}
				})//End ajax request				
			})//End onChange function
		})//End Document.Ready

		$(function() {

			$( "#slider" ).slider({
            	range: true,
            	min: 1,
            	max: 31,
            	values: [ 4, 6 ],
            	slide: function( event, ui ) {
            		$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
            	}
        });
        
        $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
            " - $" + $( "#slider-range" ).slider( "values", 1 ) );
    });
  
		</script>
	</head>

	<body>
		<h1>Select a Month</h1>
		<div id="form">
			<form name="month-selector" method="post" action="data.php">
				<select name="monthSelector" id="month">
					<option value="1">January</option>
					<option value="2">February</option>
					<option value="3">March</option>
					<option value="4">April</option>
					<option value="5">May</option>
					<option value="6">June</option>
					<option value="7">July</option>
					<option value="8">August</option>
					<option value="9">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select>
			</form>	
			<div id="day-selector">
				<form>
					<ul>
						<li>
							<input type="radio" name="weekday" value="Monday">Monday</input>
						</li>
						<li>
							<input type="radio" name="weekday" value="Tuesday">Tuesday</input>
						</li>
						<li>
							<input type="radio" name="weekday" value="Wednesday">Wednesday</input>
						</li>
						<li>
							<input type="radio" name="weekday" value="Thursday">Thursday</input>
						</li>
						<li>
							<input type="radio" name="weekday" value="Friday">Friday</input>
						</li>
						<li>
							<input type="radio" name="weekday" value="Saturday">Saturday</input>
						</li>
						<li>
							<input type="radio" name="weekday" value="Sunday">Sunday</input>
						</li>
					</ul>
				</form>
			</div>
		</div>
		<div id="chart"></div>

<!-- 		<div id="slider-container">
			<div id="slider"></div>
		</div> -->
	</body>
</html>



