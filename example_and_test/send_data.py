import datetime
import time
import random
import requests
#insert into farm_data(farm_token,ph_value,temperature,salinity,light_intensity,time) values ("testtoken",1,2,3,4,'2024-09-01  00-00-00')


#("testtoken",random.randint(10),random.randint(20),random.randint(30),random.randint(40),'2024-09-01  00:00:00')

import sys

orig_stdout = sys.stdout
f = open('out.txt', 'w')




fromtime = -31536000


def createsql():
    sys.stdout = f
    global fromtime
    orig_stdout = sys.stdout
    f = open('out.txt', 'w')
    sys.stdout = f
    print("insert into farm_data(farm_token,ph_value,temperature,salinity,light_intensity,time) values")
    while fromtime < 0 :
        datime = datetime.datetime.now(datetime.timezone(datetime.timedelta(seconds=28800))) + datetime.timedelta(seconds=fromtime)
        strtime = datime.strftime("%Y-%m-%d  %H:%M:%S")
        print(f"""("testtoken", """ ,random.randint(0,20) , "," ,  random.randint(20,40) ,"," ,random.randint(40,60),",",random.randint(80,100), ","  ,"'"  +strtime  + "')" , ",")
        fromtime += 60
    print(f"""("testtoken", """ ,random.randint(0,20) , "," ,  random.randint(20,40) ,"," ,random.randint(40,60),",",random.randint(80,100), ","  ,"'"  +strtime  + "')" , ";")
    sys.stdout = orig_stdout

'''
 $farmToken = trim($_POST['farm_token']);
    $phValue = trim($_POST['ph_value']);
    $temperature = trim($_POST['temperature']);
    $salinity = trim($_POST['salinity']);
    $lightIntensity = trim($_POST['light_intensity']);
'''

def simulator(url = "https://smartseaweed.site/Real/api.php"):
#def simulator(url = "http://localhost/Real/api.php"):
    dadata= data={"farm_token" : "8a2dcd67646081ef53ed4b21958c57f4" , "ph_value" : random.randint(8,9) , "temperature" : random.randint(28,32)  , "salinity" : random.randint(32,33) , "light_intensity" : random.randint(29,50), "sonic_distance" : random.randint(29,50)} #SAFE VALUE
    #dadata= data={"farm_token" : "6a208f040cc1e8f29e87ce75263186f4" , "ph_value" : random.randint(1,7) , "temperature" : random.randint(28,32)  , "salinity" : random.randint(32,33) , "light_intensity" : random.randint(29,50)} #UNSAFE VALUE
    r = requests.post(url,data=dadata)
    print("Sending:" , dadata)
    print(r.text)

while True:
   time.sleep(5)
   simulator()
