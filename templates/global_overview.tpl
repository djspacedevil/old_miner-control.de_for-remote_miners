<div id="global_overview_frame">
		<div class="Global_Overview">
			<div class="left">Globale &Uuml;bersicht</div>
			<div class="right size25">{$smarty.now|date_format:"%d. %b. %Y"}</div>
			<div class="right loading"><img class="wait_load" src="../images/loading.png"></div>
		</div>
		<div class="cat_separator">Benutzer</div>
		<div id="clear">&nbsp;</div>
		<div class="middle_site">
			<div class="global_reg_miner size40">
				Registrated Miner <br>
				<span id="live_reg_miner">{$CountAllMembers}</span>
			</div>
			<div class="global_active_miner size40">
				Active Miner <br>
				<span id="live_active_miner">{$AllActiveMinerCount}</span>
			</div>
		</div>
		
		<div class="cat_separator">Hash Speed</div>	
		<div id="clear">&nbsp;</div>
		<div class="hash_speed">
			<div class="hash_speed_row">
				<div class="sha-256 left" id="left">
					<div class="left bold left10">Speed SHA-256:</div>
					<div class="right right10" id="speed_sha256">{$sha256|hashpowersha}</div>
				</div>
				<div class="scrypt middle">
					<div class="left bold left10">Speed Scrypt:</div>
					<div class="right right10" id="speed_scrypt">{$scrypt|hashpower}</div>
				</div>
				<div class="scrypt-n right" id="right">
					<div class="left bold left10">Speed Scrypt-N:</div>
					<div class="right right10" id="speed_scryptn">{$scryptn|hashpower}</div>
				</div>
			</div>
			<div class="hash_speed_row">
				<div class="keccak left" id="left">
					<div class="left bold left10">Speed Keccak:</div>
					<div class="right right10" id="speed_keccak">{$keccak|hashpower}</div>
				</div>
				<div class="x11 middle">
					<div class="left bold left10">Speed X11:</div>
					<div class="right right10" id="speed_x11">{$x11|hashpower}</div>
				</div>
				<div class="quark right" id="right">
					<div class="left bold left10">Speed Quark:</div>
					<div class="right right10" id="speed_quark">{$quark|hashpower}</div>
				</div>
			</div>
			<div class="hash_speed_row">
				<div class="groestl left" id="left">
					<div class="left bold left10">Speed Groestl:</div>
					<div class="right right10" id="speed_groestl">{$groestl|hashpower}</div>
				</div>
				<div class="jha middle">
					<div class="left bold left10">Speed JHA:</div>
					<div class="right right10" id="speed_jha">{$jha|hashpower}</div>
				</div>
				<div class="blake-256 right" id="right">
					<div class="left bold left10">Speed Blake-256:</div>
					<div class="right right10" id="speed_blake256">{$blake256|hashpower}</div>
				</div>
			</div>
			<div class="hash_speed_row">
				<div class="neoscrypt left" id="left">
					<div class="left bold left10">Speed NeoScrypt:</div>
					<div class="right right10" id="speed_neoscrypt">{$neoscrypt|hashpower}</div>
				</div>
				<div class="lyra2re right" id="right">
					<div class="left bold left10">Speed Lyra2RE:</div>
					<div class="right right10" id="speed_lyra2re">{$lyra2re|hashpower}</div>
				</div>
			</div>
		</div>
		<div id="clear">&nbsp;</div>
		<div class="cat_separator">TOP 10 Pools</div>	
		<div id="clear">&nbsp;</div>
		{if count($top_pools) > 0}
		<div class="ranking size25">
			<div class="left">
				{foreach key=key  item=top_pool from=$top_pools}
				{($key+1)|string_format:"%02d"}. {$top_pool.config_name} ({$top_pool.config_value})<br>
				{if $key == 4}
			</div>
			<div class="right">
				{/if}
				
				{/foreach}
			</div>
		</div>
		{/if}
		<div id="clear">&nbsp;</div>
		<div class="cat_separator">TOP 10 Miner SHA <div class="right size25"></div></div>	
		<div id="clear">&nbsp;</div>
		{if count($top_miner_sha) > 1}
		<div class="ranking size25">
			<div class="left">
				{foreach key=key  item=top_miner from=$top_miner_sha}
				{($key+1)|string_format:"%02d"}. {$top_miner.username} <span class="size14">{$top_miner.minerSpeed|hashpowersha}</span><br>
				{if $key == 4}
			</div>
			<div class="right">
				{/if}
				
				{/foreach}
			</div>
		</div>
		{else} 
			<div class="center">No SHA Miner, be the first!</div>
		{/if}
		<div id="clear">&nbsp;</div>
		<div class="cat_separator">TOP 10 Miner Scrypt <div class="right size25"></div></div>	
		<div id="clear">&nbsp;</div>
		{if count($top_miner_scrypt) > 1}
		<div class="ranking size25">
			<div class="left">
				{foreach key=key  item=top_miner from=$top_miner_scrypt}
				{($key+1)|string_format:"%02d"}. {$top_miner.username} <span class="size14">{$top_miner.minerSpeed|hashpowersha}</span><br>
				{if $key == 4}
			</div>
			<div class="right">
				{/if}
				
				{/foreach}
			</div>
		</div>
		{else} 
			<div class="center">No Scrypt Miner, be the first!</div>
		{/if} 
		<div id="clear">&nbsp;</div>
		<div class="cat_separator">TOP 10 Contributor by Time <div class="right size25"></div></div>	
		<div id="clear">&nbsp;</div>
		{if count($top_Contributors) > 1}
		<div class="ranking size25">
			<div class="left">
				{foreach key=key  item=top_Contributor from=$top_Contributors}
				{($key+1)|string_format:"%02d"}. {$top_Contributor.username} <span class="size14">{$top_Contributor.complete_benefit_time|seconds_to_words}</span><br>
				{if $key == 4}
			</div>
			<div class="right">
				{/if}
				
				{/foreach}
			</div>
		</div>
		{else} 
			<div class="center">No Contributor, be the first!</div>
		{/if}
		<div id="clear">&nbsp;</div>
		
		<div id="clear">&nbsp;</div>
</div>		