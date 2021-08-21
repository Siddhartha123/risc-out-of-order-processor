from sys import argv


def assembler(instruction):
    machine_inst = ""
    inst = instruction.split(" ")
    print(inst)
    machine_inst += opcode[(inst[0])]
    if (inst[0] == 'ADD' or inst[0] == 'ADC' or inst[0] == 'ADZ' or inst[0] == 'NDU' or inst[0] == 'NDC' or inst[0] == 'NDZ'):
        machine_inst += register_dictionary[(inst[2])]
        machine_inst += register_dictionary[(inst[3])]
        machine_inst += register_dictionary[(inst[1])]
        if(inst[0] == 'ADD' or inst[0] == 'NDU'):
            machine_inst += "000"
        elif(inst[0] == 'ADC' or inst[0] == 'NDC'):
            machine_inst += "010"
        else:
            machine_inst += "001"
    elif(inst[0] == 'ADI'):
        machine_inst += register_dictionary[(inst[2])]
        machine_inst += register_dictionary[(inst[1])]
        machine_inst += inst[3][1:]
    elif(inst[0] == 'LW' or inst[0] == 'SW' or inst[0] == 'BEQ'):
        machine_inst += register_dictionary[(inst[1])]
        machine_inst += register_dictionary[(inst[2])]
        machine_inst += inst[3][1:]
    elif(inst[0] == 'JLR'):
        machine_inst += register_dictionary[(inst[1])]
        machine_inst += register_dictionary[(inst[2])]
        machine_inst += "000000"
    elif(inst[0] == 'JAL' or inst[0] == 'LHI'):
        machine_inst += register_dictionary[(inst[1])]
        machine_inst += inst[2][1:]
    elif(inst[0] == 'NOP' or inst[0] == ''):
        machine_inst += '000000000000'
    else:
        machine_inst += register_dictionary[(inst[1])]
        machine_inst += "0"+inst[2][1:]
    print(machine_inst)
    if(len(machine_inst) != 16):
        raise Exception("Sorry,incorrect instruction")
    file1.write(machine_inst+"\n")


def getcode():
    count = 0
    with open(argv[1], 'r') as f:
        lines = list(line for line in (l.strip() for l in f) if line)
    for i in lines:
        if((i.split(";")[0]) != ''):
            count = count+1
            assembler((i.split(";")[0]).rstrip('\n'))
    return(count)


def fillempty(c):
    for i in range(256-c):
        file1.write("1111000000000000"+"\n")


def fillram():
    file2 = open("src/memory/ram.txt", "w+")
    for i in range(256):
        file2.write("0000"+"\n")
    file2.close()


register_dictionary = {'R0': '000', 'R1': '001', 'R2': '010',
                       'R3': '011', 'R4': '100', 'R5': '101', 'R6': '110', 'R7': '111'}
opcode = {'ADD': '0000', 'ADC': '0000', 'ADZ': '0000', 'ADI': '0001', 'NDU': '0010', 'NDC': '0010', 'NDZ': '0010', 'LHI': '0011',
          'LW': '0100', 'SW': '0101', 'LM': '0110', 'SM': '0111', 'BEQ': '1100', 'JAL': '1000', 'JLR': '1001', 'NOP': '1111'}
lsb_3bits = {'ADD': '000', 'ADC': '010', 'ADZ': '001',
             'NDU': '000', 'NDC': '010', 'NDZ': '001'}
file1 = open("src/memory/rom.txt", "w+")
c = getcode()
print(c)
fillempty(c)
#fillram()
file1.close()
