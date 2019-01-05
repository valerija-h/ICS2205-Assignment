<?php include 'header.php' ?>

    <!-- Node Graph -->
    <div class="node-graph">
        <div class="text-container">
            <h2>Users Node Graph</h2>
            <p>The follow graph displays a network consisting of email users and the emails shared between them. Each node represents a person, and their edges represent their email conversations with another person. Each edge stores the keywords used in the email conversations. For best experience set zoom of the page to default (100%).</p>
            <p><b>How to use the node graph:</b></p>
            <ul>
                <li>Zoom in and out of the graph by using the mouse scroll.</li>
                <li>Move around the graph by clicking and dragging on the whitespace.</li>
                <li>Click on an edge to open up a keyword cloud.</li>
                <li>Click on a node to see the edges it is connected to.</li>
                <li>Click and drag on a node to move it around the graph.</li>
            </ul>
        </div>
        <div id="graphic">

        </div>
    </div>
	<div id="box">
		<p id="nodeAmount"></p>
		<p id="edgesAmount"></p>
		<p id="highestPageRank"></p>
		<p id="activeNode"></p>
	</div>
    <!-- Word Graph -->
    <div id="modal-box">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="word-graph">

            </div>
        </div>
    </div>
    <!-- Loading Box -->
    <div id="loading-box">
        <div class="loading-content">
            <div>
                <p><b>Please wait...</b></p>
                <p>The graph is organzing the position of the nodes..</p>
                <div class="loader"></div>
            </div>
        </div>
    </div>

<?php include 'footer.php' ?>