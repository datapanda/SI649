from HTMLParser import HTMLParser
from bs4 import *
import re
import time
import mysql.connector
from mysql.connector import errorcode
from collections import defaultdict
import sys
import glob # used to import the file directories
import os # used to import the file directories
####################################
####
#### Working code that will loop through the files and extract the data
####
####
####################################
dataSource = [] # Creates a list to store all of the file names

####################################
####
#### Loops through the directories and stores the file names into the list
####
####################################
os.chdir("directory path to data files")
for files in glob.glob("*.html"):
	print files
	dataSource.append(files)

print dataSource

####################################
####
#### Begin Database Connection
####
####################################

try:
	cnx = mysql.connector.connect(user="infovis2", password="infovis2", host="localhost", port=8889, database="infovis2")

	cursor = cnx.cursor()	

	for files in dataSource:
		logs = open("directory path to data files" + files)
		files = re.sub(".html", "", files)

	########## Diagnostic Code #######################
	##
	## 	alter table conversations AUTO_INCREMENT = 1
	##
	##################################################

		print "**************** Dates and Times for " + files # used as a seperator between chat logs
		for lines in logs.readlines():
			if re.match(r".+class=\"event\"", lines):
				continue
			elif re.match(r".+class=\"time\"", lines):
				lines = lines.replace("<tr><td colspan=\"2\" class=\"time\">", "") # Strips the beginning of the date tag
				lines = lines.replace("</td></tr>", "") # Strips the ending of the date tag
				lines = lines.replace(",", "") #strips the "," from the list
				lines = lines.rstrip()
		 		lines = time.strptime(lines, "%A %B %d %Y") # Converts the strings into a time object

		 		year = lines.tm_year
				month = lines.tm_mon
				day = lines.tm_mday
				weekday = lines.tm_wday
		 		print "Year: " + str(lines.tm_year) + " Month: " + str(lines.tm_mon) + " Day: " + str(lines.tm_mday) + " Week Day " + str(lines.tm_wday) # weekdays start at 0 with Monday
		 		#addData = ("INSERT INTO chatlogs (id, username, year, month, day, weekday) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')" % (None, files, year, month, day, weekday))
	 			#cursor.execute(addData)
	 			#cnx.commit()
			########## Diagnostic Code #######################
			##
			##	#print "Year: " + str(lines.tm_year) + " Month: " + str(lines.tm_mon) + " Day: " + str(lines.tm_mday) + " Week Day " + str(lines.tm_wday) # weekdays start at 0 with Monday
			##	#addData = ("INSERT INTO chatlogs (id, username, year, month, day, weekday) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')" % (None, files, year, month, day, weekday))
			## 	#cursor.execute(addData)
			## 	#cnx.commit()
			##
			##################################################
			elif re.match(r".+class=\"remote\"", lines):

				lines = lines.replace("<tr><td class=\"remote\">", "")
				lines = re.sub(r"^.+?;\(", "", lines) # Strips out the username
				lines = re.sub(r"Me86", " ", lines) # Strips out the username
				
				pos = lines.find(":<") #Strips the end of the tag, the entire conversation
				lines = lines[:pos]
				lines = lines.lower()
				
				lines = lines.strip()
				lines = lines.replace("&#160;", "")
				lines = lines.replace("):", "")
				lines = lines.replace(")", "")
				lines = lines.replace("(", " ")
				lines = lines.replace(":", " ")
				lines = lines.replace("pm", " ")
				lines = lines.replace("am", " ")
				lines = lines.lstrip()
				lines = lines.rstrip()

				lines = time.strptime(lines, "%H %M %S")
				print "Them -> Hours: " + str(lines.tm_hour) + " Minutes: " + str(lines.tm_min) + " Seconds: " + str(lines.tm_sec)

				### Variables

				hour = lines.tm_hour
				minute = lines.tm_min
				second = lines.tm_sec

				addDataTimeThem = ("INSERT INTO conversations (id, username, hour, minute, second, year, month, day, weekday) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')" % (None, files, hour, minute, second, year, month, day, weekday))
				cursor.execute(addDataTimeThem)
				cnx.commit()
				
			elif re.match(r".+class=\"local\"", lines):
				lines = lines.replace("<tr><td class=\"local\">", "")
				lines = re.sub(r"^.*?;", "", lines) # Strips out the username
				pos = lines.find(":<")
				lines = lines[:pos]
				lines = lines.lower()
				lines = lines.replace("&#160;", "")
				lines = lines.replace("):", "")
				lines = lines.replace(")", "")
				lines = lines.replace("(", "")
				lines = lines.replace(":", " ")
				lines = lines.replace("pm", " ")
				lines = lines.replace("am", " ")
				lines = lines.lstrip()
				lines = lines.rstrip()
				lines = time.strptime(lines, "%H %M %S")
				print "Me -> Hours: " + str(lines.tm_hour) + " Minutes: " + str(lines.tm_min) + " Seconds: " + str(lines.tm_sec)

				### Variables

				hour = lines.tm_hour
				minute = lines.tm_min
				second = lines.tm_sec

				addDataTimeMe = ("INSERT INTO conversations (id, username, hour, minute, second, year, month, day, weekday) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')" % (None, "Me", hour, minute, second, year, month, day, weekday))
				cursor.execute(addDataTimeMe)
				cnx.commit()

except mysql.connector.Error as err:
	if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
		print("Something is wrong your username or password")
	elif err.errno == errorcode.ER_BAD_DB_ERROR:
		print("Database does not exists")
	else:
		print(err)
else:
	cursor.close()
	cnx.close()	