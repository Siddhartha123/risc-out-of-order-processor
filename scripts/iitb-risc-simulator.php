<?php
/*
    This implements the programmer's model of IITB-RISC ISA.
    Assembly code is read from program.txt.
    The number of instructions to be executed is to be given 
    as a command line argument. 
    e.g. `php iitb-risc-simulator.php 4 <program.txt>` will execute 4 instructions
    All register and flag values are printed after each instruction 
    execution,  however only those RAM locations modified by an instruction
    are printed just after the instruction.
    The simulator assumes correctness of syntax of assembly code provided.
    TODO:
    - include modified ram locations
    - trim all tokens
*/

// registers
$R0 = 0;
$R1 = 0;
$R2 = 0;
$R3 = 0;
$R4 = 0;
$R5 = 0;
$R6 = 0;
$R7 = 0;

// flags
$Z = 0;
$C = 0;

//simulation variables
global $RA, $RB, $RC, $Imm6, $Imm9, $Imm8;


// memory elements
// RAM
$RAM = [];
for ($i = 0; $i < 256; $i += 1)
    $RAM[] = 0;

// load RAM with some default values
// $RAM[3] = 5;
// $RAM[4] = 545;
// $RAM[5] = 8;
// $RAM[6] = 25;
// $RAM[7] = 35;


//Instruction memory
// load program to instruction memory
$file = fopen($argv[2], 'r');
while (!feof($file)) {
    $line = fgets($file);
    // comment removal
    if (strpos("#" . trim($line), ";") > 0) {
        $line = substr($line, 0, strpos($line, ";"));
    }
    if ($line != '') {
        // write to instruction memory
        $ROM[] = trim($line);
    }
}

// run program
for ($ins_count = 0; $ins_count < $argv[1]; $ins_count += 1) {
    run_instruction();
}


function get_vars($tokens)
{
    global $RA, $RB, $RC, $Imm6, $Imm9, $Imm8;
    switch ($tokens[0]) {
        case "ADD":
        case "ADC":
        case "ADZ":
        case "NDU":
        case "NDC":
        case "NDZ":
            $RC = $tokens[1];
            $RA = $tokens[2];
            $RB = $tokens[3];
            break;
        case "ADI":
            $RB = $tokens[1];
            $RA = $tokens[2];
            // $Imm6 calculation
            if (strpos("~" . trim($tokens[3]), "#") == 1)   // binary 
                $Imm6 = bindec(str_replace("#", "", trim($tokens[3])));
            else                                            // decimal
                $Imm6 = intval(trim($tokens[3]));
            break;
        case "LHI":
        case "LM":
        case "SM":
        case "JAL":
            $RA = $tokens[1];
            // $Imm9 calculation
            if (strpos("~" . trim($tokens[2]), "#") == 1)   // binary 
                $Imm9 = bindec(str_replace("#", "", trim($tokens[2])));
            else                                            // decimal
                $Imm9 = intval(trim($tokens[2]));
            // $Imm8 calculation
            if (strpos("~" . trim($tokens[2]), "#") == 1)   // binary 
                $Imm8 = bindec(str_replace("#", "", trim($tokens[2])));
            else                                            // decimal
                $Imm8 = intval(trim($tokens[2]));
            break;
        case "LW":
        case "SW":
        case "BEQ":
        case "JLR":
            $RA = $tokens[1];
            $RB = $tokens[2];
            if ($tokens[0] != "JLR") {
                // $Imm6 calculation
                if (strpos("~" . trim($tokens[3]), "#") == 1)   // binary 
                    $Imm6 = bindec(str_replace("#", "", trim($tokens[3])));
                else                                            // decimal
                    $Imm6 = intval(trim($tokens[3]));
            }
            break;
    }
}

function update_flags($val, $updateC, $updateZ)
{
    global $Z, $C;
    if ($val == 0 && $updateZ == 1)
        $Z = 1;
    else $Z = 0;

    if ($val > 65535 && $updateC == 1)
        $C = $updateC;
    else $C = 0;
}

function show_cpu_snap()
{
    global $R0, $R1, $R2, $R3, $R4, $R5, $R6, $R7, $C, $Z;

    // for testbench use
    for ($i = 0; $i < 8; $i += 1) {
        $reg = "R$i";
        print($$reg . ",");
    }
    print("$C,$Z\n");
}

