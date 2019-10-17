import serial
import time
import struct
import datetime

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

		self.serial = serial.Serial(self.com, self.baud, timeout=1)
		
		if self.serial.isOpen():
			print("Successfully connected.")
			return True
		else:
			print("! Not connected to device !")
			return False


	def serialSend(self, command, read=False):
		if self.serial.isOpen():
			#self.serial.write(bytes(command, 'ascii'))
			self.serial.write(command)
		
		#print(self.serial.read(8).decode('ascii'))
		out = self.serial.readline()
		print(out)
		return struct.unpack('>HH', out)


	def test(self, mess):
		print("\tTEST:\t", self.serialSend(mess, True), "\n")


stm = SerialController("COM6", 9600)

print("OUTPUT1: ", stm.serialSend(1))
print("OUTPUT2: ", stm.serialSend(2))
print("OUTPUT3: ", stm.serialSend(3))
print("OUTPUT4: ", stm.serialSend(51))
print("OUTPUT5: ", stm.serialSend(51))
print("OUTPUT6: ", stm.serialSend(41))
print()
