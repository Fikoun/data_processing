from http.server import BaseHTTPRequestHandler, HTTPServer
from urllib.parse import urlparse
import json

import serial
import time
import datetime
from pprint import pprint

class SerialController():
	def __init__(self, com, baud):
		self.com = com
		self.baud = baud
		self.serial = None
		self.connect()


	def connect(self):
		print("Connecting...")

		if self.serial is not None:
			self.serial.close()

		time.sleep(1)

		self.serial = serial.Serial(self.com, self.baud, timeout=0.5)
		
		if self.serial.isOpen():
			print("Successfully connected.")
			return True
		else:
			print("! Not connected to device !")
			return False


	def serialSend(self, command, read=False):
		if self.serial.isOpen():
			self.serial.write(bytes(command + "\n", 'utf-8'))
		else:
			if self.connect():
				self.serial.write(bytes(command + "\n", 'utf-8'))
			else:
				print(command + " not sent !")
				return False

		if self.serial.isOpen() and read:
			return self.serial.readline().decode()


	def test(self):
		print("\tFirmware:\t", self.serialSend("*IDN?", True), "\n")


	def setVolt(self, channel, v=0):
		self.serialSend(f"VSET{channel}:{v}")
		print(f"Voltage set to [{v} V] on channel [CH{channel}]")

	def setAmp(self, channel, i=0):
		self.serialSend(f"ISET{channel}:{i}")
		print(f"Current set to [{i} I] on channel [CH{channel}]")

	def setCh(self, channel, v=0,i=0):
		self.setVolt(channel, v)
		self.setAmp(channel, i)
		#print(f"\n\tChannel [CH{channel}]\n\t   Voltage: {v}\tV\n\t   Current: {i}\tI")

	def getTempTime(self):
		# Get temperature and time
		temp = self.serialSend("C\r", True)
		time = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		print(f"At {time} thermocouple returned: {temp}")

		# Clean up returned string
		temp = temp.replace('\n','').replace('\r', '').replace('>','')
		return temp, time

	def output(self, value=False):
		self.serialSend(f"OUT{int(value)}")


class RequestHandler(BaseHTTPRequestHandler):
	def do_GET(self):
		values = self.path.split("/")
		self.send_response(200)
		self.end_headers()
		# For supply control use "set" command
		if self.path.startswith("/set"):
			

			if self.path.startswith("/setV"):
				supply.setVolt(int(values[2]), float(values[3]))
			elif self.path.startswith("/setI"):
				supply.setAmp(int(values[2]), float(values[3]))

			self.wfile.write(json.dumps({
				'CH': int(values[2]),
				'Voltage': float(values[3]),
			}).encode())

		# For thermocouple control use "temp" command
		if self.path.startswith("/get"):
			
			temp, time = thermocouple.getTempTime()

			self.wfile.write(json.dumps({
				'variables': {'temperature': temp},
				'time': time,
			}).encode())

		return
	
if __name__ == '__main__':
	# Setup power supply
	print("\n\n\t\tPOWER SUPPLY")
	supply = SerialController("COM4", 9600)
	supply.test()
	# Disable output and reset channels
	supply.output()
	supply.setCh(1)
	supply.setCh(2)


	# Setup themocouple
	print("\n\n\t\tTHERMOCOUPLE")
	thermocouple = SerialController("COM3", 38400)
	thermocouple.getTempTime()
	
	
	# Setup Http Server
	server = HTTPServer(('127.0.0.1', 8080), RequestHandler)
	print('\nStarting server at http://localhost:8080\n\n>')
	server.serve_forever()	