############################## Requirement ########################################


This Address grab tools require only PHP CURL lib installed.
This lib is available on most of the hosting.


############################## How this works #######################################

1. It gets input from user
2. Browse index page of yahoo/hotmail/gmail
3. Save cookies in a file and remember that file. File name is uniqe so that it can handle multiple 
   instance same time.
4.Use those cookies in that file to browse inside pages. If inside page require any new cookie, then it also
  save those in that file.
5.Parse data from Address pages

############################## Installation #######################################

1. Place all file within same directory
2. Change permission of the /temp directory to 755 or if you are FreeBSD then change permission of that directory to 777

Thats all of installation


===========================Describtion of the folders:===========================================
/temp : Used for saving temporary cookies and it delete cookies after execution of script
/includes: Contains file that importer includes


===========================Describtion of the files:=============================================
Readme.txt- Installation instruction.
index.php- That user see, it include gmail.php/aol.php/yahoo.php/hotmail.php/126.php/163.php/daum.php/fastmail.php/indiatimes.php/interia.php/libero.php/mac.php/lycos.php/linkedin.php/maildotcom.php/mailru.php/mynet.php/qq.php/rambler.php/rediff.php/sina.php/web.php/yandex.php dynamically depending on which type of webmail user selected. Feel free to change it or see how this work in code and implement its code in your application.
gmail.php- gmail importer engine. Dont change code of it.Change is not recommended.(Available if you have purchased gmail importer)
yahoo.php- yahoo importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased yahoo importer)
aol.php- Aol importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased aol importer)
hotmail.php- Hotmail importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased hotmail importer)
msn.php- Msn.com importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased msn.com importer)
grab_globals.lib.php- Grab global variables in case PHP is set to global variables is off. 
XMLParser.php- Contains code for parsing XML. 

126.php- 126 importer engine. Dont change code of it.Change is not recommended.(Available if you have purchased 126 importer)
163.php- 163 importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased 163 importer)
daum.php- daum importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased daum importer)
fastmail.php- fastmail importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased fastmail importer)
indiatimes.php- indiatimes.com importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased indiatimes importer)


interia.php- interia importer engine. Dont change code of it.Change is not recommended.(Available if you have purchased interia importer)
libero.php- libero importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased libero importer)
mac.php- mac importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased mac importer)
lycos.php- lycos importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased lycos importer)
linkedin.php- linkedin.com importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased linkedin.com importer)


maildotcom.php- maildotcom importer engine. Dont change code of it.Change is not recommended.(Available if you have purchased maildotcom importer)
mailru.php- mailru importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased mailru importer)
mynet.php- mynet importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased mynet importer)
qq.php- qq importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased qq importer)
rambler.php- rambler.com importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased rambler importer)


rediff.php- rediff importer engine. Dont change code of it.Change is not recommended.(Available if you have purchased rediff importer)
sina.php- sina importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased sina importer)
web.php- web importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased web importer)
yandex.php- yandex importer engine.Dont change code of it.Change is not recommended.(Available if you have purchased yandex importer)
rediffXMLParser.php- Contains code for parsing XML. 

All importer engine class containing three common functions, they are 
login($username,$password); Used for login
get_address_page(); Used to get address page
parser($str); Used to retreive contacts in two array, they are $email_array and $name_array


===========================Update process:=============================================
When an webmail change then we have to update our importer, we update that webmail's contact importer engine and send that to client. If hotmail.com change then we update hotmail.php and send that. Thats why we dont recommend you to change code for hotmai.php/gmail.php/yahoo.php/msn.php or aol.php.

===========================Update process:=============================================
Put your http://www.decaptcher.com/client/ auth in config.php
in DCaptcha_User, DCaptcha_Pass
