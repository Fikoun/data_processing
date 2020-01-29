import serial
import time
import datetime
from pprint import pprint

def append_hex(a, b):
    sizeof_b = 0

    # get size of b in bits
    while((b >> sizeof_b) > 0):
        sizeof_b += 1

    # align answer to nearest 4 bits (hex digit)
    sizeof_b += sizeof_b % 4

    return (a << sizeof_b) | b

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


	def test(self, mess):
		command = self.composePacket(mess)
		response = self.serialSend(command, True)
		data = self.readPacket(response)

		print("\tTEST:\t", data, "\n")

	def getFrequency(self):
		command = self.composePacket("U")
		response = self.serialSend(command, True)
		return self.readPacket(response)


stm = SerialController("COM5", 115200)

print("\n\n\t\tSTM")

stm.test('@')

print("Frequency: ", stm.getFrequency())
print("Frequency: ", stm.getFrequency())
print("Frequency: ", stm.getFrequency())
print("Frequency: ", stm.getFrequency())
print("Frequency: ", stm.getFrequency())


# packet = bytearray()

# packet.append(0x02)
# packet.append(0x10)
# packet.append(0x80)
# packet.append(0x40)
# packet.append(0x3D)
# packet.append(0x30)
# packet.append(0x0D)

# print((stm.serialSend(packet)[3:-3]))