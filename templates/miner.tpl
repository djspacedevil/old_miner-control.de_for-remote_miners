<div class="all_active_miner">
<div id="clear">&nbsp;</div>
{foreach from=$list_all_active_miners item=miner}
<div class="active_miner {$miner.id}">
	<div class="active_miner_head">
		
		<div class="left remove_miner">-</div>
		<div class="left bold">Miner Identification:</div>
		<div class="left miner_name bold">{$miner.minerName}</div>
		<div class="left edit_miner" id="{$miner.id}"><img src="../images/pencil.png" alt="Edit Name" title="Edit Miner Name"></div>
		
		<div class="right bold">Status [<span id ="{$miner.id}_miner_status">{$miner.minerJSON.SUMMERY.STATUS.0.When|date_format:"%Y-%m-%d %H:%M:%S"}</span>]</div> 
		<div class="right miner_version bold">V:{$miner.minerJSON.SUMMERY.STATUS.0.Description|upper}</div> 
	</div>
	<div class="active_miner_infos">
		<div class="info_row">
			<div class="left av5s">Av: 5s: <span id="{$miner.id}_miner_av5s">
												{if {$miner.minerScrypt} == "sha256"}
													{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS 5s'}|hashpowersha}
												{else}
													{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS 5s'}|hashpower}
												{/if}
										   </span>
			</div> 
			<div class="left av1m">Av: 1m: <span id="{$miner.id}_miner_av1m">
												{if {$miner.minerScrypt} == "sha256"}
													{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS 1m'}|hashpowersha}
												{else}
													{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS 1m'}|hashpower}
												{/if}
											</span>
			</div> 
			<div class="left av5m">Av: 5m: <span id="{$miner.id}_miner_av5m">
												{if {$miner.minerScrypt} == "sha256"}
													{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS 5m'}|hashpowersha}
												{else}
													{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS 5m'}|hashpower}
												{/if}
											</span>
			</div> 
			<div class="left av15m">Av: 15m: <span id="{$miner.id}_miner_av15m">
												{if {$miner.minerScrypt} == "sha256"}
													{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS 15m'}|hashpowersha}
												{else}
													{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS 15m'}|hashpower}
												{/if}
											</span>
			</div> 
			<div class="left avg">Avg: <span id="{$miner.id}_miner_avg">
											{if {$miner.minerScrypt} == "sha256"}
												{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS av'}|hashpowersha}  
											{else}
												{$miner.minerJSON.SUMMERY.SUMMARY.0.{'MHS av'}|hashpower} 
											{/if}
										</span>
			</div>
		</div>
		<div class="info_row2">
			<div class="left accepted">Accepted: <span id="{$miner.id}_miner_accepted">{$miner.minerJSON.SUMMERY.SUMMARY.0.Accepted}</span></div> 
			<div class="left rejected">Rejected: <span id="{$miner.id}_miner_rejected">{$miner.minerJSON.SUMMERY.SUMMARY.0.Rejected}</span></div> 
			<div class="left hardware_errors">Hardware Errors: <span id="{$miner.id}_miner_hardware_errors">{$miner.minerJSON.SUMMERY.SUMMARY.0.{'Hardware Errors'}}</span></div> 
			<div class="left wu">WU:  <span id="{$miner.id}_miner_wu">{$miner.minerJSON.SUMMERY.SUMMARY.0.{'Work Utility'}}</span>/m</div> 
		</div>
		<div class="info_row3">
			<div class="left block" title="{$miner.minerJSON.COIN.0.{'Current Block Hash'}}">Block: <span id="{$miner.id}_miner_block">{$miner.minerJSON.COIN.0.{'Current Block Hash'}|truncate:20:"...":true}</span></div>  
			<div class="left difficult">Difficulty: <span id="{$miner.id}_miner_diff">{$miner.minerJSON.COIN.0.{'Network Difficulty'}|numbertrimmer}</span></div> 
			<div class="left mine_scrypt">Scrypt: <span id="{$miner.id}_miner_scrypt">{$miner.minerScrypt}</span></div>
			<div class="left best_share">Best share: <span id="{$miner.id}_miner_best_share">{$miner.minerJSON.SUMMERY.SUMMARY.0.{'Best Share'}|numbertrimmer}</span></div>
		</div>
		<div class="miner_pool">
			<div class="left bold size25 left10">Pools</div>

			<div class="right loading"><img class="wait_load" src="../images/loading.png"></div>
			<div class="right addnew_pool">Add Pool</div>
			<div class="right edit_config">Edit Configfile</div>
		</div>
		<div class="miner_configfile">
			<textarea class="minerconfigtextfield" id="minerconfig{$miner.id}" rows="20" >{$miner.minerConfig}</textarea>
			<div class="save_configfile right" id="{$miner.id}">Save Config</div>
		</div>
		<div class="add_pool">
			<div class="center">
				<input type="text" name="new_pool{$miner.id}" placeholder="ExampleURL:Port"><br>
				<input type="text" name="new_user{$miner.id}" placeholder="Username"><br>
				<input type="text" name="new_password{$miner.id}" placeholder="Password">
			</div>
			<div id="{$miner.id}" class="right save_new_pool"> Save new Pool </div>
		</div>
		<table class="pool_list">
			<tr>
				<th class="first_th"></th>
				<th class="">URL:Port</th>
				<th class="">User</th>
				<th class="">Status</th>
				<th class="">Pr</th>
				<th class="">GW</th>
				<th class="">Acc</th>
				<th class="">Rej</th>
				<th class="">Last</th>
				<th class="">Best</th>
				<th class="last_th"></th>
			</tr>
			{assign var="pool_row" value=0}
			{foreach from=$miner.minerJSON.POOLS item=pool}
			<tr id="poolid{$pool.POOL}" class="{if $pool.Status == "Dead"}redbackground{else if $pool_row == 0}graybackground{else}whitebackground{/if}">
				<td class="first_td">
					<div id='{$pool.POOL}' class="pool_{if $pool.Priority == 0}on{else}off{/if}"  title="{if $pool.Priority == 0}Running{else}Not Running{/if}"></div>
				</td>
				{assign var="sha_dona_user" value="{$sha_donation_user}_{$miner.id}"}
				{if ($pool.URL|replace:"stratum+tcp://":"" == $sha_donation_pool || $pool.URL|replace:"stratum+tcp://":"" == $scrypt_donation_pool) &&
					  ($pool.User|strstr:$sha_dona_user || $pool.User == $scrypt_donation_user)}
					<td colspan="2">Site Donation</td> 
				{else}
					<td class="pool{$miner.id}">{$pool.URL|replace:"stratum+tcp://":""|truncate:30:"...":true}</td> 
					<td class="">{$pool.User|truncate:20:"...":true}</td> 
				{/if}
				<td class=""><span id="{$miner.id}_miner_{$pool.POOL}_pool_status">{$pool.Status}</span></td> 
				<td class=""><span id="{$miner.id}_miner_{$pool.POOL}_pool_priority">{$pool.Priority}</span></td> 
				<td class=""><span id="{$miner.id}_miner_{$pool.POOL}_pool_getworks">{$pool.Getworks}</span></td> 
				<td class=""><span id="{$miner.id}_miner_{$pool.POOL}_pool_accepted">{$pool.Accepted}</span></td> 
				<td class=""><span id="{$miner.id}_miner_{$pool.POOL}_pool_rejected">{$pool.Rejected}</span></td> 
				<td class=""><span id="{$miner.id}_miner_{$pool.POOL}_pool_last_share_time">{$pool.{'Last Share Time'}|date_format:"%Y-%m-%d %H:%M:%S"}</span></td> 
				<td class=""><span id="{$miner.id}_miner_{$pool.POOL}_pool_best_share">{$pool.{'Best Share'}|numbertrimmer}</span></td>
				<td class=""><img src="../images/removeMiner.png" alt="Remove Pool" class="delPool" PoolID="{$pool.POOL}" MinerID="{$miner.id}"></td>
			</tr>
			{if $pool_row == 0} {$pool_row = 1} {else} {$pool_row = 0} {/if}
			{/foreach}
		</table>
		
		<div class="miner_pool">
			<div class="left bold size25 left10">Devices</div>
		</div>
		<table class="devices_list">
			<tr>
				<th class="first_th">Name</th>
				<th class="">ID</th>
				<th class="">Hashrate 5s</th>
				<th class="">Hashrate AV</th>
				<th class="">Accepted</th>
				<th class="">Rejected</th>
				<th class="">HW Errors</th>
				<th class="">Utility</th>
				<th class="">Last Share Time</th>
			</tr>
			{assign var="dev_row" value=0}
			{foreach from=$miner.minerJSON.DEVICES item=device}
			{math assign="error_procent" equation="((x/(y+z))*100)" x=$device.{'Hardware Errors'} y=$device.Accepted z=$device.Rejected }
			<tr class="{if $device.Status == "Alive"}
							{if $dev_row == 0}
								graybackground
							{else}
								whitebackground
							{/if}
						{else} 
						redbackground
						{/if}
					">	
				<td class="first_td">{$device.Name}</td> 
				<td class="">{$device.ID}</td> 
				<td class=""><span id="{$miner.id}_miner_{$device.ID}_device_MHS_5s">
								{if {$miner.minerScrypt} == "sha256"}
									{$device.{'MHS 5s'}|hashpowersha}
								{else}
									{$device.{'MHS 5s'}|hashpower}
								{/if}
							</span>
				</td> 
				<td class=""><span id="{$miner.id}_miner_{$device.ID}_device_MHS_av">
								{if {$miner.minerScrypt} == "sha256"}
									{$device.{'MHS av'}|hashpowersha}
								{else}
									{$device.{'MHS av'}|hashpower}
								{/if}
							</span>
				</td> 
				<td class=""><span id="{$miner.id}_miner_{$device.ID}_device_accepted">{$device.Accepted}</span></td> 
				<td class=""><span id="{$miner.id}_miner_{$device.ID}_device_rejected">{$device.Rejected}</span></td> 
				<td class="{if $error_procent > 75}
								redbackground
							{else if $error_procent > 50}
								yellowbackground
							{/if}
						  ">
							<span id="{$miner.id}_miner_{$device.ID}_device_hardware_errors">
								{$device.{'Hardware Errors'}}
							</span>
							[<span id="{$miner.id}_miner_{$device.ID}_device_error_procent">{$error_procent|string_format:"%.2f"}</span>%]
				</td>
				<td class=""><span id="{$miner.id}_miner_{$device.ID}_device_utility">{$device.Utility}</span></td> 
				<td class=""><span id="{$miner.id}_miner_{$device.ID}_device_last_share_time">{$device.{'Last Share Time'}|date_format:"%Y-%m-%d %H:%M:%S"}</span></td> 
			</tr>
			{if $dev_row == 0} {$dev_row = 1} {else} {$dev_row = 0} {/if}
			{/foreach}
		</table>
	</div>
</div>
<div id="clear">&nbsp;</div>
{/foreach}
</div>