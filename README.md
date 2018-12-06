# footballScraper
pulling data for football bfs

This project create a web page which is an example of a Breadth-First Search, showing clubs that link various professional soccer players.  

The python script, scrape.py importing functions from footballFunctions.py, scrapes wikipedia pages containing player info.

The script stringFixes.py is run on the text file resulting from the execution of scrape.py in order to reformat the text file into a consistent JSON format that can be decoded into a php object.

bfs.php reads the resulting txt file(englishWorldCupLaLigaCapped.txt) and creates a php object to display a dropdown box containing all player names, and this is decoded into a Javascript object to create a graph of nodes which can be searched to find paths connecting players.

The page load time is very long because the graphn being constructed contains over 125,000 nodes.  This many be much better suited for running on a node server, and that will be the next update. There is also a considerable time delay is for toggling display attribute for the dropdown box containing almost 20,000 input tags which represent each player.

bfsImage.php is setup so that the function which creates the paths connecting players can submit and ajax request and return a link to the wikipedia page for each player listed along the path along with a url to and image to be displayed for that player.

This information is available as a part of the original JSON in the text file which is read to create the graph, but adding a url attribute and an image attribute to each node causes the size of information in memory to exceed what the browser can handle, so requesting this information for a specific node on the path during calculation is the only way that has been exploring when dealing with so many players, besides possibly a tiny url rather than the full destination for wikipedia pages and images, but this is outside of the scope of tools I am looking to develop in this situation.

