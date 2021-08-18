import requests
import facebook
import sys
import mysql.connector
import twitter
#from googleapiclient.discovery import build
print('Imports Loaded Succesfully')
import os
from googleapiclient.discovery import build

#FB Test Token: EAAN9EnvAyBcBADaMZCXMAzlezUtmFyZCwSBZCUZBGt35IYuLs0bpyYLbZCusIhsd7ep6VCKu7fZBb65AHklZCdHuxydZAiN19ZAZC5VVHXZAppl5UJxjdjHouZBZBrs6uhr7oINndZCeDNgZCZC31zZAZBBKRIsw5JQiOvYDDrdn8I2IoBDlkoGU2xODAFHYVC8QdH8SooJ5R0Br6dvSlDLUkvIs96VYZAZCHc5TM3VIVKAZD
#Google Test Token: ya29.a0AfH6SMBix4I7_5qOxl8enbCpWiJvpwCSqxh6QCqGTCLYRbVM1R_8hd1FkEtpp-jfQMhMMlf_FL3JhyRCAaL_wNVvDi-O-dQxAvcTwc1Xea2SlfKT__Dz5tSja6RreLIEXcwzwo-CaWHmu02t1SkPr0U-F1_z
#Twitter Test Token: 1397303523973472257-HV2ub31hrUsx0OAMaiqumYYN3XdJ9Y

# facebook_access_token = sys.argv[1]
# google_access_token = sys.argv[2]
# twitter_access_token = sys.argv[3]
facebook_access_token = 'EAAN9EnvAyBcBADaMZCXMAzlezUtmFyZCwSBZCUZBGt35IYuLs0bpyYLbZCusIhsd7ep6VCKu7fZBb65AHklZCdHuxydZAiN19ZAZC5VVHXZAppl5UJxjdjHouZBZBrs6uhr7oINndZCeDNgZCZC31zZAZBBKRIsw5JQiOvYDDrdn8I2IoBDlkoGU2xODAFHYVC8QdH8SooJ5R0Br6dvSlDLUkvIs96VYZAZCHc5TM3VIVKAZD'
#---------MySql--------------------------------
mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  password="L1Zab8BrltFoaBm5"
)

if mydb: 
	print('Connected Succesfully Connected To Travlon DB')
	mycursor = mydb.cursor()
	mycursor.execute("SHOW DATABASES")
	for x in mycursor:
		print(x)
else: 
	print('SQL Connection Error')
#------------------------------------------------

#------------Facebook--------------------------
graph = facebook.GraphAPI(access_token=facebook_access_token, version="3.1")
posts = graph.get_all_connections(id='me', connection_name='posts')

# Person = graph.get_object(id='298595051864785')
# comments = graph.get_all_connections(id='298595051864785', connection_name='user_posts ')
posts = list(posts)
print(posts)

postArray = []
for post in posts:
  print(post['id'])
  postArray.append(post['id'])
  
print(postArray)  	
chk = graph.get_objects(ids=postArray, fields='comments')
print(chk)





#-----------------------------------------------------------------

#------------Twitter---------------------------------------------------------------------------
# api = twitter.Api(consumer_key='1397303523973472257-HV2ub31hrUsx0OAMaiqumYYN3XdJ9Y',
#                   consumer_secret= 'f6ursuh6f88rQeP7QeN08qDWS7mWwM7tfmEG9C1Cykvop',
#                   access_token_key= '8lNNROhXqwS6FR1E9AtekTdVM',
#                   access_token_secret= 'r9y3LJB5J25DQVsWQA8TCoaDnGnS4jIl5QhxjEGTOglAexiQyW')
# print(api)
#----------------------------------------------------------------------------------------------

#------------Google--------------------------
# api_key = 'AIzaSyC-F4Tm7Sy1NMmwdBt_A0EaF9tq9HFUywc'  #---- ConverT To Environment variable
# youtube = build("youtube", "v3", developerKey=api_key)
# request = youtube.channels().list(
#         part="contentDetails,statistics,brandingSettings,contentDetails,contentOwnerDetails,id,localizations,snippet,statistics,status,topicDetails",
#         id="1111600480527255217372"
#     )
# response = request.execute()
# print(response)


# request = youtube.comments().list(
#         part="snippet.channelId,authorChannelUrl,id",
#         id="111160048057255217372"
#     )
# response = request.execute()
# print(response)

# request = youtube.comments().list(
#     part = [snippet,replies],

#     )
# response = request.execute()

#------------------------------------------------------

# -*- coding: utf-8 -*-

# Sample Python code for youtube.channels.list
# See instructions for running these code samples locally:
# https://developers.google.com/explorer-help/guides/code_samples#python



