from http.server import BaseHTTPRequestHandler, HTTPServer
from urllib.parse import urlparse
import json
import os
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

		if read:
			return self.serialRead();

	def serialRead(self):
		if self.serial.isOpen():
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

	def serialSendPacket(self, command, read=False):
		if self.serial.isOpen():
			self.serial.write(command)
		
		#print(self.serial.read(8).decode('ascii'))
		return bytearray(self.serial.readline())

	def composePacket(self, char):
		message = bytes(char, 'ascii')[0]

		hexsum = hex(0x10 + 0x80 + message)
		cksum1 = (eval( '0x3' + hexsum[2] ))
		cksum2 = (eval( '0x3' + hexsum[3] ))

		# Composing a packet
		packet = bytearray()
		packet.append(0x02) # is always 0x02
		packet.append(0x10) # is always 0x10 for STM-2
		packet.append(0x80) # is always 0x80 when sending a command to STM-2
		
		packet.append(message)

		packet.append(cksum1)
		packet.append(cksum2)

		packet.append(0x0D) # is always 0x0D

		return packet

	def readPacket(self, packet):
		return packet[3:-3].decode("ascii") 

	def getFrequency(self):
		command = self.composePacket("U")
		response = self.serialSendPacket(command, True)
		return self.readPacket(response)


class RequestHandler(BaseHTTPRequestHandler):
	def do_GET(self):
		values = self.path.split("/")
		self.send_response(200)
		self.end_headers()

		if self.path.startswith("/stop"):
			exit()

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

		# For getting data use "get"
		if self.path.startswith("/get"):
			temp, time = thermocouple.getTempTime()
			frequency = stm.getFrequency()
			preassure = preassure_meter.serialRead()

			self.wfile.write(json.dumps({
				'variables': {
						'temperature': temp,
						'frequency': frequency,
						'preassure'  : preassure
				},
				'time': time,
			}).encode())

		# For status check
		if self.path.startswith("/status"):
			self.wfile.write(json.dumps({
				'status': True
			}).encode())

		return
	
if __name__ == '__main__':
	# Setup power supply

	print(os.getpid())
	
	#print("\n\n\t\tPOWER SUPPLY")
	#supply = SerialController("COM4", 9600)
	#supply.test()
	# Disable output and reset channels
	#supply.output()
	#supply.setCh(1)
	#supply.setCh(2)


	# Setup themocouple
	print("\n\n\t\tTHERMOCOUPLE")
	thermocouple = SerialController("COM3", 38400)
	thermocouple.getTempTime()


	# Setup preassure
	print("\n\n\t\tPRESSURE")
	preassure_meter = SerialController("COM7", 9600)
	preassure_meter.serialRead()

	# Setup preassure
	print("\n\n\t\tSTM")
	stm = SerialController("COM5", 115200)
	stm.getFrequency()
	
	
	# Setup Http Server
	server = HTTPServer(('127.0.0.1', 8080), RequestHandler)
	print('\nStarting server at http://localhost:8080\n\n>')
	server.serve_forever()	