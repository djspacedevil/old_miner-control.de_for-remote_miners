<div class="all_inactive_miner">
{foreach from=$list_all_inactive_miners item=inactive_miner}
	<div class="inactive_miner {$inactive_miner.id}">
		<div class="inactive_miner_head">
			<div class="left remove_miner">-<input type="hidden" name="minerID" value="{$inactive_miner.id}"><input type="hidden" name="TransactionHash" value="{$inactive_miner.transactionHash}"><input type="hidden" name="minerToken" value="{$inactive_miner.minerToken}"></div>
			<div class="left bold">Miner Identification:</div>
			<div class="left miner_name bold">{$inactive_miner.minerName}</div>
			<div class="left edit_miner" id="{$miner.id}"><img src="../images/pencil.png" alt="Edit Name" title="Edit Miner Name"></div>
		
			<div class="right helpbox">?</div>
			<div class="right miner_version bold">Miner Install:</div>
		</div>
	
		<div class="inactive_miner_infos">
			<div id="clear">&nbsp;</div>
			<div class="left left20">Miner TransactionHash:</div>
			<div class="right new_miner_transactionhash right20"><textarea readonly>{$inactive_miner.transactionHash}</textarea></div>
			<div id="clear">&nbsp;</div>
			<div class="left left20">Miner Token:</div>
			<div class="right new_miner_token right20"><textarea readonly>{$inactive_miner.minerToken}</textarea></div>
			<div id="clear">&nbsp;</div>
			{if $inactive_miner.minerPool != ""}
				<div class="left left20">Last seen:</div>
				<div class="right right20">{$inactive_miner.minerTime}</div>
				<div id="clear">&nbsp;</div>
			{/if}
		</div>
	</div>
	<div id="clear">&nbsp;</div>
{/foreach}
</div>