CREATE DATABASE  IF NOT EXISTS `toevla` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `toevla`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: toevla
-- ------------------------------------------------------
-- Server version	5.1.50-community

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `user_connection`
--

DROP TABLE IF EXISTS `user_connection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_connection` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `provider` varchar(32) NOT NULL,
  `connectionId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `connectionData` blob NOT NULL,
  `connectionTokens` blob NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `connectionId_provider_UNIQUE` (`connectionId`,`provider`),
  KEY `user_connection.userId__user.ID` (`userId`),
  CONSTRAINT `user_connection.userId__user.ID` FOREIGN KEY (`userId`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_connection`
--

LOCK TABLES `user_connection` WRITE;
/*!40000 ALTER TABLE `user_connection` DISABLE KEYS */;
INSERT INTO `user_connection` VALUES (7,15,'ProviderTwitter',256426742,'{\"id\":256426742,\"is_translator\":false,\"profile_background_image_url\":\"http:\\/\\/a0.twimg.com\\/profile_background_images\\/245733671\\/twtbg5.jpg\",\"profile_background_image_url_https\":\"https:\\/\\/si0.twimg.com\\/profile_background_images\\/245733671\\/twtbg5.jpg\",\"friends_count\":103,\"profile_link_color\":\"D02B55\",\"default_profile_image\":false,\"utc_offset\":3600,\"favourites_count\":0,\"name\":\"Jelle Voet\",\"profile_use_background_image\":true,\"id_str\":\"256426742\",\"profile_text_color\":\"3E4415\",\"protected\":false,\"verified\":false,\"lang\":\"nl\",\"statuses_count\":65,\"profile_sidebar_border_color\":\"829D5E\",\"contributors_enabled\":false,\"url\":\"http:\\/\\/www.tomo-design.be\",\"time_zone\":\"Brussels\",\"created_at\":\"Wed Feb 23 09:09:17 +0000 2011\",\"description\":\"Code master at GriN during daytime, allround gamer\\/cook\\/nerd\\/dancer\\/geek\\/human at night.\",\"geo_enabled\":true,\"default_profile\":false,\"notifications\":false,\"profile_background_tile\":false,\"show_all_inline_media\":true,\"profile_image_url_https\":\"https:\\/\\/si0.twimg.com\\/profile_images\\/1266677267\\/avatar_normal.jpg\",\"profile_sidebar_fill_color\":\"99CC33\",\"follow_request_sent\":false,\"profile_image_url\":\"http:\\/\\/a0.twimg.com\\/profile_images\\/1266677267\\/avatar_normal.jpg\",\"following\":false,\"followers_count\":26,\"screen_name\":\"supmagc\",\"location\":\"Belgium\",\"listed_count\":0,\"profile_background_color\":\"352726\"}','{}','2012-04-12 14:13:02');
/*!40000 ALTER TABLE `user_connection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_character`
--

DROP TABLE IF EXISTS `game_character`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_character` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `data` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `user_character.userId__user.ID` (`userId`),
  CONSTRAINT `user_character.userId__user.ID` FOREIGN KEY (`userId`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_character`
--

LOCK TABLES `game_character` WRITE;
/*!40000 ALTER TABLE `game_character` DISABLE KEYS */;
INSERT INTO `game_character` VALUES (4,15,'supmagc','','2012-04-12 14:13:33');
/*!40000 ALTER TABLE `game_character` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_session`
--

DROP TABLE IF EXISTS `game_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_session` (
  `hash` varchar(32) NOT NULL,
  `userId` int(10) unsigned DEFAULT NULL,
  `characterId` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hash`),
  KEY `game_session.userId__user.ID` (`userId`),
  KEY `game_session.characterId__game_character.ID` (`characterId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_session`
--

LOCK TABLES `game_session` WRITE;
/*!40000 ALTER TABLE `game_session` DISABLE KEYS */;
INSERT INTO `game_session` VALUES ('6d2e052578a7407687bfc64b907e66b6',15,4,NULL,'2012-04-12 15:52:53'),('badc3bc7274d3baf77e70d70bc2014ba',9,2,NULL,'2012-04-12 13:41:06'),('fc2582e56476c73e49f909b584fda4cb',14,3,NULL,'2012-04-12 14:12:05');
/*!40000 ALTER TABLE `game_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `festival`
--

DROP TABLE IF EXISTS `festival`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `festival` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) NOT NULL,
  `name` varchar(256) NOT NULL,
  `fmiv` tinyint(4) NOT NULL DEFAULT '0',
  `fiaf` tinyint(4) NOT NULL DEFAULT '0',
  `date_start` date DEFAULT NULL,
  `date_stop` date DEFAULT NULL,
  `website` varchar(128) DEFAULT NULL,
  `location` varchar(128) DEFAULT NULL,
  `twitterName` varchar(256) DEFAULT NULL,
  `twitterHash` varchar(256) DEFAULT NULL,
  `facebook` varchar(256) DEFAULT NULL,
  `youtube` varchar(256) DEFAULT NULL,
  `flickr` varchar(256) DEFAULT NULL,
  `picasa` varchar(256) DEFAULT NULL,
  `genreId` tinyint(4) unsigned DEFAULT NULL,
  `locationTypeId` tinyint(4) unsigned DEFAULT NULL,
  `visitors` int(11) unsigned DEFAULT NULL,
  `description_NL` text,
  `description_EN` text,
  `comments` text,
  `data` text,
  PRIMARY KEY (`ID`),
  KEY `festival.localtionTypeId__fetsival_locationtype.ID` (`locationTypeId`),
  KEY `festival.genreId__festival_genre.ID` (`genreId`),
  CONSTRAINT `festival.genreId__festival_genre.ID` FOREIGN KEY (`genreId`) REFERENCES `festival_genre` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `festival.localtionTypeId__fetsival_locationtype.ID` FOREIGN KEY (`locationTypeId`) REFERENCES `festival_locationtype` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `festival`
--

LOCK TABLES `festival` WRITE;
/*!40000 ALTER TABLE `festival` DISABLE KEYS */;
INSERT INTO `festival` VALUES (1,'0a4c940c29dd34b3214191840b54b9a1','Blues Peer',1,1,'2012-07-13','2012-07-15','http://www.brbf.be/','Peer',NULL,NULL,NULL,NULL,NULL,NULL,1,4,30000,'Blues.  Peer.  Blues festival Peer is Vlaanderens bekendste Rhythm and Blues festival en een referentie in heel Europa.  Het programma is Blues maar niet puristisch: je vindt er evenzeer John Hiatt of Van Morisson.   Iets ouder en rustig publiek.  Festival met de hoogste bierconsumptie per bezoeker in Vlaanderen.  20 Ã  30.000 toeschouwers','Blues. Peer.   The most famous Flemish Rhythm and Blues Festival and known throughout Europe. The program is not purist blues: you will find John Hiatt and Van Morrison there as well. Slightly older and more sedate audience. Weâ€™re told that this festival has the highest beer consumption per visitor in Flanders. 20 to 30,000 spectators.','',NULL),(2,'21b8660c4c4efc6c7e8c7e36e721214a','Cactus Festival',1,1,'2012-07-06','2012-07-08','http://www.cactusfestival.be/','Brugge',NULL,NULL,NULL,NULL,NULL,NULL,8,5,30000,'Fijnproeversprogrammatie.  Brugge.  Bekend om zijn kwalitatieve affiche: je mag je verwachten aan alles van Calexico over Cohen tot Costello.  Bekend om zijn locatie en terreinindeling: chillen in het Minnewaterpark.  Verkozen tot sfeerrijkste festival ter wereld enkele jaren geleden (Arthur Award).  20 Ã  30.000 toeschouwers','Quality Programming. Bruges. Known for its qualitative line-up: you should expect everything from Cohen of Calexico to Costello. Known for its location and site layout: chill in the Minnewaterpark. Voted atmosphere richest festival in the world a few years ago (Arthur Award). 20 to 30,000 spectators','',NULL),(3,'49b4ce1a23c5c507130539c2fff42fd2','Dranouter Folkfestival',1,1,'2012-08-03','2012-08-05','http://www.festivaldranouter.be','Dranouter',NULL,NULL,NULL,NULL,NULL,NULL,4,4,100000,'Folk, indie & pop. Dranouter in de Westhoek.  Slagvelden rondom.  Begonnen als nichefestival voor folk werd het al gauw het grootste folkfestival in Europa.  Dat is nog steeds de core business maar programmatie werd verbreed zodat je er ook als eens Lou Reed of Deus tegen het lijf loopt.  Bijzonder gezinsvriendelijk festival.  Een occasionele geitenwollensok is nog steeds hier een daar te spotten. 80 Ã  100.000 bezoekers.','Folk, indie and pop. Dranouter in Flanders. World War I battlefields are all around. Started as a niche folk festival and soon became the largest folk festival in Europe. Still the core business but the programme has been expanded so that you can also see Lou Reed or DEUS perform here. Particularly family-friendly festival. You may still spot the occasional hard-core crunchy folkie here. 80 to 100,000 visitors.','',NULL),(4,'86c8af6ef39b6b0a9303145fd1637170','Feest in het Park',1,1,'2012-08-23','2012-08-26','http://www.feestinhetpark.be','Oudenaarde',NULL,NULL,NULL,NULL,NULL,NULL,10,5,40000,'Popfestival.  Oudenaarde.  Prachtig stadje.  Mooie affiche elk jaar.   40.000 bezoekers','Pop festival. Oudenaarde. A lovely town.  A great line-up every year. The Tour of Flanders (cycling) arrives here. 40,000 visitors','',NULL),(5,'4ecb3c3e6ecfb83d055a19bafbcc8ae8','Festival van Vlaanderen Gent',1,1,'2012-09-15','2012-09-29','http://www.gentfestival.be','Gent',NULL,NULL,NULL,NULL,NULL,NULL,6,6,55000,'Klassiek.  Gent.  Vlaggeschip van Vlaamse klassieke festivalwereld.  In september staan maar liefst 180 concerten op het programma (klassiek & wereldmuziek), waar ruim 1500 (inter)nationale artiesten aan meewerken. Het festival trekt jaarlijks ruim 55.000 bezoekers aan. Events zoals â€˜Avanti!â€™, het Venetiaans Bal, KidsOdeGand en OdeGand zijn vaste waarden tijdens het festival.','Classic. Ghent. Flagship of the Flemish classic festival world. In September nearly 180 concerts are scheduled (classical & world music), where over 1500 (inter) national artists participate. Every year the festival attracts over 55,000 visitors. Events such as â€˜Avanti!â€™, the Venetian Ball, KidsOdeGand and OdeGand are typically scheduled every year.','',NULL),(6,'9b32b3e2dd6136916c0230f71685b093','Gent Jazz Festival',1,1,'2012-07-05','2012-07-15','http://www.gentjazz.com','Gent',NULL,NULL,NULL,NULL,NULL,NULL,5,5,38000,'Jazz & pop.  Bijloke-abdij, altijd schoon als locatie.  Dit is het betere festival voor de fijnproever van goede muziek maar niet voor de jazzpurist want Sony Rollins tref je hier aan tussen Norah Jones en Agnes Obel .','Jazz and pop. Bijloke abbey, a stunning location. This is the best festival for the connoisseur of good music but not for the jazz purist as Sony Rollins, you will find anything between Norah Jones and Agnes Obel.','',NULL),(7,'1bbd98f4afa85e670934e969ff08f0d0','Gentse Feesten',1,1,'2012-07-14','2012-07-23','http://www.gentsefeesten.be','Gent',NULL,NULL,NULL,NULL,NULL,NULL,3,1,1000000,'Van alles. Gent. De hele stad veertien dagen later in comateuze toestand.  Op zowat alle kleine en grote pleinen in het stadscentrum is er gratis muziek. Er is vuurwerk, straatvoorstellingen, de Gentse Feestenparade, het Bal 1900, de Rondgang van de Stroppendragers, markten.  EÃ©n groot openluchtpodium.  De Gentse Feesten zijn eigenlijk een samenklontering van vele festivals en daarom the ultimate experience als je â€˜densiteitâ€™ als een van de aminozuren definieert waaruit onze festival experience is opgebouwd.  Heel tof zijn het Puppetbuskerfestival en het Internationaal Jeugdcircusfestival. Meer dan een miljoen bezoekers.','A bit of everything. Ghent. The whole city is engulfed in a 14 day party. On virtually all small and large squares in the city center there is free music. There is fireworks, street performances, the Ghent Festival Parade, Ball 1900, the Tour of the slings carriers, markets. A large outdoor stage. Is actually an agglomeration of many festivals and therefore the ultimate experience when you \'density\' as one of the amino acids which defines our festival experience is built. Very cool are the Puppet Buskers Festival and the International Youth Circus Festival. Over a million visitors.','',NULL),(8,'fa9a75a30a829c6967838431692b5bb5','Graspop',1,1,'2012-06-22','2012-06-24','http://www.graspop.be','Dessel',NULL,NULL,NULL,NULL,NULL,NULL,7,4,140000,'Heavy metal.  Dessel in de Antwerpse Kempen.  Ooit erg mainstream (Simple Minds, Joe Cocker) en Werchter beconcurrerend.  Dan begin jaren negentig zichzelf herdoopt tot â€˜metal meetingâ€™ en nu altijd uitverkocht als een van de grootste metalfestivals ter wereld.  Dit jaar o.a. MotÃ¶rhead, Slayer, Guns â€™n Roses, Ozzy Osbourne... Het publiek is al een attractie op zich: iets ouder, mannelijk, headbangend.  Ze zien er vervaarlijk uit tijdens het beleven van hun muziek maar volgens de securitymensen behoort het festival tot de rustigste en veiligste in zijn soort.  140.000 bezoekers','Heavy metal.   Dessel near Antwerp. Once very mainstream (Simple Minds, Joe Cocker) and a competitor to Werchter. Then in the early nineties renamed themselves \"\"metal meeting\"\" and now always sold out as one of the biggest metal festivals in the world. This yearâ€™s line-up includes Motorhead, Slayer, Guns\' n Roses, Ozzy Osbourne ... The audience is an attraction in itself: a little older, largely male, headbangers. They look quite threatening when they enjoy their music but we have been told by security people that they see it as the quietest and safest festival. 140,000 visitors','',NULL),(9,'81d2bfac4936ffddc2bd87f4f3ded2f4','Laundry Day',1,1,'2012-09-01','2012-09-01','http://www.laundryday.be/','Antwerpen',NULL,NULL,NULL,NULL,NULL,NULL,2,1,60000,'Dancefeestje op het Nieuw Zuid in Antwerpen. Begonnen als gratis en heel alternatief in de Kammenstraat op de dag dat iedereen zijn wasgoed buiten hing â€“ bedoeling was kansen te geven aan jonge DJâ€™s.  15 jaar later is het een grote dance party waar TiÃ«sto of front 242 headlinen.  Opvallend jong publiek.  Ook gek: vorig jaar terreinanimatie met circussen, acrobaten, clowns en straattheater â€“ een combinatie die je niet vaak in zoâ€™n commerciÃ«le omgeving aantreft.  60.000 bezoekers en om middernacht vuurwerk.','\"Dance Party at the New South in Antwerp, friendly neighborhood where only nice people live. Started out as a free and rather alternative party in Kammenstraat on the day that everybody hung out their laundry - was intended to give chances to young DJs. Fifteen years later this is one big dance party with headliners such as TiÃ«sto or Front 242. Noteworthy: a rather young audience. Last year also included circuses, acrobats, clowns and street theatre','a combination that you donâ€™t often find in such a commercial environment. 60,000 visitors and fireworks at midnight.\"',NULL),(10,'818b552aed11a1bba5ac1002d5aa47ab','Lokerse Feesten',1,1,'2012-08-03','2012-08-12','http://www.lokersefeesten.be','Lokeren',NULL,NULL,NULL,NULL,NULL,NULL,8,1,120000,'Popfestival.  Lokeren.  Het verhaal zit â€˜m hier  vooral in de affiche waar ze het verschil maken met veel andere stadsfestivallen (â€˜de burgemeester tracteertâ€™).  Geldt samen met Cactus als een programmatorisch sterk festival.   Zo staan er dit jaar o.a. de Beach Boys zich te verenigen op het podium. 120.000 bezoekers.','Pop festival. Lokeren. The story lies here particularly in the line-up that differentiates them from  many other city based festivals. Together with Cactus a festival with a strong program.  This year includes a Beach Boys reunion. 120,000 visitors.','',NULL),(11,'2ad054ae37e5f1748889c35396e9a1e8','Openluchttheater Rivierenhof Deurne',1,1,NULL,NULL,'http://www.openluchttheater.be','Deurne',NULL,NULL,NULL,NULL,NULL,NULL,8,5,1200,'Antwerpen (Deurne). Ondanks beperkte capaciteit van 1200 bezoekers slaagt men er vaak in grote namen aan te trekken van het niveau Manu Chao, Buena Vista of Waterboys.  dEUS deed er ooit een verassingsconcert.  De reden is simpel: de unieke openluchtlocatie in het groen.  Zulke dingen worden doorverteld door artiesten...','Antwerp (Deurne). Despite limited capacity of 1200 visitors, they succeed often in big names to attract the level of Manu Chao, Buena Vista or Waterboys. Once the band Deus performed a surprise concert there. The reason is simple: the unique outdoor location in the green. Such things are passed along through word of mouth by artists ...','60000 bezoekers heel de zomer',NULL),(12,'c8084507570f4c18a812b52d39eb9753','PolÃ© PolÃ©',1,1,'2012-07-14','2012-07-23','http://www.polepole.be','Gent',NULL,NULL,NULL,NULL,NULL,NULL,12,1,0,'World & djâ€™s.  Een succesvol  live-concept in de historische Gentse binnenstad. Uniek decor: de Graslei en de Korenlei.   Een drijvend festivaldorp, 40 live-bands, het publiek op het water. Het beste uit pop, latin en world in een uitzinnig decor... Denk aan een soort van Carnaval van VenetiÃ« zonder de maskers voor politiek correcte mensen die ook uit de bol willen','World & DJs. A successful live-concept in the historical city of Ghent. Unique scenery: the Graslei and Korenlei. A floating village festival, 40 live bands and the audience on the water. The best of pop, latin and world music in a stunning setting... Imagine the Carnival of Venice without the masks for politically correct people who also want to have a really good time.','',NULL),(13,'23aba557b1e5ba5a0439287a570bd3f0','Pukkelpop',1,1,'2012-08-16','2012-08-18','http://www.pukkelpop.be','Kiewit',NULL,NULL,NULL,NULL,NULL,NULL,10,4,180000,'Rockfestival.  Hasselt. Grote namen sowieso.   Festival van de veelheid: veelheid aan podia en tenten geeft kansen aan heel veel jong talent.  Geliefkoosd festival bij jongeren.  Hoe gaan ze om met ramp van vorig jaar tijdens deze editie?  180.000 bezoekers.','Rock festival. Hasselt. Big names. Festival of multiplicity: multiplicity of stages and tents provides opportunities for a lot of young talent. Favorite festival for young people. How do they deal with the disaster of last year during this yearâ€™s festival? 180,000 visitors.','',NULL),(14,'8228016d4f25ae892016f40e0ab95eb9','Reggae Geel',1,1,'2012-08-04','2012-08-05','http://www.reggaegeel.com/','Geel',NULL,NULL,NULL,NULL,NULL,NULL,9,5,30000,'Reggaefestival.  Geel.  Rootsfestival in geel groen rood badend en met een blauwe rookwalm er bovenop.  Plek waar de Burning Spears en Lindon Kwesi Johnsons van deze planeet zich op hun gemak voelen.  Jah leeft en dan wel midden in de Antwerpse kempen... 25.000 Ã  30.000 fans','Reggae Festival. Geel. Roots Festival bathed in green and red with a blue cloud of smoke on top. Where the Burning Spears and Lindon Kwesi Johnsons of this planet are at ease. Rasta lives in the middle of the Antwerp region... 25,000 to 30,000 fans','',NULL),(15,'8021eeddb7cdee4c5beace5043e521b4','Rock Werchter',1,1,'2012-06-28','2012-07-01','http://www.rockwerchter.be','Werchter',NULL,NULL,NULL,NULL,NULL,NULL,10,4,320000,'Rock & pop.  Werchter bij Leuven. Rock Werchter 2012 vindt plaats van donderdag 28 juni tot en met zondag 1 juli. Hoort thuis in hetzelfde rijtje als Glastonbury (die gaan niet door dit jaar) en Roskilde.  320.000 bezoekers als het uitverkocht is.  In Werchter Ã©n volgens beproefd concept.  Vier dagen lang is in het Festivalpark het beste van nu te zien en te horen op niet meer twee maar nu drie podia.','Rock and pop. Werchter in Leuven. Rock Werchter 2012 will take place from Thursday, June 28 till Sunday 1 July. Belongs in the same breath as Glastonbury (which do not go through this year) and Roskilde. 320,000 visitors when it is sold out. In accordance Werchter and proven concept. The Festival Park is the place to be, for a four-day festival where you can see and hear the best bands on not two but three stages (new this year).','',NULL),(16,'93ee976c7352afd0846c3e85af007114','Sfinks',1,1,'2012-07-27','2012-07-29','http://www.sfinks.be','Boechout',NULL,NULL,NULL,NULL,NULL,NULL,12,5,30000,'Wereldmuziek.   Boechout.  Pioniers van de nichegedachte om resoluut de folk op te geven en om world te programmeren.  Kinderdorp is even groot als de festivalweide en de sleutel van het succes nl. een gezinsfestival.  20 Ã  30.000 bezoekers.','World music. Boechout. Pioneers of the niche festival idea. The childrenâ€™s village is just as big as the festival site and the key of its success, i.e., this is a very family-friendly festival. 20 to 30,000 visitors.','',NULL),(17,'b251d2246e9048d915b4d5369a16a0e0','Suikerrock',1,1,'2012-07-26','2012-07-29','http://www.suikerrock.be','Tienen',NULL,NULL,NULL,NULL,NULL,NULL,8,1,100000,'Rock.  Tienen.  Tienen is de suikerklontjesstad.  Vandaar.  Affiche bevat als headliners vaak de seniorenafdeling van de rock: vorig jaar Deep Purple, dit jaar Alice Cooper en Status Quo enz.... aangevuld met jonger geweld.   Een succesformule.  100.000 bezoekers.','Rock. Tienen.   Tienen is a sugar-producing city.  Hence the name. Line-up contains headliners considered the senior division of the rock: last year Deep Purple, this year Alice Cooper and Status Quo etc. ... combined with younger talent. A formula for success. 100,000 visitors.','',NULL),(18,'0d9548be6fb4f7c3c10f7cce210d583b','Tomorrowland',1,1,'2012-07-27','2012-07-29','http://www.tomorrowland.be','Boom',NULL,NULL,NULL,NULL,NULL,NULL,2,5,100000,'Dancemuziek.  Boom.  FeeÃ«riek decor.  Vorig jaar kookte Wout Bru backstage voor de VIPS.  Vraag is 30.000 tickets boven het aanbod.  The sky lijkt de limit voor dit festival.  Dit jaar met o.a. Fatboy Slim, Martin Solveig en David Guetta.  90 Ã  100.000 bezoekers.','Dance Music. Boom. Magical decor. Last year Wout Bru (celebrity chef) cooked backstage for VIPs. Demand is 30,000 tickets over the supply. Have decided to apply ticket contingents or Flemings would no longer be able to visit the festival.  No longer interested in ticket sales but in package deals with Brussels Airlines and accommodation. The sky seems the limit for this festival. This year includes Fatboy Slim, David Guetta and Martin Solveig. 90 to 100,000 visitors.','',NULL),(19,'cac5ff37959bc62c607f098e97814140','Alcatraz Festival',1,0,'2012-08-12','2012-08-12','http://www.alcatrazmusic.be','Deinze',NULL,NULL,NULL,NULL,NULL,NULL,7,6,0,'','','',NULL),(20,'0a038e21bda23e290a896c9ded54b077','Bengelpop',1,0,'2012-06-26','2012-06-26','http://www.keiheuvel.be','Balen',NULL,NULL,NULL,NULL,NULL,NULL,8,5,0,'','','',NULL),(21,'d2590ba2adae4305be291450354d7287','BoerenRock',1,0,'2012-08-24','2012-08-26','http://www.boerenrock.be','Kortenaken',NULL,NULL,NULL,NULL,NULL,NULL,8,4,11000,'','','',NULL),(22,'99a58d45531803f76e1620612d7439b0','Brosella Jazz en Folk',1,0,'2012-08-14','2012-08-15','http://www.brosella.be','Brussel',NULL,NULL,NULL,NULL,NULL,NULL,5,1,25000,'','','',NULL),(23,'490130f4e383de7c807efec02de29f1d','Bruksellive',1,0,'2012-07-28','2012-07-28','http://www.bruksellive.be/','Brussel',NULL,NULL,NULL,NULL,NULL,NULL,8,5,23000,'','','',NULL),(24,'390d91a07b65da9db08e82ae8ab11ad3','Casa Blanca Festival',1,0,'2012-08-01','2012-08-04','http://www.casablancafestival.be','Hemiksem',NULL,NULL,NULL,NULL,NULL,NULL,12,4,73000,'','','',NULL),(25,'40389eb80ceec1558835bc9321c60091','Crammerock festival',1,0,'2012-08-31','2012-09-01','http://www.crammerock.be','Stekene',NULL,NULL,NULL,NULL,NULL,NULL,10,4,22000,'','','',NULL),(26,'1aa847e89e4819b46ac4ee43015f7f2c','Djangofestival',1,0,'2012-05-05','2012-05-06','http://www.djangoliberchies.be/','Liberchies',NULL,NULL,NULL,NULL,NULL,NULL,5,1,0,'','','',NULL),(27,'01c486a4d51bc7cbf4c02f6f3e29dd3d','Festival van Vlaanderen Antwerpen',1,0,NULL,NULL,'http://www.amuz.be','Antwerpen',NULL,NULL,NULL,NULL,NULL,NULL,6,6,0,'','','',NULL),(28,'36e487d5986b20b5bd70409b2b2a9710','Festival van Vlaanderen Brugge',1,0,'2012-08-03','2012-08-12','http://www.mafestival.be','Brugge',NULL,NULL,NULL,NULL,NULL,NULL,6,6,0,'','','',NULL),(29,'622c713a8aaa159d6d1700e9eb164cbc','Festival van Vlaanderen Brussel',1,0,NULL,NULL,'http://www.festivalbrxl.be','Brussel',NULL,NULL,NULL,NULL,NULL,NULL,6,6,0,'','','',NULL),(30,'75377cb847a8568bd202547af0de466c','Festival van Vlaanderen Limburg',1,0,NULL,NULL,'http://www.basilica.be','/',NULL,NULL,NULL,NULL,NULL,NULL,6,6,0,'','','',NULL),(31,'4b572ec36483daf64a09d47ebccd0e56','Festival van Vlaanderen Mechelen',1,0,NULL,NULL,'http://www.festivalmechelen.be','Mechelen',NULL,NULL,NULL,NULL,NULL,NULL,6,6,0,'','','',NULL),(32,'7db333fcd8a646d136cb026091dda3f9','Festival van Vlaanderen Vlaams Brabant',1,0,'2012-09-14','2012-10-13','http://www.festivalvlaamsbrabant.be','Leuven',NULL,NULL,NULL,NULL,NULL,NULL,6,6,0,'','','',NULL),(33,'d06cfdf1ec1b97d43df32148023b6e89','Fiesta Mundial',1,0,'2012-09-28','2012-09-30','http://www.fiestamundial.be','Balen',NULL,NULL,NULL,NULL,NULL,NULL,12,5,0,'','','',NULL),(34,'75204f460594994f7f2c8340ff69ab4d','Genk On Stage',1,0,'2012-06-22','2012-06-24','http://www.genkonstage.be','Genk',NULL,NULL,NULL,NULL,NULL,NULL,10,1,140000,'','','',NULL),(35,'25d1792f34374bac414258397538df41','Gladiolen',1,0,'2012-05-25','2012-05-26','http://www.gladiolen.be/','Olen',NULL,NULL,NULL,NULL,NULL,NULL,10,4,16000,'','','',NULL),(36,'f7396bdeda702b9f7515934cda586847','Groez Rock',1,0,'2012-04-28','2012-04-29','http://www.groezrock.be','Meerhout',NULL,NULL,NULL,NULL,NULL,NULL,10,4,0,'','','',NULL),(37,'b4e996924327e5065a887fccb9f277bd','Hasselt Ziiingt',1,0,'2012-07-11','2012-07-11','http://www.hasseltziiingt.be/','Hasselt',NULL,NULL,NULL,NULL,NULL,NULL,8,1,0,'','','',NULL),(38,'f19128a0aa83650ef1b725ee9363567e','Hestival',1,0,'2012-08-27','2012-08-27','http://www.hestival.be/','Heist-op-den-Berg',NULL,NULL,NULL,NULL,NULL,NULL,8,1,10000,'','','',NULL),(39,'c96c9390f729bdcf350f92a13f36445a','Mano Mundo Festival',1,0,'2012-05-12','2012-05-13','http://www.manomundo.be','Boom',NULL,NULL,NULL,NULL,NULL,NULL,12,5,0,'','','',NULL),(40,'8682bbf78f67ced6ca58dbcb3ed15231','Muzikale dinsdagen',1,0,NULL,NULL,'http://www.ri4vos.be/','Ieper',NULL,NULL,NULL,NULL,NULL,NULL,10,1,0,'','','',NULL),(41,'5bc083cd48a61999477a80f82c55ed0e','Na Fir Bolg folkfestival',1,0,'2012-06-29','2012-07-01','http://www.folkfestival.be/','Vorselaar',NULL,NULL,NULL,NULL,NULL,NULL,4,4,9000,'','','',NULL),(42,'0ddb81fd45c9295811c7d832d36fb308','Nekka Nacht',1,0,'2012-04-20','2012-04-20','http://www.nekka.be','Antwerpen',NULL,NULL,NULL,NULL,NULL,NULL,8,6,0,'','','',NULL),(43,'3d438502723bdd9896657e021b19e29a','Night of the Proms',1,0,'2012-11-08','2012-11-08','http://www.notp.be','Antwerpen',NULL,NULL,NULL,NULL,NULL,NULL,8,6,0,'','','',NULL),(44,'1f9c517cd2e80247ac1a05949288173c','Novarock',1,0,'2012-03-17','2012-03-17','http://www.novarock.be','Kortrijk',NULL,NULL,NULL,NULL,NULL,NULL,10,6,0,'','','',NULL),(45,'1e3e286bf39569146bc98e175de64a59','Palm Parkies',1,0,NULL,NULL,'http://www.parkies.net','/',NULL,NULL,NULL,NULL,NULL,NULL,8,5,0,'','','Heel de zomer, verschillende plaatsen',NULL),(46,'050a2ce4b95fd4cc18a65fce27f2a8be','Paulusfeesten Oostende',1,0,'2012-08-09','2012-08-15','http://www.paulusfeesten.be','Oostende',NULL,NULL,NULL,NULL,NULL,NULL,4,1,90000,'','','',NULL),(47,'fb7beacc717e7f682c01f3cad537cfd4','PolÃ© PolÃ© Beach',1,0,'2012-08-03','2012-08-05','http://www.polepole.be','Zeebrugge',NULL,NULL,NULL,NULL,NULL,NULL,12,2,40000,'','','',NULL),(48,'fffd1d358185a331288f385674f129f4','pOpwijk',1,0,'2012-09-01','2012-09-01','http://www.popwijk.be','Opwijk',NULL,NULL,NULL,NULL,NULL,NULL,8,4,6000,'','','',NULL),(49,'c550dc0101df8b8eea5e8c225c6cca0d','Pukema Rock',1,0,'2012-09-14','2012-09-16','http://www.pukemarock.be/','Puurs',NULL,NULL,NULL,NULL,NULL,NULL,10,4,23000,'','','',NULL),(50,'2866d4ed9347f3a14477f738363928b4','Rock Herk',1,0,'2012-07-13','2012-07-14','http://www.rockherk.be','Herk-de-Stad',NULL,NULL,NULL,NULL,NULL,NULL,10,5,12000,'','','',NULL),(51,'a8e598681bde60b8ba1eeb27d3883127','Rock Ternat',1,0,'2012-10-05','2012-10-06','http://www.rockternat.be','Ternat',NULL,NULL,NULL,NULL,NULL,NULL,10,4,0,'','','',NULL),(52,'30ada4db076fd6070ee6de80748165b0','Rock Waregem',1,0,NULL,NULL,'http://www.rockwaregem.be','Waregem',NULL,NULL,NULL,NULL,NULL,NULL,8,6,0,'','','Juiste datum is nog niet op site vermeld',NULL),(53,'7204d869896a272f626c57eb58f781b1','Rock Zottegem',1,0,'2012-07-06','2012-07-07','http://www.rock-zottegem.be','Zottegem',NULL,NULL,NULL,NULL,NULL,NULL,8,4,20000,'','','',NULL),(54,'40413b4ef5c69925dfbeb638c09859c4','Soiree Tropicale',1,0,'2012-07-07','2012-07-07','http://www.soireetropicale.com/','Eppegem',NULL,NULL,NULL,NULL,NULL,NULL,12,4,5000,'','','',NULL),(55,'96f83ee7ece3695a233a6aab1746d26b','Summerfestival',1,0,'2012-06-30','2012-06-30','http://www.summerfestival.be/','Antwerpen',NULL,NULL,NULL,NULL,NULL,NULL,2,1,30000,'','','',NULL),(56,'0f690d8cd974e923586e25ec346f3cb4','Woosha',1,0,'2012-07-13','2012-07-14','http://www.woosha.be/','Oostende',NULL,NULL,NULL,NULL,NULL,NULL,10,1,0,'','','',NULL),(57,'4bc43bfa863725e3243a6dd69dc76d37','10 Days Off',0,1,'2012-07-15','2012-07-25','http://www.10daysoff.be/','Gent',NULL,NULL,NULL,NULL,NULL,NULL,2,6,0,'Dance.  Gent.  Voornamelijk Electro, Drumâ€™n Base, Dubstep.  De Vooruit als locatie.  Grote namen in DJ wereld . Van Lektroluv tot Laserkraft 3D.','Dance. Ghent. Mainly Electro, Drum\'n Base, Dubstep. The forwards\' location. Big names in the DJ world (Lektroluv to Laserkraft 3D)','',NULL),(58,'c3103bdbbbdf5d1e3b458c1e8e447d6c','Afro-Latino festival',0,1,'2012-06-22','2012-06-24','http://www.afro-latino.be/','Bree',NULL,NULL,NULL,NULL,NULL,NULL,12,4,22000,'Wereldmuziek.  Bree.  Kim Clijsterstown.  CO2-neutraal en uitgeroepen tot groenste event van Vlaanderen in 2010 (Ovam-prijs).','World music. Bree. Kim Clijsterstown. CO2-neutral and voted greenest event of Flanders in 2010 (Ovam price).','',NULL),(59,'759e91a59b57a65a8e6abc3f6a37267a','Antilliaanse feesten',0,1,'2012-08-10','2012-08-11','http://www.antilliaansefeesten.be','Hoogstraten',NULL,NULL,NULL,NULL,NULL,NULL,12,4,40000,'Caribische ritmes.  Hoogstraten.  Het grootste Caribische festival ter wereld.  Hetgene waar het meest gedanst wordt sowieso...  Caribische festivals hebben hun eiegen internationaal publiek.  Half kleurrijk Nederland staat daar op de dansvloer.  30 Ã  40.000 bezoekers','Caribbean rhythms. Hoogstraten. The largest Caribbean festival in the world. In any event the festival where people dance the most. Caribbean festivals have their own international and multicultural audience. 30 to 40,000 visitors','',NULL),(60,'6c67401e336b2e65a1166e155ee85ec5','Boomtown Live',0,1,'2012-07-17','2012-07-21','http://www.boomtownlive.be','Gent',NULL,NULL,NULL,NULL,NULL,NULL,10,1,30000,'Pop. Gent.  Vijf podia en 50 acts.  Veeleer jong aanstormend talent, Belgisch getint.','Pop. Ghent. Five stages and 50 acts. Rather, young talent, Belgian tinted. Groups from Klaartje Bonaireâ€™s Rock\'oco line-up tend to dominate the line-up.','',NULL),(61,'16df890f48ff30d338bf284ae9b70cfa','Couleur CafÃ©',0,1,'2012-06-29','2012-07-02','http://www.couleurcafÃ©.be','Brussel',NULL,NULL,NULL,NULL,NULL,NULL,12,1,75000,'Urban music.  Brussel.  Wereldmuziek maar met een zwaar stedelijk accent: van Sergent Garcia en Cheb Khaled tot Snoop Dogg.   Oprichter was ooit de bedenker van het Cirque du soleil-concept.  Unieke locatie op Turm & Taxis in Brussel.  Uniek is de â€˜Rue Bien Mangerâ€™ â€“ de meest neusprikkelende reeks eetstands in BelgiÃ«.  Grote aandacht voor inkleding terrein en details. Visueel sterk.   Zaterdag altijd vuurwerk.  Ook altijd aandacht voor beeldende kunstenprojecten en sociaal-artistieke projecten.   60.000 toeschouwers.','Urban music. Brussels. World music but with a heavy urban focus: Sergent Garcia and Cheb Khaled to Snoop Dog. Founder is the creator of the Cirque du Soleil concept. Unique location on Tower & Taxis in Brussels. Unique is the rue bien manger - the most exciting series of street food stalls in Belgium. Great attention to detail and decoration. Visually strong. Saturdays always fireworks. Always focus on visual arts projects and socio-artistic projects. 60,000 spectators.','',NULL),(62,'a72ee33d71b3a145123ca142b4ec1a23','Ieperfest',0,1,'2012-08-10','2012-08-12','http://www.ieperfest.com/','Ieper',NULL,NULL,NULL,NULL,NULL,NULL,7,4,10000,'Hardcorefestival.  Ieper.  Nooit van gehoord?  In de wijde wereld wel. Klein maar internationaal festival maar heel erg niche (hardcore â€“ â€˜heavy metal is voor softiesâ€™).  Zeg trouwens nooit death metal tegen grindcore ! Het bijzondere is dat dit festival georganiseerd wordt door veganisten die dat rigoureus doortrekken in catering van publiek en artiesten en verder geen enkele toegeving doen op het vlak van duurzaamheid.  Geniet van je mueslibar en je â€˜vegan Bratwurstâ€™!   10.000 toeschouwers.','Hardcore Festival. Ieper.  Small but international festival but very niche (hardcore - \"\"heavy metal is for softiesâ€).  The interesting bit is that this festival is organized by vegans who implement this rigorously. No exceptions are made to artists and audiences. Enjoy your mueslibar and your \'vegan bratwurst\'! 10,000 spectators.','',NULL),(63,'4d711aade362b8bf1d15d168c373de3e','Jazz Middelheim',0,1,'2012-08-16','2012-08-19','http://www.jazzmiddelheim.be','Antwerpen',NULL,NULL,NULL,NULL,NULL,NULL,5,5,20000,'Jazzfestival.  Antwerpen.  Programmatie voor puristen.  Veel aandacht voor gastronomie.  De betere VIP.  Unieke locatie in park Den Brandt in Antwerpen, vlak bij Middelheimpark (deze zomer o.a. Ai Wei Wei, Thomas SchÃ¼tte)... Bekend in States.  Stokoude Toots Tielemans treedt er nog elk jaar op als soort â€˜patroonheiligeâ€™ â€“ bedoeling is dat hij op het podium zijn laatste adem uitblaast.... 20.000 bezoekers','Jazz Festival. Antwerp. Programming for the purists. A lot of attention given to fine food.  Unique location in Park Den Brandt in Antwerp, near Middelheim Park (this summer include Ai Wei Wei, Thomas SchÃ¼tte) ...Known in the States. Old timer Toots Tielemans still performs every year as some kind of â€œpatron saintâ€. Will he die on stage one day? 20,000 visitors','',NULL),(64,'d21cad76282d2f8dbe056b949eaf9231','Laus Polyphoniae',0,1,'2012-08-24','2012-09-02','http://www.amuz.be/','Antwerpen',NULL,NULL,NULL,NULL,NULL,NULL,6,6,0,'Oude muziek.  Antwerpen.  Locatie is de tot concertzaal omgetoverde en ontwijde kerk St. Augustinus.  Dit jaar focus op de oude muziek van de Adriatische kust (KroatiÃ«, SloveniÃ«, AlbaniÃ« etc...)','Music from the dark ages (literally). Antwerp. Location is the deconsecrated church of St. Augustine turned into a concert hall. This year focus on the early music of the Adriatic coast (Croatia, Slovenia, Albania etc. ..)','',NULL),(65,'f907ec3ccf71d42cec81f10a64fa850f','Marktrock',0,1,'2012-08-10','2012-08-12','http://www.live-entertainment.be/nl/nieuws/2010/06/08/marktrock-2010-bekendmaking-affiche/','Leuven',NULL,NULL,NULL,NULL,NULL,NULL,8,1,100000,'Popmuziek.  Leuven.  Stadsfestival.  De USP is 100% Belgisch en 100% gratis. Interessant om aan te tonen dat Belgen ook popmuziek maken.  De Oude markt, de langste toog ter wereld dixit de Leuvenaren.  100.000 bezoekers.','Pop music. Leuven. City Festival. The USP is that the acts are 100% Belgian and the festival is 100% free. Interesting to show that Belgians also make pop music. Make sure that your influencer doesn\'t get trampled in Oude Markt, the longest bar in the world, as the people of Leuven tend to call it. 100,000 visitors.','',NULL),(66,'328bbcb6b2666793255f1fadc50a8d62','MiramirO - Internationaal Straattheaterfestival',0,1,'2012-07-19','2012-07-22','http://www.miramiro.be/','Gent',NULL,NULL,NULL,NULL,NULL,NULL,11,1,0,'Gent.  Stad.   Straat- en circuskunsten, theater op locatie, installaties, beeldende kunst, dans... Van intimistische sfeerstukken tot grootschalige massa-evenementen.','Ghent. Downtown. Street and circus arts, theater location, facilities, visual arts, dance ... From intimate atmosphere pieces to large scale mass events.','',NULL),(67,'4503203a49eec3d3aa18621a24dcfe8c','Musiqua Antiqua',0,1,'2012-08-03','2012-08-12','http://www.mafestival.be/','Brugge / Lissewege',NULL,NULL,NULL,NULL,NULL,NULL,6,6,0,'Oude Muziek.  Brugge & Lissewege.  Brugge in het nu 10 jaar oude Concertgebouw te Brugge.  Volgens Jordi Saval de beste concertzaal qua akoestiek en architectuur van Europa.  Lissewege, boogscheut van de kust en sowieso mooiste dorp van Vlaanderen.  Vorig jaar intense samenwerking met festival van Utrecht.','Classical Music. Bruges & Lissewege. Takes place in the 10 year old Concertgebouw in Bruges. According to Jordi Saval the best concert hall in terms of acoustics and architecture of Europe. Lissewege is a stoneâ€™s throw from the coast and the most beautiful village of Flanders. Last yearâ€™s festival had an intense collaboration with Utrecht. Program will be announced on 10 March.','',NULL),(68,'3c466e6301a3ddfb977ec832cf5cfa33','Odegand',0,1,'2012-09-17','2012-09-17','http://www.odegand.be/','Gent',NULL,NULL,NULL,NULL,NULL,NULL,6,1,0,'Cultuurfestival.  Stad Gent.  Verrassende locaties.  Mooie manier om Gent van een andere kant te bekijken.  Met OdeGand knalt Gent jaarlijks het culturele seizoen op gang. Overdag krijg je zowel internationaal als nationaal muziektalent voorgeschoteld. â€™s Avonds geniet je van het concert op het drijvende podium aan de Gras- en de Korenlei, en vuurwerk! Viert dit jaar zijn 10de verjaardag als opening van het FvV Gent.','Cultural Festival. City of Ghent.   Surprising locations. Nice way to see Ghent in a different light. With OdeGand the Ghent annual cultural season starts with a bang. During the day both international and national musical talent is featured. In the evening you can enjoy the concert on the floating stage on the Grass and Korenlei, with fireworks! Celebrating its 10 th anniversary as opening of the FVV Ghent.','',NULL),(69,'f2e90e7c766bad242849f1cc99b2d999','Rimpelrock',0,1,'2012-08-11','2012-08-11','http://www.rimpelrock.be/','Kiewit',NULL,NULL,NULL,NULL,NULL,NULL,8,4,30000,'Popfestival.  Hasselt.  Op de weide van Pukkelpop staan de week daarvoor de stoelen klaar om 30.000 mensen met wat strammere gewrichten in open lucht te ontvangen.  Helmut Loti, Clouseau, Engelbert Humperdinck, Natalia: ze hebben er allemaal al opgetreden.  Soms zitten ze onder een dekentje maar vaak gaan ze gewoon uit de bol.  Dit is ongetwijfeld het festival met het grootste rolstoelpodium ter wereld.','Pop festival. Hasselt.  On the meadow of Pukkelpop, one week prior, targeting for the elderly. Helmut Loti, Clouseau, Engelbert Humperdinck, Natalia: they all have already played there. Sometimes they sit under a blanket but often they will just be thrilled. This is undoubtedly the biggest festival of the wheelchair platform in the world. 30,000 visitors.','',NULL),(70,'ce021b8ea13920c1fb78dd25c83d2a54','Rock voor Specials',0,1,'2012-06-26','2012-06-27','http://www.rockvoorspecials.be','Evergem',NULL,NULL,NULL,NULL,NULL,NULL,8,4,5000,'Rock & pop.  Evergem bij Gent.  Hier treden namen op als The Scene, Hooverphonic, Triggerfinger, Gabriel Rios, Ozark Henry & Arsenal.  Op zich niet wereldschokkend.  Bijzonder aan dit festival is het publiek dat bestaat uit tienduizenden mensen met een mentale beperking en hun begeleiders.  Altijd uitverkocht.','Rock and pop. Evergem near Ghent. This act names as The Scene, Hooverphonic, Triggerfinger, Gabriel Rios, Ozark Henry & Arsenal. Not world-shocking per se. A special feature of this festival is the audience that consists of tens of thousands of people with mental disabilities and their carers. Always sold out.','',NULL),(71,'a52c517cf3c55b86f0fad9d3aacb2ec2','Theater aan zee',0,1,'2012-07-28','2012-08-06','http://www.theateraanzee.be','Oostende',NULL,NULL,NULL,NULL,NULL,NULL,11,1,36000,'Theater, dans, performance, comedy, fotografie, straattheater.  Oostende, de enige stad aan de kust.  Tien dagen lang geeft Oostende vooral kansen aan jong aanstormend talent uit Vlaanderen en Nederland.','Theater, dance, performance, comedy, photography, street. Oostende, the only city on the coast. Ten days gives Oostende opportunities to young talent from Flanders and the Netherlands.','',NULL),(72,'ca26612a464e360a4f7e22c2fb20c3b6','TW Classic',0,1,'2012-06-23','2012-06-23','http://www.twclassic.be','Werchter',NULL,NULL,NULL,NULL,NULL,NULL,8,4,50000,'Rock.  Werchter.  De oude formule van Torhout-Werchter: Ã©Ã©n podium en lekker languit in het gras liggen (toen stonden ze nog niet als haringen op elkaar gepakt).  Jaarlijkse nostalgia en met vaak familiebezoek als resultaat.  Affiche is o.a. Sting, Lenny Kravitz, etc.... 50.000 mensen.','Rock. Werchter. The old formula of Torhout-Werchter: one stage and enjoying good music while stretching out in the grass (when they were not packed together like sardines). Annual nostalgia and often a day out for the entire family. Poster including Sting, Lenny Kravitz, etc. ... 50,000 people.','',NULL),(73,'cdf2d472371f9a05d11d01eb18f8a511','Zomer van Antwerpen',0,1,'2012-07-01','2012-08-31','http://www.zomervanantwerpen.be','Antwerpen',NULL,NULL,NULL,NULL,NULL,NULL,3,1,270000,'Film/Muziek/theater/Circus : een uitbundig, cultureel zomerfeest dat in juli en augustus op de meest onverwachte plekken opduikt en voelbaar is in heel de stad.  Het programma bestaat uit een brede waaier van erg uitlopende voorstellingen over kleine, alledaagse dingen, vaak gratis en toegankelijk voor een breed publiek van alle leeftijden. Een doorgedreven samenwerking met tientallen culturele en niet-culturele partners, stadsdiensten en honderden vrijwilligers zorgt ervoor dat het festival stevig verankerd is in de stad.  DÃ© manier om Antwerpen van een andere kant te zien.  De zomerbar op de Ledeganckkaai is bij goed weer een enorm succes.','Film / Music / Theatre / Circus: an exuberant, cultural summer festival in July and August held in the most unexpected places and is felt throughout the city. The program consists of a wide range of very divergent ideas about small, everyday things, often free and accessible to a wide audience of all ages. An extensive collaboration with dozens of cultural and non-cultural partners, city and hundreds of volunteers ensures that the festival is firmly anchored in the city. The perfect way to view Antwerp through a different lens. The summer bar on the Ledeganckkaai is a huge success when the weather is nice.','',NULL);
/*!40000 ALTER TABLE `festival` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_login`
--

DROP TABLE IF EXISTS `user_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_login` (
  `userId` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `useragent` varchar(64) NOT NULL,
  `ip` varchar(15) NOT NULL,
  KEY `user_login.userId__user.ID` (`userId`),
  CONSTRAINT `user_login.userId__user.ID` FOREIGN KEY (`userId`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_login`
--

LOCK TABLES `user_login` WRITE;
/*!40000 ALTER TABLE `user_login` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_config`
--

DROP TABLE IF EXISTS `game_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_config` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_config`
--

LOCK TABLES `game_config` WRITE;
/*!40000 ALTER TABLE `game_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `game_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `festival_all`
--

DROP TABLE IF EXISTS `festival_all`;
/*!50001 DROP VIEW IF EXISTS `festival_all`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `festival_all` (
  `ID` int(10) unsigned,
  `name` varchar(256),
  `date_start` date,
  `date_stop` date,
  `website` varchar(128),
  `location` varchar(128),
  `fmiv` tinyint(4),
  `fiaf` tinyint(4),
  `genreId` tinyint(4) unsigned,
  `locationTypeId` tinyint(4) unsigned,
  `visitors` int(11) unsigned,
  `description_NL` text,
  `description_EN` text,
  `comments` text,
  `genreName` varchar(32),
  `locationTypeName` varchar(32)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `firstname` varchar(64) DEFAULT NULL,
  `lastname` varchar(64) DEFAULT NULL,
  `timezone` varchar(8) DEFAULT NULL,
  `locale` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL,
  `data` blob,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (15,1,0,'supmagc','male',NULL,'Jelle','Voet','2','nl_NL',NULL,NULL,NULL,'2012-04-12 14:13:02');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `festival_genre`
--

DROP TABLE IF EXISTS `festival_genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `festival_genre` (
  `ID` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `festival_genre`
--

LOCK TABLES `festival_genre` WRITE;
/*!40000 ALTER TABLE `festival_genre` DISABLE KEYS */;
INSERT INTO `festival_genre` VALUES (1,'blues'),(2,'dance'),(3,'divers'),(4,'folk'),(5,'jazz'),(6,'klassiek'),(7,'metal'),(8,'pop'),(9,'reggae'),(10,'rock'),(11,'theater'),(12,'wereld');
/*!40000 ALTER TABLE `festival_genre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `festival_genre_count`
--

DROP TABLE IF EXISTS `festival_genre_count`;
/*!50001 DROP VIEW IF EXISTS `festival_genre_count`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `festival_genre_count` (
  `ID` tinyint(4) unsigned,
  `name` varchar(32),
  `count` bigint(21)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `festival_locationtype_count`
--

DROP TABLE IF EXISTS `festival_locationtype_count`;
/*!50001 DROP VIEW IF EXISTS `festival_locationtype_count`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `festival_locationtype_count` (
  `ID` tinyint(4) unsigned,
  `name` varchar(32),
  `count` bigint(21)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_email`
--

DROP TABLE IF EXISTS `user_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_email` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `email` varchar(128) NOT NULL,
  `verified` tinyint(4) NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `hash_UNIQUE` (`hash`),
  KEY `user_mail.userId__user.ID` (`userId`),
  KEY `user_email.userId` (`userId`),
  CONSTRAINT `user_email.userId` FOREIGN KEY (`userId`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_email`
--

LOCK TABLES `user_email` WRITE;
/*!40000 ALTER TABLE `user_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `festival_locationtype`
--

DROP TABLE IF EXISTS `festival_locationtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `festival_locationtype` (
  `ID` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `festival_locationtype`
--

LOCK TABLES `festival_locationtype` WRITE;
/*!40000 ALTER TABLE `festival_locationtype` DISABLE KEYS */;
INSERT INTO `festival_locationtype` VALUES (1,'stad'),(2,'strand'),(3,'bos'),(4,'wei'),(5,'park'),(6,'zaal');
/*!40000 ALTER TABLE `festival_locationtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `festival_all`
--

/*!50001 DROP TABLE IF EXISTS `festival_all`*/;
/*!50001 DROP VIEW IF EXISTS `festival_all`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`toevla_user`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `festival_all` AS select `f`.`ID` AS `ID`,`f`.`name` AS `name`,`f`.`date_start` AS `date_start`,`f`.`date_stop` AS `date_stop`,`f`.`website` AS `website`,`f`.`location` AS `location`,`f`.`fmiv` AS `fmiv`,`f`.`fiaf` AS `fiaf`,`f`.`genreId` AS `genreId`,`f`.`locationTypeId` AS `locationTypeId`,`f`.`visitors` AS `visitors`,`f`.`description_NL` AS `description_NL`,`f`.`description_EN` AS `description_EN`,`f`.`comments` AS `comments`,`g`.`name` AS `genreName`,`lt`.`name` AS `locationTypeName` from ((`festival` `f` join `festival_genre` `g` on((`f`.`genreId` = `g`.`ID`))) join `festival_locationtype` `lt` on((`f`.`locationTypeId` = `lt`.`ID`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `festival_genre_count`
--

/*!50001 DROP TABLE IF EXISTS `festival_genre_count`*/;
/*!50001 DROP VIEW IF EXISTS `festival_genre_count`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`toevla_user`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `festival_genre_count` AS select `g`.`ID` AS `ID`,`g`.`name` AS `name`,count(`f`.`genreId`) AS `count` from (`festival` `f` join `festival_genre` `g` on((`g`.`ID` = `f`.`genreId`))) group by `f`.`genreId` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `festival_locationtype_count`
--

/*!50001 DROP TABLE IF EXISTS `festival_locationtype_count`*/;
/*!50001 DROP VIEW IF EXISTS `festival_locationtype_count`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`toevla_user`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `festival_locationtype_count` AS select `lt`.`ID` AS `ID`,`lt`.`name` AS `name`,count(`f`.`locationTypeId`) AS `count` from (`festival` `f` join `festival_locationtype` `lt` on((`lt`.`ID` = `f`.`locationTypeId`))) group by `f`.`locationTypeId` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-04-13  9:54:48
