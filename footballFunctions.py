import requests
from bs4 import BeautifulSoup
era = 'blank'
prefix = 'https://en.wikipedia.org'
def AddTeam( player, team, era ):
    if(str(team).find('Youth career') == -1 and str(team).find('Senior career') == -1 and str(team).find('National team') == -1):
        if len(team) == 2:
            player['teams'].append({"type":era, "years":team[0],"team": team[1]})
        elif len(team) == 4:
            if team[0] != "Playing position":
                player['teams'].append({"type":era, "years":team[0],"team": team[1],"appearances":team[2],"goals": team[3]})
    return player

def CreatePlayer(url):
    global era
    pageList = ScrapePlayerPage(url)
    #create empty player object
    player = {"name": "","teams": [], "url": url}
    for x in pageList:
        if(str(x).find('Full name') != -1):
            era = 'blank'
            player['name'] = x[1]
        UpdateEra(x)
        if(era != 'blank'):
            player = AddTeam( player, x, era )
        if player['name'] == "":
            player['name'] = url.replace("https://en.wikipedia.org/wiki/", "")
    return player

def UpdateEra(x):
    global era
    if (str(x).find('Youth career') != -1):
        era = 'youth'
    if (str(x).find('Senior career') != -1):
        era = 'senior'
    if (str(x).find('National team') != -1):
        era = 'national'
    if (str(x).find('Honours') != -1):
        era = 'blank'
    if (str(x).find('Teams managed') != -1):
        era = 'management'
    else:
        era = era
    return era

def ScrapePlayerPage(url):
    #create list to hold info from wikipedia page
    list_of_rows = []
    #loop through list of urls and pull infobox
    response = requests.get(url)
    html = response.content
    soup = BeautifulSoup(html, "html.parser")
    table = soup.find('table', attrs={'class': 'infobox'})
    if hasattr(table, 'findAll'):
        for row in table.findAll('tr'):
            list_of_cells = []
            for label in row.findAll('th'):
                text = label.text
                list_of_cells.append(text)
            for cell in row.findAll('td'):
                text = cell.text
                list_of_cells.append(text)
            list_of_rows.append(list_of_cells)
    return list_of_rows

def ScrapeEnglandPageForURLs(playerHyperLink):
    list_of_player_links = []
    response = requests.get(playerHyperLink)
    html = response.content
    soup = BeautifulSoup(html, "html.parser")
    table = soup.find('table', attrs={'class': 'sortable'})
    for row in table.findAll('tr'):
        for cell in row.findAll('th'):
            span = cell.find('span', attrs={'class': 'fn'})
            if(span != None):
                hyper = span.find('a')
                newLink = prefix + hyper.get('href')
                list_of_player_links.append(newLink)
    return list_of_player_links

def ScrapeLigaPageForURLs(playerHyperLink):
    list_of_player_links = []
    response = requests.get(playerHyperLink)
    html = response.content
    soup = BeautifulSoup(html, "html.parser")
    table = soup.find('table', attrs={'class': 'wikitable'})
    for row in table.findAll('tr'):
        cellTextList = []
        list_of_cells = []
        for cell in row.findAll('td'):
            text = cell.text
            cellTextList.append(text)
            list_of_cells.append(cell)
        if(len(cellTextList) > 0):
            hyper = list_of_cells[0].find('a')
            newLink = prefix + hyper.get('href')
            list_of_player_links.append(newLink)
    return list_of_player_links

def ScrapeMostCappedForURLs(playerHyperLink):
    list_of_player_links = []
    response = requests.get(playerHyperLink)
    html = response.content
    soup = BeautifulSoup(html, "html.parser")
    #table = soup.find('table', attrs={'class': 'sortable wikitable jquery-tablesorter'})
    tables = soup.findAll('table', attrs={'class': 'sortable'})
    i = 0
    for table in tables:
        count = 0
        for row in table.findAll('tr'):
            count+=1
        if( i == 0 or i == 3):
            for row in table.findAll('tr'):
                cellTextList = []
                list_of_cells = []
                col = 0
                for cell in row.findAll('td'):
                    if((i == 0 and col == 1) or (i == 3 and col == 0)):
                        text = cell.text
                        cellTextList.append(text)
                        list_of_cells.append(cell)
                        if(len(cellTextList) > 0):
                            hyper = list_of_cells[0].find('a')
                            newLink = prefix + hyper.get('href')
                            list_of_player_links.append(newLink)
                    col+=1
        i+=1
    return list_of_player_links

def ScrapeWorldCupForURLs(PlayerListLink):
    #visit hyperlink for each world cup, retrieve list of urls for all players, then visit next page and do the same
    # a class = CategoryTreeLabel
    list_of_player_links = []
    response = requests.get(PlayerListLink)
    html = response.content
    soup = BeautifulSoup(html, "html.parser")
    cups = soup.findAll('a', attrs={'class': 'CategoryTreeLabel'})
    for cup in cups:
        #create list links for each world cup player for given year
        cupPlayersLink = prefix + cup.get('href')
        #get data for links from first page
        playerlistResponse = requests.get(cupPlayersLink)
        pSoup =  BeautifulSoup(playerlistResponse.content, "html.parser")
        mergeList = GetWorldCupPlayerURLs(pSoup)
        list_of_player_links = list_of_player_links + mergeList
        next_page = 'true'
        while next_page == 'true':
            if(len(pSoup.findAll("a", string="next page")) > 0):
                next_page = 'true'
                nextpageLink = pSoup.findAll('a', string="next page")[0].get('href')
                nextPageResponse = requests.get(prefix+nextpageLink)
                pSoup = BeautifulSoup(nextPageResponse.content, "html.parser")
                mergeList = GetWorldCupPlayerURLs(pSoup)
                list_of_player_links = list_of_player_links + mergeList
            else:
                next_page = 'false'
    return list_of_player_links

def GetWorldCupPlayerURLs(pSoup):
    newLinkList = []
    #create list of player links
    playerLinksContainers = pSoup.findAll('div', attrs={'class': 'mw-category-group'})
    for container in playerLinksContainers:
        items = container.findAll('a')
        for item in items:
            if(item.get('href') is not None):
                pLink = prefix + item.get('href')
                newLinkList.append(pLink)
    return newLinkList
