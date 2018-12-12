
    var frequency_list = [{"text":"Sushi","size":130},{"text":"roll","size":80},{"text":"service","size":70},{"text":"ayce","size":70},{"text":"vegas","size":70},{"text":"fish","size":65},{"text":"order","size":65},{"text":"salad","size":60},{"text":"quality","size":57},{"text":"cream","size":65},{"text":"everything","size":55},{"text":"seafood","size":55},{"text":"rice","size":55},{"text":"spicy","size":50},{"text":"sauce","size":45},{"text":"salmon","size":40},{"text":"tuna","size":40},{"text":"appetizer","size":35},{"text":"sashimi","size":35},{"text":"stars","size":30},{"text":"mochi","size":25},{"text":"spot","size":25},{"text":"shrimp","size":20},{"text":"crab","size":20},{"text":"party","size":15},{"text":"side","size":15},{"text":"dessert","size":15},{"text":"dream","size":15},{"text":"tempura","size":15},{"text":"shell","size":15},{"text":"eel","size":15}];

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