<?php
if($fh = fopen('englishWorldCupLaLigaCapped.txt', 'r')){
    $players = array();
    while(!feof($fh)){
        $line = fgets($fh);
        if(strlen($line) > 1){
            $lineArray = json_decode($line, true);
            $players[$lineArray['name']] = $lineArray;
        }
    }
    fclose($fh);
}
$graph = array();
//read playerinfo into php object
//create teams array from all team listed for each player
$teamsAndPlayers = array();
$teamNames = array();//has name and players array
$examinedPlayers = array();
foreach($players as $player){
    $playersTeams = [];
    $name = $player['name'];
    //$image = $player['image'];
    $playerInfo = [];
    $playerInfo['name'] = $name;
    //$playerInfo['url'] = $url;
    //$playerInfo['image'] = $image;
    if(in_array($name, $examinedPlayers)){
            //do nothing at all
    }else{
        $examinedPlayers[] = $name;
        foreach($player['teams'] as $team){
            $teamName = $team['team'].'-'.$team['type'];
            if(in_array($teamName, $playersTeams) || ($team['team']== "") || $team['years'] == "Playing position"){
             //do nothing because this team has been updated for this player or totals is showing up or position info
            }else{
                $playersTeams[] = $teamName;
                if(in_array($teamName, $teamNames)){
                    //add player to the roster for that team
                    $teamsAndPlayers[$teamName]['roster'][$name] = $playerInfo;;
                }else{
                    //add team to list of teams and add player to the roster
                    $teamNames[] = $teamName;
                    $teamsAndPlayers[$teamName] = array();
                    $teamsAndPlayers[$teamName]['name'] = $teamName;
                    $teamsAndPlayers[$teamName]['roster'][$name] = $playerInfo;
                }
            }
        }
    }
}
?>
<script>
    //interface functions defined here
    function myFunction() {
        document.getElementById("myDropdown").classList.toggle("show");
    }

    function startFunction() {
        var startInput, startFilter, a, i;
        startInput = document.getElementById("startInput");
        startFilter = startInput.value.toUpperCase();
        endInput = document.getElementById("endInput");
        endFilter = endInput.value.toUpperCase();
        a = document.getElementsByClassName("startList");
        for (i = 0; i < a.length; i++) {
            if (a[i].innerHTML.toUpperCase().indexOf(startFilter) > -1) {
                a[i].style.display = "";
            } else {
                a[i].style.display = "none";
            }
        }
    }

    function endFunction() {
        var endInput, endFilter, a, i;
        endInput = document.getElementById("endInput");
        endFilter = endInput.value.toUpperCase();
        a = document.getElementsByClassName("endList");
        for (i = 0; i < a.length; i++) {
            if (a[i].innerHTML.toUpperCase().indexOf(endFilter) > -1) {
                a[i].style.display = "";
            } else {
                a[i].style.display = "none";
            }
        }
    }

        //data functions defined below this line**********************

    function Node(value, type){
        this.value = value;
        this.edges = [];
        this.type = type;
        this.searched = false;
        this.parent = null;
    }

    Node.prototype.AddEdge = function(neighbor){
        this.edges.push(neighbor);
        //Both directions
        neighbor.edges.push(this);
    }

    function Graph(){
        this.nodes = [];
        this.graph = {};
        this.end = null;
        this.start = null;
    }
    //Loading data here***************
    var teams = <?php echo json_encode($teamsAndPlayers) ?>;    
    graph = new Graph();

    Graph.prototype.AddNode = function(n){
        //Node into array
        this.nodes.push(n);
        var title = n.value;
        //Node into "object"
        this.graph[title] = n;
    }

    Graph.prototype.GetNode = function(player){
        var n = this.graph[player];
        return n;
    }

    Graph.prototype.SetStart= function(player){
        this.start = this.graph[player];
        return this.start;
    }

    Graph.prototype.SetEnd= function(player){
        this.end = this.graph[player];
        return this.end;
    }

    Graph.prototype.reset = function(){
        for(var i = 0; i < this.nodes.length; i++){
            this.nodes[i].searched = false;
            this.nodes[i].parent = null;
        }
    }

    function UpdateStart(player){
        document.getElementById('startInput').value = player;
        SearchNodes();
    }

    function UpdateEnd(player){
        //this.end = this.graph.SetEnd(graph[player]);
        document.getElementById('endInput').value = player;
        SearchNodes();
    }

    //setup starts here*************************
    for(var teamName in teams){
        var team = teams[teamName].name;
        var roster = teams[teamName].roster;
        var teamNode = new Node(team, 'team');
        graph.AddNode(teamNode);

        for(var playerInfo in roster){
            var player = roster[playerInfo]['name'];
            var playerNode = graph.GetNode(player);
            if(playerNode == undefined){
                playerNode = new Node(player, 'player');
            }
            graph.AddNode(playerNode);
            playerNode.AddEdge(teamNode);
        }
    }

    function SearchNodes(){
        myFunction();
        graph.reset();
        var start = graph.SetStart(document.getElementById('startInput').value);
        var end = graph.SetEnd(document.getElementById('endInput').value);
        var queue = [];

        start.searched = true;
        queue.push(start);

        while(queue.length > 0){
            var current = queue.shift();
            if(current == end){
                console.log("Found "+current.value);
                break;
            }
            var edges = current.edges;
            for(var i = 0; i < edges.length; i++){
                var neighbor = edges[i];
                if(!neighbor.searched){
                    neighbor.searched = true;
                    neighbor.parent = current;
                    queue.push(neighbor);
                }
            }
        }
        if(queue == 0){
            alert('no connection between '+start.value+' and '+end.value);
        }

        var path = [];
        path.push(end)
        var next = end.parent;
        while(next != null){
            path.push(next);
            next = next.parent;
        }  

        var txt = '';
        for( var i = path.length-1; i >= 0; i--){
            var n = path[i];
            if(n.type =='player'){
                txt += n.value;
            }else{
                txt+=n.value;
            }
            if(i != 0){
                if(n.type == 'player'){
                    if(i != path.length-1){
                        txt+=" who ";
                    }
                    txt+=" played for<br/>";
                }else{
                    txt+=", like <br/>";
                }
            }
        }
        document.getElementById('path').innerHTML = txt;
    }
