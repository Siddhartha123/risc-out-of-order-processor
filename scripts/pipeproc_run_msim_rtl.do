python3 scripts/getprogram.py src/memory/program.txt
transcript on
if {[file exists rtl_work]} {
	vdel -lib rtl_work -all
}
vlib rtl_work
vmap work rtl_work

set current_dir [pwd]

echo $current_dir
vlog -vlog01compat -work work +incdir+$current_dir/src $current_dir/src/modules/controller.v
vlog -vlog01compat -work work +incdir+$current_dir/src $current_dir/src/utils/lm_sm.v
vlog -vlog01compat -work work +incdir+$current_dir/src $current_dir/src/memory/rom.v
vlog -vlog01compat -work work +incdir+$current_dir/src $current_dir/src/memory/ram.v

vcom -2008 -work work $current_dir/src/utils/count_3bit_sync.vhd
vcom -2008 -work work $current_dir/src/utils/start_pulse_generator.vhd
vcom -2008 -work work $current_dir/src/modules/RR_EX_Reg.vhd
vcom -2008 -work work $current_dir/src/modules/reg_status.vhd
vcom -2008 -work work $current_dir/src/utils/reg_Nbit.vhd
vcom -2008 -work work $current_dir/src/utils/priority_encoder.vhd
vcom -2008 -work work $current_dir/src/utils/pcplusone.vhd
vcom -2008 -work work $current_dir/src/modules/pc_incrementer.vhd
vcom -2008 -work work $current_dir/src/utils/mypkg.vhd
vcom -2008 -work work $current_dir/src/utils/MUX_PE.vhd
vcom -2008 -work work $current_dir/src/modules/MEM_WB_Reg.vhd
vcom -2008 -work work $current_dir/src/modules/IF_ID_Reg.vhd
vcom -2008 -work work $current_dir/src/modules/ID_RR_Reg.vhd
vcom -2008 -work work $current_dir/src/modules/forwarding_unit.vhd
vcom -2008 -work work $current_dir/src/modules/EX_MEM_Reg.vhd
vcom -2008 -work work $current_dir/src/utils/Big_MUX.vhd
vcom -2008 -work work $current_dir/src/modules/alu.vhd
vcom -2008 -work work $current_dir/src/modules/register_file.vhd
vcom -2008 -work work $current_dir/src/modules/hb_updater.vhd
vcom -2008 -work work $current_dir/src/modules/branch_history_table_lru.vhd
vcom -2008 -work work $current_dir/src/cpu.vhd
vcom -2008 -work work $current_dir/src/pipeproc.vhd

vcom -2008 -work work $current_dir/src/testbench/pipeproc_tb.vhd

vsim -t 1ps -L altera -L lpm -L sgate -L altera_mf -L altera_lnsim -L cycloneive -L rtl_work -L work -voptargs="+acc"  pipeproc_tb

add wave *
add wave   \
sim:/pipeproc_tb/uut/CPU/Load_RR_EX \
sim:/pipeproc_tb/uut/CPU/IF_IR_input \
sim:/pipeproc_tb/uut/CPU/ID_IR_Out \
sim:/pipeproc_tb/uut/CPU/RR_IR_Out \
sim:/pipeproc_tb/uut/CPU/EX_IR_Out \
sim:/pipeproc_tb/uut/CPU/MEM_IR_Out \
sim:/pipeproc_tb/uut/CPU/WB_IR_Out \
sim:/pipeproc_tb/uut/CPU/Load_Z_Flag \
sim:/pipeproc_tb/uut/CPU/Load_LW \
sim:/pipeproc_tb/uut/CPU/Z_flag_in \
sim:/pipeproc_tb/uut/CPU/EX_valid \
sim:/pipeproc_tb/uut/CPU/EX_valid_next \
sim:/pipeproc_tb/uut/CPU/MEM_valid \
sim:/pipeproc_tb/uut/CPU/d \
sim:/pipeproc_tb/uut/CPU/alu_out \
sim:/pipeproc_tb/uut/CPU/alu_1 \
sim:/pipeproc_tb/uut/CPU/alu_2 \
sim:/pipeproc_tb/uut/CPU/EX_LSM_first_time \
sim:/pipeproc_tb/uut/CPU/LSM_first_time \
sim:/pipeproc_tb/uut/CPU/rom_address \
sim:/pipeproc_tb/uut/CPU/rom_data \
sim:/pipeproc_tb/uut/CPU/mux_pc_reg \
sim:/pipeproc_tb/uut/CPU/pc_out \
sim:/pipeproc_tb/uut/CPU/sel_MuxPCIn \
sim:/pipeproc_tb/uut/CPU/sel_ALUInp2 \
sim:/pipeproc_tb/uut/CPU/LSM_first_flag \
sim:/pipeproc_tb/uut/CPU/rf_add_out_2 \
sim:/pipeproc_tb/uut/CPU/sel_RegFileAddrOut \
sim:/pipeproc_tb/uut/CPU/pe_out \
sim:/pipeproc_tb/uut/CPU/RR_pe_out \
sim:/pipeproc_tb/uut/CPU/EX_pe_out \
sim:/pipeproc_tb/uut/CPU/MEM_pe_out \
sim:/pipeproc_tb/uut/CPU/WB_pe_out \
sim:/pipeproc_tb/uut/CPU/RR_CW \
sim:/pipeproc_tb/uut/CPU/RR_pc_out \
sim:/pipeproc_tb/uut/CPU/is_one_hot_or_zero \
sim:/pipeproc_tb/uut/CPU/Load_PC \
sim:/pipeproc_tb/uut/CPU/ex_dest \
sim:/pipeproc_tb/uut/CPU/EX_valid \
sim:/pipeproc_tb/uut/CPU/is_dest_r7 \
sim:/pipeproc_tb/uut/CPU/is_not_valid_pulse\
sim:/pipeproc_tb/uut/CPU/EX_stall \
sim:/pipeproc_tb/uut/CPU/EX_stall_out \
sim:/pipeproc_tb/uut/CPU/MEM_valid_next \
sim:/pipeproc_tb/uut/CPU/WB_valid \
sim:/pipeproc_tb/uut/CPU/branch_history_table/wr \
sim:/pipeproc_tb/uut/CPU/branch_history_table/resetn \
sim:/pipeproc_tb/uut/CPU/branch_history_table/N \
sim:/pipeproc_tb/uut/CPU/branch_history_table/ins_addr_inp \
sim:/pipeproc_tb/uut/CPU/branch_history_table/ins_addr \
sim:/pipeproc_tb/uut/CPU/branch_history_table/history_out \
sim:/pipeproc_tb/uut/CPU/branch_history_table/history_inp \
sim:/pipeproc_tb/uut/CPU/branch_history_table/found \
sim:/pipeproc_tb/uut/CPU/branch_history_table/clk \
sim:/pipeproc_tb/uut/CPU/branch_history_table/bta_out \
sim:/pipeproc_tb/uut/CPU/branch_history_table/bta_inp \
sim:/pipeproc_tb/uut/CPU/branch_history_table/bht
property wave -radix unsigned *

view structure
view signals
run -all
