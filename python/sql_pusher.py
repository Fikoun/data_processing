from time import sleep
import mysql.connector
import requests

url = "http://127.0.0.1:8080/get"

mydb = mysql.connector.connect(
	host="localhost",
	user="root",
	passwd="",
	database="data_processing"
)
db = mydb.cursor()	

def interval(delay):
	r = requests.get(url=url)

	data = r.json()

	print(data)

	temp = data["variables"]["temperature"]
	time = data["time"]
	try:
		db.execute(f"INSERT INTO `data` (`measurement_id`, `type`, `value`, `created_at`, `updated_at`) VALUES ('1', 'temp', '{temp}', '{time}', '{time}');")
		mydb.commit()
	except mysql.connector.Error as err:
		print(err)
		print("Error Code:", err.errno)
		print("SQLSTATE", err.sqlstate)
		print("Message", err.msg)
	sleep(delay)
	interval(delay)

interval(2)
