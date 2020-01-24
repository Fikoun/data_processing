import serial
import time
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

		time.sleep(500)

		self.serial = serial.Serial(self.com, self.baud, timeout=1)
		
		if self.serial.isOpen():
			print("Successfully connected.")
			return True
		else:
			print("! Not connected to device !")
			return False


	def serialSend(self, command, read=False):
		if self.serial.isOpen():
			self.serial.write(bytes(command, 'ascii'))
		
		#print(self.serial.read(8).decode('ascii'))
		return self.serial.readline().decode()



	def test(self, mess):
		start = "\x02"
		command = "\x10\x80" + mess
		end = "\x0D"

		checksum = hex(sum(command.encode('ascii')) % 256)

		print("\tTEST:\t", self.serialSend(start + command + checksum + end, True), "\n")


 print("\n\n\t\tSTM")
 stm = SerialController("COM5", 9600)
 stm.test("\x40")# stm.test("\x02\x40\r")
# stm.test("\x08\x40\x03")
# stm.test("\x08\x40\r")
# stm.test("\x80@\r")
# 0x02, 0x10, 
# ser.write(serial.to_bytes([0x4C,0x12,0x01,0x00,0x03,0x40,0xFB,0x02,0x7a]))