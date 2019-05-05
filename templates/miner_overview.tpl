<div id="miner_overview_frame" style="display:none;">
	<div class="Global_Overview">
		<div class="left">Meine Miner</div>
		<div class="right size25">{$smarty.now|date_format:"%d. %b. %Y"}</div>
		<script type="text/javascript">

			var new_miner = '<div class="inactive_miner new_miner_id">';
				new_miner +='<div class="inactive_miner_head">';
				new_miner +='	<div class="left remove_miner">-<input type="hidden" name="minerID" value="new_miner_id"><input type="hidden" name="TransactionHash" value="new_miner_transactionHash"><input type="hidden" name="minerToken" value="new_miner_minerToken"></div>';
				new_miner +='	<div class="left bold">Miner Identification:</div>';
				new_miner +='	<div class="left miner_name bold">new_miner_name</div>';
				new_miner +='	<div class="left edit_miner"><img src="../images/pencil.png" alt="Edit Name" title="Edit Miner Name"></div>';
		
				new_miner +='	<div class="right helpbox">?</div>';
				new_miner +='	<div class="right miner_version bold">Miner Install:</div>';
				new_miner +='</div>';
	
				new_miner +='<div class="inactive_miner_infos">';
				new_miner +='	<div id="clear">&nbsp;</div>';
				new_miner +='	<div class="left left20">Miner TransactionHash:</div>';
				new_miner +='	<div class="right new_miner_transactionhash right20"><textarea readonly>new_miner_transactionHash</textarea></div>';
				new_miner +='	<div id="clear">&nbsp;</div>';
				new_miner +='	<div class="left left20">Miner Token:</div>';
				new_miner +='	<div class="right new_miner_token right20"><textarea readonly>new_miner_minerToken</textarea></div>';
				new_miner +='	<div id="clear">&nbsp;</div>';
				new_miner +='</div>';
			new_miner +='</div><div id="clear">&nbsp;</div>';

		</script>
		<div class="right addminer">+</div>
	</div>
	<div id="clear">&nbsp;</div>
	<div class="site_donation">
		<div class="left size20">Site Donation in %</div>
		<div class="right helpbox">?</div>
		<div class="donation_bar">
			<input class="single-slider" type="hidden" value="{$minerBenefit.site_benefit_procent}"/>
		</div>
		<div class="left size12">Your Roundtime as
		{if $minerBenefit.role == "admin_miner"}
			Admin
		{else if $minerBenefit.role == "new_miner"}
			New Miner
		{else if $minerBenefit.role == "normal_miner"}
			Normal Miner
		{else if $minerBenefit.role == "pro_miner"}
			Pro Miner
		{else if $minerBenefit.role == "dealer_miner"}
			Dealer Miner
		{/if}:
		({($minerBenefit.round_time_24h)|seconds_to_words})  
		</div>
		<div class="right size12">Your Donation: {($minerBenefit.site_benefit_procent*$minerBenefit.benefit_time)|seconds_to_words} THANK YOU!</div>
		
	</div>
	<div id="clear">&nbsp;</div>
	
	<div class="cat_separator">New or inactive Miner</div>	
	<div id="clear">&nbsp;</div>
	{include file="inactive_miner.tpl"}
	
	<div class="cat_separator">Miner List</div>	
	<div id="clear">&nbsp;</div>
	{include file="miner.tpl"}
	
	
	
	<div id="clear">&nbsp;</div>
	<div id="clear">&nbsp;</div>
</div>