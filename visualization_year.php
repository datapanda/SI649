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

		#dateSelectorForm {


		}

		#dateSelectorForm #year{

			display: inline-block;
			float: left;
		}

		#dateSelectorForm #month-form {

			display: inline-block;
			float: right;
		}

		#sort ul li {

			list-style: none;
		}

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
		.bars2 {

			background-color: black;
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
		// Global Variables
		var date;
		var dataReturned;
		var ordering;
		var month;
		var year;
		var monday, tuesday, wednesday, thursday, friday, saturday, sunday;

		d3.json("http://localhost:8888/SI649/sunday.php/", function(json){
			sunday = json;
			console.log(sunday)

				d3.json("http://localhost:8888/SI649/saturday.php/", function(json){
					saturday = json;
					console.log(saturday)

						d3.json("http://localhost:8888/SI649/friday.php/", function(json){
							friday = json;
							console.log(friday)

								d3.json("http://localhost:8888/SI649/thursday.php/", function(json){
									thursday = json;
									console.log(thursday)

										d3.json("http://localhost:8888/SI649/wednesday.php/", function(json){
											wednesday = json;
											console.log(wednesday)

												d3.json("http://localhost:8888/SI649/tuesday.php/", function(json){
													tuesday = json;
													console.log(tuesday)
												
													d3.json("http://localhost:8888/SI649/monday.php/", function(json){
														monday = json;
														console.log(monday)

														//drawBars(date, days)

														d3.json("http://localhost:8888/SI649/data.php/", function(json){
															date = json
													 		console.log(date)
													 		
													 		drawBars(date, monday, tuesday, wednesday, thursday, friday, saturday, sunday)
												
														});//End Year-Month Data
											 		});//End Monday Data		
											 	});//End Tuesday Data
											});//End Wednesday Data
										});//End Thursday Data
									});//End Friday Data
								});//End Saturday Data
							});//End Sunday Data

		//Function to draw the bar graphs
		function drawBars(data, monday, tuesday, wednesday, thursday, friday, saturday, sunday){

			max =  Math.max.apply(Math,data.map(function(d){return d.convoCount;}))

			//Setup SVG Canvas Constants
			var barWidth = 40;
			var width = (data.length * barWidth + 20);
			var height = (max + 20);
			var margin = { top: 20, right: 20, bottom: 500, left: 40 };

			var yRange = 500;

			function drawCanvas() {


				var chart = 	d3.select("body")
									.append("svg")
										.attr("class","chart")
										.attr("width",width + 100)
										.attr("height",height + margin.bottom);

				//Setup the Scales for the Data
					var x0 = d3.scale.ordinal()
	   						.rangeRoundBands([0, width], .1);

					var x1 = d3.scale.ordinal();

				var yScale = 		d3.scale.linear()
										.domain([max, -20]) //Changed to -20 in order to help the user show the 1 or the 2
										.range([10, yRange]);
				
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
					.attr("height",yRange)
					.attr("transform", "translate(50," + (yRange - 19) + ")")
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
					.attr("height",yRange)
					.attr("transform", "translate(50,-10)")
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
							.attr("height", function(d) { return yRange - yScale(d.convoCount)});

				//Draw Monday Bars
				var bars2 = chart.selectAll("rect2").data(monday);

					
				// var mondayPos = data.function(d) { return day}
				// 	console.log(monday)
					
					bars2.enter()
						.append("rect")
							.attr("title",function(d) { return d.monday + " Monday" })
							.attr("class","rect2")
							.attr("width", barWidth)//x1.rangeBands())//barWidth)//x1.rangeBand())
							.attr("x", function(d) { return x0(d.username) + 50 + "px"})
							.attr("y", function(d) { return yScale(d.monday) - 20 + "px" })
							.attr("height", function(d) { return yRange - yScale(d.monday)})
							.style("fill", "crimson");
				
				//Draw Tuesday Bars
				var bars3 = chart.selectAll("rect3").data(tuesday);

				console.log(d3.selectAll(".rect2").each( function(d, i){ d3.select(this).attr("y") }))
				// d3.selectAll(".rect2").each( function(d, i){//Grab all of the y variables for the previous code
 					 
 			// 		 //if(d.someId == targetId){
    				
    // 					console.log(d3.select(this).attr("y"))
				// 	})			

				//var test343 = d3.selectAll(".rect2").each( function(d) { return d3.select(this).attr("y") })		
					//console.log("This is a number" + test343)
					console.log(tuesday)
					
					bars3.enter()
						.append("rect")
							.attr("title",function(d) { return d.tuesday + " Tuesday" })
							.attr("class","rect3")
							.attr("width", barWidth)//x1.rangeBands())//barWidth)//x1.rangeBand())
							.attr("x", function(d) { return x0(d.username) + 50 + "px"})
							.attr("y", function(d) { return yScale(d.tuesday) - 20 + "px" })
							.attr("height", function(d) { return yRange - yScale(d.tuesday)})
							.style("fill", "green");

				//Draw Wednesday Bars
				var bars4 = chart.selectAll("rect4").data(wednesday);

					bars4.enter()
						.append("rect")
							.attr("title",function(d) { return d.wednesday + " Wednesday" })
							.attr("class","rect4")
							.attr("width", barWidth)//x1.rangeBands())//barWidth)//x1.rangeBand())
							.attr("x", function(d) { return x0(d.username) + 50 + "px"})
							.attr("y", function(d) { return yScale(d.wednesday) - 20 + "px" })
							.attr("height", function(d) { return yRange - yScale(d.wednesday)})
							.style("fill", "orange");

				//Draw Thursday Bars
				var bars5 = chart.selectAll("rect5").data(thursday);

					bars5.enter()
						.append("rect")
							.attr("title",function(d) { return d.thursday + " Thursday" })
							.attr("class","rect5")
							.attr("width", barWidth)//x1.rangeBands())//barWidth)//x1.rangeBand())
							.attr("x", function(d) { return x0(d.username) + 50 + "px"})
							.attr("y", function(d) { return yScale(d.thursday) - 20 + "px" })
							.attr("height", function(d) { return yRange - yScale(d.thursday)})
							.style("fill", "pink");

				//Draw Friday Bars
				var bars6 = chart.selectAll("rect6").data(friday);

					bars6.enter()
						.append("rect")
							.attr("title",function(d) { return d.friday + " Friday" })
							.attr("class","rect6")
							.attr("width", barWidth)//x1.rangeBands())//barWidth)//x1.rangeBand())
							.attr("x", function(d) { return x0(d.username) + 50 + "px"})
							.attr("y", function(d) { return yScale(d.friday) - 20 + "px" })
							.attr("height", function(d) { return yRange - yScale(d.friday)})
							.style("fill", "firebrick");

				//Draw Saturday Bars
				var bars7 = chart.selectAll("rect7").data(saturday);

					bars7.enter()
						.append("rect")
							.attr("title",function(d) { return d.saturday + " Saturday" })
							.attr("class","rect7")
							.attr("width", barWidth)//x1.rangeBands())//barWidth)//x1.rangeBand())
							.attr("x", function(d) { return x0(d.username) + 50 + "px"})
							.attr("y", function(d) { return yScale(d.saturday) - 20 + "px" })
							.attr("height", function(d) { return yRange - yScale(d.saturday)})
							.style("fill", "salmon");

				//Draw Sunday Bars
				var bars8 = chart.selectAll("rect8").data(sunday);

					bars8.enter()
						.append("rect")
							.attr("title",function(d) { return d.sunday + " Sunday" })
							.attr("class","rect8")
							.attr("width", barWidth)//x1.rangeBands())//barWidth)//x1.rangeBand())
							.attr("x", function(d) { return x0(d.username) + 50 + "px"})
							.attr("y", function(d) { return yScale(d.sunday) - 20 + "px" })
							.attr("height", function(d) { return yRange - yScale(d.sunday)})
							.style("fill", "yellow");

			
					$("input[name='sort']").on("change",function(){
						//console.log("test")
						ordering = $(this).val();

						if(ordering == 1){

							console.log("change to ascending")
							changeOrder()
						} else if(ordering == 2){

							console.log("change to decending")
							changeOrder2()
						}
						//console.log(ordering)
					});	
					
					function changeOrder (){

						d3.select("input").each(change);
						//d3.select("input").property("checked", true).each(change);

					}

					function changeOrder2 (){

						d3.select("input").each(change2);
						//d3.select("input").property("checked", true).each(change2);


					}
									  		
			  		function change() { //Ascending

			  			//clearTimeout(sortTimeout);

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
					function change2(){ // Descending Function

						 var x02 = x0.domain(data.sort(this.checked

					    	? function(a, b) { return  a.convoCount - b.convoCount; }
					        : function(a, b) { return d3.descending(b.convoCount,a.convoCount);

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
			}

			var canvasSelect = d3.selectAll("svg")
			console.log(canvasSelect[0].length)
			//Setup the SVG Canvas
			
			if(canvasSelect[0].length == 0){

				drawCanvas()
				//console.log("no-svg")
			} else {

				canvasSelect.remove();
				drawCanvas()
				//console.log("svg")

			}
			
		} // End function drawBars
		
		// //Begin jQuery AJAX Call to Update JSON Data
		$(document).ready(function(){

			$("#year").on("change",function(){

					year = $(this).val();
					// console.log(year);


					$.ajax({

					type: "POST",
					url: "data.php",
					dataType: "JSON",
					data: {

						month: month,
						year: year

					},
					async: true,

					success: function(Res)	{
						//Mess with code here

						dataReturned = Res;
						console.log(dataReturned)
						drawBars(dataReturned, monday, tuesday, wednesday, thursday, friday, saturday, sunday); // This works but doesn't do the enter/exit correct.y
						

					},
					error: function() {
						console.log("Failure");
					}
				})//End ajax request
			})

			$("#month").on("change",function(){

					month = $(this).val();
					// console.log(month);


					$.ajax({

					type: "POST",
					url: "data.php",
					dataType: "JSON",
					data: {

						month: month,
						year: year

					},
					async: true,

					success: function(Res)	{
						//Mess with code here


						dataReturned = Res;
						console.log(dataReturned)
						drawBars(dataReturned, monday, tuesday, wednesday, thursday, friday, saturday, sunday); // This works but doesn't do the enter/exit correct.y
						$("input[name='sort']").removeAttr("checked");
				
					},
					error: function() {
						console.log("Failure");
					}
				})//End ajax request			
			})
		});
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
		<div id="intro">

			<p>Welcome to the Conversation Explorer</p>
			<p>Select a year and a month to see the number of conversations I had with different people. </p>

		</div>
		<div id="dateSelectorForm">
			<form name ="datePicker" method="post" action="data.php">
			<h1>Select a Year</h1>
			<div id="year-form">
					<select name="yearSelector" id="year">
						<option value="2008" >2008</option>
						<option value="2009" selected="selected">2009</option>
					</select>
			</div>
			<h1>Select a Month</h1>
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
			<div id="sort">
				<ul>
					<li>
						<input type="radio" name="sort" value="1">Ascending</input>
					</li>
					<li>
						<input type="radio" name="sort" value="2">Descending</input>
					</li>
				</ul>
			</div>
			</form>	
		</div>
		<div id="chart"></div>

<!-- 		<div id="slider-container">
			<div id="slider"></div>
		</div> -->
	</body>
</html>



