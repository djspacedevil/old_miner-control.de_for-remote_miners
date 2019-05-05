
this.imagePreview = function(){	
	/* CONFIG */
		
		xOffset = -225;
		yOffset = 30;
		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
		
	/* END CONFIG */
	$("a.preview").hover(function(e){
		this.t = this.title;
		this.title = "";	
		var c = (this.t != "") ? "<br/>" + this.t : "";
		$("body").append("<p id='preview'><img src='"+ this.href +"' alt='Image preview' />"+ c +"</p>");								 
		$("#preview")
			.css("top",(e.pageY + xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");						
    },
	function(){
		this.title = this.t;	
		$("#preview").remove();
    });	
	$("a.preview").mousemove(function(e){
		$("#preview")
			.css("top",(e.pageY + xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};

//HashSpeed Converter
	function shaSpeedConvert($speed) {
		var $hash_string = [];
		$hash_string[0] = 'H/s';
		$hash_string[1] = 'KH/s';
		$hash_string[2] = 'MH/s';
		$hash_string[3] = 'GH/s';
		$hash_string[4] = 'TH/s';
		$hash_string[5] = 'PH/s';
		$count = 2;
		while ($speed > 1000) {
			$speed = ($speed/1000);
			$count++;
		}
		$speed = parseFloat($speed).toFixed(2);
		return $speed+" "+$hash_string[$count];
	}
	
	function SpeedConvert($speed) {
		var $hash_string = [];
		$hash_string[0] = 'H/s';
		$hash_string[1] = 'KH/s';
		$hash_string[2] = 'MH/s';
		$hash_string[3] = 'GH/s';
		$hash_string[4] = 'TH/s';
		$hash_string[5] = 'PH/s';
		$count = 0;
		while ($speed > 1000) {
			$speed = ($speed/1000);
			$count++;
		}
		$speed = parseFloat($speed).toFixed(2);
		return $speed+" "+$hash_string[$count];
	}
	
	function numbertrimmer($power) {
		var $hash_string = [];
	
		$hash_string[0] = '';
		$hash_string[1] = 'K';
		$hash_string[2] = 'M';
		$hash_string[3] = 'G';
		$hash_string[4] = 'T';
	
		$count = 0;
		while ($power > 1000) {
			$power = ($power/1000);
			$count++;
		}
	
		$power = parseFloat($power).toFixed(2);
		return $power+" "+$hash_string[$count];
	}
//

// starting the script on page load
$(document).ready(function(){
	
	setInterval(function() {
			$('.wait_load').fadeIn("slow");	
			
            $.post("overview.php", {refresh_global_overview: "true",
									global: "true"
									}).done(function(data) {
				if (data != "") {
					var global_script_speed = $.parseJSON(data);
					
					$('#live_reg_miner').html(global_script_speed.AllUser);
					$('#live_active_miner').html(global_script_speed.ActiveUser);
					
					$.each(global_script_speed.hashspeed, function(i, item) {
						if (item.config_name == 'sha256') {
							$('#speed_'+item.config_name).html(shaSpeedConvert(item.config_value));
						} else {
							$('#speed_'+item.config_name).html(SpeedConvert(item.config_value));
						}
					});
					
					$.each(global_script_speed.AllActiveMiners, function(i, miner) {
						miner.minerJSON = $.parseJSON(miner.minerJSON);
						var miner_status = new Date(miner.minerJSON.SUMMERY.STATUS[0].When*1000).format("Y-m-d H:i:s");
						$("#"+miner.id+"_miner_status").html(miner_status);
						
						if (miner.minerScrypt == "sha256") {
							$("#"+miner.id+"_miner_av5s").html(shaSpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS 5s']));
							$("#"+miner.id+"_miner_av1m").html(shaSpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS 1m']));
							$("#"+miner.id+"_miner_av5m").html(shaSpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS 5m']));
							$("#"+miner.id+"_miner_av15m").html(shaSpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS 15m']));
							$("#"+miner.id+"_miner_avg").html(shaSpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS av']));
						} else {
							$("#"+miner.id+"_miner_av5s").html(SpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS 5s']));
							$("#"+miner.id+"_miner_av1m").html(SpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS 1m']));
							$("#"+miner.id+"_miner_av5m").html(SpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS 5m']));
							$("#"+miner.id+"_miner_av15m").html(SpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS 15m']));
							$("#"+miner.id+"_miner_avg").html(SpeedConvert(miner.minerJSON.SUMMERY.SUMMARY[0]['MHS av']));
						}
						
						$("#"+miner.id+"_miner_accepted").html(miner.minerJSON.SUMMERY.SUMMARY[0].Accepted);
						$("#"+miner.id+"_miner_rejected").html(miner.minerJSON.SUMMERY.SUMMARY[0].Rejected);
						$("#"+miner.id+"_miner_hardware_errors").html(miner.minerJSON.SUMMERY.SUMMARY[0]['Hardware Errors']);
						$("#"+miner.id+"_miner_wu").html(miner.minerJSON.SUMMERY.SUMMARY[0]['Work Utility']);
						
						$("#"+miner.id+"_miner_block").html(miner.minerJSON.COIN[0]['Current Block Hash'].substring(0, 17)+"...");
						$("#"+miner.id+"_miner_diff").html(numbertrimmer(miner.minerJSON.COIN[0]['Network Difficulty']));
						$("#"+miner.id+"_miner_scrypt").html(miner.minerScrypt);
						$("#"+miner.id+"_miner_best_share").html(numbertrimmer(miner.minerJSON.SUMMERY.SUMMARY[0]['Best Share']));
						
						$.each(miner.minerJSON.POOLS, function(i, pool) {
							var pool_last_share = new Date(pool['Last Share Time']*1000).format("Y-m-d H:i:s");
							$("#"+miner.id+"_miner_"+pool.POOL+"_pool_status").html(pool.Status);
							$("#"+miner.id+"_miner_"+pool.POOL+"_pool_priority").html(pool.Priority);
							$("#"+miner.id+"_miner_"+pool.POOL+"_pool_getworks").html(pool.Getworks);
							$("#"+miner.id+"_miner_"+pool.POOL+"_pool_accepted").html(pool.Accepted);
							$("#"+miner.id+"_miner_"+pool.POOL+"_pool_rejected").html(pool.Rejected);
							$("#"+miner.id+"_miner_"+pool.POOL+"_pool_last_share_time").html(pool_last_share);
							$("#"+miner.id+"_miner_"+pool.POOL+"_pool_best_share").html(numbertrimmer(pool['Best Share']));
						});
						
						$.each(miner.minerJSON.DEVICES, function(i, device) {
							if (miner.minerScrypt == "sha256") {
							$("#"+miner.id+"_miner_"+device.ID+"_device_MHS_5s").html(shaSpeedConvert(device['MHS 5s']));
							$("#"+miner.id+"_miner_"+device.ID+"_device_MHS_av").html(shaSpeedConvert(device['MHS av']));
							} else {
								$("#"+miner.id+"_miner_"+device.ID+"_device_MHS_5s").html(SpeedConvert(device['MHS 5s']));
								$("#"+miner.id+"_miner_"+device.ID+"_device_MHS_av").html(SpeedConvert(device['MHS av']));	
							}
							var device_last_share = new Date(device['Last Share Time']*1000).format("Y-m-d H:i:s");
							var error_procent = parseFloat(((device['Hardware Errors']/(device.Accepted+device.Rejected))*100)).toFixed(2);
							$("#"+miner.id+"_miner_"+device.ID+"_device_accepted").html(device.Accepted);
							$("#"+miner.id+"_miner_"+device.ID+"_device_rejected").html(device.Rejected);
							$("#"+miner.id+"_miner_"+device.ID+"_device_hardware_errors").html(device['Hardware Errors']);
							$("#"+miner.id+"_miner_"+device.ID+"_device_utility").html(device.Utility);
							$("#"+miner.id+"_miner_"+device.ID+"_device_last_share_time").html(device_last_share);
							$("#"+miner.id+"_miner_"+device.ID+"_device_last_share_error_procent").html(error_procent);
						});
					});
					
				}
			});
			
			
			$('.wait_load').fadeOut("slow");	
        }, 10000);
	
	
	$('.toggle').toggles({
		drag: true, // allow dragging the toggle between positions
		click: true, // allow clicking on the toggle
		text: {
				on: 'SECURE', // text for the ON position
				off: 'OFF' // and off
		},
		on: true, // is the toggle ON on init
		animate: 250, // animation time
		transition: 'swing', // animation transition,
		checkbox: null, // the checkbox to toggle (for use in forms)
		clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
		width: 50, // width used if not set in css
		height: 20, // height if not set in css
		type: 'modern' // if this is set to 'select' then the select style toggle will be used
	});
	
	$('.toggle').on('toggle', function (e, active) {
		if (active) {
			$.post("overview.php", {set_Auth_Code: "activate", new_Auth_Code: $('#google_code').html()}).done(function(data) {
				if (data != "") alert(data);
			});
		} else {
			$.post("overview.php", {set_Auth_Code: "deactivate", new_Auth_Code: $('#google_code').html()}).done(function(data) {
				if (data != "") alert(data);
			});
		}
	});
	
	$('#Logout').on('click', function() {
		$.post("overview.php", {logout: "exit"}).done(function(data) {
				if (data != "") {
					alert(data);
				} else {
					location.reload();
				}
		});
	});
	
	$('#side_menu_logout').on('click', function() {
		$.post("overview.php", {logout: "exit"}).done(function(data) {
				if (data != "") {
					alert(data);
				} else {
					location.reload();
				}
		});
	});
	
	$('#side_menu_miner').on('click', function() {
		$("div[id*='overview_frame']").fadeOut("fast", function() {
			$('#miner_overview_frame').fadeIn("fast");
		});
	});
	
	$('#side_menu_overview').on('click', function() {
		$("div[id*='overview_frame']").fadeOut("fast", function() {
			$('#global_overview_frame').fadeIn("fast");
		});
	});
	
	$('#side_menu_first_steps').on('click', function() {
		$("div[id*='overview_frame']").fadeOut("fast", function() {
			$('#first_help_overview_frame').fadeIn("fast");
		});
	});
	
	$('.single-slider').jRange({
		from: 1,
		to: 100,
		step: 1,
		scale: [0,25,50,75,100],
		format: '%s',
		width: "100%",
		showLabels: true
	});
	
	
	$('.addminer').on('click', function() {
		$('.wait_load').fadeIn("slow");	
		var data = {"new_miner": "create"};
		var new_miner_id = '';
		var new_miner_transactionHash = '';
		var new_miner_name = '';
		var new_miner_minerToken = '';
		$.post( "overview.php", {new_miner: "create"}).done(function(data) {
			if (data != "") {
				data = jQuery.parseJSON(data);
				new_miner_id = data["id"];
				new_miner_transactionHash = data["transactionHash"];
				new_miner_name = data["minerName"];
				new_miner_minerToken = data["minerToken"];
				
				miner = new_miner.replace(/new_miner_id/g, new_miner_id)
								 .replace(/new_miner_transactionHash/g, new_miner_transactionHash)
				                 .replace(/new_miner_name/g, new_miner_name)
				                 .replace(/new_miner_minerToken/g, new_miner_minerToken);
				
				$(miner).hide().prependTo('.all_inactive_miner').slideDown("slow");
				$('.wait_load').fadeOut("slow");			
			} else {
				alert('Error on Miner create.');
				location.reload();
			}
		}, "json");
	});
	
	$(document).on('click', 'div[class="left remove_miner"]', function() {
		if (confirm("Are you sure?")) {
			$('.wait_load').fadeIn("slow");	
			var input_minerID = $(this).find("input[name='minerID']");
			var input_TransactionHash = $(this).find("input[name='TransactionHash']");
			var input_minerToken = $(this).find("input[name='minerToken']");
			$.post("overview.php", {delMiner: "miner", 
									minerID : input_minerID[0].value,
									TransactionHash: input_TransactionHash[0].value, 
									minerToken: input_minerToken[0].value
									}).done(function(data) {
										if (data != "") {
											alert(data);
										} else {
											$('.inactive_miner.'+input_minerID[0].value).slideUp('slow', function() {
												$('div').remove(".inactive_miner."+input_minerID[0].value);
											});
										}
									});
			$('.wait_load').fadeOut("slow");
		}
	});
	
	$('.edit_miner').on('click', function() {
		var _this = $(this);
		var miner = $(this).attr('id');
		var old_name = $(this).prev("div[class='left miner_name bold']").html();
		var name = window.prompt("New Miner Name?", old_name).replace(/[^a-zA-Z0-9 _]/g,'');
		
		if (old_name != name) {
			$('.wait_load').fadeIn("slow");	
			$.post("overview.php", {editMinerName: "true", 
									minerID : miner,
									newMinerName: name
									}).done(function(data) {
										if (data == "") {
											_this.prev("div[class='left miner_name bold']").html(name);
										} else {
											alert("Error cant set Minername.");
										}
									});
			$('.wait_load').fadeOut("slow");
		}
		
	});
	
	$('.pool_off').on('click', function() {
		$('.wait_load').fadeIn("slow");	
		var id = $(this).attr('id');
		var pool_off = $(this);
		$.post("overview.php", {switchPool : "true",
								newPoolID : id
								}).done(function(data) {
			if (data != "") {
				alert("Cant switch pool. Please retry later.");
			} else {
				$('.pool_on').css('background-image', "url(../images/switch_off.png)").attr('class', 'pool_off');
				pool_off.css('background-image', "url(../images/switch_on.png)").attr('class', 'pool_on');
				alert("Pool will switch on next Sync. This may take up to a minute.");
			}
		});
		$('.wait_load').fadeOut("slow");
	});
	
	$('.edit_config').on('click', function() {
		$('.miner_configfile').slideToggle("slow");
	});
	
	$('.addnew_pool').on('click', function() {
		$('.add_pool').slideToggle("slow");
	});
	
	$('.minerconfigtextfield').on('keydown', function(e) {
		var keyCode = e.keyCode || e.which; 
		if (keyCode == 9) { 
			e.preventDefault(); 
			var start = $(this).get(0).selectionStart;
			var end = $(this).get(0).selectionEnd;

			// set textarea value to: text before caret + tab + text after caret
			$(this).val($(this).val().substring(0, start)
					+ "\t"
					+ $(this).val().substring(end));

			// put caret at right position again
			$(this).get(0).selectionStart = 
			$(this).get(0).selectionEnd = start + 1;
		} 
	});
	
	$('.save_configfile').on('click', function() {
		$('.wait_load').fadeIn("slow");	
		var id = $(this).attr('id');
		var configfile = $("#minerconfig"+id).val(); 
		if (configfile != "") {
			$.post("overview.php", {editMinerConfig : "true",
									minerID : id,
									MinerConfig : configfile
									}).done(function(data) {
			if (data != "") {
				alert("Cant edit Configfile, Please retry later.");
			} else {
				$('.miner_configfile').slideToggle("slow");
			}			
								
			});
		}
		$('.wait_load').fadeOut("slow");
	});
	
	$('.save_new_pool').on('click', function() {
		$('.wait_load').fadeIn("slow");	
		var id = $(this).attr('id');
		var pool =  $("input[name='new_pool"+id+"']").val();
		var username = $("input[name='new_user"+id+"']").val();
		var password = $("input[name='new_password"+id+"']").val();
		
		$.post("overview.php", {newMinerPool : "true",
								MinerID : id,
								Pool : pool,
								User : username,
								Password : password
								}).done(function(data) {
			if (data != "") {
				alert("Cant save new Pool, Please retry later.");
			} else {
				alert("New pool saved. Please wait 1 min to sync with your miner.");
				$('.add_pool').slideToggle("slow");
				$("input[name='new_pool"+id+"']").val("");
				$("input[name='new_user"+id+"']").val("");
				$("input[name='new_password"+id+"']").val("");
			}								
		});
		$('.wait_load').fadeOut("slow");
	});
	
	$('.delPool').on('click', function() {
		if (confirm("Are you sure?")) {
			$('.wait_load').fadeIn("slow");	
			var PoolID = $(this).attr('PoolID');
			var ID = $(this).attr('MinerID');
			
			$.post("overview.php", {delMinerPool : "true",
									minerID : ID,
									Pool : PoolID
									}).done(function(data) {
			if (data != "") {
				alert("Cant delete Pool, Please retry later.");
			} else {
				alert("Pool will delete on next Sync. This may take up to a minute.");
				$("tr[id*='poolid"+PoolID+"']").fadeOut(1000);
			}			
								
			});
			$('.wait_load').fadeOut("slow");
		}
	});
});



