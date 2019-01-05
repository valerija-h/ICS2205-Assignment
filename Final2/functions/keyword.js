function createKeyCloud(keywords){
    //Turn the KeyWords Into A Frequency List
    frequency_list = [];
    for(var i = 0; i < keywords.length; i++) {
        frequency_list.push({
            text: keywords[i].word,
            size: keywords[i].weight + "2"
        })
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
        d3.select(".word-graph").append("svg")
            .attr("width", w)
            .attr("height", h)
            .append("g")
            .attr("transform", "translate(450,300)")
            .selectAll("text")
            .data(words)
            .enter().append("text")
            .style("font-size", function (d) { return d.size + "px"; })
            .style("font-family", "Impact")
            .style("fill", function (d, i) { return fill(i); })
            .attr("text-anchor", "middle")
            .attr("transform", function (d) {
                return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
            })
            .text(function (d) { return d.text; });
    }
}