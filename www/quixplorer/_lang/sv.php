<?php
/*
	sv.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of Quixplorer (http://quixplorer.sourceforge.net).
	Authors: quix@free.fr, ck@realtime-projects.com.
	The Initial Developer of the Original Code is The QuiX project.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer.
	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
// Swedish Language Module

$GLOBALS["charset"] = "UTF-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y/m/d H:i";
$GLOBALS["error_msg"] = array(

	// error
	"error"			=> "FEL(S)",
	"back"			=> "Tillbaka",

	// root
	"home"			=> "Hemkatalogen finns ej, kontrollera dina isntällningar.",
	"abovehome"		=> "Aktuell katalog kan inte vara ovanför hemkatalogen.",
	"targetabovehome"	=> "Målkatalogen kan inte vara ovanför hemkatalogen.",

	// exist
	"direxist"		=> "Denna katalog finns ej.",
	"fileexist"		=> "Denna fil finns inte.",
	"itemdoesexist"		=> "Detta objekt finns redan.",
	"itemexist"		=> "Detta objekt finns inte.",
	"targetexist"		=> "Målkatalogen finns .",
	"targetdoesexist"	=> "Målobjektet finns redan.",

	// open
	"opendir"		=> "Kan inte öppna katalog.",
	"readdir"		=> "Kan inte läsa katalog.",

	// access
	"accessdir"		=> "Du har inte rättigheter att komma åt denna katalog.",
	"accessfile"		=> "Du har inte rättigheter att komma åt denna fil.",
	"accessitem"		=> "Du har inte rättigheter att komma åt detta objekt.",
	"accessfunc"		=> "Du har inte rättigheter att använda denna funktion.",
	"accesstarget"		=> "Du har inte rättigheter att komma åt målkatalog.",

	// actions
	"chmod_not_allowed"	=> 'Ändring av rättigheter till NONE är inte tillåtet!',
	"permread"		=> "Läsning av rättighter misslyckades.",
	"permchange"		=> "Ändring av rättigheter misslyckades.",
	"openfile"		=> "Öppning av fill misslyckades.",
	"savefile"		=> "Misslyckades att spara filen.",
	"createfile"		=> "Misslyckades att skapa filen.",
	"createdir"		=> "Misslyckades att skapa katalog.",
	"uploadfile"		=> "Uppladdning av fil misslyckades.",
	"copyitem"		=> "Kopiering misslyckades.",
	"moveitem"		=> "Flytt misslyckades.",
	"delitem"		=> "Borttagning misslyckades.",
	"chpass"		=> "Ändring av lösenord misslyckades.",
	"deluser"		=> "Misslyckades att ta bort användare.",
	"adduser"		=> "Misslyckades att lägga till användare.",
	"saveuser"		=> "Misslyckades att spara användare.",
	"searchnothing"		=> "Du måste ange något att söka efter.",

	// misc
	"miscnofunc"		=> "Funktion saknas.",
	"miscfilesize"		=> "Filen överskrider maxstorlek.",
	"miscfilepart"		=> "Filen endast delvis uppladdad.",
	"miscnoname"		=> "Du måste ange ett namn.",
	"miscselitems"		=> "Du har inte valt något (några) objekt.",
	"miscdelitems"		=> "Är du säker på att du vill ta bort dessa \"+num+\" objekt?",
	"miscdeluser"		=> "Är du säker på att du vill ta bort anvvändaere '\"+user+\"'?",
	"miscnopassdiff"	=> "Det nya löseordet skiljer sig inte från det gamla.",
	"miscnopassmatch"	=> "Lösenorden matchar inte.",
	"miscfieldmissed"	=> "Du missade ett viktigt fält.",
	"miscnouserpass"	=> "Användarnamn eller lösenord är felaktigt.",
	"miscselfremove"	=> "Du kan inte ta bort dig själv.",
	"miscuserexist"		=> "Användaren finns redan.",
	"miscnofinduser"	=> "Hittar inte användaren.",
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "ÄNDRA RÄTTIGHETER",
	"editlink"		=> "ÄNDRA",
	"downlink"		=> "LADDA NER",
	"download_selected"	=> "LADDA NER VALDA FILER",
	"uplink"		=> "UPP",
	"homelink"		=> "HEM",
	"reloadlink"		=> "LADDA OM",
	"copylink"		=> "KOPIERA",
	"movelink"		=> "FLYTTA",
	"dellink"		=> "TA BORT",
	"comprlink"		=> "ARKIVERA",
	"adminlink"		=> "ADMIN",
	"logoutlink"		=> "LOGGA UT",
	"uploadlink"		=> "LADDA UPP",
	"searchlink"		=> "SÖK",
	"unziplink"		=> "PACKA UPP",

	// list
	"nameheader"		=> "Namn",
	"sizeheader"		=> "Storlek",
	"typeheader"		=> "Typ",
	"modifheader"		=> "Ändrad",
	"permheader"		=> "Rättigheter",
	"actionheader"		=> "Åtgärder",
	"pathheader"		=> "Sökväg",

	// buttons
	"btncancel"		=> "Avbryt",
	"btnsave"		=> "Spara",
	"btnchange"		=> "Ändra",
	"btnreset"		=> "Återställ",
	"btnclose"		=> "Stäng",
	"btncreate"		=> "Skapa",
	"btnsearch"		=> "Sök",
	"btnupload"		=> "Ladda upp",
	"btncopy"		=> "Kopiera",
	"btnmove"		=> "Flytta",
	"btnlogin"		=> "Logga in",
	"btnlogout"		=> "Logga ut",
	"btnadd"		=> "Lägg till",
	"btnedit"		=> "Ändra",
	"btnremove"		=> "Ta bort",
	"btnunzip"		=> "Packa upp",

	// actions
	"actdir"		=> "Katalog",
	"actperms"		=> "Ändra rättigheter",
	"actedit"		=> "Ändra fil",
	"actsearchresults"	=> "Sökresultat",
	"actcopyitems"		=> "Kopiera objekt",
	"actcopyfrom"		=> "Kopiera från/%s till /%s ",
	"actmoveitems"		=> "Flytta objekt",
	"actmovefrom"		=> "Flytta objekt från /%s till /%s ",
	"actlogin"		=> "Logga in",
	"actloginheader"	=> "Logga in för att använda Filhanteraren",
	"actadmin"		=> "Administration",
	"actchpwd"		=> "Ändra lösenord",
	"actusers"		=> "Användare",
	"actarchive"		=> "Arkivera objekt",
	"actunzipitem"		=> "Packar upp",
	"actupload"		=> "Ladda upp fil(er)",

	// misc
	"miscitems"		=> "Objekt",
	"miscfree"		=> "Ledigt",
	"miscusername"		=> "Användarnamn",
	"miscpassword"		=> "Lösenord",
	"miscoldpass"		=> "Gammalt lösenord",
	"miscnewpass"		=> "Nytt läsenord",
	"miscconfpass"		=> "Bekräfta lösenord",
	"miscconfnewpass"	=> "Bekräfta nytt lösenord",
	"miscchpass"		=> "Byt lösenord",
	"mischomedir"		=> "Hemkatalog",
	"mischomeurl"		=> "Hem URL",
	"miscshowhidden"	=> "Visa dolda objekt",
	"mischidepattern"	=> "Göm mönster",
	"miscperms"		=> "Rättigheter",
	"miscuseritems"		=> "(namn, hemkatalog, visa dolda objekt, rättigheter, aktiva)",
	"miscadduser"		=> "lägg till användare",
	"miscedituser"		=> "ändra användare'%s'",
	"miscactive"		=> "Aktiv",
	"misclang"		=> "Språk",
	"miscnoresult"		=> "Inga resultat tillgängliga.",
	"miscsubdirs"		=> "Sök i underkataloger",
	"miscpermissions"	=> array(
					"läs"		=> array("Läs", "Användaren får läsa och ladda ner filen"),
					"create" 	=> array("Skriv", "Användaren får skapa en ny fil"),
					"ändra"		=> array("Ändra", "Användaren får ändra och ladda upp en extiterande fil"),
					"ta bort"	=> array("Ta bort", "Användaren får ta bort en fil"),
					"lösenord"	=> array("Ändra lösenord", "Användare får ändra lösenordet"),
					"admin"		=> array("Administratör", "Fulla rättigheter"),
			),
	"miscyesno"		=> array("Ja","Nej","J","N"),
	"miscchmod"		=> array("Ägare", "Grupp", "Publik"),
);

?>