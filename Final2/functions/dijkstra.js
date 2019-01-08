//dijkstra solve graph starting at s
function solve(graph, s) {
    var solutions = {};
    solutions[s] = [];
    solutions[s].dist = 0;

    while (true) {
        var parent = null;
        var nearest = null;
        var dist = Infinity;

        //for each existing solution
        for (var n in solutions) {
            if (!solutions[n])
                continue
            var ndist = solutions[n].dist;
            var adj = graph[n];
            //for each of its adjacent nodes...
            for (var a in adj) {
                //without a solution already...
                if (solutions[a])
                    continue;
                //choose nearest node with lowest *total* cost
                var d = adj[a] + ndist;
                if (d < dist) {
                    //reference parent
                    parent = solutions[n];
                    nearest = a;
                    dist = d;
                }
            }
        }

        //no more solutions
        if (dist === Infinity) {
            break;
        }

        //extend parent's solution path
        solutions[nearest] = parent.concat(nearest);
        //extend parent's cost
        solutions[nearest].dist = dist;
    }

    return solutions;
}




// From '10' to
//  -> 2: [7, 5, 4, 2]   (dist:4)
//  -> 3: [7, 5, 4, 3]   (dist:4)
//  -> 4: [7, 5, 4]   (dist:3)
//  -> 5: [7, 5]   (dist:2)
//  -> 6: [7, 5, 4, 3, 6]   (dist:5)
//  -> 7: [7]   (dist:1)
//  -> 8: [7, 5, 4, 8]   (dist:4)
//  -> 9: [7, 5, 4, 3, 13, 14, 9]   (dist:7)
//  -> 10: []   (dist:0)
//  -> 11: [7, 5, 11]   (dist:3)
//  -> 12: [7, 5, 11, 12]   (dist:4)
//  -> 13: [7, 5, 4, 3, 13]   (dist:5)
//  -> 14: [7, 5, 4, 3, 13, 14]   (dist:6)
//  -> 15: [7, 5, 4, 3, 6, 15]   (dist:6)
//  -> R: [7, 5, 4, 2, R]   (dist:5)