</script>
<html>
    <head>
        <title>Degrees of Messi</title>
        <link rel="shortcut icon" href="./images/ise.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
        .dropbtn {
            background-color: #4CAF50;
            color: white;
            padding: 16px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .dropbtn:hover, .dropbtn:focus {
            background-color: #3e8e41;
        }

        #myInput {
            border-box: box-sizing;
            background-image: url('searchicon.png');
            background-position: 14px 12px;
            background-repeat: no-repeat;
            font-size: 16px;
            padding: 14px 20px 12px 45px;
            border: none;
            border-bottom: 1px solid #ddd;
        }

        #myInput:focus {outline: 3px solid #ddd;}

        .dropdown {
            display: inline-block;
            clear:both;
        }

        .dropdown-content {
            display: none;
            background-color: #f6f6f6;
            min-width: 230px;
            overflow: auto;
            border: 1px solid #ddd;
            z-index: 1;
        }

        .dropdown-content div {
            color: black;
            padding: 12px 16px;
            display: block;
        }

        .startList:hover {background-color: #ddd;}

        .endList:hover {background-color: #ddd;}


        .show {display: block;}
        </style>
    </head>
    <body>
    <center>
        <h1>Degrees of Messi</h1>
    </center>
        <p>
            This is an example of a  <a href="https://en.wikipedia.org/wiki/Breadth-first_search">Breadth-First Search</a> to show clubs that link professional soccer players.<br/>
            A python script was run to scrape wikipedia pages containing links to the following list of <?php echo count($examinedPlayers); ?> players:
        </p>
            <ul>
                <a href="https://en.wikipedia.org/wiki/Category:FIFA_World_Cup_players"><li>All World Cup players</li></a>
                <a href="https://en.wikipedia.org/wiki/Category:Bundesliga_players"><li>Bundesliga players</li></a>
                <a href="https://en.wikipedia.org/wiki/List_of_England_international_footballers"><li>English National Team players</li></a>
                <a href="https://en.wikipedia.org/wiki/Category:Premier_League_players"><li>English Premier League players</li></a>
                <a href="https://en.wikipedia.org/wiki/List_of_La_Liga_players"><li>La Liga players</li></a>
                <a href="https://en.wikipedia.org/wiki/List_of_Ligue_1_players"><li>Ligue 1 players with 100 or more appearances</li></a>
                <a href="https://en.wikipedia.org/wiki/List_of_footballers_with_100_or_more_caps"><li>Players with 100 appearances for their Senior National team</li></a>
                <li><a href="https://en.wikipedia.org/wiki/List_of_Serie_A_players">top 100 Serie A players</a></li>
            </ul><br/>
            <p>
                Click on the button to open the dropdown menu, and use the input field to search for players to connect.
            </p>
        <div class="dropdown">
        <button onclick="myFunction()" class="dropbtn">Dropdown</button>
          <div id="myDropdown" class="dropdown-content">
            <div style="float:left; width:40%;">
                <input type="text" placeholder="Search.." id="startInput" onkeyup="startFunction()" value="Cristiano Ronaldo dos Santos Aveiro">
<?php
                foreach($examinedPlayers as $player){
                    //$playerName = str_replace('_', ' ',$player);
?>
                <div class="startList" onclick="UpdateStart('<?php echo $player; ?>')"><?php echo $player; ?></div>
<?php
                }
?>
            </div>
            <div style="float:left; width:40%;">
                <input type="text" placeholder="Search.." id="endInput" onkeyup="endFunction()" value="Lionel Andres Messi Cuccittini">
                <?php
                foreach($examinedPlayers as $player){
                    //$playerName = str_replace('_', ' ',$player);
?>              
                <div class="endList" onclick="UpdateEnd('<?php echo $player; ?>')"><?php echo $player; ?></div>
<?php
                }
?>
            </div>
          </div>
        </div>
        <div id="graphArea">
            <center>
                <p id="path">   
                </p>
            </center>
        </div>
    </body>
</html>