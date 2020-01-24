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

		self.serial = serial.Serial(self.com, self.baud, timeout=0.5)
		
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


#load knihoven
#import ctypes
#hllDll = ctypes.WinDLL ("C:\\xampp\\htdocs\\data_processing\\python\\SMDP_SVRPS.dll")

print("\n\n\t\tSTM")

<<<<<<< HEAD

stm = SerialController("COM5", 115200)

stm.test("0x021080403D300D")
stm.test("<0x02><0x10><0x80><0x40><0x3D><0x30><0x0D>")
#stm.test("\x02\x10\x80\x40\x3D\x30\x0D")
#<STX><ADDR><CMD_RSP>[<DATA>...]<CKSUM1><CKSUM2><CR>
=======
stm.test("\x40")


# 
# stm.test("\x02\x40\r")

# stm.test("\x08\x40\x03")
# stm.test("\x08\x40\r")
>>>>>>> 3d2981811a3e5a947f1050bfb52fae7d5fc74795


# stm.test("\x80@\r")


# 0x02, 0x10, 
# ser.write(serial.to_bytes([0x4C,0x12,0x01,0x00,0x03,0x40,0xFB,0x02,0x7a]))