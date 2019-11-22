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


	def serialSend(self, command=False, read=False):
		if self.serial.isOpen() or command != False:
			#self.serial.write(bytes(command, 'ascii'))
			self.serial.write(command)
		
		time.sleep(1)
		#print(self.serial.read(8).decode('ascii'))
		out = self.serial.readline()
		return out.decode("ascii")


	def test(self, mess):
		print("\tTEST:\t", self.serialSend(mess, True), "\n")


stm = SerialController("COM7", 9600)

print("OUTPUT1: ", stm.serialSend())
print("OUTPUT2: ", stm.serialSend())
print("OUTPUT3: ", stm.serialSend())
print("OUTPUT4: ", stm.serialSend())
print("OUTPUT5: ", stm.serialSend())
print("OUTPUT6: ", stm.serialSend())
print()