function run_instruction()
{
    global $ROM, $RAM, $RA, $RB, $RC, $Imm6, $Imm9, $Imm8, $C, $Z;
    global $R0, $R1, $R2, $R3, $R4, $R5, $R6, $R7;
    if (!isset($ROM[$R7]))
        die();
    $ins = $ROM[$R7];

    $tokens = preg_split('/[\ \n\,]+/', $ins);
    get_vars($tokens);
    switch ($tokens[0]) {
        case "ADD":
            $$RC = ($$RA + $$RB);
            update_flags($$RC, 1, 1);
            $$RC = $$RC & 65535;    // limit result to 16 bits
            print("# REG,$RC,".$$RC."\n");
            if ($RC != "R7") $R7 = $R7 + 1;
            break;
        case "ADC":
            if ($C == 1) {
                $$RC = ($$RA + $$RB);
                update_flags($$RC, 1, 1);
                $$RC = $$RC & 65535;    // limit result to 16 bits
                print("# REG,$RC,".$$RC."\n");
                if ($RC != "R7") $R7 = $R7 + 1;
            } else $R7 = $R7 + 1;
            break;
        case "ADZ":
            if ($Z == 1) {
                $$RC = ($$RA + $$RB);
                update_flags($$RC, 1, 1);
                $$RC = $$RC & 65535;    // limit result to 16 bits
                print("# REG,$RC,".$$RC."\n");
                if ($RC != "R7") $R7 = $R7 + 1;
            } else $R7 = $R7 + 1;
            break;
        case "ADI":
            $$RB = $$RA + $Imm6;
            update_flags($$RB, 1, 1);
            $$RB = $$RB & 65535;    // limit result to 16 bits
            print("# REG,$RB,".$$RB."\n");
            if ($RC != "R7") $R7 = $R7 + 1;
            break;
        case "NDU":
            $$RC = ~($$RA & $$RB);
            update_flags($$RC, 0, 1);
            $$RC = $$RC & 65535;    // limit result to 16 bits
            print("# REG,$RC,".$$RC."\n");
            if ($RC != "R7") $R7 = $R7 + 1;
            break;
        case "NDC":
            if ($C == 1) {
                $$RC = ~($$RA & $$RB);
                update_flags($$RC, 0, 1);
                $$RC = $$RC & 65535;    // limit result to 16 bits
                print("# REG,$RC,".$$RC."\n");
                if ($RC != "R7") $R7 = $R7 + 1;
            } else $R7 = $R7 + 1;
            break;
        case "NDZ":
            if ($Z == 1) {
                $$RC = ~($$RA & $$RB);
                update_flags($$RC, 0, 1);
                $$RC = $$RC & 65535;    // limit result to 16 bits
                print("# REG,$RC,".$$RC."\n");
                if ($RC != "R7") $R7 = $R7 + 1;
            } else $R7 = $R7 + 1;
            break;
        case "LHI":
            $$RA = $Imm9 * 128;
            print("# REG,$RA,".$$RA."\n");
            if ($RA != "R7") $R7 = $R7 + 1;
            break;
        case "LW":
            $$RA = $RAM[$$RB + $Imm6];
            print("# REG,$RA,".$$RA."\n");
            update_flags($$RA, 0, 1);
            if ($RA != "R7") $R7 = $R7 + 1;
            break;
        case "SW":
            $RAM[$$RB + $Imm6] == $$RA;
            print("# RAM," . ($$RB + $Imm6) . "," . $$RA . "\n");
            $R7 = $R7 + 1;
            break;
        case "LM":
            $count = 0;
            $base_addr = $$RA;
            for ($i = 0; $i < 8; $i++) {
                if (((1 << $i) & $Imm8) > 0) {
                    $reg = "R" . $i;
                    $$reg = $RAM[$base_addr + $count];
                    print("# REG,$reg,".$$reg."\n");
                    $count += 1;
                }
            }
            // if PC not updated by LM
            if (($Imm8 & (1 << 7)) == 0)
                $R7 = $R7 + 1;  // increment PC
            break;
        case "SM":
            $count = 0;
            $base_addr = $$RA;
            for ($i = 0; $i < 8; $i++) {
                if ((1 << $i & $Imm8) > 0) {
                    $reg = "R" . $i;
                    $RAM[$base_addr + $count] = $$reg;
                    print("# RAM," . ($base_addr + $count) . "," . $$reg . "\n");
                    $count += 1;
                }
            }
            break;
        case  "BEQ":
            if ($$RA == $$RB)
                $R7 = $R7 + $Imm6;
            else
                $R7 = $R7 + 1;
            break;
        case "JAL":
            $$RA = $R7 + 1;
            $$RA = $$RA & 65535;    // limit result to 16 bits
            print("# REG,$RA,".$$RA."\n");
            $R7 = $R7 + $Imm6;
            $R7 = $R7 & 65535;    // limit result to 16 bits
            break;
        case "JLR":
            $$RA = $R7 + 1;
            $$RA = $$RA & 65535;    // limit result to 16 bits
            print("# REG,$RA,".$$RA."\n");
            $R7 = $$RB;
            break;
        default:
            // NOP
            $R7 = $R7 + 1;
    }
    // print_r($tokens);
    // show_cpu_snap();
}
