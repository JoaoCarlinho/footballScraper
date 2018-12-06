from footballFunctions import CreatePlayer, ScrapeEnglandPageForURLs, ScrapeMostCappedForURLs, ScrapeEnglandPageForURLs, ScrapeWorldCupForURLs, ScrapeLigaPageForURLs
#list of pages with multiple player links
#urlPages = [https://en.wikipedia.org/wiki/List_of_top_international_association_football_goal_scorers_by_country',  'https://en.wikipedia.org/wiki/List_of_men%27s_footballers_with_500_or_more_goals', 'https://en.wikipedia.org/wiki/List_of_one-club_men_in_association_football', 'https://en.wikipedia.org/wiki/List_of_most_expensive_association_football_transfers', 'https://en.wikipedia.org/wiki/List_of_European_Cup_and_UEFA_Champions_League_winning_players', 'https://en.wikipedia.org/wiki/List_of_UEFA_Champions_League_hat-tricks']
urlPages = ['https://en.wikipedia.org/wiki/List_of_England_international_footballers', 'https://en.wikipedia.org/wiki/List_of_La_Liga_players', 'https://en.wikipedia.org/wiki/List_of_footballers_with_100_or_more_caps', 'https://en.wikipedia.org/wiki/Category:FIFA_World_Cup_players']
#need to add these pages to crawl specific links recursively, then look for players ['https://en.wikipedia.org/wiki/Lists_of_association_football_players', 'https://en.wikipedia.org/wiki/Category:Premier_League_players', 'https://en.wikipedia.org/wiki/List_of_Ligue_1_players', 'https://en.wikipedia.org/wiki/List_of_footballers_in_England_by_number_of_league_appearances', 'https://en.wikipedia.org/wiki/List_of_Olympic_medalists_in_football#Men', 'https://en.wikipedia.org/wiki/Category:FIFA_World_Cup_players', 'https://en.wikipedia.org/wiki/List_of_current_Major_League_Soccer_players_with_national_team_caps#Listed_by_MLS_team', 'https://en.wikipedia.org/wiki/List_of_Serie_A_players', 'https://en.wikipedia.org/wiki/Category:Bundesliga_players']
#create empty object to hold list of players
players = []
#get list of urls from wikipedia with player info
#playerURLS = ['https://en.wikipedia.org/wiki/Lionel_Messi']
urls = ['https://en.wikipedia.org/wiki/Neymar', 'https://en.wikipedia.org/wiki/Mesut_%C3%96zil', 'https://en.wikipedia.org/wiki/Thibaut_Courtois','https://en.wikipedia.org/wiki/Juan_Cuadrado', 'https://en.wikipedia.org/wiki/Philippe_Coutinho', 'https://en.wikipedia.org/wiki/Ousmane_Demb%C3%A9l%C3%A9']
newUrls = []
pI = 0
for page in urlPages:
    if(page == 'https://en.wikipedia.org/wiki/List_of_England_international_footballers'):
        newUrls = ScrapeEnglandPageForURLs(page)
    elif(page == 'https://en.wikipedia.org/wiki/List_of_La_Liga_players'):
        newUrls = ScrapeLigaPageForURLs(page)
    elif(page == 'https://en.wikipedia.org/wiki/List_of_footballers_with_100_or_more_caps'):
        newUrls = ScrapeMostCappedForURLs(page)
    elif(page == 'https://en.wikipedia.org/wiki/Category:FIFA_World_Cup_players'):
        newUrls = ScrapeWorldCupForURLs(page)
    if(newUrls is not None):
        mergedList = urls + newUrls
        urls =  mergedList
#print urls
#read info from wikipedia into list
for link in urls:
    player = CreatePlayer(link)
    if player not in players:
        if (pI == 0):
            f = open("demofile.txt", "w")
            f.write(str(player)+'\n'+'\n')
        else:
            f = open("demofile.txt", "a")
            f.write(str(player)+'\n'+'\n')
        pI+=1
        #print "\n" + "\n"
        #add latest player to list
        players.append(player)
print 'collected data for '+str(len(players))+' players'
#will be writing to file or database
