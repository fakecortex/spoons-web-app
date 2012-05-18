<?php
	
	include "include.php";
	$notes = "";
	$removed = false;
	$action = "None";
	if(substr($_REQUEST['Body'], 0, 5) == 'Spoon') {
		$action = "spoon";
		$spooned = substr($_REQUEST['Body'], 6);
		$spoonfile = file_get_contents($listfilename);
		$contestants = explode("\n", $spoonfile);
		
		$contestants = array_map('trim', $contestants);
		foreach ($contestants as $contestant) {
			if($contestant == $spooned) {
				//$notes .= "Contestant: \"$contestant\", spooned: \"$spooned\"";
				
				$key = array_search($contestant, $contestants);
				
				if($key !== false)
				{
				    unset($contestants[$key]);
				    $contestants = array_values($contestants);
				    
				    if(count($contestants) == $key) {
				  		$target = $contestants[0];
					} else {
						$target = $contestants[$key];
					}
					if($key == 0) {
						$player = $contestants[count($contestants) - 1];
					} else {
						$player = $contestants[$key - 1];
					}
				    $removed = true;
				}
				
				
			}
		}
		
		
		$towrite = "";
		foreach ($contestants as $contestant) {
			$towrite .= $contestant . "\n";
		}
		$towrite = trim($towrite);
		
		
		file_put_contents($listfilename, $towrite);
		
	} elseif (substr($_REQUEST['Body'], 0, 6) == 'Status') {
		$action = "status";
		$search = substr($_REQUEST['Body'], 7);
		$spoonfile = file_get_contents($listfilename);
		$contestants = explode("\n", $spoonfile);
		
		$contestants = array_map('trim', $contestants);
		$status = "Spooned";
		if(array_search($search, $contestants) !== false) {
			$key = array_search($search, $contestants);
			$status = "Not spooned";
			if(count($contestants) == $key + 1) {
				$target = $contestants[0];
			} else {
				$target = $contestants[$key + 1];
			}
		}
	} else {
		exit(0);
	}
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Sms><?php 
		if($action == "spoon") {
	    	if($removed) {
	    		echo "Player $spooned has been eliminated. New target for $player: $target";
	    	} else {
	    		echo "Tried and failed to eliminate \"$spooned\"";
	    	}
    	}
    	elseif ($action == "status") {
    		echo "Player $search status: $status.";
    		if(isset($target)) {
    			echo " $search's target: $target.";
    		}
    	}
    ?></Sms>
</Response>