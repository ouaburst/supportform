/* 		----------------- LOG OUT SCRIPT -------------------		 */

		var IDLE_TIMEOUT = 1200; // 20 mins
		var _idleSecondsCounter = 0;
		document.onclick = function() {
		    _idleSecondsCounter = 0;
		};
		document.onmousemove = function() {
		    _idleSecondsCounter = 0;
		};
		document.onkeypress = function() {
		    _idleSecondsCounter = 0;
		};
		window.setInterval(CheckIdleTime, 1000);
		
		function CheckIdleTime() {
		    _idleSecondsCounter++;
		    var oPanel = document.getElementById("SecondsUntilExpire");
		    if (oPanel)
		        oPanel.innerHTML = (IDLE_TIMEOUT - _idleSecondsCounter) + "";
		    if (_idleSecondsCounter >= IDLE_TIMEOUT) {
		/*         alert("Tid expired!"); */
		        document.location.href = "logout.php";
		    }
		}
		
/* 		----------------- / LOG OUT SCRIPT -------------------		 */
		
