 <script>
			  function getParameterByName(name, url) {
				  if (!url) url = window.location.href;
				  name = name.replace(/[\[\]]/g, '\\$&');
				  var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
				  results = regex.exec(url);
				  if (!results) return null;
				  if (!results[2]) return '';
				  return decodeURIComponent(results[2].replace(/\+/g, ' '));

              }
			  
			  console.log()

              const param=getParameterByName('words',window.location.href);
			  const urlParams = new URLSearchParams(window.location.search);
			  
				let myParam = urlParams.get('words');

				const check = myParam.slice(-2);

				if(check === "}]"){

				  var keywords=JSON.parse(param);
			  
					  // as attributes has text: and size:
					var frequency_list = [];
					for(var x = 0; x < keywords.length; x++){
						frequency_list.push({
							text: keywords[x].word,
							size: keywords[x].weight + "2"
						});
					}

				}else{

					var n = myParam.length;
					var status = true;

					while(status){
						if(myParam.charAt(n) === '}'){
							myParam = myParam + "]";
							status = false;
						}
						else{
							status = true;
							n--;
						}
					}
				
				    var keywords=JSON.parse(myParam);
			  
					  // as attributes has text: and size:
					var frequency_list = [];
					for(var x = 0; x < keywords.length; x++){
						frequency_list.push({
							text: keywords[x].word,
							size: keywords[x].weight + "2"
						});
					}
				
				}
				
					var w = 960,
						h = 600;

					var fill = d3.scale.category20b();

					d3.layout.cloud().size([w, h])
							.words(frequency_list)
							.rotate(0)
							.padding(5)
							.fontSize(function(d) { return d.size; })
							.on("end", draw)
							.start();

					function draw(words, bounds) {
					d3.select("body").append("svg")
						.attr("width", w)
						.attr("height", h)
					  .append("g")
						.attr("transform", "translate(450,300)")
					  .selectAll("text")
						.data(words)
					  .enter().append("text")
						.style("font-size", function(d) { return d.size + "px"; })
						.style("font-family", "Impact")
						.style("fill", function(d, i) { return fill(i); })
						.attr("text-anchor", "middle")
						.attr("transform", function(d) {
						  return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
						})
						.text(function(d) { return d.text; })
				  }
			  </script>