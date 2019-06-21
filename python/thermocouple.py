import serial
import mysql.connector
import time as t
import datetime
f = '%Y-%m-%d %H:%M:%S'

mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  passwd="",
  database="data_processing"
)

db = mydb.cursor()


serial = serial.Serial("COM3", 9600, timeout=1)


for i in range(0,60):
    serial.write(bytes("C\r", 'utf-8'))
    temp = serial.readline().decode()
    t.sleep(1);
    #print("value: ", temp)
    temp = temp.replace('\n','').replace('\r', '').replace('>','')
    time = datetime.datetime.now().strftime(f)
    try:
        db.execute(f"INSERT INTO `data` (`measurement_id`, `type`, `value`, `created_at`, `updated_at`) VALUES ('1', 'temp', '{temp}', '{time}', '{time}');")
        mydb.commit()
     
    except mysql.connector.Error as err:
        print(err)
        print("Error Code:", err.errno)
        print("SQLSTATE", err.sqlstate)
        print("Message", err.msg)

serial.close()