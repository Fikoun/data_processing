import mysql.connector
import requests
import sys
import time


url = "http://127.0.0.1:8080/get"

mydb = mysql.connector.connect(
	host="localhost",
	user="root",
	passwd="",
	database="data_processing"
)
db = mydb.cursor()	

start_time = time.time()
measurement_id = sys.argv[1]
max_counter = int(sys.argv[2])

def saveData(measurement_id, typ, value, timestamp):
	try:
		db.execute(f"INSERT INTO `data` (`measurement_id`, `type`, `value`, `created_at`, `updated_at`) VALUES ('{measurement_id}', '{typ}', '{value}', '{timestamp}', '{timestamp}');")
		mydb.commit()
	except mysql.connector.Error as err:
		print(err)
		print("Error Code:", err.errno)
		print("SQLSTATE", err.sqlstate)
		print("Message", err.msg)
	pass


def interval(delay):
	global measurement_id, max_counter, start_time

	if (time.time() - start_time) >= max_counter:
		db.execute(f"UPDATE `measurements` SET status='done' WHERE id='{measurement_id}';")
		mydb.commit()
		return

	r = requests.get(url=url)
	data = r.json()

	print(data)

	timestamp = data["time"]

	# TEMPERATURE
	saveData(measurement_id, "temp", data["variables"]["temperature"], timestamp)
	
	# PREASSURE
	saveData(measurement_id, "press", data["variables"]["frequency"], timestamp)
	
	time.sleep(delay)
	interval(delay)


interval(1)
