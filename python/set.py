import sys
import usbtmc
from time import sleep

instr = usbtmc.Instrument("USB0::0x1AB1::0x0E11::DP8C193203752::INSTR")

def reconnect():
	try:
		print("\nReconnecting...")
		instr.ask('*IDN?')
	except Exception as e:
		clear()

def setVoltage(volt):
	command = ":VOLT " + str(volt);
	print("\nSending: '"+ command +"'")
	instr.write(command)


def sweepVoltage(start, end, step, delay):
	for volt in range(start, end+1, step):
		setVoltage(volt)
		sleep(delay)


reconnect()
setVoltage(int(sys.argv[1]))