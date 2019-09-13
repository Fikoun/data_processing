import time
import mysql.connector
import urllib.request, json 
url = "localhost:8080/get"

url = urllib.request.urlopen("http://maps.googleapis.com/maps/api/geocode/json?address=google")

data = json.loads(url.read().decode())

print(data)

mydb = mysql.connector.connect(
	host="localhost",
	user="root",
	passwd="",
	database="data_processing"
)
	
temp = 7
time = datetime.datetime.now().strftime(f)
try:
	db.execute(f"INSERT INTO `data` (`measurement_id`, `type`, `value`, `created_at`, `updated_at`) VALUES ('1', 'temp', '{temp}', '{time}', '{time}');")
	mydb.commit()
except mysql.connector.Error as err:
	print(err)
	print("Error Code:", err.errno)
	print("SQLSTATE", err.sqlstate)
	print("Message", err.msg)

def interval(delay):

	time.sleep()
	interval()

interval(4)
