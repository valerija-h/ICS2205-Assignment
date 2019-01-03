<script>
function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
	var myParam = getCookie('myCookie');
	console.log(myParam);
	//var url = new URL(window.location.href);
	//let myParam = url.searchParams.get("words");		 
			  
	//let myParam = urlParams.get('words');

	const check = myParam.slice(-1);

	if(check === ']'){

		var keywords=JSON.parse(myParam);
			  
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
				myParam = myParam.slice(0,n);
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